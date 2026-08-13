<?php
declare(strict_types=1);

namespace TRAW\AccessRules\Events;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Database\Event\AlterTableDefinitionStatementsEvent;

#[AsEventListener(identifier: 'traw-access-rules/db-definition', event: AlterTableDefinitionStatementsEvent::class)]
final readonly class AlterTableDefinitionStatementsEventListener
{
    public function __invoke(AlterTableDefinitionStatementsEvent $event): void
    {
        $GLOBALS['TCA']['tx_accessrules_rule']['registered'] ??= [];

        $sqlPattern = "CREATE TABLE `%s` (`tx_accessrules_rules` INT(11) unsigned DEFAULT '0' NOT NULL);";

        foreach ($GLOBALS['TCA']['tx_accessrules_rule']['registered'] as $tableName) {
            $event->addSqlData(sprintf($sqlPattern, $tableName));
        }
    }
}
