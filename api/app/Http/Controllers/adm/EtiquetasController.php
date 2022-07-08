<?php

namespace App\Http\Controllers\adm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\models\i12Etiquetas;
use App\models\Clientes;

class EtiquetasController extends Controller
{
  protected $dir = '';
  protected $storagedisk = 's3';

  public function __construct () {
    $this->dir = env('STORAGE_DIR_ETIQUETA','');
    $this->storagedisk = ENV('STORAGE_MODO', 's3');
  }

  public function index(Request $request)  {
    $diretorioatual = isset($request->diretorio) ? $request->diretorio : '';
    $dir = $this->dir;
    if ($diretorioatual != '') $dir = $dir . $diretorioatual;

    $grupos = i12Etiquetas::select('directory', \DB::raw('count(distinct id) as qtde'))
                    ->where('ativo', '=', '1')
                    // ->where('directory', '=',   $dir) //str_replace("\\", "\\\\", $dir)
                    ->groupBy('directory')
                    ->orderBy('directory')
                    ->get();
    $diretorios = [];
    foreach ($grupos as $grupo) {
      $l = str_replace($this->dir, "", $grupo["directory"]);
      if ($l != '') {
          $cliente = Clientes::whereRaw('if(pessoa="F", concat("000", cpf), cnpj)=?', [str_replace("\\", "", $l)])->first();
          $dado = [
            'dir' => $l,
            'qtde' => $grupo['qtde'],
            'cliente' => $cliente
          ];
        
          $diretorios[] = $dado;

      }
    }
    $etiquetas = i12Etiquetas::where('ativo', '=', '1')
                                ->where('directory', '=', $dir)
                                ->orderBy('nome')
                                ->paginate(15);
    return view('adm.etiquetas', compact('etiquetas', 'diretorios', 'diretorioatual'));
  }

  public function add(Request $request)  {
    $clientes = Clientes::where('ativo', '=', '1')->orderBy('nome')->get();
    return view('adm.etiquetasadd', compact('clientes'));
  }



  public function upload(Request $request)  {
    $qtde = 0 ;
    $updates = [];
    $usuario = Auth::guard('adm')->user();
    $localdir = $this->dir;
    $diretorio = isset($request->diretorio) ? $request->diretorio : '';
    if ($diretorio != '') $localdir = $localdir . '/' . $diretorio;
    try {
      if($request->hasfile('filenames')) {
        foreach($request->file('filenames') as $file) {
          $ext = $file->getClientOriginalExtension();
          if (mb_strtolower($ext)=='eti') {
            $name = $file->getClientOriginalName();
            $md5file = md5_file($file);
            $filenamedestino = $localdir . '/' . $md5file . '.' . $ext;
            Storage::disk($this->storagedisk)->put($filenamedestino, file_get_contents($file), 'public');
            $qtde = $qtde + 1;
            $etiqueta = i12Etiquetas::where('md5file', '=', $md5file)->first();
            if (!$etiqueta) {
              $etiqueta = new i12Etiquetas();
              $etiqueta->md5file = $md5file;
              $etiqueta->ativo = 1;
              $etiqueta->idusercad = $usuario->codusuario;
            } 
            $etiqueta->nome = $name;
            $etiqueta->iduseralt = $usuario->codusuario;
            $etiqueta->ext = $ext;
            $etiqueta->filename = $md5file . '.' . $ext;
            $etiqueta->directory = $localdir;
            $etiqueta->urldownload = Storage::disk($this->storagedisk)->url( $filenamedestino);
            $updates[] = $etiqueta;
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
        return redirect()->route('adm.etiquetas')->with('success', $qtde . ' arquivo(s) atualizados(s)!');
      }else{
        throw new Exception('Nenhum arquivo informado');
      }
    } catch (Exception $e) {
      return back()->withErrors($e->getMessage());
    }
  }  
  

  public function delete(Request $request, $md5filename)  {
    try {
      $deletado = false;
      $localdir = $this->dir;

      $etiqueta = i12Etiquetas::where('md5file', '=', $md5filename)->first();
      if (!$etiqueta) throw new Exception('Nenhuma etiqueta encontrada');


      $filenamefull = $etiqueta->directory . "/" . $etiqueta->filename;
      $disk = Storage::disk($this->storagedisk);
      if ($disk->exists($filenamefull)) {
        $disk->delete($filenamefull);
      }

      try{
        DB::beginTransaction();
        $etiqueta->delete();
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

      $etiquetas = i12Etiquetas::whereIn('md5file',$selecionados)->get();
      if (!$etiquetas) throw new Exception('Nenhuma etiqueta encontrada');

      foreach ($etiquetas as $etiqueta) {
        $filenamefull = $etiqueta->directory . "/" . $etiqueta->filename;
        $disk = Storage::disk($this->storagedisk);
        if ($disk->exists($filenamefull)) {
          $disk->delete($filenamefull);
        }
      }

      try{
        DB::beginTransaction();
        foreach ($etiquetas as $etiqueta) {
          $etiqueta->delete();
        }
        DB::commit();
      } catch (Exception $e) {
          DB::rollBack();
          throw new Exception('Arquivo excluído do storage porém ocorreu erro ao salvar no banco de dados. Erro: ' . $e->getMessage());
      }

      return back()->with('success', $etiquetas->count() . ' arquivo(s) excluido');

    } catch (Exception $e) {
      return back()->withErrors($e->getMessage());
    }
  } 
  
}
