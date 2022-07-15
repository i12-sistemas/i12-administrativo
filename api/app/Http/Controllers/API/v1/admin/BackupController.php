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
use App\models\I12BackupfileS3Log;
use App\Jobs\adm\BackupDeleteJob;
use App\Jobs\adm\BackupDownloadJob;
use Illuminate\Support\Str;

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


            
      $clienteEmAlerta = Clientes::select('clientes.*',
            DB::Raw('sum(i12_backupfiles3.size) as totalsize'), 
            DB::Raw('max(i12_backupfiles3.lastmodified) as ultimolastmodified'),
            DB::Raw('count(distinct i12_backupfiles3.md5) as qtdearquivos'),
            DB::RAW('TIMESTAMPDIFF(HOUR, max(i12_backupfiles3.lastmodified), NOW()) as horasatras')
          )
        ->leftJoin('i12_backupfiles3', DB::RAW('if(clientes.pessoa="F",concat("000",clientes.cpf),clientes.cnpj)'), '=', 'i12_backupfiles3.cnpj' )
        ->where('clientes.ativo', '=', 1)
        ->havingRaw('(horasatras > ?) or (horasatras is null)', [72])
        ->whereIn('clientes.i12controlabkp', [1,2])
        ->groupBy('clientes.codcliente')
        ->get();


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
          'emalertaclientes' => $clienteEmAlerta ? count($clienteEmAlerta) : 0,
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
      $alerta = false;
      if ($status === '20') {
        $status = '1';
        $alerta = true;
        $nivel = [1,2];
      }

      $dataset = Clientes::select('clientes.*',
              DB::Raw('sum(i12_backupfiles3.size) as totalsize'), 
              DB::Raw('max(i12_backupfiles3.lastmodified) as ultimolastmodified'),
              DB::Raw('count(distinct i12_backupfiles3.md5) as qtdearquivos'),
              DB::RAW('TIMESTAMPDIFF(HOUR, max(i12_backupfiles3.lastmodified), NOW()) as horasatras')
            )
          // ->with('cliente')
          ->leftJoin('i12_backupfiles3', DB::RAW('if(clientes.pessoa="F",concat("000",clientes.cpf),clientes.cnpj)'), '=', 'i12_backupfiles3.cnpj' )
          // ->leftJoin('clientes', function($query){
          //   $query->on(DB::raw('if(clientes.pessoa="F", concat("000", clientes.cpf), clientes.cnpj)'), '=', 'i12_backupfiles3.cnpj');
          // })
          //status 
          //="[ { value: '2', label: 'Todos' }, { value: '1', label: 'Ativos' }, { value: '0', label: 'Inativo' } ]"
          ->when(($status === '1' || $status === '0'), function ($query) use ($status) {
            if ($status === '1') {
              return $query->where('clientes.ativo', '=', 1);
            } else {
              return $query->whereRaw('(clientes.codcliente is null) or (clientes.ativo!=1)');
            }
          })
          ->when($alerta, function ($query) {
            return $query->havingRaw('(horasatras > ?) or (horasatras is null)', [72]);
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
          ->groupBy('clientes.codcliente')
          ->orderBy('totalsize', 'desc')
          ->paginate($perpage);

      $dados = [];
      foreach ($dataset as $row) {
        $linha = [
          'cliente' => [
            'id' => $row->codcliente,
            'doc' => $row->pessoa === 'J' ? $row->cnpj : $row->cpf,
            'nome' => $row->nome,
            'controlabkp' => $row->i12controlabkp,
            'fantasia' => $row->fantasia,
            'cidade' => $row->cidade,
            'uf' => $row->uf,
            'ativo' => $row->ativo,
          ],
          'ultimolastmodified' => $row->ultimolastmodified ? $row->ultimolastmodified->format('Y-m-d H:i:s') : null,
          'ultimolastmodifiedhour' => $row->horasatras,
          'totalsize' => $row->totalsize,
          'qtdearquivos' => $row->qtdearquivos,
        ];
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
    $ret = new RetApiController;
    try {
      $perpage = isset($request->perpage) ? $request->perpage : 20;
      try {
        $mesano = isset($request->mesano) ? strval($request->mesano) : null;
        if ($mesano ? $mesano !== '' : false) {
          $mesano = Carbon::createFromFormat('Y-m-d', $mesano . '-01', 'America/Sao_Paulo');
        } else {
          $mesano = null;
        }
      } catch (\Throwable $th) {
        $mesano = null;
      }      
      
      $dataset = I12BackupfileS3::select('i12_backupfiles3.*', DB::raw('ifnull(count(i12_backupfiles3_log.id),0) as downloadcount'))
          ->where('i12_backupfiles3.cnpj', '=', $diretorio)
          ->leftJoin('i12_backupfiles3_log', 'i12_backupfiles3.md5', '=', 'i12_backupfiles3_log.md5file')
          ->when($request->has('mesano') && ($mesano ? $mesano !== '' : false), function ($query) use ($mesano) {
            return $query->whereRaw('LAST_DAY(i12_backupfiles3.lastmodified)=LAST_DAY(?)', [$mesano]);
          })  
          ->orderBy('i12_backupfiles3.lastmodified', 'desc')
          ->groupBy('i12_backupfiles3.md5')
          ->paginate($perpage);

      $dados = [];
      foreach ($dataset as $row) {
        $linha = [
          'md5' => $row->md5,
          'filename' => $row->filename,
          'size' => $row->size,
          'lastmodified' => $row->lastmodified ? $row->lastmodified->format('Y-m-d H:i:s') : null,
          'lastcheck' => $row->lastcheck ? $row->lastcheck->format('Y-m-d H:i:s') : null,
          'size' => $row->size,
          'bucket' => $row->bucket,
          'downloadcount' => $row->downloadcount,
        ];
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

  
  public function filespormes(Request $request, $diretorio)  {
    $ret = new RetApiController;
    try {
      
      $dataset = I12BackupfileS3::select(
                DB::raw('min(i12_backupfiles3.lastmodified) as primeirolastmodified'),
                DB::raw('sum(i12_backupfiles3.size) as totalsize'),
                DB::raw('max(i12_backupfiles3.lastmodified) as ultimolastmodified'),
                DB::raw('count(distinct i12_backupfiles3.md5) as qtdearquivos'),
              )
              ->where('i12_backupfiles3.cnpj', '=', $diretorio)
              ->orderBy('lastmodified', 'desc')
              ->groupBy(DB::raw('year(i12_backupfiles3.lastmodified), month(i12_backupfiles3.lastmodified)'),)
              ->get();     

      $dados = [];
      foreach ($dataset as $row) {
        $linha = [
          'totalsize' => $row->totalsize,
          'qtdearquivos' => $row->qtdearquivos,
          'primeirolastmodified' => $row->primeirolastmodified ? $row->primeirolastmodified->format('Y-m-d H:i:s') : null,
          'ultimolastmodified' => $row->ultimolastmodified ? $row->ultimolastmodified->format('Y-m-d H:i:s') : null,
        ];
        $dados[] = $linha;
      }                

      $ret->data=$dados;
      $ret->ok=True;
  
    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();
  } 

  public function deletefiles(Request $request)  {
    $ret = new RetApiController;
    try{
      $usuario = session('usuario');
      if (!$usuario) throw new Exception('Nenhum usuário logado');

      $ids = isset($request->ids) ? $request->ids : null;
      if (!$ids) throw new Exception('Nenhum registro selecionado');
      if ($ids ? $ids === '' : false) throw new Exception('Nenhum registro selecionado');
      $ids = explode(',', $ids);
      if (count($ids)<=0) throw new Exception('Nenhum registro selecionado');

      $dados = I12BackupfileS3::whereIn('md5', $ids)->orderBy('lastmodified')->get();  

      BackupDeleteJob::dispatch($dados, $usuario);

      $ret->msg=count($dados) . ' arquivo(s) incluidos na fila de exclusão';
      $ret->ok=True;

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();    
  } 
    


  // public function reportalert(Request $request)  {
  //   $ret = new RetApiController;
  //   try {
  //     $perpage = isset($request->perpage) ? $request->perpage : 20;
  //     $q = isset($request->q) ? $request->q : '';

      
  //     $dataset = Clientes::select('clientes.*', 
  //                 DB::RAW('max(i12_backupfiles3.lastmodified) as ultimolastmodified'), 
  //                 DB::RAW('ifnull(max(i12_backupfiles3.size),0) as totalsize'),
  //                 DB::RAW('count(distinct i12_backupfiles3.id) as qtdearquivos'),
  //                 DB::RAW('TIMESTAMPDIFF(HOUR, max(i12_backupfiles3.lastmodified), NOW()) as horasatras')
  //               )
  //               ->leftJoin('i12_backupfiles3', DB::RAW('if(clientes.pessoa="F",concat("000",clientes.cpf),clientes.cnpj)'), '=', 'i12_backupfiles3.cnpj' )
  //               ->where('clientes.ativo', '=', 1)
  //               ->whereIn('clientes.i12controlabkp', [1,2])
  //               ->whereRaw('coalesce(if(clientes.pessoa="F",clientes.cpf,clientes.cnpj),"")<>""')
  //               ->when($q, function ($query, $role) {
  //                 return $query->where(function ($query) use ($role) {
  //                   $query->where('clientes.nome', 'like', $role . '%')
  //                       ->orWhere('clientes.fantasia', 'like', $role . '%')
  //                       ->orWhere('clientes.cidade', 'like', $role . '%');
  //                 });
  //               })
  //               ->groupBy('clientes.codcliente')
  //               ->havingRaw('(horasatras > ?) or (horasatras is null)', [72])
  //               ->orderBy(DB::RAW('if(lastmodified, 1, 0)'))
  //               ->orderBy('lastmodified')
  //               ->paginate($perpage);

  //     $dados = [];
  //     foreach ($dataset as $row) {
  //       $linha = [
  //         'cliente' => [
  //           'id' => $row->codcliente,
  //           'doc' => $row->pessoa === 'J' ? $row->cnpj : $row->cpf,
  //           'nome' => $row->nome,
  //           'controlabkp' => $row->i12controlabkp,
  //           'fantasia' => $row->fantasia,
  //           'cidade' => $row->cidade,
  //           'uf' => $row->uf,
  //           'ativo' => $row->ativo,
  //         ],
  //         'ultimolastmodified' => $row->ultimolastmodified ? $row->ultimolastmodified->format('Y-m-d H:i:s') : null,
  //         'ultimolastmodifiedhour' => $row->horasatras,
  //         // 'ultimolastmodifiedhour' => $row->ultimolastmodified ? Carbon::now()->diffInHours($row->ultimolastmodified) : null
  //         'totalsize' => $row->totalsize,
  //         'qtdearquivos' => $row->qtdearquivos,
  //       ];
  //       $dados[] = $linha;
  //     }                
        

  //     $ret->collection=$dataset;
  //     $ret->data=$dados;
  //     $ret->ok=True;

  //   } catch (\Throwable $th) {
  //     $ret->msg = $th->getMessage();
  //     return $ret->toJson();
  //   }
  //   return $ret->toJson();
  // } 

  public function sync(Request $request, $diretorio)  {
    $ret = new RetApiController;
    try {
      if ($diretorio<>'') {
        $retClear = $this->clearCNPJBD($diretorio);
        if (!$retClear->ok) {
          throw new Exception($retClear->msg);
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
          $retSave = $this->saveBD($file, $size, $lastmodified, $diretorio, ($s3atual=='s3backup' ? 0 : 1)); 
          if (!$retSave->ok) {
            throw new Exception($retSave->msg);
          }
        }

        if ($s3atual == 's3backup_1') {
          $s3atual = 's3backup';
          goto processa;
        };        
      }

      
      $ret->msg='Fim do processamento diretório: ' . '/' . $diretorio;
      $ret->ok=True;

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();    
  }   
 


  public function download(Request $request, $md5)  {
    $ret = new RetApiController;
    try{
      $usuario = session('usuario');
      if (!$usuario) throw new Exception('Nenhum usuário logado');
      
      $motivo = isset($request->motivo) ? strval($request->motivo) : null;
      if ($motivo ? Str::length($motivo) <= 2 : false) throw new Exception('Nenhum registro selecionado');

      $s3 = I12BackupfileS3::where('md5', $md5)->first();  
      if (!$s3) throw new Exception('Arquivo não foi encontrado');

      

      try{
        DB::beginTransaction();
        
        $log = new I12BackupfileS3Log();
        $log->created_at = Carbon::now();
        $log->md5file = $s3->md5;
        $log->size = $s3->size;
        $log->motivo = $motivo;
        $log->filename = $s3->filename;
        $log->userid = $usuario->codusuario;
        $log->ip = \Request::getClientIp(true);
        $log->save();

        DB::commit();
      } catch (Exception $e) {
        DB::rollBack();
        throw new Exception('Erro ao registrar log - ' . $e->getMessage());
      }

      $url = $s3->urldownload(Carbon::now()->addSeconds(30));
      BackupDownloadJob::dispatch($s3, $usuario);
      
      $ret->data=$url;
      $ret->ok=True;
    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();    
  } 
}
