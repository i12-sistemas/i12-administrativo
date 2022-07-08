<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class OrdemServico extends Model
{

    public $timestamps = false;
    protected $table = 'osordem';
    protected $primaryKey = 'id';
    protected $dates = [
        'dtabertura',
        'dtfechamento',
        'ultalteracao',
        'agendainicio',
        'agendafinal',
        'contratoinivigencia',
        'contratofimvigencial',
        'contratodiafaturamento',
        'agendainicio'
    ];  

    public function getproblemareclamadoAttribute($value)
    {
        return utf8_encode($value);
    }
    public function getsituacaoAttribute($value)
    {
        return utf8_encode($value);
    }
    public function setproblemareclamadoAttribute($value)
    {
        $this->attributes['problemareclamado'] = ucutf8dec($value);
    }   


    public function fase() {
        return $this->hasOne('App\models\OSFases','id', 'idfase');
    }    

    public function cliente() {
        return $this->hasOne('App\models\Clientes','codcliente', 'idcliente');
    }      

    public function contato() {
        return $this->hasOne('App\models\Contato','id', 'idclientecontato');
    }       

    public function usuario() {
        return $this->hasOne('App\models\User', 'codusuario', 'idusuariovenda');
    }     

    public function leituras() {
        return $this->hasMany('App\models\OSLeitura', 'idos', 'id')->orderBy('dataleitura', 'desc');
    }      

    public function interacoespublicas() {
        return $this->hasMany('App\models\InteracaoOS', 'idos', 'id')
                                                                ->where('interacao.restrito', 0)
                                ->where('interacao.excluido', 0)
                                ->join('interacao', 'interacaoos.idinteracao', '=', 'interacao.id')
                                ->where('interacao.restrito', 0)
                                ->where('interacao.excluido', 0)
                                ->orderBy('interacao.datahora', 'desc');
    } 

  
}

