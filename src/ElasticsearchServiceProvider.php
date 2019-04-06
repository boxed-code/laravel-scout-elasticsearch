<?php

namespace BoxedCode\Laravel\Scout;

use Laravel\Scout\EngineManager;
use Illuminate\Support\ServiceProvider;
use Elasticsearch\ClientBuilder as ElasticBuilder;

class ElasticsearchServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        app(EngineManager::class)->extend('elasticsearch', function($app) {
            $client = ElasticBuilder::create()
                ->setHosts(config('scout.elasticsearch.hosts'))
                ->build();

            return new ElasticsearchEngine($client);
        });
    }
}
