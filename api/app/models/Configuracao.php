<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;

class Configuracao extends Model
{
    public $timestamps = false;
    private $location;
    protected $table = 'configuracao';
    protected $primaryKey = 'flagid';
    public $incrementing = false;


    public function getflagidAttribute($value)
    {
        return utf8_encode($value);
    }  
    public function setflagidAttribute($value)
    {
        $this->attributes['flagid'] = utf8dec($value) ;
    }   
    public function gettipocampoAttribute($value)
    {
        return utf8_encode($value);
    }  
    public function settipocampoAttribute($value)
    {
        $this->attributes['tipocampo'] = utf8dec($value) ;
    }  
    public function getvalorAttribute($value)
    {
        return utf8_encode($value);
    }  
    public function setvalorAttribute($value)
    {
        $this->attributes['valor'] = utf8dec($value) ;
    }            
    public function getdescricaoAttribute($value)
    {
        return utf8_encode($value);
    }  
    public function setdescricaoAttribute($value)
    {
        $this->attributes['descricao'] = ucutf8dec($value) ;
    }      

    public function gettextoAttribute($value)
    {
        return utf8_encode($value);
    }  
    public function settextoAttribute($value)
    {
        $this->attributes['texto'] = utf8dec($value) ;
    }     

    public function getUrlImagem()
    {
        try {
            if ($this->imagem) {
                $filename = $this->valor;
                $internal = 'images/configuracao/' . $filename;
                $exists = Storage::disk('public')->exists($internal);
                if (!$exists) {
                    Storage::disk('public')->put($internal, $this->imagem);
                }
                return url('/storage/' . $internal);
            } else {
                return '';
            }

        } catch (Exception $e) {
            return 'error';
        }
    }

    public function toArray(){
      
        if($this->flagid==''){
            return '';
        }

        $valor = '';
        //ENUM('MOEDA',  'PATH', '', '', '')
        switch ($this->tipocampo) {
            case 'JSON':
                $valor = json_decode($this->texto);
                break;
            case 'LOGICO':
                $valor = (($this->valor=='1')||($this->valor==1)||($this->valor=='S')||($this->valor=='s'));
                break;
            case 'TEXTO':
                $valor = $this->texto;
                break;
            case 'INTEIRO':
                $valor = intval($this->valor);
                break;   
            case 'DATAHORA':
                    try{
                        if (!Carbon::createFromFormat('Y-m-d h:i:s', $this->valor)) {
                            throw new Exception('Data de nascimento inválida. ' . $dados->dtnasc);
                        }else{
                            $valor = Carbon::createFromFormat('Y-m-d h:i:s', $this->valor)->format('Y-m-d h:i:s');
                        }
                    } catch (Exception $e) {
                        $valor = '';
                    }   
                break;   
            case 'DATA':
                    try{
                        if (!Carbon::createFromFormat('Y-m-d', $this->valor)) {
                            throw new Exception('Data de nascimento inválida. ' . $dados->dtnasc);
                        }else{
                            $valor = Carbon::createFromFormat('Y-m-d', $this->valor)->format('Y-m-d');
                        }
                    } catch (Exception $e) {
                        $valor = '';
                    }  
                break;                                           
            case 'IMAGEM':
                $valor = $this->getUrlImagem();
                break;                              
            default:
                $valor=  $this->valor;

        }   
       
        return ['valor' => $valor, 'tipo' => $this->tipocampo ];
    }    
}
