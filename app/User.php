<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'access', 'regions', 'admin_uid', 'keyword'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    /* Access to admin panel
     * 0 - admin - full access
     * 1 - manager - access to statistic and orders for Moskov
     * 2 - manager - access to statistic and orders for Regions
     * 3 - manager - access to statistic and orders for All
     * 4 - manager - access only to statistic for All
     */
    
    public function getAccess()
    {
        return !$this->access;
    }

    public function client()
    {
        return $this->hasOne('App\Client', 'uid', 'admin_uid');
    }

    public function resellerClients()
    {
        return $this->hasMany('App\Client');
    }

    public function percents()
    {
        return $this->hasMany('App\Percent');
    }
}
