<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class OSFases extends Model
{
    public $timestamps = false;
    protected $table = 'osfases';
    protected $primaryKey = 'id';
    public $remember_token = false;

    public function exportCliente()
    {
        return [
          'id' => $this->id,
          'descricaocliente' => $this->descricaocliente,
          'classificacao' => $this->classificacao,
          'pendentecliente' => $this->pendentecliente
        ];
    }    
    
    public function getdescricaoAttribute($value)
    {
        return strtoupper(utf8_encode($value));
    }
    public function setdescricaoAttribute($value)
    {
        $this->attributes['descricao'] = ucutf8dec($value);
    }    
    public function getdescricaoclienteAttribute($value)
    {
        return strtoupper(utf8_encode($value));
    }
    public function setdescricaoclienteAttribute($value)
    {
        $this->attributes['descricaocliente'] = ucutf8dec($value);
    }  
    public function getcorAttribute($value)
    {
        return utf8_encode($value);
    }
    public function setcorAttribute($value)
    {
        $this->attributes['cor'] = utf8dec($value);
    }   
    public function getclassificacaoAttribute($value)
    {
        return strtoupper(utf8_encode($value));
    }
    public function setclassificacaoAttribute($value)
    {
        $this->attributes['classificacao'] = ucutf8dec($value);
    }   

    public function getcorfonteAttribute($value)
    {
        return utf8_encode($value);
    }
    public function setcorfonteAttribute($value)
    {
        $this->attributes['corfonte'] = utf8dec($value);
    }              
}
