<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class i12Terminal extends Model
{
  protected $table = 'i12_terminal';
  protected $primaryKey = 'serialnumber';
  public $incrementing = false;  
  protected $dates = ['created_at', 'updated_at'];

  public function getprocFamiliyAttribute($value)
  {
      return utf8_encode($value);
  }
  public function setprocFamiliyAttribute($value)
  {
      $this->attributes['proc_familiy'] = utf8_decode($value);
  } 
  
  public function getprocVersionAttribute($value)
  {
      return utf8_encode($value);
  }
  public function setprocVersionAttribute($value)
  {
      $this->attributes['proc_version'] = utf8_decode($value);
  }   
      
  public function getmbManufacterAttribute($value)
  {
      return utf8_encode($value);
  }
  public function setmbManufacterAttribute($value)
  {
      $this->attributes['mb_manufacter'] = utf8_decode($value);
  }   
          
  public function getmbProductAttribute($value)
  {
      return utf8_encode($value);
  }
  public function setmbProductAttribute($value)
  {
      $this->attributes['mb_product'] = utf8_decode($value);
  }   
              
  public function getsoNameAttribute($value)
  {
      return utf8_encode($value);
  }
  public function setsoNameAttribute($value)
  {
      $this->attributes['so_name'] = utf8_decode($value);
  }   
                  
  public function getprocManufacturerAttribute($value)
  {
      return utf8_encode($value);
  }
  public function setprocManufacturerAttribute($value)
  {
      $this->attributes['proc_manufacturer'] = utf8_decode($value);
  }   
                      
  public function getsoComputadorAttribute($value)
  {
      return utf8_encode($value);
  }
  public function setsoComputadorAttribute($value)
  {
      $this->attributes['so_computador'] = utf8_decode($value);
  }   
    
  
                      
  public function getso_homedriveAttribute($value)
  {
      return utf8_encode($value);
  }
  public function setso_homedriveAttribute($value)
  {
      $this->attributes['so_homedrive'] = utf8_decode($value);
  }   
    
  public function getso_homepathAttribute($value)
  {
      return utf8_encode($value);
  }
  public function setso_homepathAttribute($value)
  {
      $this->attributes['so_homepath'] = utf8_decode($value);
  }   
    
    
  public function getso_logonserverAttribute($value)
  {
      return utf8_encode($value);
  }
  public function setso_logonserverAttribute($value)
  {
      $this->attributes['so_logonserver'] = utf8_decode($value);
  }   
    

}
