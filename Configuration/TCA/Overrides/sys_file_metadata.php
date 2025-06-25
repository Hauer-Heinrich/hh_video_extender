<?php
defined('TYPO3') || die();

call_user_func(function(string $extensionKey) {
    $trackKinds = ['captions', 'subtitles', 'chapters', 'descriptions', 'metadata'];
    $customColumns = [];
    $customColumnsFieldNames = '';

    foreach ($trackKinds as $kind) {
        $fieldName = 'vtt_'.$kind.'_label';
        $customColumnsFieldNames .= $fieldName.',';
        $customColumns[$fieldName] = [
            'exclude' => true,
            // 'l10n_mode' => 'exclude',
            // 'l10n_display' => 'defaultAsReadonly',
            'label' => 'LLL:EXT:'.$extensionKey.'/Resources/Private/Language/locallang_tca.xlf:track.'.$kind.'.label',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
            ],
        ];
    }

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
        'sys_file_metadata',
        $customColumns
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
        'sys_file_metadata',
        'tracks_vtt',
        $customColumnsFieldNames
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'sys_file_metadata',
        '--div--;LLL:EXT:'.$extensionKey.'/Resources/Private/Language/locallang_tca.xlf:div.tracks,--palette--;LLL:EXT:'.$extensionKey.'/Resources/Private/Language/locallang_tca.xlf:palette.tracks;tracks_vtt,',
        4, // type video
        'after:duration'
    );
}, 'hh_video_extender');
