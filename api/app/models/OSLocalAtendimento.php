<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class OSLocalAtendimento extends Model
{
    public $timestamps = false;
    protected $table = 'oslocalatendimento';
    public $autoincrement = false;

    public function getlocalAttribute($value)
    {
        return utf8_encode($value);
    }
    public function setlocalAttribute($value)
    {
        $this->attributes['local'] = ucutf8dec($value);
    }

    public function getdescricaopadraoAttribute($value)
    {
        return utf8_encode($value);
    }
    public function setdescricaopadraoAttribute($value)
    {
        $this->attributes['descricaopadrao'] = ucutf8dec($value);
    }

}
