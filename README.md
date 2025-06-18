# hh_video_extender
hh_video_extender is a TYPO3 extension.
Extends sys_file_reference video/media. Added attributes to select in content element (eg textmedia) e.g.: muted, loop, controls, previewImage and so on.

Extends TYPO3 core videorenderer with properties like:
- for vimeo and youtube: defer loading (uses javascript to load video resource after page load)
- for youtube: loop, controls, relatedVideos, autoplay
- for internal videos webp, mp4 and so on: loop, muted, preload, controls

### Installation
... like any other TYPO3 extension [extensions.typo3.org](https://extensions.typo3.org/extension/hh_video_extender/ "TYPO3 Extension Repository")

#### Using the site set as dependency in a site config
https://docs.typo3.org/m/typo3/reference-coreapi/13.4/en-us/ApiOverview/SiteHandling/SiteSets.html#site-sets-usage

```
base: 'https://example.com/'
rootPageId: 1
dependencies:
  - hauerheinrich/hh-video-extender
  - hauerheinrich/hh-video-extender/FluidCe (optional, or)
```

### Features
- added default html5 video attributes to enable at the TYPO3 backend
- no changes in FLUID required
- automatically add TypoScript, can be disabled in the extension configuration (BE). Then you can choose it from template-modul = static-templates.
- previewImage / poster-image can be set in the backend directly in the content-element
- defaultPreviewImage can be set too as fallback (constants-editor)
- you can overwrite the previewImage witch is set at the content-element (constants-editor)
- you can output the preview image always as additonal img-tag instead of poster-attribute (constants-editor)
- determines whether in the same directory as the mp4 video synonymous webm or ogg / ogv / ogm videos are with the same name, if so they are added as source
- usage of html5 video tracks-tags see below.

### Todos
- move preview-image to a css background solution

### Deprecated
- currently nothing

## Explanations
- Option "defer" uses JavaScript which is included, but it is recommended to use your own JavaScript or copy this to your JavaScript library. Cause of loading extra file for a view lines of code.

### TypoScript Constants
- TypoScript constants are editable via the Constants-Editor at the TYPO3 backend.

##### includeCss
Enable or disable automaticaly inclusion of the CSS file(s) of this Extension (default: on).
The file is automatically included only when it is needed. See FLUID file: [Video.html](Resources/Private/Partials/Media/Type/Video.html).
##### includeJavaScript
Enable or disable automaticaly inclusion of the JavaScript file(s) of this Extension (default: on).
The file is automatically included only when it is needed. See FLUID file: [Video.html](Resources/Private/Partials/Media/Type/Video.html).
##### includeJavaScriptChapters
Include JavaScript code for video chapter generation? (regardless of includeJavaScript option!)
See video tracks section below!
The file is automatically included only when it is needed. See FLUID file: [Video.html](Resources/Private/Partials/Media/Type/Video.html).
##### previewImage
Set the path to the default previewImage, this image is also used as fallback if no PreviewImage is set at the ContentElement (CE).
##### previewImage_alt
Set the previewImage html-alt-attribute.
##### previewImage_title
Set the previewImage html-title-attribute.
##### previewOverride
Overwrites the preview image that was inserted via the ContentElement (CE) (default: off).
##### showAllwaysPreviewImageAsImage
Default show PreviewImage as html-poster-attribute. If you turn this option on, the PreviewImage will always be output as img tag as well (default: off).
Only valid for local videos, not for youtube and vimeo etc!
##### clickPreviewImageToShowVideo
Should the video only be visible after clicking on the PreviewImage? Default: off - as soon as the video is loaded, the video is displayed immediately. This option only hides the preview-image (-wrapper), no autoplay of the video!
This option uses JavaScript which is included, but it is recommended to use your own JavaScript or copy this to your JavaScript library. Cause of loading extra file for a view lines of code.
(default: off).

#### Video tracks-tags - Usage (currently)
For example, you have a video file in "fileadmin/user_uploads/my_videos/" and the video is called your_video_name.mp4", then you need to create a folder with the same name of the video plus the suffix "_tracks"(!), in our example: "fileadmin/user_uploads/my_videos/your_video_name_tracks/".
Within this folder, you must create a folder for each language you want to use, for example "en" or ‘de’ ("fileadmin/user_uploads/my_videos/your_video_name_tracks/en/").
You can then create your ".vtt" files - i.e. "captions.vtt", "chapters.vtt", "descriptions.vtt", "subtitles.vtt" - within this language folder.
You can find examples of these here: [VTT files](Examples/VTT/).

##### If you want custom track-labels
Default (fallback): uses the "title" of the language from your site config.yaml
Custom:
Add an entry below "videoTracks" -> "labels" for each language in your site config.yaml for each type of track (structure is important), e. g.:
```
languages:
  -
    title: English
    enabled: true
    languageId: '1'
    base: /en/
    typo3Language: en
    locale: en_US.UTF-8
    iso-639-1: en
    navigationTitle: EN
    hreflang: en
    direction: ltr
    fallbackType: free
    fallbacks: '0'
    flag: en-us-gb
    videoTracks:
      labels:
        captions: custom label for caption
        subtitles: label customized for subtiles
```

HINT:
- after changeing the site-config, clear the cache :)
- "chapters" are loaded depending on the html-tag attribute "lang" (with fallback to english if available).
- currently only languages are loaded that are also stored in the TYPO3 site-config.yaml.

More information about VTT files
[Web_Video_Text_Tracks_Format](https://developer.mozilla.org/en-US/docs/Web/API/WebVTT_API/Web_Video_Text_Tracks_Format ".mozilla.org WebVTT")


### IMPORTENT NOTICE
- Vimeo is not tested!
- If you have problems with autoplay video on IE/EDGE then try to set "options.preload" to "auto".
- poster attribute only works if preload is set to 'none', but you have the option to set the preview image as img-tag: see features

![example picture from backend](github/images/preview.jpg?raw=true "Title")

### Development
#### Setup
1. `npm install`

#### Usage of Build Tools
- `npm run init` (only needed on first setup)
- `npm run build` (build css, css nesting, js, sourcemaps & other files)
- `npm run watch` (build css, css nesting, js, sourcemaps & other files everytime a file changes)
- `npm run options` (show current build configuration/options)

More informations/docs about the esbuild usage: https://github.com/iocron/esbuild-template-starter

##### Copyright notice

This repository is part of the TYPO3 project. The TYPO3 project is
free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

The GNU General Public License can be found at
http://www.gnu.org/copyleft/gpl.html.

This repository is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

This copyright notice MUST APPEAR in all copies of the repository!

##### License
----
GNU GENERAL PUBLIC LICENSE Version 3
