<?php

namespace App\Http\Controllers\adm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\RetornoApiController;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\models\I12BackupfileS3;
use App\models\Clientes;
use App\Jobs\adm\BackupDeleteJob;
use App\Jobs\adm\BackupDownloadJob;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Mail;
use App\Mail\adm\BackupDeleteMail;

use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
  //console
  public function clearCNPJBD($cnpj) 
  {
    try{
      DB::beginTransaction();
      $del = I12BackupfileS3::where('cnpj', $cnpj)->delete();
      DB::commit();
      return (object)['ok' => true];
    } catch (Exception $e) {
      DB::rollBack();
      return (object)['ok' => false, 'msg' => $e->getMessage() ];
    }
  }
  // console
  public function saveBD($filename, $size, $lastmodified, $cnpj, $bucketIndex) 
  {
    try{
      $md5 = md5($filename);
      $files3 = I12BackupfileS3::where('md5', $md5)->first();
      if (!$files3) {
        $files3 = new I12BackupfileS3;
        $files3->md5 = $md5;
        $files3->filename = $filename;
      }

      DB::beginTransaction();

      $files3->bucket = $bucketIndex;
      $files3->size = $size;
      $files3->lastmodified = $lastmodified;
      $files3->cnpj = $cnpj;
      $files3->lastcheck = Carbon::now();
      $upd = $files3->save();

      DB::commit();

      return (object)['ok' => true];
    } catch (Exception $e) {
      DB::rollBack();
      return (object)['ok' => false, 'msg' => $e->getMessage() ];
    }
  }

  //console
  public function processa($cnpj, $s3disk, $forceclear)  {
    try {
      // limpando tudo
      if (($cnpj<>'') && ($forceclear)) {
        $ret = $this->clearCNPJBD($cnpj);
        if (!$ret->ok) 
          throw new Exception($ret->msg);
      }

      $disk=Storage::disk($s3disk);

      // get all files aws
      $d = $disk->allFiles('backup-mysql' . ($cnpj == '' ? '' : '/' . $cnpj));
  
      foreach ($d as $key => $file) {
        $size = $disk->size($file);
        $lastmodified = $disk->lastModified($file);
        $ret = $this->saveBD($file, $size, $lastmodified, $cnpj,  ($s3disk=='s3backup' ? 0 : 1)); 
        if (!$ret->ok)
          throw new Exception($ret->msg);
      }
      return (object)['ok' => true];
    } catch (Exception $e) {
      return (object)['ok' => false, 'msg' => $e->getMessage() ];
    }
  }

  //view
  public function dash(Request $request)  {

    $qtdeclientes = Clientes::where('ativo', '=', 1)->whereIn('i12controlabkp', [1, 2])->count();
    $totalsize = I12BackupfileS3::sum('size');
    $totalqtde = I12BackupfileS3::count();

    $ativos = I12BackupfileS3::select(
        DB::Raw('sum(if(clientes.i12controlabkp=2, i12_backupfiles3.size, 0)) as pagosize'), 
        DB::Raw('count(distinct if(clientes.i12controlabkp=2, i12_backupfiles3.md5, null)) as pagoqtdearquivos'),
        DB::Raw('count(distinct if(clientes.i12controlabkp=2, clientes.codcliente, null)) as pagoqtdeclientes'),

        DB::Raw('sum(if(clientes.i12controlabkp!=2, i12_backupfiles3.size, 0)) as freesize'), 
        DB::Raw('count(distinct if(clientes.i12controlabkp!=2, i12_backupfiles3.md5, null)) as freeqtdearquivos'),
        DB::Raw('count(distinct if(clientes.i12controlabkp!=2, clientes.codcliente, null)) as freeqtdeclientes')
      )
      ->join('clientes', function($query){
        $query->on(DB::raw('if(clientes.pessoa="F", concat("000", clientes.cpf), clientes.cnpj)'), '=', 'i12_backupfiles3.cnpj');
      })
      ->where('clientes.ativo','=', 1)
      ->first();    

    $inativos = I12BackupfileS3::select(
                  DB::Raw('sum(i12_backupfiles3.size) as size'), 
                  DB::Raw('count(distinct i12_backupfiles3.md5) as qtdearquivos'),
                  DB::Raw('max(i12_backupfiles3.lastmodified) as maxlastmodified'),
                  DB::Raw('min(i12_backupfiles3.lastmodified) as minlastmodified'),
                  DB::Raw('count(distinct clientes.codcliente) as qtdeclientes'),
                  DB::Raw('count(distinct if(clientes.codcliente is null, i12_backupfiles3.cnpj, null)) as qtdesemclientes')
                )
          ->leftJoin('clientes', function($query){
            $query->on(DB::raw('if(clientes.pessoa="F", concat("000", clientes.cpf), clientes.cnpj)'), '=', 'i12_backupfiles3.cnpj');
          })
          ->whereRaw('(clientes.codcliente is null) or (clientes.ativo=0)')
          ->first();

    $dados = (object)[
      'nivelpago' => (object)[
        'total' => $ativos->pagosize,
        'qtdearquivos' => $ativos->pagoqtdearquivos,
        'qtdeclientes' => $ativos->pagoqtdeclientes,
        'mediaarquivoscliente' => ($ativos->pagoqtdeclientes>0 ? round($ativos->pagosize / $ativos->pagoqtdeclientes, 0) : 0),
        'mediageral' => ($totalsize>0 ? round(($ativos->pagosize / $totalsize)*100,2) : 0)
      ],
      'nivelgratuito' => (object)[
        'total' => $ativos->freesize,
        'qtdearquivos' => $ativos->freeqtdearquivos,
        'qtdeclientes' => $ativos->freeqtdeclientes,
        'mediaarquivoscliente' => ($ativos->freeqtdeclientes>0 ? round($ativos->freesize / $ativos->freeqtdeclientes, 0) : 0),
        'mediageral' => ($totalsize>0 ? round(($ativos->freesize / $totalsize)*100,2) : 0)
      ],
      'inativos' => (object)[
        'total' => $inativos->size,
        'qtdearquivos' => $inativos->qtdearquivos,
        'maxlastmodified' => $inativos->maxlastmodified ? Carbon::parse($inativos->maxlastmodified) : null,
        'minlastmodified' => $inativos->minlastmodified ? Carbon::parse($inativos->minlastmodified) : null,
        'qtdeclientes' => $inativos->qtdeclientes,
        'qtdesemclientes' => $inativos->qtdesemclientes,
        'mediageral' => ($totalsize>0 ? round(($inativos->size / $totalsize)*100,2) : 0)
      ],
      'total' => (object)[
        'qtdeclientes' => $qtdeclientes,
        'qtde' => $totalqtde,
        'size' => $totalsize,
        'mediaarquivos' => ($totalqtde> 0 ? round($totalsize / $totalqtde, 0) : 0),
        'mediacliente' => ($qtdeclientes>0 ? round($totalsize / $qtdeclientes, 0) : 0),

      ]
      ];
    return view('adm.backup.dash', compact('dados'));
  }  
  
  //view
  public function list(Request $request)  {

    $inativo = isset($request->inativos) ? $request->inativos == "on" : false;
    $q = isset($request->q) ? $request->q : '';
    $nivel = isset($request->nivel) ? $request->nivel : '';
    
    $dados = I12BackupfileS3::select('i12_backupfiles3.*', DB::Raw('sum(i12_backupfiles3.size) as totalsize'), DB::Raw('max(i12_backupfiles3.lastmodified) as ultimolastmodified'),
                  DB::Raw('count(distinct i12_backupfiles3.md5) as qtdearquivos'))
              ->leftJoin('clientes', function($query){
                $query->on(DB::raw('if(clientes.pessoa="F", concat("000", clientes.cpf), clientes.cnpj)'), '=', 'i12_backupfiles3.cnpj');
              })
              ->whereRaw('if(?=1, (clientes.codcliente is null) or (clientes.ativo=0), (clientes.ativo=1))', [$inativo ? 1 : 0])
              ->when($q, function ($query, $role) {
                return $query->where('clientes.nome', 'like', $role . '%')
                          ->orWhere('clientes.fantasia', 'like', $role . '%')
                          ->orWhere('clientes.cidade', 'like', $role . '%')
                          ->orWhere('i12_backupfiles3.filename', 'like', $role . '%');
              })
              ->when($nivel, function ($query, $role) {
                return $query->where('clientes.i12controlabkp', '=', $role);
              })
              ->groupBy('i12_backupfiles3.cnpj')
              ->orderBy('size', 'desc')
              ->paginate(10);

    return view('adm.backup.list', compact('dados'));
  } 

  //view
  public function listfile(Request $request, $diretorio)  {

    $pagesize = isset($request->page_size) ? $request->page_size : 15;
    $mes = isset($request->mes) ? intval($request->mes) : 0;
    $ano = isset($request->ano) ? intval($request->ano) : 0;
    $cliente = Clientes::whereRaw('if(pessoa="F", concat("000", cpf), cnpj)=?', [$diretorio])
                ->first();
    
    $dados = I12BackupfileS3::where('i12_backupfiles3.cnpj', '=', $diretorio)
              ->when(isset($request->mes) && (($mes>0) && ($mes<=12)), function ($query) use ($mes) {
                return $query->whereRaw('MONTH(i12_backupfiles3.lastmodified)=?', [$mes]);
              })  
              ->when(isset($request->ano) && (($ano>2000) && ($ano<=3000)), function ($query) use ($ano) {
                return $query->whereRaw('YEAR(i12_backupfiles3.lastmodified)=?', [$ano]);
              })    
              ->orderBy('lastmodified', 'desc')
              ->paginate($pagesize);

    $resumo = I12BackupfileS3::select(
              DB::raw('min(i12_backupfiles3.lastmodified) as primeirolastmodified'),
              DB::raw('sum(i12_backupfiles3.size) as totalsize'),
              DB::raw('max(i12_backupfiles3.lastmodified) as ultimolastmodified'),
              DB::raw('count(distinct i12_backupfiles3.md5) as qtdearquivos'),
            )
            ->where('i12_backupfiles3.cnpj', '=', $diretorio)
            ->orderBy('lastmodified', 'desc')
            ->groupBy(DB::raw('year(i12_backupfiles3.lastmodified), month(i12_backupfiles3.lastmodified)'),)
            ->get();              

    return view('adm.backup.listfiles', compact('dados', 'resumo', 'cliente', 'diretorio' ));
  } 

  //view
  public function reportalert(Request $request)  {

    $q = isset($request->q) ? $request->q : '';

    $dados = Clientes::select('clientes.codcliente', 'clientes.nome', 'clientes.fantasia', 'clientes.cidade',
              'clientes.i12controlabkp', 'clientes.pessoa', 'clientes.cpf', 'clientes.cnpj',
              DB::RAW('max(i12_backupfiles3.lastmodified) as lastmodified'), 
              DB::RAW('ifnull(max(i12_backupfiles3.size),0) as totalsize'),
              DB::RAW('count(distinct i12_backupfiles3.id) as qtdearquivos'),
              DB::RAW('TIMESTAMPDIFF(HOUR, max(i12_backupfiles3.lastmodified), NOW()) as horasatras')

              )
              ->leftJoin('i12_backupfiles3', DB::RAW('if(clientes.pessoa="F",concat("000",clientes.cpf),clientes.cnpj)'), '=', 'i12_backupfiles3.cnpj' )
              ->where('clientes.ativo', '=', 1)
              ->whereIn('clientes.i12controlabkp', [1,2])
              ->whereRaw('coalesce(if(clientes.pessoa="F",clientes.cpf,clientes.cnpj),"")<>""')
              ->when($q, function ($query, $role) {
                return $query->where(function ($query) use ($role) {
                  $query->where('clientes.nome', 'like', $role . '%')
                      ->orWhere('clientes.fantasia', 'like', $role . '%')
                      ->orWhere('clientes.cidade', 'like', $role . '%');
                });
              })
              ->groupBy('clientes.codcliente')
              ->havingRaw('(horasatras > ?) or (horasatras is null)', [72])
              ->orderBy(DB::RAW('if(lastmodified, 1, 0)'))
              ->orderBy('lastmodified')
              ->get();

    return view('adm.backup.alert', compact('dados'));
  } 

  //sync
  public function sync(Request $request, $diretorio)  {

    try {
      if ($diretorio<>'') {
        $ret = $this->clearCNPJBD($diretorio);
        if (!$ret->ok) {
          throw new Exception($ret->msg);
        }
      }

      $s3atual='s3backup_1';
      goto processa;
      
      processa: {
        $disk=Storage::disk($s3atual);
        $d = $disk->allFiles('backup-mysql' . ($diretorio == '' ? '' : '/' . $diretorio));

        foreach ($d as $key => $file) {
          try {
            $dtbackupStr = (substr(basename($file), 0, 19));
            $lastmodified = Carbon::createFromFormat('Y-m-d-H-i-s', $dtbackupStr, 'America/Sao_Paulo');
          } catch (\Throwable $th) {
            $lastmodified = null;
          }
          
          $size = $disk->size($file);
          if (!$lastmodified) $lastmodified = $disk->lastModified($file);
          $ret = $this->saveBD($file, $size, $lastmodified, $diretorio, ($s3atual=='s3backup' ? 0 : 1)); 
          if (!$ret->ok) {
            throw new Exception($ret->msg);
          }
        }

        if ($s3atual == 's3backup_1') {
          $s3atual = 's3backup';
          goto processa;
        };        
      }

      return redirect()->back()->with('success', 'Fim do processamento diretório: ' . '/' . $diretorio);

    } catch (Exception $e) {
        return redirect()->back()->withErrors($e->getMessage());
        exit();
    }
  }   
 
  //view
  public function deletefiles(Request $request)  {
    try{
      $ids = isset($request->selecionado) ? $request->selecionado : null;
      if (!$ids) throw new Exception('Nenhum registro selecionado');
      $dados = I12BackupfileS3::whereIn('md5', $ids)->orderBy('lastmodified')->get();  

      $usuario = Auth::guard('adm')->user();
      BackupDeleteJob::dispatch($dados, $usuario);

      return redirect()->back()->with('success', count($dados) . ' arquivo(s) incluidos na fila de exclusão');
    } catch (Exception $e) {
        return redirect()->back()->withErrors($e->getMessage());
    }
  } 
    

  //view
  public function download(Request $request, $md5)  {

    try {
      
      $ret = (object) [
        'ok' => false,
        'msg' => ''
      ];
      $s3 = I12BackupfileS3::where('md5', $md5)->first();  
      if (!$s3) throw new Exception('Arquivo não foi encontrado');
      // if (!$s3) throw new Exception('Arquivo não foi encontrado');
      $url = $s3->urldownload(Carbon::now()->addSeconds(30));

      $usuario = Auth::guard('adm')->user();
      BackupDownloadJob::dispatch($s3, $usuario);

      $ret->ok = true;
    } catch (Exception $e) {
        $ret->msg = $e->getMessage();
    }
    return view('adm.backup.download', compact('s3', 'ret', 'url'));

  } 

}
