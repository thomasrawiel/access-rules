<?php
defined('TYPO3') or die();

$ll = 'LLL:EXT:access_rules/Resources/Private/Language/locallang_tca.xlf:';

return [
    'ctrl' => [
        'title' => $ll . 'tx_accessrule_rule',
        'label' => 'Access rule',
        'label_userFunc' => \TRAW\AccessRules\Tca\AccessRuleLabel::class . '->getLabel',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'editlock' => 'editlock',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'hideTable' => true,
        'typeicon_column' => 'mode',
        'typeicon_classes' => [
            'default' => 'status-user-group-frontend',
            '0' => 'status-user-group-frontend',
            '1' => 'actions-ban',
        ],
        'enablecolumns' => [
            'disabled' => 'disabled',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'columns' => [
        'pid' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'parent' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'parent_table' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'sorting' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'disabled' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.disabled',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
            ],
        ],
        'mode' => [
            'exclude' => true,
            'label' => $ll . 'accessrule.mode',
            'description' => $ll . 'accessrule.mode.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['label' => $ll . 'accessrule.mode.include', 'value' => 0, 'icon' => 'tx_accessrules_mode_include'],
                    ['label' => $ll . 'accessrule.mode.exclude', 'value' => 1, 'icon' => 'tx_accessrules_mode_exclude'],
                ],
                'fieldWizard' => [
                    'selectIcons' => ['disabled' => false],
                ],
            ],
        ],
        'usergroups' => [
            'exclude' => true,
            'label' => $ll . 'accessrule.usergroups',
            'description' => $ll . 'accessrule.usergroups.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'fe_groups',
                'foreign_table_where' => 'ORDER BY fe_groups.title',
                'MM' => 'tx_accessrules_rule_group_mm',
                'size' => 5,
                'maxitems' => 200,
                'minitems' => 1,
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'mode, usergroups',
        ],
    ],
];
