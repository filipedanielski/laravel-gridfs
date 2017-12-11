<?php

class TestCase extends Orchestra\Testbench\TestCase
{   
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            Jenssegers\Mongodb\MongodbServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application    $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        //$app['path.base'] = __DIR__ . '/../src';
        $config = require 'config/database.php';
        $app['config']->set('app.key', 'ZsZewWyUJ5FsKp9lMwv4tYbNlegQilM7');
        $app['config']->set('database.default', 'mongodb');
        $app['config']->set('database.connections.mongodb', $config['connections']['mongodb']);        
        $app['config']->set('cache.driver', 'array');
    }
}