<?php

namespace Tests;

use BoxedCode\Laravel\Scout\ElasticsearchEngine;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Scout\Builder;
use Mockery;
use Tests\Fixtures\TestModel;

class ElasticsearchEngineTest extends AbstractTestCase
{
    public function test_update_adds_objects_to_index()
    {
        $client = Mockery::mock('Elasticsearch\Client');
        $client->shouldReceive('bulk')->with([
            'body' => [
                [
                    'update' => [
                        '_id'    => 1,
                        '_index' => 'table',
                        '_type'  => 'table',
                    ],
                ],
                [
                    'doc'           => ['id' => 1],
                    'doc_as_upsert' => true,
                ],
            ],
        ]);

        $engine = new ElasticsearchEngine($client);
        $this->assertNull($engine->update(Collection::make([new TestModel()])));
    }

    public function test_delete_removes_objects_to_index()
    {
        $client = Mockery::mock('Elasticsearch\Client');
        $client->shouldReceive('bulk')->with([
            'body' => [
                [
                    'delete' => [
                        '_id'    => 1,
                        '_index' => 'table',
                        '_type'  => 'table',
                    ],
                ],
            ],
        ]);

        $engine = new ElasticsearchEngine($client);
        $this->assertNull($engine->delete(Collection::make([new TestModel()])));
    }

    public function test_search_sends_correct_parameters_to_elasticsearch()
    {
        $client = Mockery::mock('Elasticsearch\Client');
        $client->shouldReceive('search')->with([
            'index' => 'table',
            'type'  => 'table',
            'body'  => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['simple_query_string' => [
                                'query'            => 'zonda',
                                'default_operator' => 'and', ]],
                            ['match_phrase' => ['foo' => 1]],
                            ['terms'        => ['bar' => [1, 3]]],
                        ],
                    ],
                ],
                'sort' => [
                    ['id' => 'desc'],
                ],
            ],
        ])->andReturn($return = [1, 2, 3]);

        $engine = new ElasticsearchEngine($client);
        $builder = new \Laravel\Scout\Builder(new TestModel(), 'zonda');
        $builder->where('foo', 1);
        $builder->where('bar', [1, 3]);
        $builder->orderBy('id', 'desc');
        $this->assertSame($return, $engine->search($builder));
    }

    public function test_builder_callback_can_manipulate_search_parameters_to_elasticsearch()
    {
        /** @var \Elasticsearch\Client|\Mockery\MockInterface $client */
        $client = Mockery::mock(\Elasticsearch\Client::class);
        $client->shouldReceive('search')->with('modified_by_callback');

        $engine = new ElasticsearchEngine($client);
        $builder = new \Laravel\Scout\Builder(
            new TestModel(),
            'huayra',
            function (\Elasticsearch\Client $client, $query, $params) {
                $this->assertNotEmpty($params);
                $this->assertEquals('huayra', $query);
                $params = 'modified_by_callback';

                return $client->search($params);
            }
        );

        $engine->search($builder);
    }

    public function test_map_correctly_maps_results_to_models()
    {
        $client = Mockery::mock('Elasticsearch\Client');
        $engine = new ElasticsearchEngine($client);

        $builder = Mockery::mock(Builder::class);

        $model = Mockery::mock('Illuminate\Database\Eloquent\Model');
        $model->shouldReceive('getScoutKey')->andReturn('1');
        $model->shouldReceive('getScoutModelsByIds')->once()->with($builder, ['1'])->andReturn($models = Collection::make([$model]));
        $model->shouldReceive('newCollection')->andReturn($models);

        $results = $engine->map($builder, [
            'hits' => [
                'total' => '1',
                'hits'  => [
                    [
                        '_id' => '1',
                    ],
                ],
            ],
        ], $model);

        $this->assertEquals(1, count($results));
    }
}
