<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class i12clibackuplog extends Model
{
    public $timestamps = false;
    protected $table = 'i12_cli_backuplog';
    protected $primaryKey = 'id';
    protected $dates = [
        'dhlocal',
        'dhcad',
    ];  

    public function getfilenameAttribute($value)
    {
        return utf8_encode($value);
    }
    // public function setfilenameAttribute($value)
    // {
    //     $this->attributes['filename'] = utf8_encode($value);
    // }

    public function getcnpjAttribute($value)
    {
        return utf8_encode($value);
    }
    public function setcnpjAttribute($value)
    {
        $this->attributes['cnpj'] = utf8_encode($value);
    }
        
    public function getdetalheAttribute($value)
    {
        return utf8_encode($value);
    }
    // public function setdetalheAttribute($value)
    // {
    //     $this->attributes['detalhe'] = utf8_encode($value);
    // }

    public function getservercloudAttribute($value)
    {
        return utf8_encode($value);
    }
    // public function setservercloudAttribute($value)
    // {
    //     $this->attributes['servercloud'] = utf8_encode($value);
    // }

    public function getversaoappAttribute($value)
    {
        return utf8_encode($value);
    }    
    // public function setversaoappAttribute($value)
    // {
    //     $this->attributes['versaoapp'] = utf8_encode($value);
    // }

    public function getversaoappbkpAttribute($value)
    {
        return utf8_encode($value);
    }
    // public function setversaoappbkpAttribute($value)
    // {
    //     $this->attributes['versaoappbkp'] = utf8_encode($value);
    // }

    public function getversaobdAttribute($value)
    {
        return utf8_encode($value);
    }
    // public function setversaobdpAttribute($value)
    // {
    //     $this->attributes['versaobd'] = utf8_encode($value);
    // }


}
