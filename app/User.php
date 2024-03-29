<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username', 'type', 'last_shift_start', 'last_shift_end', 'shift_active', 'blocked',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    public static function userTypeToStr($type)
    {
        switch ($type) {
            case 'manager':
                return 'Manager';
            case 'cook':
                return 'Cozinheiro';
            case 'cashier':
                return 'Caixa';
            case 'waiter':
                return 'Empregado';
        }

        return 'Desconhecido';
    }

    public function orders() {
        return $this->hasMany('App\Order', 'responsible_cook_id', 'id');
    }

    public function isManager(){
        return $this->type === "manager";
    }
    public function isCook(){
        return $this->type === "cook";
    }
    public function isCashier(){
        return $this->type === "cashier";
    }
    public function isWaiter(){
        return $this->type === "waiter";
    }
}
