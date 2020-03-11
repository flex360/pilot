<?php

namespace Flex360\Pilot\Pilot;

use Flex360\Pilot\Pilot\Role;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, Notifiable, SoftDeletes, HasEmptyStringAttributes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = ['username', 'password', 'email'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function getName()
    {
        if (!empty($this->name)) {
            return $this->name;
        } else {
            return ucwords($this->username);
        }
    }

    public function hasRole($key)
    {
        $role = Role::findByKey($key);

        if (empty($role)) {
            return false;
        }

        return $this->role_id == $role->id;
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }
}
