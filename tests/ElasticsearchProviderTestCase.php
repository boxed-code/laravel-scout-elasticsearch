<?php

namespace Tests;

use BoxedCode\Laravel\Scout\ElasticsearchEngine;
use BoxedCode\Laravel\Scout\ElasticsearchServiceProvider;
use Elasticsearch\ClientBuilder as ElasticBuilder;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Container\Container;
use Laravel\Scout\EngineManager;
use Mockery;
use Psr\Log\LoggerInterface;
use \Elasticsearch\Client as ElasticClient;

class ElasticsearchServiceProviderTestCase extends AbstractTestCase
{
    public function test_binds_elastic_builder()
    {
        $app = Mockery::mock(Container::class);

        $provider = new ElasticsearchServiceProvider($app);

        $app->shouldReceive('bound')
            ->once()
            ->with(ElasticBuilder::class)
            ->andReturnFalse();

        $app->shouldReceive('bind')
            ->once()
            ->with(ElasticBuilder::class, Mockery::any())
            ->andReturnNull();

        $this->assertNull($provider->register());
    }

    public function test_honors_currently_bound_elastic_builder()
    {
        $app = Mockery::mock(Container::class);

        $provider = new ElasticsearchServiceProvider($app);

        $app->shouldReceive('bound')
            ->once()
            ->with(ElasticBuilder::class)
            ->andReturnTrue();

        $this->assertNull($provider->register());
    }

    protected function setupEngineRegistration()
    {
        $app = Mockery::mock(Container::class);

        $provider = new ElasticsearchServiceProvider($app);

        $config = Mockery::mock(Config::class);

        $app->shouldReceive('make')
            ->twice()
            ->with('config')
            ->andReturn($config);

        $app->shouldReceive('make')
            ->once()
            ->with(EngineManager::class)
            ->andReturn(
                $manager = new EngineManager($app)
            );

        $app->shouldReceive('make')
            ->once()
            ->with(ElasticBuilder::class)
            ->andReturn(
                $builder = Mockery::mock(ElasticBuilder::class)
            );

        $config->shouldReceive('get')
            ->once()
            ->with('scout.elasticsearch.hosts')
            ->andReturn(['127.0.0.1']);

        $builder->shouldReceive('setHosts')
            ->once()
            ->with(['127.0.0.1'])
            ->andReturnSelf();

        $builder->shouldReceive('build')
            ->once()
            ->andReturn(
                $elastic = Mockery::mock(ElasticClient::class)
            );

        return [$app, $config, $provider, $manager, $builder];
    }

    public function test_engine_registration()
    {
        [$app, $config, $provider, $manager, $builder] = $this->setupEngineRegistration();

        $config->shouldReceive('get')
            ->once()
            ->with('scout.elasticsearch.debug', false)
            ->andReturnFalse();

        $provider->boot();

        $this->assertInstanceOf(ElasticsearchEngine::class, $manager->engine('elasticsearch'));
    }

    public function test_engine_registration_with_debug_logger()
    {
        [$app, $config, $provider, $manager, $builder] = $this->setupEngineRegistration();

        $config->shouldReceive('get')
            ->once()
            ->with('scout.elasticsearch.debug', false)
            ->andReturnTrue();

        $app->shouldReceive('make')
            ->once()
            ->with(LoggerInterface::class)
            ->andReturn(
                $logger = Mockery::mock(LoggerInterface::class)
            );

        $builder->shouldReceive('setLogger')
            ->once()
            ->with($logger)
            ->andReturnSelf();

        $provider->boot();

        $this->assertInstanceOf(ElasticsearchEngine::class, $manager->engine('elasticsearch'));
    }
}