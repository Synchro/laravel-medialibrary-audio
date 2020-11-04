<?php

 it('converts an mp3 file', function () {
     $sourceBase = __DIR__ . '/testfiles/test.';
     $audioFile = $sourceBase . 'mp3';
     $gen = new \Synchro\MediaLibrary\Conversions\ImageGenerators\AudioWaveform();
     $thumbPath = $gen->convert($audioFile);
     expect($thumbPath)->toEqual($sourceBase . 'png');
 });
