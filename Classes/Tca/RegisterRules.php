<?php
declare(strict_types=1);

namespace TRAW\AccessRules\Tca;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

final class RegisterRules
{
    public static function register(string $tableName, string $typeList = '', string $position = '', ?string $palette = null): void
    {
        if (self::isRegistered($tableName)) {
            throw new \Exception($tableName . ' is already registered');
        }

        $ll = 'LLL:EXT:access_rules/Resources/Private/Language/locallang_tca.xlf:';
        ExtensionManagementUtility::addTCAcolumns($tableName, [
            Rules::FIELDNAME => [
                'exclude' => true,
                'l10n_mode' => 'exclude',
                'label' => $ll . 'tca.tx_accessrule_rule',
                'description' => $ll . 'tca.tx_accessrule_rule.description',
                'config' => [
                    'type' => 'inline',
                    'foreign_table' => 'tx_accessrules_rule',
                    'foreign_field' => 'parent',
                    'foreign_table_field' => 'parent_table',
                    'foreign_sortby' => 'sorting',
                    'appearance' => [
                        'collapseAll' => true,
                        'expandSingle' => true,
                        'levelLinksPosition' => 'top',
                        'useSortable' => true,
                        'showSynchronizationLink' => false,
                        'newRecordLinkTitle' => $ll . 'tca.news.access_rules.add',
                        'enabledControls' => ['info' => false],
                    ],
                ],
            ],
        ]);

        if (!empty($palette)) {
            ExtensionManagementUtility::addFieldsToPalette($tableName, $palette, Rules::FIELDNAME, $position);
        } else {
            ExtensionManagementUtility::addToAllTCAtypes($tableName, Rules::FIELDNAME, $typeList, $position);
        }

        self::registerTableName($tableName);
    }

    private static function registerTableName(string $tableName): void
    {
        $GLOBALS['TCA'][$tableName]['tx_accessrules']['registered'] ??= true;

        $GLOBALS['TCA']['tx_accessrules_rule']['registered'] ??= [];
        if (!in_array($tableName, $GLOBALS['TCA']['tx_accessrules_rule']['registered'], true)) {
            $GLOBALS['TCA']['tx_accessrules_rule']['registered'][] = $tableName;
            sort($GLOBALS['TCA']['tx_accessrules_rule']['registered']);
        }
    }

    public static function isRegistered(string $tableName): bool
    {
        return (bool)($GLOBALS['TCA'][$tableName]['tx_accessrules']['registered'] ?? false)
            && in_array($tableName, $GLOBALS['TCA']['tx_accessrules_rule']['registered'] ?? [], true);
    }
}
