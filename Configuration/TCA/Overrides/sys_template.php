<?php
defined('TYPO3') || die();

call_user_func(function(string $extensionKey) {
    // If automatically include of TypoScript is disabled, then you can include it in the (BE) static-template select-box
    if (
        isset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extensionKey]['config']['typoScript'])
        && $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extensionKey]['config']['typoScript'] === '0'
    ) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            $extensionKey,
            'Configuration/TypoScript',
            'Hauer-Heinrich - Video Extender'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            $extensionKey,
            'Configuration/TypoScript/addFluid.typoscript',
            'Hauer-Heinrich - Video Extender: add fluid html'
        );
    }
}, 'hh_video_extender');
