<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class OSClassificacao extends Model
{
  public $timestamps = false;
  protected $table = 'osstatus';
  protected $primaryKey = 'id';

  public function exportCliente()
    {
        return [
          'id' => $this->id,
          'classificacao' => $this->classificacao,
        ];
    }    
    
    public function getstatusAttribute($value)
    {
        return utf8_encode($value);
    }
    public function setstatusAttribute($value)
    {
        $this->attributes['status'] = utf8_decode($value);
    }    

        
    public function getclassificacaoAttribute($value)
    {
        return $this->status;
    }
    public function setclassificacaoAttribute($value)
    {
        $this->attributes['status'] = utf8_decode($value);
    }    
}
