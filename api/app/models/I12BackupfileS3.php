<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class I12BackupfileS3 extends Model
{
  public $timestamps = false;
  protected $table = 'i12_backupfiles3';
  protected $primaryKey = 'id';
  protected $dates = [
      'lastmodified',
      'lastcheck',
  ];  

  public function setfilenameAttribute($value)
  {
      $this->attributes['filename'] = utf8_decode($value);
  }    

  public function getfilenameAttribute($value)
  {
      return utf8_encode($value);
  }  
  
  public function setbucketAttribute($value)
  {
      $this->attributes['bucket'] = utf8_decode($value);
  }    

  public function getbucketAttribute($value)
  {
      return utf8_encode($value);
  }

  public function getshortfilenameAttribute($value)
  {
      $a = explode('/', $this->filename);
      $shortfilename = $a[count($a)-1];
      return $shortfilename;
  }


  public function cliente() {
    return $this->hasOne(Clientes::class, 'cnpj', 'cnpj');
  }

  public function urldownload($expireAt)
  {
    if ($this->bucket == 1) {
      $disk=Storage::disk('s3backup_1');
    } else {
      $disk=Storage::disk('s3backup');
    }
    return $disk->temporaryUrl($this->filename, $expireAt);
  }  
}
