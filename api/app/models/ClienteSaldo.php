<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ClienteSaldo extends Model
{
  public $timestamps = false;
  public $incrementing = false;
  protected $table = 'cliente_saldo';
  protected $primaryKey = null;
}
