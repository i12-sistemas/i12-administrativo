<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ContatoResetPwdTokens extends Model
{
  protected $table = 'contato_resetpwdtokens';

  protected $dates = ['created_at', 'updated_at'];

  public function contato()
  {
      return $this->hasOne(Contato::class, 'id', 'contatoid');
  }

  public function getexpiradoAttribute($value)
  {
    return ($this->processado != 0) || ($this->expire_at < Carbon::now());
  }

  public function getusernameAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setusernameAttribute($value)
  {
    $this->attributes['username'] =  utf8_decode($value);
  }
}
