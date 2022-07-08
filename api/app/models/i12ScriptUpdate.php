<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class i12ScriptUpdate extends Model
{
  protected $table = 'i12_scriptupdate';

  protected $dates = [
    'validade',
  ];
    
  public function getstatusdescAttribute($value)
  {
    switch ($this->status) {
      case 0:
        $s = 'Inativo';
        break;
      case 1:
        $s = 'Dev';
        break;
      case 2:
        $s =  'Prod';
        break;
    }

    return $this->status . '-' . $s;
  }  

}