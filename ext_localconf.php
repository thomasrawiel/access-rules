<?php
declare(strict_types=1);

defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['DB']['additionalQueryRestrictions'][\TRAW\AccessRules\Database\Query\Restriction\GroupAccessRuleRestriction::class] ??= [];

$typo3Version = (\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class))
    ->getMajorVersion();
if ($typo3Version < 13) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Imaging\IconFactory::class]['overrideIconOverlay'] ??= [];
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Imaging\IconFactory::class]['overrideIconOverlay'][]
        = \TRAW\AccessRules\Hooks\IconOverlay::class;
}
