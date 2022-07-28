<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ContatoTokens extends Model
{
  use SoftDeletes;

  protected $table = 'contatotokens';
  protected $primaryKey = 'session';
  public $incrementing = false;
  protected $dates = ['expire_at', 'deleted_at', 'created_at', 'updated_at'];


  public function gerarAccessCode()
  {
    $r = Str::random(10);
    $this->attributes['accesscode']  = \Hash::make($r  . $this->token, ['rounds' => 12]);
  }

  public function getaccesscodeAttribute($value)
  {
    return utf8_encode($value);
  }

  public function getusernameAttribute($value)
  {
    return utf8_encode($value);
  }

  public function getisemailAttribute($value)
  {
    return isemail($this->username);
  }

  public function setusernameAttribute($value)
  {
    $this->attributes['username'] =  utf8_decode($value);
  }

  public function contato()
  {
    return $this->hasOne(Contato::class, 'id', 'contatoid');
  }

  public function cliente()
  {
    return $this->hasOne(Clientes::class, 'codcliente', 'clienteid');
  }


}
