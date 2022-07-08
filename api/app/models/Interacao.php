<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Interacao extends Model
{
    const CREATED_AT = 'datacad';
    const UPDATED_AT = 'dataalt';

    protected $table = 'interacao';
    protected $primaryKey = 'id';
    protected $dates = [
        'datahora',
        'datahorafinal',
        'datacad',
        'dataalt'
    ];  

    public function gettextoAttribute($value)
    {
        return utf8_encode($value);
    }
    
    public function settextoAttribute($value)
    {
        $this->attributes['texto'] = utf8dec($value);
    }       
    public function getcategoriaAttribute($value)
    {
        return utf8_encode($value);
    }
    public function getclassificacaoAttribute($value)
    {
        return utf8_encode($value);
    }    
    public function getformaAttribute($value)
    {
        return utf8_encode($value);
    }

    public function contatoacao() {
        return $this->hasOne('App\models\Contato','id', 'idcontatoacao');
    }    
    
    public function anexos() {
        return $this->hasMany('App\models\InteracaoAnexoLink', 'idinteracao', 'id')
                                // ->with('interacao')
                                ->join('interacaoanexo', 'interacaoanexolink.idanexo', '=', 'interacaoanexo.id')
                                ->orderBy('interacaoanexo.dhcad', 'desc');
    }           
}
