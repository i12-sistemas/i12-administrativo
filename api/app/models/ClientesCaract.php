<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ClientesCaract extends Model
{
    public $timestamps = false;
    protected $table = 'clientescaract';
    protected $primaryKey = 'id';
    public $remember_token = false;
    
    public function getcaracAttribute($value)
    {
        return utf8_encode($value);
    }

    public function setcaracAttribute($value)
    {
        $this->attributes['carac'] = utf8dec($value);
    }   

    public function gettipoAttribute($value)
    {
        return utf8_encode($value);
    }

    public function settipoAttribute($value)
    {
        $this->attributes['tipo'] = utf8dec($value);
    }      
}
