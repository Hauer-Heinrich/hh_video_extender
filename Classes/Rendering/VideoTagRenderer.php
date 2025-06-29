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
use TYPO3\CMS\Core\Database\ConnectionPool;

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

        // create video tracks
        $tracks = $this->createVideoTracks($originalFile, $resource, $absoluteFilePath);

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

    /**
     * createVideoTracks
     * creates HTML-track-tags for the HTML-video-tag
     *
     * @param  [type] $originalFile
     * @param  [type] $resource
     * @param  [type] $absoluteFilePath
     * @return string
     */
    public function createVideoTracks($originalFile, $resource, $absoluteFilePath): string {
         // Code for Track(s)-HTML-Tags
        $tracks = '';
        $trackKinds = ['captions', 'subtitles', 'chapters', 'descriptions', 'metadata'];

        $originalFileNameWithExtension = $originalFile->getName();
        // $originalFileName = rtrim(substr_replace($originalFileNameWithExtension ,"", -1 * \strlen($file->getExtension())), '.');
        $originalFileName = pathinfo($originalFileNameWithExtension, PATHINFO_FILENAME);
        $absoluteFileDirectoryPath = \str_replace($originalFile->getName(), '', $absoluteFilePath);
        $tracksRelativeDirectoryPath = rtrim($resource, $originalFileNameWithExtension);

        if (!is_dir($absoluteFileDirectoryPath) || !isset($GLOBALS['TSFE']->id)) {
            return '';
        }

        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $currentSite = $siteFinder->getSiteByPageId($GLOBALS['TSFE']->id);
        $isExtFilemetadataLoaded = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('filemetadata');

        // Enthält alle konfigurierten Sprachen
        $availableLanguages = [];
        if($isExtFilemetadataLoaded) {
            $fileVttLabelsAllLanguagesOriginal = $this->getSysFileMetaDataAllLanguages($originalFile->getUid(), $trackKinds);

            // set array key to languageKey for easier access later
            $fileVttLabelsAllLanguages = [];
            foreach ($fileVttLabelsAllLanguagesOriginal as $value) {
                $fileVttLabelsAllLanguages[$value['sys_language_uid']] = $value;
            }
        }

        foreach ($currentSite->getLanguages() as $language) {
            $languageUid = $language->getLanguageId();
            $languageCode = $language->getLocale()->getLanguageCode(); // z. B. "de", "en"
            $availableLanguages[$languageCode]['uid'] = $languageUid;
            $fallbackLabel = $language->getTitle(); // z. B. "Deutsch", "English"
            $siteLanguageArray = $language->toArray();

            foreach ($trackKinds as $kind) {
                // set label to global values set in site configuration
                $label = $fallbackLabel;

                // Configuration from site-config
                if (!empty($siteLanguageArray['videoTracks']['labels'][$kind])) {
                    $label = $siteLanguageArray['videoTracks']['labels'][$kind];
                }

                // overwrite labels if EXT:filemetadata is enabled and if track label values are given for this file
                if (!empty($fileVttLabelsAllLanguages[$languageUid]['vtt_' . $kind . '_label'])) {
                    $label = $fileVttLabelsAllLanguages[$languageUid]['vtt_' . $kind . '_label'];
                }

                $availableLanguages[$languageCode]['labels'][$kind] = $label;
            }
        }

        foreach ($availableLanguages as $langCode => $lang) {
            if(!empty($lang['labels'])) {
                foreach ($lang['labels'] as $kind => $label) {
                    // special case "default"
                    foreach (['default', null] as $suffix) {
                        $suffix = $suffix ? $suffix : '';
                        $trackFileName = $originalFileName . '.' . $kind . '.' . $langCode . ($suffix ? '.'.$suffix : '') . '.vtt';
                        $absoluteTrackPath = $absoluteFileDirectoryPath . $trackFileName;

                        if (file_exists($absoluteTrackPath)) {
                            $relativeFilePath = $tracksRelativeDirectoryPath . $trackFileName;

                            $tracks .= sprintf(
                                '<track src="%s" kind="%s" srclang="%s" label="%s" %s>' . PHP_EOL,
                                htmlspecialchars($relativeFilePath),
                                $kind,
                                $langCode,
                                $label,
                                $suffix
                            );
                            break;
                        }
                    }
                }
            }
        }

        return $tracks;
    }

    /**
     * getSysFileMetaDataAllLanguages
     * Get all track-kind labels for all available languages
     *
     * @param  integer $fileUid
     * @param  array   $trackKinds
     * @return array
     */
    public function getSysFileMetaDataAllLanguages(int $fileUid, array $trackKinds): array {
        $dbTrackFields = [];
        foreach ($trackKinds as $kind) {
            $dbTrackFields[] = 'vtt_'.$kind.'_label';
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file_metadata');

        return $queryBuilder
            ->select('file', 'sys_language_uid', 'l10n_parent', ...$dbTrackFields)
            ->from('sys_file_metadata')
            ->where(
                $queryBuilder->expr()->eq('file', $queryBuilder->createNamedParameter($fileUid)),
            )
            ->orWhere(
                $queryBuilder->expr()->eq('l10n_parent', $queryBuilder->createNamedParameter($fileUid))
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
