<?php
declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'tx_accessrules_mode_include' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:access_rules/Resources/Public/Icons/TCA/check.svg',
    ],
    'tx_accessrules_mode_exclude' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:access_rules/Resources/Public/Icons/TCA/ban.svg',
    ],
];
