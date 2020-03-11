<?php

namespace Flex360\Pilot\Pilot\Crud;

use Illuminate\Support\Fluent;
use Flex360\Pilot\Pilot\Crud\CrudSchema;
use File;
use Input;

trait CrudableTrait
{

    public static function crudBoot()
    {
        $schema = with(new static)->getSchema();
        if (is_object($schema)) {
            $columns = $schema->getColumns();
        } else {
            $columns = array();
        }

        self::saving(function ($item) use ($columns) {
            foreach ($columns as $column) {
                if ($column->type == 'dateTime') {
                    $item->{$column->name} = date('Y-m-d H:i:s', strtotime($item->{$column->name}));
                }

                if ($column->type == 'date') {
                    $item->{$column->name} = date('Y-m-d', strtotime($item->{$column->name}));
                }

                if ($column->type == 'time') {
                    $item->{$column->name} = date('H:i:s', strtotime($item->{$column->name}));
                }
            }
        });
    }

    public function loaded()
    {
        //$this->setAttribute('published_at', null);
    }

    public function getSchema()
    {
        return \Cache::get('CrudSchema.' . $this->table);
    }

    public function storeSchema($schema)
    {
        \Cache::forever('CrudSchema.' . $this->table, $schema);
    }

    public function setSchema(\Closure $callback)
    {
        $schema = new CrudSchema($this->table);

        $callback($schema);

        if (\Schema::hasTable($this->table)) {
            // see if any columns have been added
            $oldSchema = $this->getSchema();

            // diff old columns and new columns
            $oldColumns = $oldSchema->getColumns();
            $newColumns = $schema->getColumns();
            $diff = array('added' => array(), 'removed' => array());

            foreach ($newColumns as $new) {
                $found = false;
                foreach ($oldColumns as $old) {
                    if ($old->name == $new->name) {
                        $found = true;
                    }
                }

                if (! $found) {
                    // add new field to diff
                    $diff['added'][] = $new;
                }
            }

            // not removing fields at this point because it seems really dangerous

            if (! empty($diff['added'])) {
                // create blueprint to add new columns
                $blueprint = new CrudSchema($this->table, function ($table) use ($diff) {
                    foreach ($diff['added'] as $added) {
                        $table->addMyColumn($added);
                    }
                });

                // execute the blueprint
                $blueprint->execute();
            }
        } else {
            // create table based on schema
            $schema->create();
            $schema->execute();
        }

        // cache schema
        $this->storeSchema($schema);

        return $schema;
    }

    public function form()
    {
        $html = array();
        // $schema = $this->getSchema();
        // $columns = $schema->getColumns();

        $columns = $this->schema;

        $columns = json_decode(File::get(base_path() . '/storage/crud/schemas/' .
        strtolower(get_class($this)) . '.json'));

        foreach ($columns as $column) {
            // $column = (object) $column;

            if ($column->type == 'string') {
                $label = \Form::label($column->name, isset($column->label) ? $column->label :
                str_title(str_replace(array('-', '_'), ' ', $column->name)));
                $input = \Form::text(
                    "flex_data[$column->name]",
                    $this->getFlexValue($column->name),
                    array('class' => 'form-control')
                );

                $html[] = '<div class="form-group">' . $label . $input . '</div>';
            }

            if ($column->type == 'text') {
                // gallery uploader stored in a text field
                if (isset($column->gallery)) {
                    // dd($column);

                    $label = \Form::label($column->name, isset($column->label) ? $column->label :
                    str_title(str_replace(array('-', '_'), ' ', $column->name)));

                    $input = \Form::textarea(
                        "flex_data[$column->name]",
                        $this->getFlexValue($column->name),
                        array('class' => 'form-control', 'style' => 'display: none;')
                    );

                    $images = json_decode($this->getFlexValue($column->name));

                    $images = is_null($images) ? array() : $images;

                    $uploader = \View::make('admin.partials.uploader', compact('column', 'input', 'images'));

                    $html[] = '<div class="form-group">' . $label . $uploader . '</div>';
                } else { // regular text field
                    $label = \Form::label($column->name, isset($column->label) ? $column->label :
                    str_title(str_replace(array('-', '_'), ' ', $column->name)));
                    $input = \Form::textarea(
                        "flex_data[$column->name]",
                        $this->getFlexValue($column->name),
                        array('class' => 'form-control ' . (isset($column->wysiwyg) && $column->wysiwyg ?
                        'wysiwyg-editor' : ''))
                    );

                    $html[] = '<div class="form-group">' . $label . $input . '</div>';
                }
            }

            if (in_array($column->type, array('dateTime', 'date', 'time'))) {
                // determine class
                $class = strtolower($column->type) . 'picker';

                // format date
                if (! empty($this->{$column->name})) {
                    $this->{$column->name} = date($column->format, strtotime($this->{$column->name}));
                }

                $label = \Form::label(
                    $column->name,
                    isset($column->label) ?
                    $column->label : str_title(str_replace(array('-', '_'), ' ', $column->name))
                );
                $input = \Form::text(
                    "flex_data[$column->name]",
                    $this->getFlexValue($column->name),
                    array('class' => 'form-control ' . $class)
                );

                $html[] = '<div class="form-group">' . $label . $input . '</div>';
            }
        }

        return implode('', $html);
    }

    public function getIndexColumns()
    {
        $indexColumns = array();
        $schema = $this->getSchema();
        // $columns = $schema->getColumns();
        if (is_object($schema)) {
            $columns = $schema->getColumns();
        } else {
            $columns = array();
        }

        foreach ($columns as $index => $column) {
            // remove guarded columns when no index fields are specified
            foreach ($this->guarded as $guarded) {
                if ($column->name == $guarded) {
                    unset($columns[$index]);
                }
            }

            if (isset($column->onIndex) && $column->onIndex) {
                $indexColumns[] = $column;
            }
        }

        if (empty($indexColumns)) {
            $indexColumns = $columns;
        }

        return $indexColumns;
    }

    public function getIndexValue(Fluent $column)
    {
        $value = $this->{$column->name};

        if ($column->type == 'dateTime') {
            $value = date('n/j/Y g:i a', strtotime($this->{$column->name}));
        }

        if ($column->type == 'date') {
            $value = date('n/j/Y', strtotime($this->{$column->name}));
        }

        if ($column->type == 'time') {
            $value = date('g:i a', strtotime($this->{$column->name}));
        }

        return $value;
    }

    public function getFlexValue($name, $default = null)
    {
        $flexData = json_decode($this->flex_data);

        $value = isset($flexData->$name) ? $flexData->$name : null;

        return empty($value) ? $default : $value;
    }

    /**
     * Alias for the getFlexValue function
     */
    public function fv($name, $default = null)
    {
        return $this->getFlexValue($name, $default);
    }

    public function updateFlexData()
    {
        // update flex data
        $flexData = Input::has('flex_data') ? Input::get('flex_data') : [];

        // decode existing data from flex_data field
        $existingData = empty($existingData) ? [] : json_decode($this->flex_data);

        // update existing data with any new data
        foreach ($flexData as $name => $data) {
            $existingData[$name] = $flexData[$name];
        }

        // set flex_data field
        $this->flex_data = json_encode($existingData);

        // save item with flex_data
        $this->save();
    }
}
