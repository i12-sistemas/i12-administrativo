<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class I12BackupfileS3Log extends Model
{
  public $timestamps = false;
  protected $table = 'i12_backupfiles3_log';
  protected $primaryKey = 'id';
  protected $dates = [
      'created_at',
  ];  

  public function setmotivoAttribute($value)
  {
      $this->attributes['motivo'] = utf8_decode($value);
  }    

  public function getmotivoAttribute($value)
  {
      return utf8_encode($value);
  }    
  
}
