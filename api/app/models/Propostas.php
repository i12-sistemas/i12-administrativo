<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Propostas extends Model
{
  
    public $timestamps = false;
    protected $table = 'propostas';
    protected $primaryKey = 'id';
    protected $dates = [
        'dtemissao',
        'dtvalidade',
        'dtinicioprojeto',
        'dtencerramentoprojeto'
    ];  

    public function getdtinicioprojetoAttribute($value)
    {
        return Carbon::parse($value);
    }
    public function getdtencerramentoprojetoAttribute($value)
    {
        return  Carbon::parse($value);
    }
    

    public function getdiasdeprojetoAttribute($value)
    {
        $dias = 0;
        if ($this->temdataplanejamento){
            $dias = $this->dtinicioprojeto->diffInDays($this->dtencerramentoprojeto);
        }
        return $dias;
    }  
    

    
    public function getdiasdecorridosprojetoAttribute($value)
    {
        $dias = 0;
        if ($this->temdataplanejamento){
            $dias = $this->dtinicioprojeto->diffInDays(($this->statusprojeto='ENCERRADO') ? $this->dtencerramentoprojeto :  Carbon::now());
        }
        return $dias;
    }    

    public function getdiasdeprojetopercAttribute($value)
    {
        $perc = 0;
        if (($this->temdataplanejamento) and ($this->diasdeprojeto<>0)){
            $perc = round((($this->diasdecorridosprojeto  / $this->diasdeprojeto)*100),2);
        }
        if($perc>100){
            $perc = 100;
        }
        return $perc;
    }           

    public function gettemdataplanejamentoAttribute($value)
    {
        return (($this->dtinicioprojeto) and ($this->dtencerramentoprojeto));
    }       

    public function getnomeprojetoAttribute($value)
    {
        return utf8_encode($value);
    }
    public function setnomeprojetoAttribute($value)
    {
        $this->attributes['nomeprojeto'] = ucutf8dec($value);
    } 
    public function getnumpropostaAttribute($value)
    {
        return utf8_encode($value);
    }    
    public function getcategAttribute($value)
    {
        return utf8_encode($value);
    }     
    public function getformapgtoAttribute($value)
    {
        return utf8_encode($value);
    }  
    public function getprazodeentregaAttribute($value)
    {
        return utf8_encode($value);
    }    
    public function gettipoAttribute($value)
    {
        return utf8_encode($value);
    }                    
    public function cliente() {
        return $this->hasOne('App\models\Clientes', 'id', 'idcliente' )->select('razao', 'fantasia');
    }  

    public function usuariotecnico() {
        return $this->hasOne('App\models\User', 'id', 'idusuariorespterc' )->select('id', 'nome', 'email');
    }  

    public function empresa() {
        return $this->hasOne('App\models\Empresa', 'id', 'idempresaresp' )->select('razao', 'fantasia', 'nomecurto');
    }  
    
    public function atividades() {
        return $this->hasMany('App\models\PropostaAtividade', 'idproposta');
    }   

    public function sharing() {
        return $this->hasMany('App\models\Sharedprojeto', 'idprojeto')->orderBy('dhcreate', 'asc');
    }   
    public function sharingativos() {
        return $this->hasMany('App\models\Sharedprojeto', 'idprojeto')->where('revogado', 0)->whereRaw('cast(dhvalidade as date)>=cast(now() as date)')->orderBy('dhcreate', 'asc');
    }       

    public function getatividadesemabertoAttribute() {
        return $this->atividades->where('apontencerrado', 0)->count();
    }   
    public function getatividadesparcialAttribute() {
        return $this->atividades->where('apontencerrado', '>', 0)->where('apontencerrado', '<', 100)->count();
    }   
    public function getatividadesencerradoAttribute() {
        return $this->atividades->where('apontencerrado', '>=', 100)->count();
    }   

    public function statusprojetodescricao($showicon = false)
    {
        $r = 'Indefinido';
        switch ($this->statusprojeto) {
            case 'GANHO':
                $r = ($showicon ? '<span class="fa fa-shield fa-1" aria-hidden="true"></span> ' : '') . 'Ganho' ;
                break;
            case 'PLANEJAMENTO':
                $r = ($showicon ? '<span class="fa fa-paper-plane fa-1" aria-hidden="true"></span> ' : '') . 'Planejamento';
                break;
            case 'EXECUCAO':
                $r = ($showicon ? '<span class="fa fa-tasks fa-1" aria-hidden="true"></span> ' : '') .  'Execução';
                break;
            case 'CLIENTE':
                $r = 'Cliente';
                break;
            case 'DOCUMENTACAO':
                $r = 'Documentação';
                break;
            case 'ENCERRADO':
                $r = 'Encerrado';
                break;
        } 

        return $r;
    }

    
    
}
