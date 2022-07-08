<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class InteracaoOS extends Model
{
    public $timestamps = false;
    protected $table = 'interacaoos';
    protected $primaryKey = 'id';


    public function interacao() {
        return $this->hasOne('App\models\Interacao','id', 'idinteracao');
    }   
    
    
}
