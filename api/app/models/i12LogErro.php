<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\Clientes;

class i12LogErro extends Model
{

    // MODEL DA TABELA DE LOG DE ERROS QUE COLETAM OS LOGS DO ICOM DOS CLIENTES DA I12 SISTEMAS.

    //rodar
    // php artisan migrate 
    //2020_04_01_172104_create_i12_cli_logerros_table
    protected $table = 'i12_cli_logerros';
    protected $primaryKey = 'id';
    protected $dates = ['datahora', 'created_at', 'updated_at', 'releaseapp'];


    //RELACIONAMENTO COM A TABELA DE CLIENTES
    public function clientes () {
        return $this->belongsTo(Clientes::class, 'cnpj', 'cnpj');
    }

    public function cliente () {
      return $this->hasOne(Clientes::class, 'cnpj', 'cnpj');
    }



    public function setcnpjAttribute($value)
    {
        $this->attributes['cnpj'] = utf8_decode($value);
    }    

    public function getproblemaAttribute($value)
    {
        return utf8_encode($value);
    }

    public function setproblemaAttribute($value)
    {
        $this->attributes['problema'] = utf8_decode($value);
    }   
  
    public function getversaoappAttribute($value)
    {
        return utf8_encode($value);
    }

    public function getmoduloappAttribute($value)
    {
        return utf8_encode($value);
    }

    public function getversaobdAttribute($value)
    {
        return utf8_encode($value);
    }

    public function getformularioppAttribute($value)
    {
        return utf8_encode($value);
    }

    public function getcontroleAttribute($value)
    {
        return utf8_encode($value);
    }

    public function getusuariosoAttribute($value)
    {
        return utf8_encode($value);
    }

    public function getnomecomputadorAttribute($value)
    {
        return utf8_encode($value);
    }

    public function getsoAttribute($value)
    {
        return utf8_encode($value);
    }

    public function setsoAttribute($value)
    {
        $this->attributes['so'] = utf8_decode($value);
    }  

    public function setfilenameAttribute($value)
    {
        $this->attributes['filename'] = utf8_decode($value);
    }  

    public function setversaoappAttribute($value)
    {
        $this->attributes['versaoapp'] = utf8_decode($value);
    }  

    public function setformularioAttribute($value)
    {
        $this->attributes['formulario'] = utf8_decode($value);
    }  

    public function setcontroleAttribute($value)
    {
        $this->attributes['controle'] = utf8_decode($value);
    }  


    public function setmoduloappAttribute($value)
    {
        $this->attributes['moduloapp'] = utf8_decode($value);
    }  

    public function setversaobdAttribute($value)
    {
        $this->attributes['versaobd'] = utf8_decode($value);
    }  

    public function setfilenameurlAttribute($value)
    {
        $this->attributes['filenameurl'] = utf8_decode($value);
    }  

    public function setusuariosoAttribute($value)
    {
        $this->attributes['usuarioso'] = utf8_decode($value);
    }  

    public function setusuariosistemaAttribute($value)
    {
        $this->attributes['usuariosistema'] = utf8_decode($value);
    }  
    
    public function setnomecomputadorAttribute($value)
    {
        $this->attributes['nomecomputador'] = utf8_decode($value);
    }      

}
