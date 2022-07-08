<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Contato extends Model
{
  protected $table      = 'contato';
  protected $primaryKey = 'id';
  public $timestamps    = false;
  public $hidden        = ['foto', 'fotomd5'];
  protected $dates      = ['datanasc', 'i12ultimaatualizacao'];

  // protected $fillable   = ['pessoa', 'cpfcnpj', 'nome', 'fantasia', 'logradouro', 'endereco', 'num', 'bairro', 'cidade', 'compl','uf', 'cep', 'obs', 'idcliente',  idfornecedor',  'idusuario', 'idcolaborador', 'tel1', 'tel2', 'tel3', 'tel4', 'tel5', 'desctel1', 'desctel2', 'desctel3', 'desctel4', 'desctel5', 'email', 'datanasc', 'lixeira', 'password', 'foto', 'fotomd5', 'i12ultimaatualizacao']

  // atributes
    public function getfotoiconurlAttribute() {
      try {
        if ($this->foto) {
          $filename = $this->fotomd5;
          $internal = 'images/contato/size-100x100/' . $filename;
          $exists = Storage::disk('domains')->exists($internal);
          if (!$exists) {
            $image = $this->foto;
            $img = Image::make($image);
            // resize the image to a width of 300 and constrain aspect ratio (auto height)
            $img->resize(100, null, function ($constraint) {
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
        return null;
      }
    }

    // cpfcnpj
    public function getcpfcnpjAttribute($value)
    {
      return utf8_encode($value);
    }
    public function setcpfcnpjAttribute($value)
    {
      $this->attributes['cpfcnpj'] =  utf8_decode($value);
    }

    // nome
    public function getnomeAttribute($value)
    {
      return utf8_encode($value);
    }
    public function setnomeAttribute($value)
    {
      $this->attributes['nome'] =  utf8_decode($value);
    }

    // fantasia
    public function getfantasiaAttribute($value)
    {
      return utf8_encode($value);
    }
    public function setfantasiaAttribute($value)
    {
      $this->attributes['fantasia'] =  utf8_decode($value);
    }

    // logradouro
    public function getlogradouroAttribute($value)
    {
      return utf8_encode($value);
    }
    public function setlogradouroAttribute($value)
    {
      $this->attributes['logradouro'] =  utf8_decode($value);
    }

    // endereco
    public function getenderecoAttribute($value)
    {
      return utf8_encode($value);
    }
    public function setenderecoAttribute($value)
    {
      $this->attributes['endereco'] =  utf8_decode($value);
    }

    // num
    public function getnumAttribute($value)
    {
      return utf8_encode($value);
    }
    public function setnumAttribute($value)
    {
      $this->attributes['num'] =  utf8_decode($value);
    }

    // bairro
    public function getbairroAttribute($value)
    {
      return utf8_encode($value);
    }
    public function setbairroAttribute($value)
    {
      $this->attributes['bairro'] =  utf8_decode($value);
    }

    // cidade
    public function getcidadeAttribute($value)
    {
      return utf8_encode($value);
    }
    public function setcidadeAttribute($value)
    {
      $this->attributes['cidade'] =  utf8_decode($value);
    }

    // compl
    public function getccomplttribute($value)
    {
      return utf8_encode($value);
    }
    public function setcomplAttribute($value)
    {
      $this->attributes['compl'] =  utf8_decode($value);
    }

    // uf
    public function getufAttribute($value)
    {
      return utf8_encode($value);
    }
    public function setufAttribute($value)
    {
      $this->attributes['uf'] =  utf8_decode($value);
    }

    // cep
    public function getcepAttribute($value)
    {
      return utf8_encode($value);
    }
    public function setcepAttribute($value)
    {
      $this->attributes['cep'] =  utf8_decode($value);
    }

    // obs
    public function getobsAttribute($value)
    {
      return utf8_encode($value);
    }
    public function setobsAttribute($value)
    {
      $this->attributes['obs'] =  utf8_decode($value);
    }

    // tel1
    public function gettel1Attribute($value)
    {
      return utf8_encode($value);
    }
    public function settel1Attribute($value)
    {
      $this->attributes['tel1'] =  utf8_decode($value);
    }

    // tel2
    public function gettel2Attribute($value)
    {
      return utf8_encode($value);
    }
    public function settel2Attribute($value)
    {
      $this->attributes['tel2'] =  utf8_decode($value);
    }

    // tel3
    public function gettel3Attribute($value)
    {
      return utf8_encode($value);
    }
    public function settel3Attribute($value)
    {
      $this->attributes['tel3'] =  utf8_decode($value);
    }

    // tel4
    public function gettel4Attribute($value)
    {
      return utf8_encode($value);
    }
    public function settel4Attribute($value)
    {
      $this->attributes['tel4'] =  utf8_decode($value);
    }

    // tel5
    public function gettel5Attribute($value)
    {
      return utf8_encode($value);
    }
    public function settel5Attribute($value)
    {
      $this->attributes['tel5'] =  utf8_decode($value);
    }

    // desctel1
    public function getdesctel1Attribute($value)
    {
      return utf8_encode($value);
    }
    public function setdesctel1Attribute($value)
    {
      $this->attributes['desctel1'] =  utf8_decode($value);
    }

    // desctel2
    public function getdesctel2Attribute($value)
    {
      return utf8_encode($value);
    }
    public function setdesctel2Attribute($value)
    {
      $this->attributes['desctel2'] =  utf8_decode($value);
    }

    // desctel3
    public function getdesctel3Attribute($value)
    {
      return utf8_encode($value);
    }
    public function setdesctel3Attribute($value)
    {
      $this->attributes['desctel3'] =  utf8_decode($value);
    }

    // desctel4
    public function getdesctel4Attribute($value)
    {
      return utf8_encode($value);
    }
    public function setdesctel4Attribute($value)
    {
      $this->attributes['desctel4'] =  utf8_decode($value);
    }

    // desctel5
    public function getdesctel5Attribute($value)
    {
      return utf8_encode($value);
    }
    public function setdesctel5Attribute($value)
    {
      $this->attributes['desctel5'] =  utf8_decode($value);
    }

    // email
    public function getemailAttribute($value)
    {
      return utf8_encode($value);
    }

    public function getemailsAttribute($value)
    {
      $emails = explode (';', $this->email);
      if (count($emails) > 0) {
        $valids = [];
        foreach ($emails as $email) {
          if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $valids[] = $email;
          }
        }
        return (count($valids) > 0) ? $valids : null;
      } else {
        return null;
      }
    }

    public function setemailAttribute($value)
    {
      $this->attributes['email'] =  utf8_decode($value);
    }

    // password
    public function getpasswordAttribute($value)
    {
      return utf8_encode($value);
    }
    public function setpasswordAttribute($value)
    {
      $this->attributes['password'] =  utf8_decode($value);
    }

    // telefones
    public function gettelefonesAttribute($value)
    {
      // trata telefones
      $tel = [];
      if ($this->tel1 != '') {
        $tel[] = [
          'numero'    => $this->tel1,
          'desc'      => $this->desctel1
        ];
      }
      if ($this->tel2 != '') {
        $tel[] = [
          'numero'    => $this->tel2,
          'desc'      => $this->desctel2
        ];
      }
      if ($this->tel3 != '') {
        $tel[] = [
          'numero'    => $this->tel3,
          'desc'      => $this->desctel3
        ];
      }
      if ($this->tel4 != '') {
        $tel[] = [
          'numero'    => $this->tel4,
          'desc'      => $this->desctel4
        ];
      }
      return count($tel) > 0 ? $tel : null;
    }

    public function gettelefonesstringAttribute($value)
    {
      // trata telefones
      $tels = $this->telefones;
      $lista = [];
    if ($tels) {
        foreach ($tels as $tel) {
          $lista[] = $tel['numero'];
        }
      }
      $str = implode (' | ', $lista);
      return $str;
    }
    // atributes


  public function colaborador()
  {
    return $this->hasOne(Colaborador::class, 'codcolaborador', 'idcolaborador');
  }

  public function usuario()
  {
    return $this->hasOne(Usuario::class, 'codusuario', 'idusuario');
  }

  public function contatocliente()
  {
    return $this->hasMany(ContatoCliente::class, 'idcontato', 'id')
                  ->join('clientes', 'contatocliente.idcliente', '=', 'clientes.codcliente')
                  ->where('clientes.ativo', '=', 1)
                  ->orderBy('clientes.nome');
  }

  public function export($complete = false)
  {
    $a = [
      'id'        =>  $this->id,
      'pessoa'    =>  $this->pessoa,
      'doc'       =>  $this->cpfcnpj,
      'nome'      =>  $this->nome,
      'email'     =>  $this->emails,
      'telefones' =>  $this->telefones,
      'fotourl'   =>  $this->fotoiconurl
    ];

    if ($complete) {
      $clientes = [];
      foreach ($this->contatocliente as $contatocliente) {
        $clientes[] = $contatocliente->cliente->toArray();
      }
      if (count($clientes)==0) $clientes = null;
      $a['clientes']  = $clientes;
    }
    return $a;
  }


}
