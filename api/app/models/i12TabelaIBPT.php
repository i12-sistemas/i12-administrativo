<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class i12TabelaIBPT extends Model
{
  protected $table = 'i12_tabelaibpt';
  public $timestamps = false;

  protected $dates = [
    'created_at'
  ];

  public function export()
  {
    $dados = [
      'uf'    =>  $this->uf,
      'created_at'       =>  $this->created_at->format('Y-m-d H:i:s'),
      'md5file'          =>  $this->md5file,
      'filename'         =>  $this->filename,
      'sizebytes'        =>  $this->sizebytes,
    ];
    if ($this->usuario ? $this->usuario->codusuario > 0 : false) $dados['usuario']  = $this->usuario->toArrayDef(false);
    return $dados;
  }

  public function usuario() {
    return $this->hasOne(Usuario::class, 'codusuario', 'idusercad');
  }    
}
