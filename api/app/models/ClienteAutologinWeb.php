<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ClienteAutologinWeb extends Model
{
    protected $dates = [
        'dhcad',
        'dhactive',
    ];    
    public $timestamps = false;
    protected $table = 'clienteautologinweb';
    protected $primaryKey = 'id';
    public $remember_token = false;
}
