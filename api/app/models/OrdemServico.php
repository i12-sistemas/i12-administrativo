<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class OrdemServico extends Model
{

    public $timestamps = false;
    protected $table = 'osordem';
    protected $primaryKey = 'id';
    protected $dates = [
        'dtabertura',
        'dtfechamento',
        'ultalteracao',
        'agendainicio',
        'agendafinal',
        'contratoinivigencia',
        'contratofimvigencial',
        'contratodiafaturamento',
        'agendainicio',
        'datavalidade',
        'contato_leitura_at',
        'usuario_leitura_at'
    ];  

    public function exportCliente($detalhe = false)
    {
      $atendimento = [
        'id' => $this->id, 
        'codigoacesso' => $this->codigoacesso, 
        'situacao' => $this->situacao, 
        'pendentecliente' => $this->pendentecliente, 
        'problemareclamado' => $this->problemareclamado, 
        'dtabertura' => $this->dtabertura->format('Y-m-d H:i:s'), 
        'categoria' => $this->categoria ? $this->categoria->exportCliente() : null, 
        'classificacao' => $this->classificacao ? $this->classificacao->exportCliente() : null, 
        'fase' => $this->fase ? $this->fase->exportCliente() : null, 
        'ultimainteracao' => $this->ultimainteracao ? $this->ultimainteracao->exportCliente(false, true, true) : null, 
        'contato_leitura_at' => $this->contato_leitura_at ? $this->contato_leitura_at->format('Y-m-d H:i:s') : null, 
      ];
      if ($this->datavalidade) $atendimento['prazoprevisao'] = $this->datavalidade->format('Y-m-d H:i:s');
      if ($this->dtfechamento) $atendimento['dtfechamento'] = $this->dtfechamento->format('Y-m-d H:i:s');

      if ($detalhe) {
        if($this->cliente){
          $atendimento['cliente'] = [
            'id' => $this->cliente->codcliente,
            'nome' => $this->cliente->nome,
            'fantasia' => $this->cliente->fantasia,
            'doc' => $this->cliente->cnpj,
          ];
        }   
        if($this->contato){
          $atendimento['contato'] = [
            'id' => $this->contato->id,
            'nome' => $this->contato->nome,
          ];
        }   

        if($this->interacoespublicas){
          $interacoes = [];
          foreach ($this->interacoespublicas as $interacaoos) {
            $interacao = $interacaoos->interacao;
            $interacoes[] = $interacao->exportCliente(false, true, true);
          } 
          $atendimento['interacoes'] = $interacoes;   
        }      
      }

      if ($this->dtfechamento) {
        $atendimento['dtfechamento'] = $this->dtfechamento->format('Y-m-d H:i:s');
      }

      return $atendimento;
    }        

    public function getproblemareclamadoAttribute($value)
    {
        return utf8_encode($value);
    }
    public function getsituacaoAttribute($value)
    {
        return utf8_encode($value);
    }
    public function setproblemareclamadoAttribute($value)
    {
        $this->attributes['problemareclamado'] = ucutf8dec($value);
    }   


    public function fase() {
        return $this->hasOne(OSFases::class, 'id', 'idfase');
    }    

    public function categoria() {
        return $this->hasOne(OSCategoria::class, 'id', 'idcategoria');
    }        
    
    public function classificacao() {
        return $this->hasOne(OSClassificacao::class, 'status', 'status');
    }    

    public function cliente() {
        return $this->hasOne(Clientes::class,'codcliente', 'idcliente');
    }      

    public function contato() {
        return $this->hasOne(Contato::class,'id', 'idclientecontato');
    }       

    public function usuario() {
        return $this->hasOne(Usuario::class, 'codusuario', 'idusuariovenda');
    }     

    public function leituras() {
        return $this->hasMany('App\models\OSLeitura', 'idos', 'id')->orderBy('dataleitura', 'desc');
    }      

    public function ultimainteracao() {
        return $this->hasOne(Interacao::class, 'id', 'idinteracaoultimapublica');
    } 
    
    public function interacoespublicas() {
        return $this->hasMany(InteracaoOS::class, 'idos', 'id')->with('interacao')
                    ->join('interacao', 'interacaoos.idinteracao', '=', 'interacao.id')
                    ->where('interacao.restrito', 0)
                    ->where('interacao.excluido', 0)
                    ->orderBy('interacao.datahora', 'asc')
                    ->orderBy('interacao.datacad', 'asc');
    } 

  
}

