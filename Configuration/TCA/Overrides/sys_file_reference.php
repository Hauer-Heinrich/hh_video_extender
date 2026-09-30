<?php
defined('TYPO3') || die();

call_user_func(function(string $extensionKey) {
    $customColumns = [
        'controls' => [
            // 'displayCond' => 'USER:HauerHeinrich\\HhVideoExtender\\UserFunc\\CheckFile->isLocalFile',
            'exclude' => true,
            'label' => 'LLL:EXT:'.$extensionKey.'/Resources/Private/Language/locallang_tca.xlf:video.options.controls',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 1,
                'items' => [
                    [
                        'label' => 'on',
                    ]
                ],
            ]
        ],
        'loop' => [
            // 'displayCond' => 'USER:HauerHeinrich\\HhVideoExtender\\UserFunc\\CheckFile->isLocalFile',
            'exclude' => true,
            'label' => 'LLL:EXT:'.$extensionKey.'/Resources/Private/Language/locallang_tca.xlf:video.options.loop',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
                'items' => [
                    [
                        'label' => 'on',
                    ]
                ],
            ]
        ],
        'muted' => [
            'displayCond' => 'USER:HauerHeinrich\\HhVideoExtender\\UserFunc\\CheckFile->isLocalFile',
            'exclude' => true,
            'label' => 'LLL:EXT:'.$extensionKey.'/Resources/Private/Language/locallang_tca.xlf:video.options.muted',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
                'items' => [
                    [
                        'label' => 'on',
                    ]
                ],
            ]
        ],
        'preload' => [
            'displayCond' => 'USER:HauerHeinrich\\HhVideoExtender\\UserFunc\\CheckFile->isLocalFile',
            'exclude' => true,
            'label' => 'LLL:EXT:'.$extensionKey.'/Resources/Private/Language/locallang_tca.xlf:video.options.preload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'auto',
                        'value' => 0,
                    ],
                    [
                        'label' => 'metadata',
                        'value' => 1,
                    ],
                    [
                        'label' => 'none',
                        'value' => 2,
                    ],
                ],
                'default' => 0,
            ]
        ],

        'defer' => [
            'displayCond' => 'USER:HauerHeinrich\\HhVideoExtender\\UserFunc\\CheckFile->isExternalFile',
            'exclude' => true,
            'label' => 'LLL:EXT:'.$extensionKey.'/Resources/Private/Language/locallang_tca.xlf:video.options.defer',
            'description' => 'Note: uses javascript to do that',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
                'items' => [
                    [
                        'label' => 'on',
                    ]
                ],
            ]
        ],
        'related_videos' => [
            'displayCond' => 'USER:HauerHeinrich\\HhVideoExtender\\UserFunc\\CheckFile->isExternalFile',
            'exclude' => true,
            'label' => 'LLL:EXT:'.$extensionKey.'/Resources/Private/Language/locallang_tca.xlf:video.options.related_videos',
            'description' => 'LLL:EXT:'.$extensionKey.'/Resources/Private/Language/locallang_tca.xlf:video.options.related_videos.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
                'items' => [
                    [
                        'label' => 'on',
                        'invertStateDisplay' => true,
                    ],
                ],
            ],
        ],
        'aspect_ratio' => [
            'exclude' => true,
            'label' => 'LLL:EXT:'.$extensionKey.'/Resources/Private/Language/locallang_tca.xlf:video.options.aspect_ratio',
            'description' => 'LLL:EXT:'.$extensionKey.'/Resources/Private/Language/locallang_tca.xlf:video.options.aspect_ratio.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => '16:9',
                        'value' => '16:9',
                    ],
                    [
                        'label' => '9:16',
                        'value' => '9:16',
                    ],
                    [
                        'label' => '1:1',
                        'value' => '1:1',
                    ],
                    [
                        'label' => '21:9',
                        'value' => '21:9',
                    ],
                ],
                'default' => '16:9',
            ]
        ],
        'preview_image' => [
            'label' => 'LLL:EXT:'.$extensionKey.'/Resources/Private/Language/locallang_tca.xlf:video.options.preview_image',
            'config' => [
                'type' => 'file',
                'minitems' => 0,
                'maxitems' => 1,
                'allowd' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
            ],
        ],
        'click_preview_image_to_show_video' => [
            'exclude' => true,
            'label' => 'LLL:EXT:'.$extensionKey.'/Resources/Private/Language/locallang_tca.xlf:video.options.click_preview_image_to_show_video',
            'description' => '',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 1,
                'items' => [
                    [
                        'label' => 'on',
                    ]
                ],
            ]
        ],
    ];

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
        'sys_file_reference',
        $customColumns
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
        'sys_file_reference',
        'videoOverlayPalette',
        'loop, muted, preload, defer, related_videos, controls, aspect_ratio, --linebreak--, preview_image, --linebreak--, click_preview_image_to_show_video'
    );
}, 'hh_video_extender');
