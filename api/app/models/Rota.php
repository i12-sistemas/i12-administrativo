<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Rota extends Model
{
    const CREATED_AT = 'altultdata';
    const UPDATED_AT = 'altultdata';

    // public $timestamps = false;
    protected $dates = [
        'altultdata',
    ];
    protected $table = 'rota';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'descricao',
        'ativo',
    ];


    
    public function getdescricaoAttribute($value)
    {
        return strtoupper(utf8_encode($value));
    }
    public function setdescricaoAttribute($value)
    {
        $this->attributes['descricao'] = ucutf8dec($value);
    }

    public function getmotoristaAttribute($value)
    {
        return strtoupper(utf8_encode($value));
    }
    public function setmotoristaAttribute($value)
    {
        $this->attributes['motorista'] = ucutf8dec($value);
    }   

    public function getaltuserloginAttribute($value)
    {
        return strtoupper(utf8_encode($value));
    }
    public function setaltuserloginAttribute($value)
    {
        $this->attributes['altuserlogin'] = utf8dec($value);
    }       
}
