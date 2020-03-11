<?php

namespace Flex360\Pilot\Pilot\Crud;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Fluent;
use File;

class CrudSchema extends Blueprint
{
    public static function getModels()
    {
        $storageDirectory = base_path() . '/storage/crud';
        $modelFile = $storageDirectory . '/models.json';
        if (File::exists($modelFile)) {
            $json = File::get($modelFile);
            $models = (array) json_decode($json);
        } else {
            $models = array();
        }

        return $models;
    }

    public function execute()
    {
        $connection = \Schema::getConnection();
        $this->build($connection, $connection->getSchemaGrammar());
    }

    public function addMyColumn(Fluent $column)
    {
        $type = $column->type;
        $name = $column->name;

        $params = $column->getAttributes();
        unset($params['type']);
        unset($params['name']);

        $this->addColumn($type, $name, $params);
    }
}
