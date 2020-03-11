<?php

namespace Flex360\Pilot\Pilot\Traits;

use DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

trait MigrateableTrait
{

    public $autoMigrate = false;

    public function migrate()
    {
        if ($this->needsMigration()) {
            $model = $this;
            $newColumns = $this->getNewColumns();

            foreach ($newColumns as $column) {
                \Schema::table($this->table, function (Blueprint $table) use ($column, $model) {
                    $model->migrateColumn($table, $column);
                });
            }
        }

        return $this;
    }

    public function autoMigrate()
    {
        $this->autoMigrate = true;

        return $this;
    }

    public function needsMigration()
    {
        $newColumns = $this->getNewColumns();

        return ! empty($newColumns);
    }

    public function getNewColumns()
    {
        $columns = $this->getTableColumns();

        $attributes = array_keys($this->attributes);

        return array_diff($attributes, $columns);
    }

    public function getTableColumns()
    {
        return DB::getSchemaBuilder()->getColumnListing($this->table);
    }

    public function migrateColumn($table, $column)
    {
        $value = $this->attributes[$column];

        if (is_numeric($value) && ! is_string($value)) {
            return $table->integer($column);
        }

        if (strlen($value) >= 255) {
            return $table->text($column);
        }

        if (is_bool($value)) {
            return $table->boolean($column);
        }

        return $table->string($column);
    }
}
