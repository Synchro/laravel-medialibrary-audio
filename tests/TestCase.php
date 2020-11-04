<?php

namespace Synchro\MediaLibrary\Conversions\ImageGenerators\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            MediaLibraryServiceProvider::class,
        ];
    }
}
