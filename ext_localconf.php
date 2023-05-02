<?php
defined('TYPO3') || die();

call_user_func(function() {
    $extensionKey = 'hh_video_extender';

    // automatically add TypoScript, can be disabled in the extension configuration (BE)
    if ($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extensionKey]['config']['typoScript'] === '1') {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('@import "EXT:'.$extensionKey.'/Configuration/TypoScript/constants.typoscript"');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('@import "EXT:'.$extensionKey.'/Configuration/TypoScript/setup.typoscript"');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('@import "EXT:'.$extensionKey.'/Configuration/TypoScript/addFluid.typoscript"');
    }

    // Overwrite Core Classes
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Core\\Resource\\Rendering\\VideoTagRenderer'] = [
        'className' => 'HauerHeinrich\\HhVideoExtender\\Rendering\\VideoTagRenderer'
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Core\\Resource\\Rendering\\YouTubeRenderer'] = [
        'className' => 'HauerHeinrich\\HhVideoExtender\\Rendering\\YouTubeRenderer'
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Core\\Resource\\Rendering\\VimeoRenderer'] = [
        'className' => 'HauerHeinrich\\HhVideoExtender\\Rendering\\VimeoRenderer'
    ];
});
