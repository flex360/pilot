<?php

namespace Flex360\Pilot\Pilot;

use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{

    use SoftDeletes, HasEmptyStringAttributes;

    protected $table = 'roles';

    protected $guarded = array('id', 'created_at', 'updated_at');

    protected $emptyStrings = ['name', 'key'];

    public function users()
    {
        return $this->hasMany('User');
    }

    public static function findByKey($key)
    {
        return Role::withoutGlobalScopes()->where('key', $key)->first();
    }
}
