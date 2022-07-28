<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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


    public function exportCliente($compactText, $showContato = true, $showAnexo = false)
    {
      if (($this->restrito === 1) || ($this->excluido === 1)) return null;

      $dados = [
        'id' => $this->id,
        'texto' => $this->texto,
        'origem' => $this->origem,
        'ip' => $this->ip,
        'datahora' => $this->datahora->format('Y-m-d H:i:s'),
      ];
      if ($compactText) {
        $options = array(
          'ignore_errors' => true,
          // other options go here
        );
        $texto = Str::limit(\Soundasleep\Html2Text::convert($this->texto, $options), 75);
        $dados['descricao'] = $texto;
      }

      if ($showContato) {
        if ($this->contatoacao) {
          $dados['contatoacao'] = $this->contatoacao->exportPublico();
        }
      }
      if ($showAnexo) {
          $anexos = [];
          $totalbytes = 0;
          foreach ($this->anexos as $anexolink) {
            $totalbytes = $totalbytes + $anexolink->anexo->anexosize;
            $anexos[] = $anexolink->anexo->exportCliente();
          }  
          if (count($anexos)) {
            $dados['anexostotalbytes'] = $totalbytes;      
            $dados['anexos'] = $anexos;      
          }
      }
      return $dados;
    }       

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


    public function getemailerrorAttribute($value)
    {
        return utf8_encode($value);
    }    
    
    public function setemailerrorAttribute($value)
    {
        $this->attributes['emailerror'] = utf8_decode($value);
    }       

    public function contatoacao() {
        return $this->hasOne(Contato::class,'id', 'idcontatoacao')->withTrashed();
    }    
    
    public function anexos() {
        return $this->hasMany(InteracaoAnexoLink::class, 'idinteracao', 'id')
                    ->select('interacaoanexolink.*')
                    ->with('anexo')
                    ->join('interacaoanexo', 'interacaoanexolink.idanexo', '=', 'interacaoanexo.id')
                    ->orderBy('interacaoanexo.dhcad', 'desc');
    }           
}
