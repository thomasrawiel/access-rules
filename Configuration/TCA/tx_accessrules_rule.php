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
            (string)\TRAW\AccessRules\Tca\Rules::MODE_INCLUDE => 'tx_accessrules_mode_include',
            (string)\TRAW\AccessRules\Tca\Rules::MODE_EXCLUDE => 'tx_accessrules_mode_exclude',
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
    'types' => [
        '0' => [
            'showitem' => 'mode, match, usergroups',
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
                    ['label' => $ll . 'accessrule.mode.include', 'value' => \TRAW\AccessRules\Tca\Rules::MODE_INCLUDE, 'icon' => 'tx_accessrules_mode_include'],
                    ['label' => $ll . 'accessrule.mode.exclude', 'value' => \TRAW\AccessRules\Tca\Rules::MODE_EXCLUDE, 'icon' => 'tx_accessrules_mode_exclude'],
                ],
                'fieldWizard' => [
                    'selectIcons' => ['disabled' => false],
                ],
            ],
        ],
        'match' => [
            'exclude' => true,
            'label' => $ll . 'accessrule.match',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['label' => $ll . 'accessrule.match.any', 'value' => \TRAW\AccessRules\Tca\Rules::MATCH_ANY],
                    ['label' => $ll . 'accessrule.match.all', 'value' => \TRAW\AccessRules\Tca\Rules::MATCH_ALL],
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
];
