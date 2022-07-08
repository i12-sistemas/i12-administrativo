<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class PropostaAtividade extends Model
{

    protected $table = 'propostaatividade';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function getfaseAttribute($value)
    {
        return utf8_encode($value);
    }
    public function getatividadeAttribute($value)
    {
        return utf8_encode($value);
    }
    public function getresponsavelAttribute($value)
    {
        return utf8_encode($value);
    }
    

    public function apontamentos() {
        return $this->hasMany('App\models\PropostasExecucaoApont', 'idatividade');
        // left join propostasexecucaoapont on propostasexecucaoapont.idatividade=propostaatividade.id AND propostasexecucaoapont.idconsultor=:idconsultor
    }      

    public function consultoresnomes() {
        // return 'okokokookok';
        $consutores = $this->apontamentos->groupBy('consultor.nome');
        $list = [];
        foreach($consutores as $consultor => $det){
            $list[] = utf8_decode( $consultor );
        }

        return $list;
        // left join propostasexecucaoapont on propostasexecucaoapont.idatividade=propostaatividade.id AND propostasexecucaoapont.idconsultor=:idconsultor
    }        
    
    public function empresa() {
        return $this->hasOne('App\models\Empresa', 'id', 'idempresa' )->select('razao', 'fantasia', 'nomecurto');
    }   
   
    
}
