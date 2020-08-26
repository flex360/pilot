<?php

namespace Flex360\Pilot\Pilot;

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

    public function hasRole(...$keys)
    {
        $roles = Role::findByKey(...$keys);

        if (empty($roles)) {
            return false;
        }

        $roles = is_array($roles) ? $roles : [$roles];

        $roleIds = collect($roles)->pluck('id');

        return $roleIds->contains($this->role_id);
    }

    public function isAdmin()
    {
        return $this->hasRole('super', 'admin');
    }

    public function canEditUser(User $user)
    {
        if ($this->hasRole('super')) {
            return true;
        }

        if ($this->hasRole('admin') && !$user->hasRole('super')) {
            return true;
        }

        return false;
    }
}
