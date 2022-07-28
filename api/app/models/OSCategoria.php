<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class OSCategoria extends Model
{
  public $timestamps = false;
  protected $table = 'oscategoria';
  protected $primaryKey = 'id';

  public function exportCliente()
    {
        return [
          'id' => $this->id,
          'categoria' => $this->categoria,
        ];
    }    
    
    public function getcategoriaAttribute($value)
    {
        return strtoupper(utf8_encode($value));
    }
    public function setcategoriaAttribute($value)
    {
        $this->attributes['categoria'] = ucutf8dec($value);
    }    
}
