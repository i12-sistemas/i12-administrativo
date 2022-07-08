<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ClienteLicencai12 extends Model
{
  public $timestamps = false;
  protected $table = 'i12_clientelicenca';
  protected $primaryKey = 'id';
  public $remember_token = false;

  protected $dates =['dhemissao', 'dhcad', 'dhalt', 'dhativadaonline'];

  public function getnumeroAttribute($value)
  {
      return strtoupper(utf8_encode($value));
  }
  public function setnumeroAttribute($value)
  {
      $this->attributes['numero'] = utf8dec($value);
  }    

  public function getobsAttribute($value)
  {
      return strtoupper(utf8_encode($value));
  }
  public function setobsAttribute($value)
  {
      $this->attributes['obs'] = ucutf8dec($value);
  }   

  public function getcnpjAttribute($value)
  {
      return strtoupper(utf8_encode($value));
  }
  public function setcnpjAttribute($value)
  {
      $this->attributes['cnpj'] = ucutf8dec($value);
  }    
  
  
  public function getdatavencimentoAttribute($value)
  {
      $d = $this->dhemissao->addMonths($this->tipolicenca);
      return $d;
  }

  public function getdatabloqueiototalAttribute($value)
  {
      $d = $this->datavencimento->addDays(10);
      return $d;
  }

  public function getbloqueadoAttribute($value)
  {
      $agora = Carbon::now();
      $d = ($this->databloqueiototal < $agora) || ($this->atual != 1);
      return $d;
  }
  public function getexpiradoAttribute($value)
  {
      $agora = Carbon::now();
      $d = ($this->datavencimento < $agora) || ($this->atual != 1);
      return $d;
  }  

  
  
}
