<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

use Tymon\JWTAuth\Contracts\JWTSubject as AuthenticatableUserContract;


/**
 * @property mixed username
 * @property mixed password
 * @property mixed id
 * @property mixed is_authorized
 * @property mixed is_client
 */
class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    AuthenticatableUserContract
{
    use Authenticatable, Authorizable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Check if the user is a client type
     * @return bool
     */
    public function isClient() {
        return $this->is_client == 1;
    }

    /**
     * Check if the user is an authorized client
     * @return bool
     */
    public function isAuthorized() {
        return $this->is_authorized == 1;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function phones()
    {
        return $this->hasMany('App\Phone');
    }


    /**
     * @return User
     */
    public static function root()
    {
        $model = new static();
        return $model->whereUsername('root')->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('App\Role', 'user_role')
            ->using('App\UserRole')->withTimestamps();
    }

    /**
     * Check if the user has a specified role
     *
     * @param $uid
     * @return bool
     */
    public function hasRole($uid)
    {
        foreach ($this->roles()->get() as $userRole) {
            if ($userRole->uid === $uid) {
                return true;
            }
        }
        return false;
    }

    /**
     * Link role
     *
     * @param string $uid
     * @return User
     */
    public function assignRole($uid)
    {
        $role = Role::fromUid($uid);
        if (!!$role && !$this->hasRole($uid)) {
            $this->roles()->attach($role->id, []);
        }

        return $this;
    }

    /**
     * Unlink role
     * @return User
     *
     * @param $uid
     */
    public function revokeRole($uid)
    {
        $role = Role::fromUid($uid);
        if (!!$role && $this->hasRole($uid)) {
            $this->roles()->detach($role->id, []);
        }

        return $this;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->id;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * @param $id
     * @param array $data
     * @return User
     */
    public static function updateExisting($id, array $data) {
        /** @var User $instance */
        $instance = with(new static())->find($id);
        $instance->update($data);

        return $instance;
    }

    public static function registerClient($data)
    {
        /** @var User $instance */
        $instance = with(new static())->newInstance($data);
        $instance->password = $data['password'];
        $instance->is_client = true;
        $instance->is_authorized = false;
        $instance->save();

        return $instance;
    }
}
