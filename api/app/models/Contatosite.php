<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Contatosite extends Model
{
    public $timestamps = false;
    protected $table = 'contatosite';
    protected $primaryKey = 'id';
    protected $dates = [
        'dh',
    ];

    public function getnomeAttribute($value)
    {
        return utf8_encode($value);
    }
    public function setnomeAttribute($value)
    {
        $this->attributes['nome'] = ucutf8dec($value);
    }
    public function getemailAttribute($value)
    {
        return mb_strtolower(utf8_encode($value));
    }

    public function setemailAttribute($value)
    {
        $this->attributes['email'] = ucutf8dec($value);
    }

    public function gettelefoneAttribute($value)
    {
        return utf8_encode($value);
    }
    public function settelefoneAttribute($value)
    {
        $this->attributes['telefone'] = ucutf8dec($value);
    }

    public function getassuntoAttribute($value)
    {
        return utf8_encode($value);
    }
    public function setassuntoAttribute($value)
    {
        $this->attributes['assunto'] = ucutf8dec($value);
    }

    public function getmensagemAttribute($value)
    {
        return utf8_encode($value);
    }
    public function setmensagemAttribute($value)
    {
        $this->attributes['mensagem'] = ucutf8dec($value);
    }


    public function getdestinatarioAttribute($value)
    {
        return utf8_encode($value);
    }
    public function setdestinatarioAttribute($value)
    {
        $this->attributes['destinatario'] = utf8dec($value);
    }    
}
