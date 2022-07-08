<?php

namespace App\Http\Controllers\api\v1\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\Clientes;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\RetApiController;
use App\Models\i12TabelaIBPT;
use App\Models\i12TabelaIBPTLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TabelaIBPTController extends Controller
{
  protected $dir = '/arquivos/sistemas-down/icom/';
  
  public function OldFormat_index(Request $request)  {
    $ret = new RetApiController;
    try {
      $usuario= session('usuario');
      if (!$usuario) throw new Exception('Nenhum usuário loagdo');

      $localdir = $this->dir;
      // $diretorio = isset($request->diretorio) ? $request->diretorio : '';
      // if ($diretorio != '') $localdir = $localdir . "\\" . $diretorio;
      $disk = Storage::disk('public');
      $files = $disk->files($localdir);
      $lista = [];
      foreach ($files as $file) {
        $f = pathinfo(storage_path() . $file);
        $ext = mb_strtolower($f['extension']);
        $name = $f['filename'];
        $pos = strpos($name, 'tabelaibpt_');
        if ($pos !== false) {
          if ($ext == 'csv') {
            $newurl = $disk->url( $file);
            $lista[] = [
              'url'       =>  $newurl,
              'name'      =>  $name
            ];
          }
        }
      }
      $ret->data=$lista;
      $ret->ok=True;

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();

    
  }

  public function OldFormat_delete(Request $request)  {
    $ret = new RetApiController;
    try {
      $usuario= session('usuario');
      if (!$usuario) throw new Exception('Nenhum usuário loagdo');

      $filename = isset($request->filename) ? trim($request->filename) : null;
      if ($filename ? $filename === '' : true) throw new Exception('Arquivo não informado');

      $arquivo = $this->dir . '\\' . $filename . '.csv';
      $disk = Storage::disk('public');

      if (!$disk->exists($arquivo)) throw new Exception('Arquivo não encontrado no diretório');
      $disk->delete($arquivo);


      

      $ret->msg='Arquivo removido com sucesso';
      $ret->ok=True;

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();
  }


  public function index(Request $request)  {
    $ret = new RetApiController;
    try {
      $usuario= session('usuario');
      if (!$usuario) throw new Exception('Nenhum usuário loagdo');

      $dataset = i12TabelaIBPT::orderBy('created_at', 'DESC')->get();
      $dados = [];
      foreach ($dataset as $key => $file) {
        $dados[] = $file->export();
      }

      $ret->collection=$dataset;
      $ret->data=$dados;
      $ret->ok=True;

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();

    
  }

  public function logs(Request $request)  {
    $ret = new RetApiController;
    try {
      $usuario= session('usuario');
      if (!$usuario) throw new Exception('Nenhum usuário loagdo');

      $perpage = isset($request->perpage) ? $request->perpage : 25;

      $dataset = i12TabelaIBPTLog::orderBy('created_at', 'DESC')->paginate($perpage);
      $dados = [];
      foreach ($dataset as $key => $file) {
        $dados[] = $file->export();
      }

      $ret->collection=$dataset;
      $ret->data=$dados;
      $ret->ok=True;

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();

    
  }

  
  public function download(Request $request, $uf)  {
    $ret = new RetApiController;
    try {
      // $usuario= session('usuario');
      // if (!$usuario) throw new Exception('Nenhum usuário loagdo');

      $localUF = $uf;
      $localUF = mb_substr($localUF, 0, 2);
      if ($localUF ? $localUF === '' : true) throw new Exception('UF não informada');

      $dataset = i12TabelaIBPT::where('uf','=', $localUF)->first();
      if (!$dataset) throw new Exception('Arquivo não encontrado');
      
      $disk = Storage::disk('s3tabelaibpt');
      if (!$disk->exists($dataset->filename)) throw new Exception('Arquivo não encontrado em disco');

      $nomeexport = $localUF . '.csv';

      try{
        DB::beginTransaction();

        $log = new i12TabelaIBPTLog();
        $log->uf = $uf;
        $log->created_at = Carbon::now();
        $log->filename = $dataset->filename;
        $log->ip = \Request::getClientIp(true);
        $log->urlcompleta =$request->fullUrl();
        $log->sizebyte =$disk->size($dataset->filename);
        if ($request->has('cnpj')) $log->cnpj =$request->cnpj;
        if ($request->has('usuario')) $log->usuario =$request->usuario;
        if ($request->has('sistema')) $log->sistema =$request->sistema;
        if ($request->has('ambiente')) $log->ambiente = intval($request->ambiente) === 1 ? '1' : '2';
        $log->save();
        
        DB::commit();
      } catch (Exception $e) {
          DB::rollBack();
          throw new Exception($e->getMessage());
      }          

      // return $disk->download($dataset->filename, $nomeexport, [
      //   'Content-Type' => 'text/csv',
      //   'Content-Disposition' => 'inline; filename="'.$nomeexport.'"'
      // ]);

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();

    
  }


  public function upload(Request $request)  {
    $ret = new RetApiController;
    try {
      $usuario= session('usuario');
      if (!$usuario) throw new Exception('Nenhum usuário loagdo');
      
      $uf = isset($request->uf) ? $request->uf : '';
      if ($uf ? $uf === '' : true) throw new Exception('UF não informada');

      $formatoAntigo = false;
      if ($request->has('semestre') || $request->has('ano')) {
        $semestre = isset($request->semestre) ? intVal($request->semestre) : 0;
        if (($semestre < 1) || ($semestre>2)) throw new Exception('Semestre inválido');
        
        $ano = isset($request->ano) ? intVal($request->ano) : 0;
        if ($ano < (intVal(date("Y"))-2))  throw new Exception('Ano inválido');

        $formatoAntigo = true;
      }
      $arquivo = $request->hasfile('arquivo') ? $request->file('arquivo') : null;
      if (!$arquivo) throw new Exception('Nenhum arquivo informado');
      
      $md5 = md5_file($arquivo);
      $check=i12TabelaIBPT::where('md5file', '=', $md5)->first();
      if ($check) throw new Exception('Arquivo já foi cadastrado');

      $file = file_get_contents($arquivo);

      if ( $formatoAntigo) {
        $localdir = $this->dir;
        $name = Str::replaceArray('?', [$semestre, $ano, $uf], 'tabelaibpt_sem_?_ano_?_?.csv');
        Storage::disk('public')->put($localdir . '\\' . $name, $file );
      }

      //SUBINDO PARA AWS
      $disk = Storage::disk('s3tabelaibpt');
      $filenames3 = env('AWS_IBPT_REPO_PATH') . '/' . $uf . '.csv';
      if ($disk->exists($filenames3)) $disk->delete($filenames3);
      $disk->put($filenames3, $file, 'private');

      try{
        DB::beginTransaction();

        i12TabelaIBPT::where('uf', '=', $uf)->delete();

        $novo = new i12TabelaIBPT();
        $novo->uf = $uf;
        $novo->created_at = Carbon::now();
        $novo->idusercad = $usuario->codusuario;
        $novo->filename = $filenames3;
        $novo->sizebytes = $disk->size($filenames3); 
        $novo->md5file = $md5;
        $novo->save();
        
        DB::commit();
      } catch (Exception $e) {
          DB::rollBack();
          throw new Exception($e->getMessage());
      }          

      $ret->msg='Arquivo enviado com sucesso';
      $ret->ok=True;

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();

  }  

  public function delete(Request $request)  {
    $ret = new RetApiController;
    try {
      $usuario= session('usuario');
      if (!$usuario) throw new Exception('Nenhum usuário loagdo');

      $uf = isset($request->uf) ? $request->uf : '';
      if ($uf ? $uf === '' : true) throw new Exception('UF não informada');

      $check=i12TabelaIBPT::where('uf', '=', $uf)->first();
      if (!$check) throw new Exception('Nenhum arquivo encontrado');

      //SUBINDO PARA AWS
      $disk = Storage::disk('s3tabelaibpt');
      if ($disk->exists($check->filename)) $disk->delete($check->filename);

      try{
        DB::beginTransaction();

        $check->delete();
        
        DB::commit();
      } catch (Exception $e) {
          DB::rollBack();
          throw new Exception($e->getMessage());
      }          

      $ret->msg='Arquivo removido';
      $ret->ok=True;

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();
  }
  
}
