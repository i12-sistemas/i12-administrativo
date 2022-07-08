<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class IntegracaoItem extends Model
{
    protected $table = 'integracao_item';
    protected $primaryKey = 'id';
    public $remember_token = false;
    public $timestamps = false;
    protected $dates = [
        'dtvecto',
        'dtpagto',
        'dtcredito',
        'dhconciliacao'
    ];

    public function getnumchaveAttribute($value)
    {
        return utf8_encode($value);
    } 
    
    public function setnumchaveAttribute($value)
    {
        $this->attributes['numchave'] = utf8dec($value);
    } 

    public function getcpfAttribute($value)
    {
        return utf8_encode($value);
    }   

    public function setcpfAttribute($value)
    {
        $this->attributes['cpf'] = utf8dec($value);
    } 

    public function getconvenioAttribute($value)
    {
        return utf8_encode($value);
    }   

    public function setconvenioAttribute($value)
    {
        $this->attributes['convenio'] = utf8dec($value);
    } 


    public function getstatusAttribute($value)
    {
        return utf8_encode($value);
    }   

    public function setstatusAttribute($value)
    {
        $this->attributes['status'] = utf8dec($value);
    }  
    
    public function getnomeAttribute($value)
    {
        return utf8_encode($value);
    }   

    public function setnomeAttribute($value)
    {
        $this->attributes['nome'] = utf8dec($value);
    }  
    
    public function getstatusmsgAttribute($value)
    {
        return utf8_encode($value);
    }   

    public function setstatusmsgAttribute($value)
    {
        $this->attributes['statusmsg'] = utf8dec($value);
    }       

    public function fatura() {
        return $this->hasOne('App\models\Fatura','id', 'idfatura');
    } 

    public function usuarioconciliacao() {
        return $this->hasOne('App\models\User','id', 'idusuarioconciliacao')->select('id', 'nome');
    } 
    

}
