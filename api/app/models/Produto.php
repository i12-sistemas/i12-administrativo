<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    const CREATED_AT = 'altdata';
    const UPDATED_AT = 'altdata';

    // public $timestamps = false;
    protected $dates = [
        'altdata'
    ];
    protected $table = 'produto';
    protected $primaryKey = 'id';
    public $remember_token = false;
    protected $fillable = [
        'id',
        'descricao',
        'unid',
        'ativo',
        'validadedia',
        'qtdemax',
        'precototal',
        'tipo',
        'qtde',
    ];

    public function getdescricaoAttribute($value)
    {
        return utf8_encode($value);
    }   
    public function getunidAttribute($value)
    {
        return utf8_encode($value);
    }         
}
