<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class i12Etiquetas extends Model
{
  protected $table = 'i12_etiquetas';


  public function getnomeAttribute($value)
  {
      return utf8_encode($value);
  }

  public function setnomeAttribute($value)
  {
      $this->attributes['nome'] = utf8dec($value);
  } 

}
