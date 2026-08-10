<?php
declare(strict_types=1);

namespace TRAW\AccessRules\Database\Query\Restriction;

use Doctrine\DBAL\ParameterType;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\EnforceableQueryRestrictionInterface;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Frontend access control for tx_news based on per-record group rules.
 *
 * A news record may carry any number of access rules, each listing usergroups and a mode:
 *   - INCLUDE rule: matches when the user is in ALL of its groups (AND). INCLUDE rules combine
 *     with OR.
 *   - EXCLUDE rule: matches when the user is in ANY of its groups (OR), and then hides the record.
 */
final class GroupAccessRuleRestriction implements QueryRestrictionInterface, EnforceableQueryRestrictionInterface
{
    private const string RULE_TABLE = 'tx_accessrules_rule';
    private const string MM_TABLE = 'tx_accessrules_rule_group_mm';

    private const int MODE_INCLUDE = 0;
    private const int MODE_EXCLUDE = 1;

    /**
     * @var int[]|null Current frontend user's (subgroup-resolved) group ids, resolved once.
     */
    private ?array $frontendUserGroups = null;

    public function isEnforced(): bool
    {
        return true;
    }

    public function buildExpression(array $queriedTables, ExpressionBuilder $expressionBuilder): CompositeExpression
    {
        if (!$this->isFrontend()) {
            return $expressionBuilder->and();
        }

        $constraints = [];
        foreach ($queriedTables as $alias => $tableName) {
            if ($GLOBALS['TCA'][$tableName]['tx_accessrules']['registered'] ?? false) {
                $constraints[] = $this->tableConstraints($alias, $tableName, $expressionBuilder);
            }
        }

        return $expressionBuilder->and(...$constraints);
    }

    private function tableConstraints(string $alias, string $tableName, ExpressionBuilder $expr): CompositeExpression
    {
        return $expr->and(
        // No INCLUDE rule at all, or the user matches at least one of them.
            $expr->or(
                'NOT EXISTS (' . $this->rulesQuery($alias, $tableName, self::MODE_INCLUDE, false) . ')',
                'EXISTS (' . $this->rulesQuery($alias, $tableName, self::MODE_INCLUDE, true) . ')'
            ),
            // No EXCLUDE rule the user matches.
            'NOT EXISTS (' . $this->rulesQuery($alias, $tableName, self::MODE_EXCLUDE, true) . ')'
        );
    }

    /**
     * Subquery for rules of the given mode that belong to the news record and reference at least
     * one group. deleted/disabled are added automatically from the rule table's TCA.
     *
     * When $onlyIfUserMatches is true, the rule only counts when the user matches it: INCLUDE
     * needs the user in ALL of its groups, EXCLUDE needs the user in ANY of its groups.
     */
    private function rulesQuery(string $alias, string $tableName, int $mode, bool $onlyIfUserMatches): string
    {
        $query = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::RULE_TABLE);
        $expr = $query->expr();

        $query
            ->select('rule.uid')
            ->from(self::RULE_TABLE, 'rule')
            ->where(
                $expr->eq('rule.mode', $mode),
                $expr->or(
                    $expr->eq('rule.parent', $query->quoteIdentifier($alias . '.uid')),
                    $expr->eq('rule.parent', $query->quoteIdentifier($alias . '.l10n_parent'))
                ),
                //why does this not work??
                $expr->eq('rule.parent_table', $query->quote($tableName)),
                'EXISTS (' . $this->groupsQuery('any') . ')'
            );

        if ($onlyIfUserMatches) {
            $query->andWhere($mode === self::MODE_INCLUDE
                // in ALL groups: no referenced group is one the user is missing
                ? 'NOT EXISTS (' . $this->groupsQuery('missing') . ')'
                // in ANY group: at least one referenced group is one the user has
                : 'EXISTS (' . $this->groupsQuery('present') . ')');
        }

        return $query->getSQL();
    }

    /**
     * Subquery over the MM table, correlated to the outer "rule". $filter selects which of the
     * rule's groups to return: 'any' = all of them, 'missing' = those the user is not in,
     * 'present' = those the user is in.
     */
    private function groupsQuery(string $filter): string
    {
        $query = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::MM_TABLE);
        $expr = $query->expr();

        $query
            ->select('mm.uid_local')
            ->from(self::MM_TABLE, 'mm')
            ->where($expr->eq('mm.uid_local', $query->quoteIdentifier('rule.uid')));

        if ($filter === 'missing') {
            $query->andWhere($expr->notIn('mm.uid_foreign', $this->groupIds()));
        } elseif ($filter === 'present') {
            $query->andWhere($expr->in('mm.uid_foreign', $this->groupIds()));
        }

        return $query->getSQL();
    }

    /**
     * Subgroup-resolved group ids of the current frontend user. Fails closed to the anonymous
     * set ([0]) when the frontend.user aspect is unavailable, so a missing context never widens
     * access.
     *
     * @return int[]
     */
    private function groupIds(): array
    {
        if ($this->frontendUserGroups === null) {
            $context = GeneralUtility::makeInstance(Context::class);
            $groups = $context->hasAspect('frontend.user')
                ? $context->getAspect('frontend.user')->getGroupIds()
                : [0];
            $groups = array_values(array_unique(array_map('intval', $groups)));
            $this->frontendUserGroups = $groups === [] ? [0] : $groups;
        }

        return $this->frontendUserGroups;
    }

    private function isFrontend(): bool
    {
        $context = GeneralUtility::makeInstance(Context::class);
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;

        return $request instanceof ServerRequestInterface
            && $request->getAttribute('applicationType') !== null
            && ApplicationType::fromRequest($request)->isFrontend();
    }
}
