<?php

namespace Synchro\MediaLibrary\Conversions\ImageGenerators\Tests;

use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Synchro\MediaLibrary\Conversions\ImageGenerators\AudioWaveform;

class AudioWaveformTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $ffprobe = self::findFfprobePath();
        if (! $ffprobe) {
            $this->markTestSkipped('ffprobe CLI tool is not installed.');
        }
        Config::set('media-library.ffprobe_path', $ffprobe);
        $ffmpeg = self::findFfmpegPath();
        if (! $ffmpeg) {
            $this->markTestSkipped('ffmpeg CLI tool is not installed.');
        }
        Config::set('media-library.ffmpeg_path', $ffmpeg);
    }

    protected function tearDown(): void
    {
        $images = glob(__DIR__ . '/testfiles/*.png');
        if ($images) {
            foreach ($images as $image) {
                unlink($image);
            }
        }

        parent::tearDown();
    }

    #[Test]
    #[DataProvider('audioFiles')]
    public function itConvertsAudioFile(string $audioFilePath): void
    {
        Config::set('media-library.temporary_directory_path', sys_get_temp_dir());
        $generator     = new AudioWaveform();
        $imageFilePath = $generator->convert($audioFilePath);

        $this->assertStringEndsWith('.png', $imageFilePath);

        $info = getimagesize($imageFilePath);
        $this->assertNotFalse($info);
        $this->assertSame(2048, $info[0]);
        $this->assertSame(2048, $info[1]);
        $this->assertSame('image/png', $info['mime']);
    }

    #[Test]
    public function itConvertsAudioFileWithCustomColors(): void
    {
        Config::set('media-library.temporary_directory_path', sys_get_temp_dir());
        $generator     = new AudioWaveform(
            foreground: '#ffffff',
            background: '#000000',
        );
        $imageFilePath = $generator->convert(__DIR__ . '/testfiles/test_mp3.mp3');

        $this->assertStringEndsWith('.png', $imageFilePath);

        $info = getimagesize($imageFilePath);
        $this->assertNotFalse($info);
        $this->assertSame(2048, $info[0]);
        $this->assertSame(2048, $info[1]);
        $this->assertSame('image/png', $info['mime']);
    }

    #[Test]
    public function itConvertsAudioFileWithCustomDimensions(): void
    {
        Config::set('media-library.temporary_directory_path', sys_get_temp_dir());
        $generator     = new AudioWaveform(
            width: 2048,
            height: 512,
        );
        $imageFilePath = $generator->convert(__DIR__ . '/testfiles/test_mp3.mp3');

        $this->assertStringEndsWith('.png', $imageFilePath);

        $info = getimagesize($imageFilePath);
        $this->assertNotFalse($info);
        $this->assertSame(2048, $info[0]);
        $this->assertSame(512, $info[1]);
        $this->assertSame('image/png', $info['mime']);
    }

    public static function audioFiles(): array
    {
        return [
            'aiff' => [__DIR__ . '/testfiles/test_aiff.aiff'],
            'flac' => [__DIR__ . '/testfiles/test_flac.flac'],
            'm4a'  => [__DIR__ . '/testfiles/test_m4a.m4a'],
            'mp3'  => [__DIR__ . '/testfiles/test_mp3.mp3'],
            'ogg'  => [__DIR__ . '/testfiles/test_ogg.ogg'],
            'wav'  => [__DIR__ . '/testfiles/test_wav.wav'],
            'wma'  => [__DIR__ . '/testfiles/test_wma.wma'],
        ];
    }

    public static function findFfprobePath(): string
    {
        $ffprobePath = config('media-library.ffprobe_path');
        if (is_executable($ffprobePath)) {
            return $ffprobePath;
        }

        $output    = [];
        $returnVar = 0;
        exec('which ffprobe', $output, $returnVar);
        if ($returnVar === 0 && count($output) > 0) {
            return $output[0];
        }

        throw new \RuntimeException('FFProbe not found');
    }

    public static function findFfmpegPath(): string
    {
        $ffmpegPath = config('media-library.ffmpeg_path');
        if (is_executable($ffmpegPath)) {
            return $ffmpegPath;
        }

        $output    = [];
        $returnVar = 0;
        exec('which ffmpeg', $output, $returnVar);
        if ($returnVar === 0 && count($output) > 0) {
            return $output[0];
        }

        throw new \RuntimeException('FFMpeg not found');
    }
}
