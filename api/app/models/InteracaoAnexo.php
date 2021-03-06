<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class InteracaoAnexo extends Model
{
    public $timestamps = false;
    protected $table = 'interacaoanexo';
    protected $primaryKey = 'id';
    protected $dates = [
        'dhcad',
    ];  
    protected $hidden = [
        'anexo'
    ];    

    public function exportCliente()
    {
      $dados =  [
        'descricao' => $this->descricao,
        'url' => url('/painelcliente/arquivos/') . '/'.  $this->anexomd5,
        'md5' => $this->anexomd5,
        'ext' => $this->anexoext,
        'size' => $this->anexosize,
        'dhcad' => $this->dhcad->format('Y-m-d H:i:s'),
      ];
      return $dados;
    }        


    
    public function getdescricaoAttribute($value)
    {
        return utf8_encode($value);
    }
    public function getanexoextAttribute($value)
    {
        return utf8_encode($value);
    }
    public function getanexomd5Attribute($value)
    {
        return utf8_encode($value);
    }   
    
    
    public function setdescricaoAttribute($value)
    {
        $this->attributes['descricao'] = utf8dec($value);
    }   
    public function setanexomd5Attribute($value)
    {
        $this->attributes['anexomd5'] = utf8dec($value);
    }   
    public function setanexoextAttribute($value)
    {
        $this->attributes['anexoext'] = utf8dec($value);
    }           
}
