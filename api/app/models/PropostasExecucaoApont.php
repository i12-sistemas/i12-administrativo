<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class PropostasExecucaoApont extends Model
{
    protected $dates = [
        'dataexec',
    ];     
    public $timestamps = false;
    protected $table = 'propostasexecucaoapont';
    protected $primaryKey = 'id';

    public function getturnoAttribute($value)
    {
        return utf8_encode($value);
    }  
    public function setturnoAttribute($value)
    {
        $this->attributes['turno'] = ucutf8dec($value) ;
    }        

    public function consultor() {
        return $this->hasOne('App\models\User', 'id', 'idconsultor' )->select('nome', 'id');
    }        
        
}
