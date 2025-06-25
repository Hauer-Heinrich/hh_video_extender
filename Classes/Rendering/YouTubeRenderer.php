<?php
namespace HauerHeinrich\HhVideoExtender\Rendering;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

// use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Resource\FileInterface;

/**
 * YouTube renderer class
 */
class YouTubeRenderer extends \TYPO3\CMS\Core\Resource\Rendering\YouTubeRenderer {

    /**
     * Render for given File(Reference) html output
     *
     * @param FileInterface $file
     * @param int|string $width TYPO3 known format; examples: 220, 200m or 200c
     * @param int|string $height TYPO3 known format; examples: 220, 200m or 200c
     * @param array $options
     * @param bool $usedPathsRelativeToCurrentScript See $file->getPublicUrl()
     * @return string
     */
    public function render(FileInterface $file, $width, $height, array $options = [], $usedPathsRelativeToCurrentScript = false) {
        $configurationManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface');
        $settings = $configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT,
            'hh_video_extender',
            'hhvideoextender'
        );
        $typoScript = $settings['plugin.']['tx_hhvideoextender.']['settings.'];

        $previewImage = '';
        $previewImageResult = '';

        $options = $this->collectOptions($options, $file);
        $options['relatedVideos'] = $file->getProperty('related_videos') ? 1 : 0;
        $options['autoplay'] = $file->getProperty('autoplay');
        $options['loop'] = $file->getProperty('loop');
        $options['controls'] = $file->getProperty('controls') ? 2 : 0;

        if (empty($options['autoplay'])) {
            // if previewImage in TypoScript is set and should override images from content-element
            if($typoScript['previewOverride'] === '1' && !empty($typoScript['previewImage'])) {
                list($previewImageWidth, $previewImageHeight) = getimagesize($typoScript['previewImage']);
                $previewImage .= '<img src="'.$typoScript['previewImage'].'" alt="'.$typoScript['previewImage_alt'].'" title="'.$typoScript['previewImage_title'].'" height="'.$previewImageHeight.'" width="'.$previewImageWidth.'" />';
            } else if($typoScript['previewOverride'] === '0') {
                $fileRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\FileRepository::class);
                $fileObjects = $fileRepository->findByRelation('sys_file_reference', 'preview_image', $file->getProperty('uid'));
                if(!empty($fileObjects)) {
                    foreach ($fileObjects as $value) {
                        $previewImage .= '<img src="'.$value->getPublicUrl().'" alt="'.$value->getAlternative().'" title="'.$value->getTitle().'" height="'.$value->getProperty('height').'" width="'.$value->getProperty('width').'" />';
                    }
                } else if(!empty($typoScript['previewImage'])) {
                    list($previewImageWidth, $previewImageHeight) = getimagesize($typoScript['previewImage']);
                    $previewImage .= '<img src="'.$typoScript['previewImage'].'" alt="'.$typoScript['previewImage_alt'].'" title="'.$typoScript['previewImage_title'].'" height="'.$previewImageHeight.'" width="'.$previewImageWidth.'" />';
                }
            }

            if(!empty($previewImage)) {
                $previewImageResult .= '<span class="video-preview">';
                $previewImageResult .= $previewImage;
                $previewImageResult .= '</span>';
            }
        }

        $src = $this->createYouTubeUrl($options, $file);
        $attributes = $this->collectIframeAttributes($width, $height, $options);

        $string = sprintf(
            '<iframe src="%s"%s></iframe>',
            htmlspecialchars($src, ENT_QUOTES | ENT_HTML5),
            empty($attributes) ? '' : ' ' . $this->implodeAttributes($attributes)
        );

        // If TypoScript default previewImage is set
        if ($file->getProperty('defer') === 1) {
            $newString = str_replace('<iframe', '<iframe class="video-defer"', $string);
            $dataSrc = str_replace('src=', 'data-src=', $newString);

            return $dataSrc.$previewImageResult;
        }

        return $string.$previewImageResult;
    }
}
