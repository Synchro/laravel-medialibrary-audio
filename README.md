# Audio File Thumbnail Generator for Spatie's Laravel Media Library

This audio image generator generates thumbnails for audio files uploaded through [Spatie's Media Library](https://github.com/spatie/laravel-medialibrary), just as it already does for image, video, and PDF formats. By default they look like this:

![Example waveform image](https://github.com/Synchro/laravel-medialibrary-audio/blob/main/tests/testfiles/example_waveform.png)

Spatie's docs have more details of [how plugins integrate with Media Library](https://spatie.be/docs/laravel-medialibrary/v9/converting-other-file-types/creating-a-custom-image-generator).
Thumbnails of a mono waveform of the whole audio file are generated using ffmpeg's `waveform` converter. It uses the same [PHP FFMpeg package](https://packagist.org/packages/php-ffmpeg/php-ffmpeg) that is used for the video formats already supported by Media Library, so there are no additional dependencies.

## Installation & configuration
Install using composer with:

    composer require synchro/laravel-medialibrary-audio 

Installing Spatie's Media Library (version 9.1.0 or later) in your Laravel project will generate a config file in `config/media-library.php`. Add the audio waveform generator to the list of generators in the `image_generators` section, including optionally setting default `width`, `height`, `foreground` and `background` properties (default values shown):

```php
'image_generators' => [
    ...,
    Synchro\MediaLibrary\Conversions\ImageGenerators\AudioWaveform::class => [
        'width' => 2048,
        'height' => 2048,
        'foreground' => '#113554',
        'background' => '#CBE2F4',
    ]
],
```

These parameters are optional - you can leave them out if you're happy with the defaults.

## Thumbnail colours
The waveform is drawn in the foreground colour, over the background colour. Both should be specified using standard HTML 6-digit hex values (with a leading `#`) passed through the media library config, as above. You may also pass an empty string for background to generate a transparent background; make sure you use ->format('png') when registering media conversions on the model.

## Thumbnail sizing
The base size of the thumbnails can be set via the media library config, as shown above. The default is 2048 x 2048 pixels, neutral values chosen because audio files have no inherent size or aspect ratio. This is quite large, but since the images are very simple, they will compress very well in PNG format.
This size doesn't directly affect the thumbnails delivered to clients because media library itself generates scaled-down versions to match client requests, however, it does have a direct effect on the aspect ratio of the thumbnails, so if you want a 16:9 ratio, change the height to `1152`, or `1536` for 4:3. 

## Supported formats

* `aiff`
* `flac`
* `m4a`
* `mp3`
* `mp4`
* `ogg`
* `wav`
* `wma`

## Running tests

If your local ffmpeg installation is in a different path you can use the environment variables to define your own path:

```bash
FFMPEG_PATH=/usr/local/bin/ffmpeg FFPROBE_PATH=/usr/local/bin/ffprobe vendor/bin/phpunit
```
Or on recent macOS with homebrew:

```bash
FFMPEG_PATH=/opt/homebrew/bin/ffmpeg FFPROBE_PATH=/opt/homebrew/bin/ffprobe vendor/bin/phpunit
```
