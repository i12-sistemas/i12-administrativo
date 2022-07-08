<?php

namespace App\Http\Controllers\API\v1\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;
use Carbon\Carbon;
use App\Http\Controllers\RetornoApiController;

class FileManagerController extends Controller
{
    public function list(Request $request){
        $ret = new RetornoApiController;
        try {
            $path = isset($request->path) ? $request->path : '';
            $disk = isset($request->disk) ? $request->disk : 'public';

            if($path=='') throw new Exception('Nenhum diretório informado');
            

            $internal = $path;
            // $exists = Storage::disk($local)->exists($internal);
            $storage = Storage::disk($disk);
            $files = $storage->files($internal);
            $filesinfo = [];
            foreach ($files as $file) {
                $pathinfo = pathinfo(storage_path($file));
                $info = $pathinfo;
                $info['size'] = $storage->size($file);
                $info['lastmodified'] =  Carbon::createFromTimestamp($storage->lastModified($file))->format('Y-m-d h:i:s');
                $filesinfo[] = $info;
            }
            $ret->rows = $filesinfo;
            $ret->ok = true;

            
        } catch (Exception $e) {
            $ret->msg = $e->getMessage();
            return $ret->toJson();
        }
        return $ret->toJson();
    }

    public function delete(Request $request){
        $ret = new RetornoApiController;
        try {
            $path = isset($request->path) ? $request->path : '';
            $disk = isset($request->disk) ? $request->disk : 'public';
            $arquivosstr = isset($request->arquivos) ? $request->arquivos : '';
            if($arquivosstr=='') throw new Exception('Nenhum arquivo informado');
            $files = json_decode($arquivosstr);
            if(count($files)<=0) throw new Exception('Nenhum arquivo informado');
            if($path=='') throw new Exception('Nenhum diretório informado');

            $internal = $path;
            $storage = Storage::disk($disk);
            $filesinfo = [];
            foreach ($files as $file) {
                $del = $storage->delete($internal . '/'. $file);
            }
            $ret->ok = true;
            
        } catch (Exception $e) {
            $ret->msg = $e->getMessage();
            return $ret->toJson();
        }
        return $ret->toJson();
    }    
}
