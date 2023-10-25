<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    public static function checkAccountExist($data) {
        if ( isset($data['username']) ) {
            return self::where(
                'users_username', $data['username']
            )->orWhere(
                'users_email', $data['email']
            )->exists();
        }
        else {
            return self::where(
                'users_email', $data['email']
            )->exists();
        }
    }

    public static function getUserInfoBId($id) {
        return self::where(['id' => $id])->first();
    }

    public static function getUserInfoByAccount($data) {
        return self::where([
            'users_username' => $data['username'],
            'users_password' => $data['password']
        ])->first();
    }

    public static function updateUserProfileById($id, $data) {
        return self::where('id', $id)->update([
            'users_name'     => $data['name'],
            'users_email'    => $data['email'],
            'users_age'      => $data['age'],
            'users_birthday' => $data['birthday'],
            'users_gender'   => $data['gender'],
            'users_avatar'   => $data['avatar']
        ]);
    }

    public static function updateUserPWDById($id, $password) {
        return self::where('id', $id)->update([
            'users_password' => $password
        ]);
    }
}
