<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class i12TabelaIBPTLog extends Model
{
  protected $table = 'i12_tabelaibpt_log';
  public $timestamps = false;

  protected $dates = [
    'created_at'
  ];

  public function export()
  {
    $dados = [
      'id'    =>  $this->id,
      'uf'    =>  $this->uf,
      'created_at'       =>  $this->created_at->format('Y-m-d H:i:s'),
      'filename'         =>  $this->filename,
      'ip'          =>  $this->ip,
      'sizebyte'        =>  $this->sizebyte,
      'cnpj'        =>  $this->cnpj,
      'usuario'        =>  $this->usuario,
      'sistema'        =>  $this->sistema,
      'ambiente'        =>  ($this->ambiente ? $this->ambiente === '1' : false) ? '1' : '2',
    ];
    if ($this->cliente ? $this->cliente->codcliente > 0 : false) 
      $dados['cliente'] = [
        'id' => $this->cliente->codcliente,
        'nome' => $this->cliente->nome,
      ];
    return $dados;
  }

  public function cliente() {
    return $this->hasOne(Clientes::class, 'cnpj', 'cnpj');
  }    
}
