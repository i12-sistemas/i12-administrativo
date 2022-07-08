<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class FaturaFormapgto extends Model
{
 
    protected $table = 'fatura_formapgto';
    protected $primaryKey = 'id';
    public $remember_token = false;
    protected $fillable = [
        'id',
        'forma',
        'ativo',
        'pago',
        'categ'
    ];

    public function getformaAttribute($value)
    {
        return utf8_encode($value);
    } 
    
    public function getcategAttribute($value)
    {
        return utf8_encode($value);
    }       
}
