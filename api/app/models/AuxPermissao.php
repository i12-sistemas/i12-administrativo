<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuxPermissao extends Model
{
  protected $table      = 'auxpermissao';
  public $timestamps    = false;
  protected $primaryKey = 'idauxpermissao';
  // protected $fillable   = ['idpermissao', 'idperfil'];
}
