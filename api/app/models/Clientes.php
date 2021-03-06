<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\i12LogErro;

class Clientes extends Model
{
    public $timestamps = false;
    protected $table = 'clientes';
    protected $primaryKey = 'codcliente';
    public $remember_token = false;

    //ultimolastmodified usado no backup

    protected $dates =['i12ultimaatualizacao', 'ultimolastmodified', 'saldovencidodesde'];
    
    public function getnomeAttribute($value)
    {
        return strtoupper(utf8_encode($value));
    }
    public function setnomeAttribute($value)
    {
        $this->attributes['nome'] = utf8_decode($value);
    }    

    public function getfantasiaAttribute($value)
    {
        return strtoupper(utf8_encode($value));
    }

    public function getemailAttribute($value)
    {
        return utf8_encode($value);
    }

    public function getcidadeAttribute($value)
    {
        return utf8_encode($value);
    }   
    
    public function getdocAttribute($value)
    {
        return ($this->pessoa === 'J' ? $this->cnpj : $this->cpf);
    }   

    public function setemailAttribute($value)
    {
        $this->attributes['email'] = ucutf8dec($value);
    }   

    public function ultimo_backup() {
        return $this->hasOne(i12clibackuplog::class,'cnpj', 'cnpj')->where('statuslocal',1)->orderby('dhlocal', 'desc');
    } 
    
    public function licencaatual() {
      return $this->hasOne(ClienteLicencai12::class,'idcliente', 'codcliente')->where('atual',1);
    }

    public function faturasemaberto() {
      return $this->hasOne(Faturamento::class, 'codcliente')->where('pago', '=', 'N');
    }

    public function cli_log_erros() {
        return $this->hasMany(i12LogErro::class,'cnpj', 'cnpj');
    }
}
