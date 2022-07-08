<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ContatoCliente extends Model
{
    public $timestamps = false;
    protected $table = 'contatocliente';
    protected $primaryKey = 'id';
    public $remember_token = false;
    protected $dates =['ultimosynci12'];


}
