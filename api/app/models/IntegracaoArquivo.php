<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class IntegracaoArquivo extends Model
{
    public $timestamps = false;
    protected $table = 'integracao_arquivo';
    protected $primaryKey = 'id';
    public $remember_token = false;
    protected $fillable = [
        'id',
        'forma',
        'ativo',
        'pago',
        'categ'
    ];
    protected $dates = [
        'dhcad',
        'dtref'
    ];

    public function getfilenameAttribute($value)
    {
        return utf8_encode($value);
    } 
    
    public function setfilenameAttribute($value)
    {
        $this->attributes['filename'] = utf8dec($value);
    } 

    public function getmd5fileAttribute($value)
    {
        return utf8_encode($value);
    }   

    public function setmd5fileAttribute($value)
    {
        $this->attributes['md5file'] = utf8dec($value);
    } 

    public function itens() {
        return $this->hasMany('App\models\IntegracaoItem','idarquivo', 'id');
    }

    public function usuario() {
        return $this->hasOne('App\models\User','id', 'idusuario');
    }

    
}
