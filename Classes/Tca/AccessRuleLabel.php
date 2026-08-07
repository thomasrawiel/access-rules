<?php
declare(strict_types=1);

namespace TRAW\AccessRules\Tca;

use Doctrine\DBAL\ParameterType;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Throwable;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Builds the inline record title for an access rule
 */
#[Autoconfigure(public: true)]
final class AccessRuleLabel
{
    private array $labels;

    public function __construct(
        private readonly LanguageServiceFactory $languageServiceFactory,
    )
    {
        $this->labels = $this->getLanguageService()
            ->getLabelsFromResource('EXT:access_rules/Resources/Private/Language/locallang_tca.xlf');
    }

    private const MM_TABLE = 'tx_accessrules_rule_group_mm';

    public function getLabel(array &$params): void
    {
        $row = $params['row'] ?? [];
        $uid = (int)($this->scalar($row['uid'] ?? 0));
        $isExclude = (int)($this->scalar($row['mode'] ?? 0)) === 1;


        $prefix = $this->translate($isExclude ? 'accessrule.label.exclude' : 'accessrule.label.include');
        $glue = ' ' . $this->translate($isExclude ? 'accessrule.label.or' : 'accessrule.label.and') . ' ';

        $titles = $uid > 0 ? $this->groupTitles($uid) : [];
        $groups = $titles === [] ? $this->translate('accessrule.label.empty') : implode($glue, $titles);

        $params['title'] = trim($prefix . ' ' . $groups);
    }

    /**
     * @return string[]
     */
    private function groupTitles(int $ruleUid): array
    {
        try {
            $qb = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable(self::MM_TABLE);

            $rows = $qb
                ->select('g.title')
                ->from(self::MM_TABLE, 'mm')
                ->join('mm', 'fe_groups', 'g', $qb->expr()->eq('g.uid', $qb->quoteIdentifier('mm.uid_foreign')))
                ->where($qb->expr()->eq('mm.uid_local', $qb->createNamedParameter($ruleUid, ParameterType::INTEGER)))
                ->orderBy('mm.sorting')
                ->executeQuery()
                ->fetchFirstColumn();

            return array_map('strval', $rows);
        } catch (Throwable) {
            return [];
        }
    }

    private function scalar(mixed $value): mixed
    {
        return is_array($value) ? ($value[0] ?? 0) : $value;
    }

    private function translate(string $key): string
    {
        return $this->labels[$key] ?? $key;
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'] ?? $this->languageServiceFactory->createFromUserPreferences($this->getBackendUser());
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
