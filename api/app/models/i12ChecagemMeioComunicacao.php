<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
 
class i12ChecagemMeioComunicacao extends Model 
{

  use SoftDeletes;
  public $timestamps = false;
  protected $table = 'i12_checagem_meiocomunicacao';
  protected $primaryKey = 'id';
  public $incrementing = false;

  protected $dates = [
      'created_at',
      'expire_at',
      'checked_at'
  ];  


  public static function boot()
  {
    parent::boot();

    self::creating(function ($model) {
      $uuid4 = Uuid::uuid4();
      $model->id = $uuid4->toString();
    });
  }

  public function getexpiradoAttribute($value)
  {
      return ($this->expire_at < Carbon::now()) || ($this->deleted_at);
  }  
}
