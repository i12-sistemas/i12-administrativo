<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class UsuarioAutologinweb extends Model
{
    protected $dates = [
        'dhcad',
        'dhactive',
    ];    
    public $timestamps = false;
    protected $table = 'usuarioautologinweb';
    protected $primaryKey = 'id';
    public $remember_token = false;
}
