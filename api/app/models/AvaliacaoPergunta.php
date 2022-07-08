<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class AvaliacaoPergunta extends Model
{
    const CREATED_AT = 'dhcad';
    const UPDATED_AT = 'dhalt';
    

    protected $table = 'avaliacaopergunta';
    protected $primaryKey = 'id';
    protected $dates = [
        'dhalt',
        'dhcad',
    ];  

    public function getperguntaAttribute($value)
    {
        return utf8_encode($value);
    }

    public function getopcao1Attribute($value)
    {
        return utf8_encode($value);
    }
    public function getopcao2Attribute($value)
    {
        return utf8_encode($value);
    }
    public function getopcao3Attribute($value)
    {
        return utf8_encode($value);
    }
    public function getopcao4Attribute($value)
    {
        return utf8_encode($value);
    }
    public function getopcao5Attribute($value)
    {
        return utf8_encode($value);
    }

    

}
