<?php

namespace App\Http\Controllers\api\v1\admin;

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

use App\Http\Controllers\RetApiController;
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
    $ret = new RetApiController;
    try {
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
          'maxlastmodified' => $inativos->maxlastmodified ? Carbon::parse($inativos->maxlastmodified)->format('Y-m-d H:i:s') : null,
          'minlastmodified' => $inativos->minlastmodified ? Carbon::parse($inativos->minlastmodified) ->format('Y-m-d H:i:s') : null,
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

      $ret->data=$dados;
      $ret->ok=True;

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();
  }  
  
  public function list(Request $request)  {
    $ret = new RetApiController;
    try {
      $find = isset($request->find) ? strval($request->find) : '';
      $nivel = isset($request->nivel) ? strval($request->nivel) : '';
      if ($nivel ? $nivel !== '' : false) {
        $nivel = explode(',', $nivel);
        if (count($nivel) === 0) $nivel = null;
      } else {
        $nivel = null;
      }
      
      $perpage = isset($request->perpage) ? $request->perpage : 20;
      $status = isset($request->status) ? strVal($request->status) : '1';

      $dataset = I12BackupfileS3::select('i12_backupfiles3.*',
              DB::Raw('sum(i12_backupfiles3.size) as totalsize'), 
              DB::Raw('max(i12_backupfiles3.lastmodified) as ultimolastmodified'),
              DB::Raw('count(distinct i12_backupfiles3.md5) as qtdearquivos'))
          ->with('cliente')
          ->leftJoin('clientes', function($query){
            $query->on(DB::raw('if(clientes.pessoa="F", concat("000", clientes.cpf), clientes.cnpj)'), '=', 'i12_backupfiles3.cnpj');
          })
          //status 
          //="[ { value: '2', label: 'Todos' }, { value: '1', label: 'Ativos' }, { value: '0', label: 'Inativo' } ]"
          ->when(($status === '1' || $status === '0'), function ($query) use ($status) {
            if ($status === '1') {
              return $query->where('clientes.ativo', '=', 1);
            } else {
              return $query->whereRaw('(clientes.codcliente is null) or (clientes.ativo!=1)');
            }
          })
          ->when(($request->has('find') && ($find ? $find !== '' : false)), function ($query) use ($find) {
            return $query->where('clientes.nome', 'like', '%' . $find . '%')
                      ->orWhere('clientes.fantasia', 'like', '%' . $find . '%')
                      ->orWhere('clientes.cidade', 'like', '%' . $find . '%')
                      ->orWhere('i12_backupfiles3.filename', 'like', '%' . $find . '%');
          })
          ->when($nivel, function ($query) use ($nivel) {
            return $query->whereIn('clientes.i12controlabkp', $nivel);
          })
          ->groupBy('i12_backupfiles3.cnpj')
          ->orderBy('totalsize', 'desc')
          ->paginate($perpage);

      $dados = [];
      foreach ($dataset as $row) {
        $linha = [
          'id' => $row->id,
          'md5' => $row->md5,
          'filename' => $row->filename,
          'size' => $row->size,
          'lastmodified' => $row->lastmodified ? $row->lastmodified->format('Y-m-d H:i:s') : null,
          'lastcheck' => $row->lastcheck ? $row->lastcheck->format('Y-m-d H:i:s') : null,
          'ultimolastmodified' => $row->ultimolastmodified ? $row->ultimolastmodified->format('Y-m-d H:i:s') : null,
          'totalsize' => $row->totalsize,
          'qtdearquivos' => $row->qtdearquivos,
          'bucket' => $row->bucket,
        ];
        if ($row->cliente ? $row->cliente->codcliente > 0 : false) {
          $cliente = [
            'id' => $row->cliente->codcliente,
            'doc' => $row->cliente->pessoa === 'J' ? $row->cliente->cnpj : $row->cliente->cpf,
            'nome' => $row->cliente->nome,
            'controlabkp' => $row->cliente->i12controlabkp,
            'fantasia' => $row->cliente->fantasia,
            'cidade' => $row->cliente->cidade,
            'uf' => $row->cliente->uf,
            'ativo' => $row->cliente->ativo,
          ];
        } else {
          $cliente = [
            'doc' => $row->cnpj,
            'nome' => 'Sem cadastro',
            'controlabkp' => 9,
            'ativo' => 0
          ];
        }
        $linha['cliente'] = $cliente;
        $dados[] = $linha;
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
