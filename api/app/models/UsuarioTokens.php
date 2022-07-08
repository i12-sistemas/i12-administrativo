<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioTokens extends Model
{
  use SoftDeletes;

  protected $table = 'usuariotokens';
  protected $primaryKey = 'session';
  public $incrementing = false;
  protected $dates = ['expire_at', 'deleted_at', 'created_at', 'updated_at'];

  //username
  public function getusernameAttribute($value)
  {
    return utf8_encode($value);
  }

  public function setusernameAttribute($value)
  {
    $this->attributes['username'] =  utf8_decode($value);
  }

  public function usuario()
  {
    return $this->hasOne(Usuario::class, 'codusuario', 'idusuario');
  }

  public function empresa()
  {
    return $this->hasOne('App\Models\Empresa', 'codempresa', 'idempresa');
  }


}
