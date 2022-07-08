<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Propostas;

class Sharedprojeto extends Model
{
    protected $dates = [
        'dhcreate',
        'dhvalidade',
        'dhrevogacao'
    ];
    protected $appends = ['expirado', 'linkpublico'];
   
    protected $table = 'sharedprojeto';
    protected $primaryKey = 'id';
    public $remember_token=false;
    public $timestamps = false;

    public function getemailAttribute($value)
    {
        return utf8_encode($value);
    }

    public function getexpiradoAttribute()
    {
        return ($this->dhvalidade < Carbon::today() );
    }    

    public function getnomecontatoAttribute($value){
        return utf8_encode($value);
    }     
    
    public function leituras() {
        return $this->hasMany('App\models\ClienteReadProjeto', 'hash', 'hash')->orderBy('dhread', 'asc');
    }  

    public function projeto() {
        return $this->hasOne('App\models\Propostas', 'id', 'idprojeto' )->where('status', 'GANHO');
    }     
    
    public function getlinkpublicoAttribute() {
        $projeto = $this->projeto;
        $url = url('/') . '/projetos/' . $projeto->numproposta . '/statusreport/sharing/' . $this->hash;
        return $url;
    }       
}
