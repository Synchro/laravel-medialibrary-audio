<?php

namespace Synchro\MediaLibrary\Conversions\ImageGenerators;

use FFMpeg\FFMpeg;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Conversions\ImageGenerators\ImageGenerator;
use Spatie\MediaLibrary\Support\ImageFactory;

class AudioWaveform extends ImageGenerator
{
    private int $width = 2048;
    private int $height = 2048;
    private string $foreground = '#113554';
    private string $background = '#CBE2F4';

    public function __construct(array $args = [])
    {
        //Validate width and height
        if (array_key_exists('width', $args) && (int)$args['width'] > 0 && (int)$args['width'] <= 8192) {
            $this->width = (int)$args['width'];
        }
        if (array_key_exists('height', $args) && (int)$args['height'] > 0 && (int)$args['height'] <= 8192) {
            $this->height = (int)$args['height'];
        }
        //Validate colours
        if (array_key_exists('foreground', $args) && preg_match('/^#[A-Fa-f0-9]{6}$/', (string)$args['foreground'])) {
            $this->foreground = (string)$args['foreground'];
        }
        if (array_key_exists('background', $args) && preg_match('/^#[A-Fa-f0-9]{6}$/', (string)$args['background'])) {
            $this->background = (string)$args['background'];
        }
    }

    public function convert(string $file, Conversion $conversion = null): string
    {
        $imageFile = pathinfo($file, PATHINFO_DIRNAME) . '/' . pathinfo($file, PATHINFO_FILENAME) . '.png';

        $ffmpeg = FFMpeg::create(
            [
                'ffmpeg.binaries'  => config('media-library.ffmpeg_path'),
                'ffprobe.binaries' => config('media-library.ffprobe_path'),
            ]
        );

        $audio = $ffmpeg->open($file);
        //This generates a waveform image drawn with the foreground colour on a transparent background
        $waveform = $audio->waveform(
            $this->width,
            $this->height,
            [$this->foreground]
        );
        $waveform->save($imageFile);

        //Read the file back in again so we can fill in the background colour
        //Yes, this is inefficient, but it doesn't look like FFMpeg lets us get our hands on the image data directly
        $image = ImageFactory::load($imageFile);
        //Fill in the background colour.
        //@TODO This method is not ideal; because it doesn't overlay the background correctly,
        //resulting in fringing around the waveform. PRs welcomed to fix this!
        //This function wants a hex colour without a # prefix
        $image->background(str_replace('#', '', $this->background));
        $image->optimize();
        $image->save($imageFile);

        return $imageFile;
    }

    public function requirementsAreInstalled(): bool
    {
        return class_exists('\\FFMpeg\\FFMpeg');
    }

    public function supportedExtensions(): Collection
    {
        return collect(
            [
                'aac',
                'aif',
                'aifc',
                'aiff',
                'flac',
                'm4a',
                'mp3',
                'mp4',
                'ogg',
                'wav',
                'wma',
            ]
        );
    }

    public function supportedMimeTypes(): Collection
    {
        return collect(
            [
                'audio/aac',
                'audio/flac',
                'audio/mp4',
                'audio/mpeg',
                'audio/mpeg3',
                'audio/ogg',
                'audio/vnd.wav',
                'audio/x-aiff',
                'audio/x-flac',
                'video/x-ms-asf',
            ]
        );
    }
}
