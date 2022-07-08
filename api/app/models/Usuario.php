<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\Empresa;
use App\Models\AuxPermissao;
use Illuminate\Support\Facades\DB;

class Usuario extends Model
{
  protected $table = 'usuario';
  public $timestamps = false;
  protected $primaryKey = 'codusuario';
  protected $dates = ['dtalt', 'dtaltpwd', 'dtcad', 'dhsenharesetada'];
  protected $hidden = ['foto', 'fotomd5', 'senha'];
  // protected $fillable = ['pessoa', 'dtalt', 'dtaltpwd', 'dtcad', 'dhsenharesetada', 'ativo', 'codprofissional', 'idrelacionamentoi12', 'idusercad', 'iduseralt',
  // 'iduserreset', 'foto', 'skin', 'cpfcnpj', 'email', 'login', 'tel1', 'tel2', 'websession', 'senha', 'fotomd5', 'nome', 'idrelacionamentoi12msg'];

  //atrributes
    // foto original do cadastro
    // pode ser 1px x 1px ou até 2048px x 2048px
    public function getfotourlAttribute() {
      try {
          if ($this->foto) {
              $filename = $this->fotomd5 ;
              $internal = 'images/usuarios/size-origin/' . $filename;
              $exists = Storage::disk('domains')->exists($internal);

              if (!$exists) {
              //     $image = $this->foto;
              //     $img = Image::make($image)->encode($this->fotoext);
              //     $img->stream(); // <-- Key point
                Storage::disk('domains')->put($internal, $this->foto);
                $exists = Storage::disk('domains')->exists($internal);
              }
              return $exists ? url('/storage/' . $internal) : null;
          } else {
              return null;
          }

      } catch (\Exception $e) {
          // return asset('assets/images/semfoto.png');
        return null;
      }
    }

    // foto tamanho icon 48x48px
    public function getfotoiconurlAttribute() {
      try {

        if ($this->foto) {
          $filename = $this->fotomd5;
          $internal = 'images/usuarios/size-48x48/' . $filename;

          $exists = Storage::disk('domains')->exists($internal);

          if (!$exists) {
            $image = $this->foto;
            $img = Image::make($image)->encode($this->fotoext, 75);
            // resize the image to a width of 300 and constrain aspect ratio (auto height)
            $img->resize(48, null, function ($constraint) {
              $constraint->aspectRatio();
            });
            $img->stream(); // <-- Key point
            Storage::disk('domains')->put($internal, $img, 'domains');
            $exists = Storage::disk('domains')->exists($internal);
          }
        } else {
          $exist = false;
          return null;
        }
        return $exists ? Storage::disk('domains')->url($internal) : null;
      } catch (\Exception $e) {
        \Log::error($e->getMessage());
          // return asset('assets/images/semfoto.png');
        return null;
      }
    }

    // // foto tamanho icon 48x48px
    // public function getfotoiconurlAttribute() {
    //   try {
    //       if ($this->foto) {
    //           $filename = $this->fotomd5;
    //           $internal = 'images/usuarios/size-48x48/' . $filename;

    //           $exists = Storage::disk('domains')->exists($internal);
    //           if (!$exists) {
    //             $image = $this->foto;
    //             $img = Image::make($image)->encode($this->fotoext, 75);
    //             // resize the image to a width of 300 and constrain aspect ratio (auto height)
    //             $img->resize(48, null, function ($constraint) {
    //               $constraint->aspectRatio();
    //             });
    //             $img->stream(); // <-- Key point
    //             Storage::disk('domains')->put($internal, $img, 'domains');
    //             $exists = Storage::disk('domains')->exists($internal);
    //           }
    //           return $exists ? url('/storage/' . $internal) : null;
    //       } else {
    //           return null;
    //       }

    //   } catch (\Exception $e) {
    //       // return asset('assets/images/semfoto.png');
    //     return null;
    //   }
    // }

    // //foto
    // public function getfotoAttribute($value)
    // {
    //   return utf8_encode($value);
    // }

    // public function setfotoAttribute($value)
    // {
    //   $this->attributes['foto'] =  utf8_decode($value);
    // }

    //skin
    public function getskinAttribute($value)
    {
      return utf8_encode($value);
    }

    public function setskinAttribute($value)
    {
      $this->attributes['skin'] =  utf8_decode($value);
    }

    //cpfcnpj
    public function getcpfcnpjAttribute($value)
    {
      return utf8_encode($value);
    }

    public function setcpfcnpjAttribute($value)
    {
      $this->attributes['cpfcnpj'] =  utf8_decode($value);
    }

    //email
    public function getemailAttribute($value)
    {
      return utf8_encode($value);
    }

    public function setemailAttribute($value)
    {
      $this->attributes['email'] =  utf8_decode($value);
    }

    //login
    public function getloginAttribute($value)
    {
      return utf8_encode($value);
    }

    public function setloginAttribute($value)
    {
      $this->attributes['login'] =  utf8_decode($value);
    }

    //websession
    public function getwebsessionAttribute($value)
    {
      return utf8_encode($value);
    }

    public function setwebsessionAttribute($value)
    {
      $this->attributes['websession'] =  utf8_decode($value);
    }

    //tel1
    public function gettel1Attribute($value)
    {
      return utf8_encode($value);
    }

    public function settel1Attribute($value)
    {
      $this->attributes['tel1'] =  utf8_decode($value);
    }

    //tel2
    public function gettel2Attribute($value)
    {
      return utf8_encode($value);
    }

    public function settel2Attribute($value)
    {
      $this->attributes['tel2'] =  utf8_decode($value);
    }


    //senha
    public function getsenhaAttribute($value)
    {
      return utf8_encode($value);
    }

    public function setsenhaAttribute($value)
    {
      $this->attributes['senha'] =  utf8_decode($value);
    }

    //fotomd5
    public function getfotomd5Attribute($value)
    {
      return utf8_encode($value);
    }

    public function setfotomd5Attribute($value)
    {
      $this->attributes['fotomd5'] =  utf8_decode($value);
    }

    //nome
    public function getnomeAttribute($value)
    {
      return utf8_encode($value);
    }

    public function setnomeAttribute($value)
    {
      $this->attributes['nome'] =  utf8_decode($value);
    }

    //idrelacionamentoi12msg
    public function getidrelacionamentoi12msgAttribute($value)
    {
      return utf8_encode($value);
    }

    public function setidrelacionamentoi12msgAttribute($value)
    {
      $this->attributes['idrelacionamentoi12msg'] =  utf8_decode($value);
    }

     // toArrary
    public function export($complete = false)
    {
      $a = [
        'codusuario'        =>  $this->codusuario,
        'nome'              =>  $this->nome,
        'login'             =>  $this->login,
        'email'             =>  $this->email,
        'ativo'             =>  $this->ativo,
        'fotoiconurl'       =>  $this->fotoiconurl
      ];

      if ($complete) {
        $a['contato']  = $this->contato ? $this->contato->export(false) : null;
        $a['colaborador']  = $this->colaborador ? $this->colaborador->toArrayDef(false) : null;
      }
      return $a;
    }

  // toArrary
  public function toArray($forma = 'simples')
  {
    $a = [
      'codusuario'        =>  $this->codusuario,
      'nome'              =>  $this->nome,
      'login'             =>  $this->login,
      'email'             =>  $this->email,
      'ativo'             =>  $this->ativo,
      'contato'           =>  $this->contato ? $this->contato->export(false) : null,
      'colaborador'       =>  $this->colaborador? $this->colaborador->toArrayDef(false) : null,
      'fotoiconurl'       =>  $this->fotoiconurl
    ];
    return $a;
  }

  public function toArrayDef($complete = false)
  {
    $a = [
      'codusuario'        =>  $this->codusuario,
      'nome'              =>  $this->nome,
      'fotoiconurl'       =>  $this->fotoiconurl
    ];
    return $a;
  }

  public function empresa($cnpj)
  {
    return Empresa::select('empresa.*')
                  ->join('perfil','perfil.idempresa', '=', 'empresa.codempresa')
                  ->join('usuario_perfil','usuario_perfil.idperfil', '=', 'perfil.id')
                  ->join('auxpermissao','auxpermissao.idperfil', '=', 'perfil.id')
                  ->join('permissao','permissao.idpermissao', '=', 'auxpermissao.idpermissao')
                  ->where('permissao.idpermissao', '=', 1)
                  ->where('permissao.permite', '=', 1)
                  ->where('empresa.ativo', '=', 1)
                  ->where('empresa.ativomovimento', '=', 1)
                  ->where('empresa.cnpj', '=', $cnpj)
                  ->where('usuario_perfil.idusuario', '=', $this->codusuario)
                  ->orderBy('empresa.padrao', 'DESC')
                  ->orderby('empresa.apelido', 'ASC')
                  ->groupby('empresa.codempresa')
                  ->first();

  }


  public function permissoes($pIDEmpresa)
  {
    $permissoes = AuxPermissao::select('auxpermissao.*', 'permissao.titulo', 'permissao.descricao', DB::Raw('group_concat(distinct perfil.descricao) as perfilNome'))
                  ->join('usuario_perfil','usuario_perfil.idperfil', '=', 'auxpermissao.idperfil')
                  ->join('perfil','perfil.id', '=', 'usuario_perfil.idperfil')
                  ->join('empresa','empresa.codempresa', '=', 'perfil.idempresa')
                  ->join('permissao','permissao.idpermissao', '=', 'auxpermissao.idpermissao')
                  ->where('usuario_perfil.idusuario', '=', $this->codusuario)
                  ->where('empresa.codempresa', '=', $pIDEmpresa)
                  ->where('permissao.permite', '=', 1)
                  ->where('empresa.ativo', '=', 1)
                  ->where('perfil.ativo', '=', 1)
                  ->where('empresa.ativomovimento', '=', 1)

                  ->orderBy('permissao.idpermissao', 'ASC')
                  ->groupby('auxpermissao.idpermissao')
                  ->get();

    $dados = [];
    foreach ($permissoes as $permissao) {
      $dados[] = [
        'idpermissao'   =>  $permissao->idpermissao,
        'titulo'        =>  utf8_encode($permissao->titulo),
        'descricao'     =>  utf8_encode($permissao->descricao),
        'perfis'        =>  utf8_encode($permissao->perfilNome)
      ];
    }
    return $dados;
  }


    // telefones
    public function gettelefonesAttribute($value)
    {
      // trata telefones
      $tel = [];
      if ($this->tel1 != '') {
        $tel[] = [
          'numero'    => $this->tel1,
        ];
      }
      if ($this->tel2 != '') {
        $tel[] = [
          'numero'    => $this->tel2,
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

  public function contato()
  {
    return $this->hasOne(Contato::class, 'idusuario', 'codusuario');
  }

  public function colaborador()
  {
    return $this->hasOne(Colaborador::class, 'codcolaborador', 'codprofissional');
  }

}
