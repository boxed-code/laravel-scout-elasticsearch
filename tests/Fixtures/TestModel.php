<?php

namespace Tests\Fixtures;

class TestModel extends \Illuminate\Database\Eloquent\Model
{
    public function getIdAttribute()
    {
        return 1;
    }

    public function searchableAs()
    {
        return 'table';
    }

    public function getKey()
    {
        return '1';
    }

    public function toSearchableArray()
    {
        return ['id' => 1];
    }
}
