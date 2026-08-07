<?php
declare(strict_types=1);

namespace TRAW\AccessRules\Tca;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

final class RegisterRules
{
    public static function register(string $tableName, string $typeList = '', string $position = ''): void
    {
        $ll = 'LLL:EXT:access_rules/Resources/Private/Language/locallang_tca.xlf:';
        ExtensionManagementUtility::addTCAcolumns($tableName, [
            'tx_accessrules_rules' => [
                'exclude' => true,
                'l10n_mode' => 'exclude',
                'label' => $ll . 'tca.news.access_rules',
                'description' => $ll . 'tca.news.access_rules.description',
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
        ExtensionManagementUtility::addToAllTCAtypes($tableName, 'tx_accessrules_rules', $typeList, $position);

        $GLOBALS['TCA'][$tableName]['tx_accessrules']['registered'] = true;
    }
}
