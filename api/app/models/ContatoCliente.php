<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContatoCliente extends Model
{
    public $timestamps = false;
    protected $table = 'contatocliente';
    protected $primaryKey = 'id';
    public $remember_token = false;
    protected $dates =['ultimosynci12'];

    public function getpermissoesAttribute($value)
    {
      return utf8_encode($value);
    }
    public function setpermissoesAttribute($value)
    {
      $this->attributes['permissoes'] = Str::lower(utf8_decode($value));
    }

    public function getpermissoeslistaAttribute($value)
    {
      $lista = null;
      if ($this->permissoes ? Str::length($this->permissoes) > 0 : false) {
        $lista = explode(',', $this->permissoes);
      }
      return $lista;
    }

    public function getisadministradorAttribute($value)
    {
      $permite = false;
      if ($this->permissoeslista) {
        $permite = in_array('administrador', $this->permissoeslista);
      }
      return $permite;
    }

    public function getisoperadorAttribute($value)
    {
      $permite = false;
      if ($this->permissoeslista) {
        $permite = in_array('operador', $this->permissoeslista) || in_array('administrador', $this->permissoeslista);
      }
      return $permite;
    }

    public function getisfinanceiroAttribute($value)
    {
      $permite = false;
      if ($this->permissoeslista) {
        $permite = in_array('financeiro', $this->permissoeslista) || in_array('administrador', $this->permissoeslista);
      }
      return $permite;
    }

    public function getiscontadorAttribute($value)
    {
      $permite = false;
      if ($this->permissoeslista) {
        $permite = in_array('iscontador', $this->permissoeslista) || in_array('administrador', $this->permissoeslista);
      }
      return $permite;
    }    

}
