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
use \TYPO3\CMS\Core\Resource\FileReference;
use \TYPO3\CMS\Core\Site\SiteFinder;

class VideoTagRenderer extends \TYPO3\CMS\Core\Resource\Rendering\VideoTagRenderer {

    /**
     * Render for given File(Reference) HTML output
     *
     * @param FileInterface $file
     * @param int|string $width TYPO3 known format; examples: 220, 200m or 200c
     * @param int|string $height TYPO3 known format; examples: 220, 200m or 200c
     * @param array $options controls = TRUE/FALSE (default TRUE), autoplay = TRUE/FALSE (default FALSE), loop = TRUE/FALSE (default FALSE)
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

        $params = ['loop', 'muted'];
        if($file instanceof FileReference) {
            foreach ($params as $param) {
                if(!isset($options[$param])) {
                    $val = $file->getProperty($param);
                    if($val !== null) {
                        $options[$param] = $val;
                    }
                }
            }

            // If autoplay isn't set manually check if $file is a FileReference take autoplay from there
            // Set muted for browsersupport: https://www.clickstorm.de/blog/chrome-autoplay-policy/
            if(!isset($options['autoplay'])) {
                $val = $file->getProperty('autoplay');
                if($val !== null) {
                    $options['autoplay'] = $val;
                    $options['muted'] = $val;
                }
            }
        }

        if(!isset($options['preload'])) {
            $val = $file->getProperty('preload');
            if($val !== null) {
                switch ($val) {
                    case '0':
                        $options['preload'] = 'auto';
                        break;
                    case '1':
                        $options['preload'] = 'metadata';
                        break;
                    default:
                        $options['preload'] = 'none';
                        break;
                }
            }
        }

        $attributes = [];
        $attributes[] = 'playsinline'; // WebKit for autoplay set default playsinline: https://html.spec.whatwg.org/multipage/media.html#attr-video-playsinline
        if (isset($options['additionalAttributes']) && is_array($options['additionalAttributes'])) {
            $attributes[] = GeneralUtility::implodeAttributes($options['additionalAttributes'], true, true);
        }
        if (isset($options['data']) && is_array($options['data'])) {
            array_walk($options['data'], function (&$value, $key) {
                $value = 'data-' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
            });
            $attributes[] = implode(' ', $options['data']);
        }
        if ((int)$width > 0) {
            $attributes[] = 'width="' . (int)$width . '"';
        }
        if ((int)$height > 0) {
            $attributes[] = 'height="' . (int)$height . '"';
        }
        if($file->getProperty('controls') === 1) {
            $attributes[] = 'controls';
        }
        if (!empty($options['autoplay'])) {
            $attributes[] = 'autoplay';
        }
        if (!empty($options['muted'])) {
            $attributes[] = 'muted';
        }
        if (!empty($options['loop'])) {
            $attributes[] = 'loop';
        }
        if (isset($options['additionalConfig']) && is_array($options['additionalConfig'])) {
            foreach ($options['additionalConfig'] as $key => $value) {
                if ((bool)$value) {
                    $attributes[] = htmlspecialchars($key);
                }
            }
        }

        foreach (['class', 'dir', 'id', 'lang', 'style', 'title', 'accesskey', 'tabindex', 'onclick', 'controlsList', 'preload'] as $key) {
            if (!empty($options[$key])) {
                $attributes[] = $key . '="' . htmlspecialchars($options[$key]) . '"';
            }
        }

        $previewImage = '';
        $previewImageResult = '';

        if(empty($options['autoplay'])) {
            // if override is set and typoscript previewImage is set
            if($typoScript['previewOverride'] === '1' && !empty($typoScript['previewImage'])) {
                $attributes[] = 'poster="'.$typoScript['previewImage'].'"';

                if($typoScript['showAllwaysPreviewImageAsImage'] === '1') {
                    list($previewImageWidth, $previewImageHeight) = getimagesize($typoScript['previewImage']);
                    $previewImage .= '<img src="'.$typoScript['previewImage'].'" alt="'.$typoScript['previewImage_alt'].'" title="'.$typoScript['previewImage_title'].'" height="'.$previewImageHeight.'" width="'.$previewImageWidth.'" />';
                }
            } else if($typoScript['previewOverride'] === '0') { // standard behavior
                $fileRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\FileRepository::class);
                $fileObjects = $fileRepository->findByRelation('sys_file_reference', 'preview_image', $file->getProperty('uid'));

                if(!empty($fileObjects)) {
                    $attributes[] = 'poster="'.$fileObjects[0]->getPublicUrl().'"';

                    if($typoScript['showAllwaysPreviewImageAsImage'] === '1') {
                        foreach ($fileObjects as $value) {
                            $previewImage .= '<img src="'.$value->getPublicUrl().'" alt="'.$value->getAlternative().'" title="'.$value->getTitle().'" height="'.$value->getProperty('height').'" width="'.$value->getProperty('width').'" />';
                        }
                    }
                } else if(!empty($typoScript['previewImage'])) { // Fallback to previewImage from typoscript / contant-editor if set
                    $attributes[] = 'poster="'.$typoScript['previewImage'].'"';

                    if($typoScript['showAllwaysPreviewImageAsImage'] === '1') {
                        list($previewImageWidth, $previewImageHeight) = getimagesize($typoScript['previewImage']);
                        $previewImage .= '<img src="'.$typoScript['previewImage'].'" alt="'.$typoScript['previewImage_alt'].'" title="'.$typoScript['previewImage_title'].'" height="'.$previewImageHeight.'" width="'.$previewImageWidth.'" />';
                    }
                }
            }

            if(!empty($previewImage)) {
                $previewImageResult .= '<span class="video-preview">';
                $previewImageResult .= $previewImage;
                $previewImageResult .= '</span>';
            }
        }

        // Clean up duplicate attributes
        $attributes = array_unique($attributes);

        $resource = $file->getPublicUrl($usedPathsRelativeToCurrentScript);
        $removedExtension = substr_replace($resource ,"", -1 * \strlen($file->getExtension()));

        if($file instanceof \TYPO3\CMS\Core\Resource\FileReference) {
            $originalFile = $file->getOriginalFile();
        }
        if($file instanceof \TYPO3\CMS\Core\Resource\File) {
            $originalFile = $file;
        }

        $absoluteFilePath = $originalFile->getForLocalProcessing(false);
        $absoluteFilePathRemovedExtension = substr_replace($absoluteFilePath ,"", -1 * \strlen($file->getExtension()));

        // Code for Track(s)-HTML-Tags
        $tracks = '';
        $trackKinds = ['captions', 'subtitles', 'chapters', 'descriptions', 'metadata'];


        $originalFileNameWithExtension = $originalFile->getName();
        $originalFileName = rtrim(substr_replace($originalFileNameWithExtension ,"", -1 * \strlen($file->getExtension())), '.');
        $absoluteFileDirectoryPath = \str_replace($originalFile->getName(), '', $absoluteFilePath);
        $tracksAbsoluteDirectoryPath = $absoluteFileDirectoryPath . $originalFileName . '_tracks/';
        $tracksRelativeDirectoryPath = rtrim($resource, $originalFileNameWithExtension) . $originalFileName . '_tracks/';

        if(is_dir($tracksAbsoluteDirectoryPath)) {
            $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
            $currentSite = $siteFinder->getSiteByPageId($GLOBALS['TSFE']->id);

            // Enthält alle konfigurierten Sprachen
            $availableLanguages = [];
            foreach ($currentSite->getLanguages() as $language) {
                $languageCode = $language->getLocale()->getLanguageCode(); // z. B. "de", "en"
                $label = $language->getTitle(); // z. B. "Deutsch", "English"
                $availableLanguages[$languageCode] = $label;
            }

            foreach ($availableLanguages as $langCode => $langLabel) {
                $langDir = $tracksAbsoluteDirectoryPath . $langCode . '/';
                if (!is_dir($langDir)) {
                    continue;
                }

                foreach ($trackKinds as $kind) {
                    $absoluteFilePath = $tracksAbsoluteDirectoryPath . $langCode . '/' . $kind . '.vtt';
                    if (file_exists($absoluteFilePath)) {
                        $relativeFilePath = $tracksRelativeDirectoryPath . $langCode . '/' . $kind . '.vtt';
                        $tracks .= sprintf(
                            '<track src="%s" kind="%s" srclang="%s" label="%s">' . PHP_EOL,
                            htmlspecialchars($relativeFilePath),
                            $kind,
                            $langCode,
                            $langLabel
                        );
                    }
                }
            }
        }

        $videoSources = '';
        // webm
        if(file_exists($absoluteFilePathRemovedExtension.'webm')) {
            $videoSources .= sprintf(
                '<source src="%s" type="%s">',
                htmlspecialchars($removedExtension.'webm'),
                "video/webm"
            );
        }

        // mp4 - default
        $videoSources .= sprintf(
            '<source src="%s" type="%s">',
            htmlspecialchars($resource),
            $file->getMimeType()
        );

        // ogv - ogg - ogm
        if(file_exists($absoluteFilePathRemovedExtension.'ogv')) {
            $videoSources .= sprintf(
                '<source src="%s" type="%s">',
                htmlspecialchars($removedExtension.'ogv'),
                "video/ogg"
            );
        }
        if(file_exists($absoluteFilePathRemovedExtension.'ogg')) {
            $videoSources .= sprintf(
                '<source src="%s" type="%s">',
                htmlspecialchars($removedExtension.'ogg'),
                "video/ogg"
            );
        }
        if(file_exists($absoluteFilePathRemovedExtension.'ogm')) {
            $videoSources .= sprintf(
                '<source src="%s" type="%s">',
                htmlspecialchars($removedExtension.'ogm'),
                "video/ogg"
            );
        }

        $videoTagBegin = sprintf(
            '<video%s>',
            empty($attributes) ? '' : ' ' . implode(' ', $attributes)
        );
        $videoTagEnd = '</video>';

        return $videoTagBegin . $videoSources . $tracks . $videoTagEnd . $previewImageResult;
    }
}
