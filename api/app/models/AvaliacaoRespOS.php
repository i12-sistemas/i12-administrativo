<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class AvaliacaoRespOS extends Model
{
   
    public $timestamps = false;
    protected $table = 'avaliacaorespos';
    protected $primaryKey = 'id';
    protected $dates = [
        'dhavaliacao',
    ];  

    public function getipAttribute($value)
    {
        return utf8_encode($value);
    }
    public function getresplivreAttribute($value)
    {
        return utf8_encode($value);
    }

    public function setipAttribute($value)
    {
        $this->attributes['ip'] = utf8dec($value);
    }   
    public function setresplivreAttribute($value)
    {
        $this->attributes['resplivre'] = utf8dec($value);
    }           
    
}
