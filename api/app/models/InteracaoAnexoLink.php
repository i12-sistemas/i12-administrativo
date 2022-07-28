<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class InteracaoAnexoLink extends Model
{
    public $timestamps = false;
    protected $table = 'interacaoanexolink';
    protected $primaryKey = 'id';

    public function anexo() {
      return $this->hasOne(InteracaoAnexo::class, 'id', 'idanexo');
    } 
  
  
    public function getdescricaoAttribute($value)
    {
        return utf8_encode($value);
    }
    public function getanexoextAttribute($value)
    {
        return utf8_encode($value);
    }
}
