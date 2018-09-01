<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer id
 * @property string uid
 * @property string name
 */
class Role extends Model
{
    use SoftDeletes;

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_role')
                ->withTimestamps()->using('App\UserRole');
    }

    /**
     * @param string $uid
     * @return Role
     */
    public static function fromUid($uid) {
        return static::whereUid($uid)->first();
    }

    /**
     * @param string $name
     * @return Role
     */
    public static function fromName($name) {
        return static::whereName($name)->first();
    }

}
