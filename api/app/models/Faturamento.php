<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Faturamento extends Model
{
  protected $table      = 'faturamento';
  protected $primaryKey = 'codfaturamento';
  public $timestamps    = false;
  // const CREATED_AT      = 'dhcad';
  // const UPDATED_AT      = 'dhalt';
  // public $incrementing  = false;
  protected $dates      = ['vencimento', 'datacompetencia', 'datapgto'];

  // protected $fillable   = ['codfaturamento', 'codvenda', 'codcliente', 'nparc', 'vencimento', 'datacompetencia', 'tipo', 'valor', 'pago', 'datapgto', 'acrescimo', 'valorpago', 'codusuario', 'codauxcaixa', 'restante', 'obs', 'codcontacli', 'codcheque', 'troco', 'dinheiro', 'numnfe', codcaixa', 'idempresa', 'dcc_titular', 'dcc_banco', 'dcc_agencia', 'dcc_conta', 'dcc_idcontadestino', 'idcompraretencaonfse'];

  // dcc_conta
  public function getdccContaAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setdccContaAttribute($value)
  {
    $this->attributes['dcc_conta'] =  utf8_decode($value);
  }

  // dcc_agencia
  public function getdccAgenciaAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setdccAgenciaAttribute($value)
  {
    $this->attributes['dcc_agencia'] =  utf8_decode($value);
  }

  // dcc_banco
  public function getdccBancoAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setdccBancoAttribute($value)
  {
    $this->attributes['dcc_banco'] =  utf8_decode($value);
  }

  // dcc_titular
  public function getdccTitularAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setdccTitularAttribute($value)
  {
    $this->attributes['dcc_titular'] =  utf8_decode($value);
  }

  // numnfe
  public function getnumnfeAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setnumnfeAttribute($value)
  {
    $this->attributes['numnfe'] =  utf8_decode($value);
  }

  // obs
  public function getobsAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setobsAttribute($value)
  {
    $this->attributes['obs'] =  utf8_decode($value);
  }

  // tipo
  public function gettipoAttribute($value)
  {
    return utf8_encode($value);
  }
  public function settipoAttribute($value)
  {
    $this->attributes['tipo'] =  utf8_decode($value);
  }

  // nparc
  public function getnparcAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setnparcAttribute($value)
  {
    $this->attributes['nparc'] =  utf8_decode($value);
  }
}
