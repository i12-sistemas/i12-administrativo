<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
 
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
      $model->id = Uuid::uuid4();
    });
  }

  public function getexpiradoAttribute($value)
  {
      return ($this->expire_at < Carbon::now()) || ($this->deleted_at);
  }  
}
