<?php

namespace BoxedCode\Laravel\Scout;

use Elasticsearch\ClientBuilder as ElasticBuilder;
use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;
use Psr\Log\LoggerInterface;

class ElasticsearchServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind the elastic connection builder to the 
        // container if it's not already present.
        if (!$this->app->bound(ElasticBuilder::class)) {
            $this->app->bind(ElasticBuilder::class, function() {
                return new ElasticBuilder();
            });
        }
    }

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->make(EngineManager::class)->extend('elasticsearch', function() {
            $config = $this->app->make('config');
            
            $builder = $this->app->make(ElasticBuilder::class)
                ->setHosts($config->get('scout.elasticsearch.hosts'));

            if ($config->get('scout.elasticsearch.debug', false)) {
                $builder->setLogger($this->app->make(LoggerInterface::class));
            }

            return new ElasticsearchEngine($builder->build());
        });
    }
}
