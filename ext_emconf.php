<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "hh_video_extender".
 *
 * Auto generated 12-08-2022 06:03
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF['hh_video_extender'] = [
    'title' => 'Hauer-Heinrich - Video Extender',
    'description' => 'Hauer-Heinrich - Extends sys_file_reference video/media. Added attributes to select in content element (eg textmedia) like: muted, loop, controls, previewImage and so on.',
    'category' => 'fe',
    'version' => '0.3.0',
    'state' => 'beta',
    'uploadfolder' => false,
    'clearcacheonload' => false,
    'author' => 'Christian Hackl',
    'author_email' => 'chackl@hauer-heinrich.de',
    'author_company' => 'www.hauer-heinrich.de',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.4.99',
            'fluid_styled_content' => '12.4.0-13.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'HauerHeinrich\\HhVideoExtender\\' => 'Classes'
        ],
    ],
];
