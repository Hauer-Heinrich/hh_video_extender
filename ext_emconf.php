<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "hh_video_extender".
 *
 * Auto generated 04-05-2026 09:10
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
    'title' => 'Hauer-Heinrich - Video Extender',
    'description' => 'Hauer-Heinrich - Extends sys_file_reference video/media. Added attributes to select in content element (eg textmedia) like: muted, loop, controls, previewImage and so on.',
    'category' => 'fe',
    'version' => '0.4.1',
    'state' => 'beta',
    'uploadfolder' => false,
    'clearcacheonload' => false,
    'author' => 'Christian Hackl',
    'author_email' => 'chackl@hauer-heinrich.de',
    'author_company' => 'www.hauer-heinrich.de',
    'constraints' =>
    array (
        'depends' =>
        array (
        'typo3' => '13.4.0-14.3.99',
        'fluid_styled_content' => '13.4.0-14.3.99',
        ),
        'conflicts' =>
        array (
        ),
        'suggests' =>
        array (
        ),
    ),
);

