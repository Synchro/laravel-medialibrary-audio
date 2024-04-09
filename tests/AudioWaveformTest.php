<?php

namespace Synchro\MediaLibrary\Conversions\ImageGenerators\Tests;

use Synchro\MediaLibrary\Conversions\ImageGenerators\AudioWaveform;

class AudioWaveformTest extends TestCase
{
    protected function tearDown(): void
    {
        foreach (glob(__DIR__ . '/testfiles/*.png') as $image) {
            unlink($image);
        }

        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider audioFiles
     *
     * @param string $audioFilePath
     */
    public function itConvertsAudioFile(string $audioFilePath): void
    {
        $generator = new AudioWaveform();
        $imageFilePath = $generator->convert($audioFilePath);

        $this->assertStringEndsWith('.png', $imageFilePath);

        $info = getimagesize($imageFilePath);
        $this->assertSame(2048, $info[0]);
        $this->assertSame(2048, $info[1]);
        $this->assertSame('image/png', $info['mime']);
    }

    /** @test */
    public function itConvertsAudioFileWithCustomColors(): void
    {
        $generator = new AudioWaveform(
            foreground: '#ffffff',
            background: '#000000'
        );
        $imageFilePath = $generator->convert(__DIR__ . '/testfiles/test_mp3.mp3');

        $this->assertStringEndsWith('.png', $imageFilePath);

        $info = getimagesize($imageFilePath);
        $this->assertNotFalse($info);
        $this->assertSame(2048, $info[0]);
        $this->assertSame(2048, $info[1]);
        $this->assertSame('image/png', $info['mime']);
    }

    /** @test */
    public function itConvertsAudioFileWithCustomDimensions(): void
    {
        $generator = new AudioWaveform(
            width:  2048,
            height: 512
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
}
