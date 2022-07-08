<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Empresa extends Model
{
  protected $connection = 'mysql';
  protected $table        = 'empresa';
  protected $primaryKey = 'codempresa';
  public $timestamps      = false;
  protected $hidden       = ['logotipo'];

  // protected $fillable     = ['fantasia',  'razao', 'telefone', 'cnpj', 'inscr', 'endereco', 'cep', 'cidade', 'uf', 'bairro', 'complemento', 'email', 'logotipo', 'codmunicipio', 'im', 'cnae', 'crt', 'nro', 'csosn', 'padrao', 'ativo', 'ativomovimento', 'skin', 'apelido', 'idempresapai', 'idestoquepadrao'];

  public function getfotourlAttribute() {
    try {
        if ($this->logotipo) {
            $filename = md5($this->id) . '.png';
            $internal = 'images/Empresa/size-origin/' . $filename;
            $exists = Storage::disk('domains')->exists($internal);
            if (!$exists) {
              $image = $this->logotipo;
              $img = Image::make($image)->encode('png', 75);
              $img->stream();
              Storage::disk('domains')->put($internal, $img, 'domains');
              $exists = Storage::disk('domains')->exists($internal);
            }
            return $exists ? url('/storage/' . $internal) : null;
        } else {
            return null;
        }

    } catch (\Exception $e) {
        return asset('assets/images/semfoto.png');
      // return null;
    }
  }

  // foto tamanho icon 60x60px
  public function getfotoiconurlAttribute() {
    try {
      if ($this->logotipo) {
        $filename = md5($this->id) . '.png';
        $internal = 'images/Empresa/size-60x60/' . $filename;
        $exists = Storage::disk('domains')->exists($internal);
        if (!$exists) {
          $image = $this->logotipo;
          $img = Image::make($image)->encode('png', 75);
          // resize the image to a width of 300 and constrain aspect ratio (auto height)
          $img->resize(60, null, function ($constraint) {
            $constraint->aspectRatio();
          });
          $img->stream(); // <-- Key point
          Storage::disk('domains')->put($internal, $img, 'domains');
          $exists = Storage::disk('domains')->exists($internal);
        }
        return $exists ? url('/storage/' . $internal) : null;
      } else {
        return null;
      }

    } catch (\Exception $e) {
      // return asset('assets/images/semfoto.png');
      return 'nenhuma imagem';
    }
  }

  // criptokey
  public function getkeyAttribute($value)
  {
    return md5($this->codempresa . $this->cnpj);
  }

  // fantasia
  public function getfantasiaAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setfantasiaAttribute($value)
  {
    $this->attributes['fantasia'] = utf8_decode($value);
  }

  // razao
  public function getrazaoAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setrazaoAttribute($value)
  {
    $this->attributes['razao'] = utf8_decode($value);
  }

  // telefone
  public function gettelefoneAttribute($value)
  {
    return utf8_encode($value);
  }
  public function settelefoneAttribute($value)
  {
    $this->attributes['telefone'] = utf8_decode($value);
  }

  // inscr
  public function getinscrAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setinscrAttribute($value)
  {
    $this->attributes['inscr'] = utf8_decode($value);
  }

  // endereco
  public function getenderecoAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setenderecoAttribute($value)
  {
    $this->attributes['endereco'] = utf8_decode($value);
  }

  // cep
  public function getcepAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setcepAttribute($value)
  {
    $this->attributes['cep'] = utf8_decode($value);
  }

  // cidade
  public function getcidadeAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setcidadeAttribute($value)
  {
    $this->attributes['cidade'] = utf8_decode($value);
  }

  // uf
  public function getufAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setufAttribute($value)
  {
    $this->attributes['uf'] = utf8_decode($value);
  }

  // bairro
  public function getbairroAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setbairroAttribute($value)
  {
    $this->attributes['bairro'] = utf8_decode($value);
  }

  // complemento
  public function getcomplementoAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setcomplementoAttribute($value)
  {
    $this->attributes['complemento'] = utf8_decode($value);
  }

  // email
  public function getemailAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setemailAttribute($value)
  {
    $this->attributes['email'] = utf8_decode($value);
  }

  // im
  public function getimAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setimAttribute($value)
  {
    $this->attributes['im'] = utf8_decode($value);
  }

  // cnae
  public function getcnaeAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setcnaeAttribute($value)
  {
    $this->attributes['cnae'] = utf8_decode($value);
  }

  // nro
  public function getnroAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setnroAttribute($value)
  {
    $this->attributes['nro'] = utf8_decode($value);
  }

  // skin
  public function getskinAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setskinAttribute($value)
  {
    $this->attributes['skin'] = utf8_decode($value);
  }

  // apelido
  public function getapelidoAttribute($value)
  {
    return utf8_encode($value);
  }
  public function setapelidoAttribute($value)
  {
    $this->attributes['apelido'] = utf8_decode($value);
  }


  public function toCompleteArray()
  {
    $dados = $this->toArray();
    $dados['key'] = $this->key;
    $dados['fotourl'] = $this->fotourl;
    return $dados;
  }

  // toArrary
  public function toSimpleArray()
  {
    $dados = [
      'codempresa'    =>  $this->codempresa,
      'apelido'       =>  $this->apelido,
      'cnpj'          =>  $this->cnpj,
      'telefone'      =>  $this->telefone,
      'email'         =>  $this->email,
      'cidade'        =>  $this->cidade,
      'fotourl'       =>  $this->fotourl
    ];
    return $dados;
  }
}
