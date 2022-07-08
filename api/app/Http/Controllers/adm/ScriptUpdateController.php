<?php

namespace App\Http\Controllers\adm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\models\i12ScriptUpdate;
use App\models\Clientes;

class ScriptUpdateController extends Controller
{

  protected $dir = '';
  protected $storagedisk = 's3';

  public function __construct () {
    $this->dir = env('STORAGE_DIR_SCRIPT','');
    $this->storagedisk = ENV('STORAGE_MODO', 's3');
  }


  public function index(Request $request)  {
    $contadores = i12ScriptUpdate::select(\DB::raw('min(nordem) as menor, max(nordem) as maior, count(distinct id) as qtde'))->first();
    $primeiro = $contadores->menor;
    $ultimo = $contadores->maior;
    $qtdetotal = $contadores->qtde;
    $deveriater = ($ultimo - $primeiro + 1);
    $diferenca = $qtdetotal - $deveriater;

    $arquivos = i12ScriptUpdate::orderBy('nordem', 'desc')->paginate(15);
    $lista = [];

    $nfirst = -1;
    $nlast = -1;
    if ($arquivos) {
      $nfirst = $arquivos[count($arquivos)-1]->nordem;
      $nlast = $arquivos[0]->nordem;
    }

    for ($i=$nlast; $i >= $nfirst; $i--) { 
      $status =  $arquivos->search(function ($item, $key) use ($i) {
        return $item->nordem == $i;
      });
      $arquivo = null;
      if ($status !== false) $arquivo = $arquivos[$status];
      $lista[] = [
        'status' => $status,
        'arquivo' =>  $arquivo,
        'numero'  => $i
      ];
    }
    return view('adm.scriptupdate.index', compact('arquivos', 'lista', 'primeiro', 'ultimo', 'qtdetotal', 'diferenca'));
  }

  public function add(Request $request)  {
    return view('adm.scriptupdate.add');
  }

  public function upload(Request $request)  {
    $qtde = 0 ;
    $updates = [];
    $usuario = Auth::guard('adm')->user();
    $localdir = $this->dir;
    $status = isset($request->status) ? intVal($request->status) : 1;
    try {
      if($request->hasfile('filenames')) {
        foreach($request->file('filenames') as $file) {
          $ext = $file->getClientOriginalExtension();
          if (mb_strtolower($ext)=='sql') {
            $name = $file->getClientOriginalName();
            $md5file = md5_file($file);
            $filenamedestino = $localdir . '/' . $md5file . '.' . $ext;
            Storage::disk($this->storagedisk)->put($filenamedestino, file_get_contents($file), 'public' );
            $qtde = $qtde + 1;
            $arquivo = i12ScriptUpdate::where('md5file', '=', $md5file)->first();
            if (!$arquivo) {
              $arquivo = new i12ScriptUpdate();
              $arquivo->idusercad = $usuario->codusuario;
            } 
            $arquivo->md5file = $md5file;
            $arquivo->iduseralt = $usuario->codusuario;
            $n = intVal(str_replace('.' . mb_strtolower($ext), "", mb_strtolower($name)));
            $arquivo->nordem = $n;
            $arquivo->filename =  $md5file . '.' . $ext;
            $arquivo->status = $status;
            $arquivo->directory = $localdir;
            $arquivo->urldownload = Storage::disk($this->storagedisk)->url($filenamedestino);
            $updates[] = $arquivo;
          }
        }
      }
      if (count($updates)>0) {
        try{
          DB::beginTransaction();
          foreach ($updates as $item) {
            $item->save();
          }
          DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        return redirect()->route('adm.scripts.listagem')->with('success', $qtde . ' arquivo(s) atualizados(s)!');
      }else{
        throw new Exception('Nenhum arquivo informado');
      }
    } catch (Exception $e) {
      return back()->withErrors($e->getMessage());
    }
  }  


  public function delete(Request $request, $md5filename)  {
    try {
      $arquivo = i12ScriptUpdate::where('md5file', '=', $md5filename)->first();
      if (!$arquivo) throw new Exception('Nenhum arquivo encontrado');


      $filenamefull = $arquivo->directory . "/" . $arquivo->filename;
      $disk = Storage::disk($this->storagedisk);
      if ($disk->exists($filenamefull)) {
        $disk->delete($filenamefull);
      }

      try{
        DB::beginTransaction();
        $arquivo->delete();
        DB::commit();
      } catch (Exception $e) {
          DB::rollBack();
          throw new Exception('Arquivo excluído do storage porém ocorreu erro ao salvar no banco de dados. Erro: ' . $e->getMessage());
      }

      return back()->with('success', 'Arquivo(s) excluido');

    } catch (Exception $e) {
      return back()->withErrors($e->getMessage());
    }
  }  
  
  
  public function deletemulti(Request $request)  {
    try {
      $selecionados = isset($request->selecionado) ? $request->selecionado : null;
      if (!$selecionados) throw new Exception('Nenhum item selecionado');

      $arquivos = i12ScriptUpdate::whereIn('md5file',$selecionados)->get();
      if (!$arquivos) throw new Exception('Nenhum arquivo encontrado');

      foreach ($arquivos as $arquivo) {
        $filenamefull = $arquivo->directory . "/" . $arquivo->filename;
        $disk = Storage::disk($this->storagedisk);
        if ($disk->exists($filenamefull)) {
          $disk->delete($filenamefull);
        }
      }

      try{
        DB::beginTransaction();
        foreach ($arquivos as $arquivo) {
          $arquivo->delete();
        }
        DB::commit();
      } catch (Exception $e) {
          DB::rollBack();
          throw new Exception('Arquivo excluído do storage porém ocorreu erro ao salvar no banco de dados. Erro: ' . $e->getMessage());
      }

      return back()->with('success', $arquivos->count() . ' arquivo(s) excluido');

    } catch (Exception $e) {
      return back()->withErrors($e->getMessage());
    }
  } 
}
