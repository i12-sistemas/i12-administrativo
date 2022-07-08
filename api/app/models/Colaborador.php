<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colaborador extends Model
{
  protected $table      = 'colaborador';
  protected $primaryKey = 'codcolaborador';
  public $timestamps    = false;
  protected $dates      = ['dataalt', 'datanasc'];

  // protected $fillable   = ['nome', 'telefone1', 'telefone2', 'endereco', 'bairro', 'cep', 'uf', 'cidade', 'datanasc', 'obs', 'dataalt', 'usuarioalt', 'profissao', 'ativo', 'ativo_agenda', 'ativo_garcom', 'ativo_iservice', 'cor', 'idcliente', 'pessoa', 'cpfcnpj', 'codfornecedor', 'custohora'];

  public function toArrayDef($complete = false)
  {
    $dados = $this->toArray();
    if (!$complete) {
      $dados = [
        'codcolaborador' => $this->codcolaborador,
        'nome' => $this->nome,
        'profissao' => $this->profissao,
        'ativo' => $this->ativo,
      ];
    }
    return $dados;
  }

  // obs
  public function getobsAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setobsAttribute($value)
  {
    $this->attributes['obs'] =  utf8_decode($value);
  }

  // cep
  public function getcepAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setcepAttribute($value)
  {
    $this->attributes['cep'] =  utf8_decode($value);
  }

  // telefone1
  public function gettelefone1Attribute($value)
  {
    return utf8_encode($value);
  }
  public function settelefone1Attribute($value)
  {
    $this->attributes['telefone1'] =  utf8_decode($value);
  }

  // telefone2
  public function gettelefone2Attribute($value)
  {
    return utf8_encode($value);
  }
  public function settelefone2Attribute($value)
  {
    $this->attributes['telefone2'] =  utf8_decode($value);
  }

  // cpfcnpj
  public function getcpfcnpjAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setcpfcnpjAttribute($value)
  {
    $this->attributes['cpfcnpj'] =  utf8_decode($value);
  }

  // uf
  public function getufAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setufAttribute($value)
  {
    $this->attributes['uf'] =  utf8_decode($value);
  }

  // cidade
  public function getcidadeAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setcidadeAttribute($value)
  {
    $this->attributes['cidade'] =  utf8_decode($value);
  }

  // bairro
  public function getbairroAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setbairroAttribute($value)
  {
    $this->attributes['bairro'] =  utf8_decode($value);
  }

  // nome
  public function getnomeAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setnomeAttribute($value)
  {
    $this->attributes['nome'] =  utf8_decode($value);
  }

  // endereco
  public function getenderecoAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setenderecoAttribute($value)
  {
    $this->attributes['endereco'] =  utf8_decode($value);
  }

  // profissao
  public function geprofissaoAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setprofissaoAttribute($value)
  {
    $this->attributes['profissao'] =  utf8_decode($value);
  }
}
