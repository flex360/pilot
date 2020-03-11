<?php

namespace Flex360\Pilot\Pilot\Forms\Wufoo;

use DB;
use Illuminate\Database\Eloquent\Model;

class WufooForm
{

    public $hash = null;
    public $name = null;
    public $link = null;

    public function __construct($hash)
    {
        $this->hash = $hash;

        $forms = config('forms.forms');

        $form = self::getFormByHash($hash);

        if (is_array($form)) {
            foreach ($form as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    public static function make($hash)
    {
        return new WufooForm($hash);
    }

    public static function getFormByHash($hash)
    {
        $forms = config('forms.forms');

        foreach ($forms as $index => $form) {
            if ($form['hash'] == $hash) {
                return $form;
            }
        }

        return null;
    }

    public static function hasForms()
    {
        $forms = config('forms.forms');

        return ! empty($forms);
    }

    public static function getForms()
    {
        $forms = [];

        foreach (config('forms.forms') as $form) {
            $forms[] = WufooForm::make($form['hash']);
        }

        return $forms;
    }

    public function makeMagic()
    {
        eval("
		class MagicWufooFormEntry extends \App\Pilot\Forms\Wufoo\WufooFormEntry
		{
		    protected \$table = '{$this->getTable()}';
            protected \$primaryKey = 'EntryId';
		}");

        return $this;
    }

    public function getTable()
    {
        return 'wufoo_' . $this->hash;
    }

    public function getColumns($limit = null)
    {
        $columns = DB::getSchemaBuilder()->getColumnListing($this->getTable());

        if (! empty($limit)) {
            $columns = array_slice($columns, 0, $limit);
        }

        return $columns;
    }

    public function getColumnComment($name)
    {
        $result = \DB::select('SHOW FULL COLUMNS FROM ' . $this->getTable() . ' WHERE `Field`=?', [$name]);

        if (isset($result[0])) {
            return $result[0]->Comment;
        }

        return null;
    }

    public function getQueryBuilder()
    {
        return DB::table($this->getTable())->whereRaw('1=1');
    }

    public function getRecordCount()
    {
        if (! $this->tableExists()) {
            return 0;
        }

        return $this->getQueryBuilder()->count();
    }

    public static function syncAll()
    {
        $results = [];

        $forms = config('forms.forms');

        foreach ($forms as $form) {
            $wufoo = new WufooForm($form['hash']);

            $result = $wufoo->sync();

            $results = array_merge($results, $result);
        }

        return $results;
    }

    public function sync()
    {
        $default = ini_get('max_execution_time');
        set_time_limit(300);

        $db = DB::connection()->getPdo();

        $connector = WufooConnector::make([
            'hash' => $this->hash,
            'api_key' => config('forms.api_key'),
            'subdomain' => config('forms.account')
        ]);

        $fields = $connector->getFields();

        $sql_fields = [];

        foreach ($fields as $field) {
            switch ($field->Type) {
                case 'shortname':
                    $sql_fields[$field->SubFields[0]->ID] = array(
                        'type' => $field->Type,
                        'title' => $field->SubFields[0]->Label
                    );

                    $sql_fields[$field->SubFields[1]->ID] = array(
                        'type' => $field->Type,
                        'title' => $field->SubFields[1]->Label
                    );
                    break;

                default:
                    $sql_fields[$field->ID] = array(
                        'type' => $field->ID == 'EntryId' ? 'id' : $field->Type,
                        'title' => $field->Title
                    );
                    break;
            }

            if (isset($field->SubFields)) {
                $sql_fields[$field->ID]['subfields'] = $field->SubFields;
            }
        }

        if (isset($sql_fields['LastUpdated'])) {
            $temp = $sql_fields['LastUpdated'];
            unset($sql_fields['LastUpdated']);
            $sql_fields['DateUpdated'] = $temp;
        }

        $table = $this->getTable();

        DB::statement('DROP TABLE IF EXISTS ' . $table);

        $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (";

        foreach ($sql_fields as $k => $v) {
            if (is_array(WufooConnector::$types[$v['type']])) {
                $types = array_values(WufooConnector::$types[$v['type']]);

                foreach ($types as $typeIndex => $typeAndLength) {
                    $subfield = $v['subfields'][$typeIndex];
                    $sql .= "`{$subfield->ID}` " . $typeAndLength . " COMMENT " .
                    $db->quote($v['title'] . ' - ' . $subfield->Label) . ",\n";
                }
            } else {
                $sql .= "`{$k}` " . WufooConnector::$types[$v['type']] . " COMMENT " . $db->quote($v['title']) . ",\n";
            }
        }

        $sql .= "
        PRIMARY KEY ( `EntryId` )
        ) ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci;";

        DB::statement($sql);

        $next_page = 0;
        $entry_count[$this->hash] = 0;

        do {
            $response = $connector->getEntries('json', $next_page);
            $entries = $response['entries'];

            $next_page = $response['next_page'];

            foreach ($entries as $entry) {
                $keys = array();
                $values = array();

                foreach ($sql_fields as $k => $v) {
                    $keys[] = $k;
                    if ($v['type'] == 'checkbox') {
                        $vals = array();
                        foreach ($v['subfields'] as $sub) {
                            if (!empty($entry->{$sub->ID})) {
                                $vals[] = $entry->{$sub->ID};
                            }
                        }
                        $values[] = implode('; ', $vals);
                    } else {
                        $values[] = $entry->$k;
                    }
                }

                $placeholders = array_map(function ($value) {
                    return '?';
                }, $values);

                $sql = 'INSERT INTO `'.$table.'` (`'.join('`, `', $keys).'`) VALUES ('.join(', ', $placeholders).')';
                // echo $sql;
                DB::insert($sql, $values);
            }

            $entry_count[$this->hash] += count($entries);
        } while (!is_null($next_page));

        set_time_limit($default);

        return $entry_count;
    }

    public function getHandshakeKey()
    {
        return md5('#handshake-' . $this->hash . '-key#');
    }

    public function tableExists()
    {
        return \Schema::hasTable($this->getTable());
    }
}
