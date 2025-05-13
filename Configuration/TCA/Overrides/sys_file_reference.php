<?php
defined('TYPO3') || die();

$customColumns = [
    'controls' => [
        // 'displayCond' => 'USER:HauerHeinrich\\HhVideoExtender\\UserFunc\\CheckFile->isLocalFile',
        'exclude' => true,
        'label' => 'Controls',
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
        'label' => 'Loop',
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
        'label' => 'muted (automatically on autoplay)',
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
        'label' => 'preload',
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
        'label' => 'Defer loading',
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
    'relatedVideos' => [
        'displayCond' => 'USER:HauerHeinrich\\HhVideoExtender\\UserFunc\\CheckFile->isExternalFile',
        'exclude' => true,
        'label' => 'Disable related videos',
        'description' => 'Works only for youtube, still shows channel own videos',
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
    'preview_image' => [
        'label' => 'preview image',
        'config' => [
            'type' => 'file',
            'minitems' => 0,
            'maxitems' => 1,
            'allowd' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
        ],
    ],
    'click_preview_image_to_show_video' => [
        'exclude' => true,
        'label' => 'Show the video only after clicking on the preview image',
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
    'loop, muted, preload, defer, relatedVideos, controls, --linebreak--, preview_image, --linebreak--, click_preview_image_to_show_video'
);
