<?php

namespace Wacky159\TelenorMM\Test;

use Orchestra\Testbench\TestCase as Orchestra;
use Wacky159\TelenorMM\TelenorMMServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            TelenorMMServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('services.telenor-mm', [
            'client_id' => 'test-client-id',
            'client_secret' => 'test-client-secret',
            'api_url' => 'https://test.api.url',
            'callback_url' => 'https://test.callback.url',
        ]);
    }
}
