<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ClientesCaractValor extends Model
{
    public $timestamps = false;
    protected $table = 'clientescaractvalor';
    protected $primaryKey = 'id';
    public $remember_token = false;
    protected $dates = ['dhupdate'];
    
    public function getvstringAttribute($value)
    {
        return utf8_encode($value);
    }

    public function setvstringAttribute($value)
    {
        $this->attributes['vstring'] = utf8dec($value);
    }
          
}


