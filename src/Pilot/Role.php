<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;

class Role extends Model
{
    use SoftDeletes, HasEmptyStringAttributes;

    protected $table = 'roles';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = ['name', 'key'];

    public static function boot()
    {
        parent::boot();

        Role::saving(function ($role) {
            Cache::forget('pilot-all-roles');
        });

        Role::deleted(function () {
            Cache::forget('pilot-all-roles');
        });
    }

    public function users()
    {
        return $this->hasMany('User');
    }

    public static function findByKey(...$keys)
    {
        $roles = Cache::rememberForever('pilot-all-roles', function () {
            return Role::withoutGlobalScopes()->get();
        });

        // if multiple keys, return multiple roles
        if (count($keys) > 1) {
            return $roles->whereIn('key', $keys)->all();
        }

        return $roles->whereIn('key', $keys)->first();
    }
}
