@extends('layouts.adm.template')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-12 my-sm-3">
              <h5>Diretório {{'/'. $diretorio . '/'}}</h5>
              @if($cliente)
                <h6>
                  Listagem de backups - {{$cliente->nome . ($cliente->nome!=$cliente->fantasia ? ' - ' . $cliente->fantasia : '') }}
                  @if($cliente->ativo != 1)
                  <span class="badge badge-danger">inativo</span>
                  @endif                  
                </h6>
              @endif
            </div>
          </div>
          @if (session()->has('success'))
            <div class="form-group row">
              <div class="col-md-12">
                <div class="alert alert-success" role="alert">
                  <strong>{{session('success')}}</strong>
                </div>
              </div>
            </div>
          @endif
          @if($errors->any())
            <div class="form-group row">
              <div class="col-md-12">
                <div class="alert alert-danger" role="alert">
                  <strong>{{$errors->first()}}</strong>
                </div>
              </div>
            </div>
          @endif
          @if(isset($resumo))
              <table class="table table-sm table-striped table-light ">
                <thead>
                  <tr>
                    <th scope="col" class="text-center">Mês/Ano</th>
                    <th scope="col" class="text-center">Primeiro e Último dia</th>
                    <th scope="col" class="text-center" >Qtde</th>
                    <th scope="col" class="text-right"  >Tamanho</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $totalbytes =0;
                  ?>
            @foreach ($resumo as $item)
                 <?php
                  $totalbytes += $item->totalsize;
                  $classbg = "";
                  if ( intval(\Carbon\Carbon::parse($item->primeirolastmodified)->format('mY')) === intval(request()->mes . request()->ano))  {
                    $classbg = 'table-primary';
                  }
                  
                  ?>

                <tr class="clickable-resumo {{$classbg}}"
                  style="cursor: pointer;" data-href="{{ route('adm.backup.list.files', ['page_size' => 100, 'diretorio' => $diretorio, 'ano' => \Carbon\Carbon::parse($item->primeirolastmodified)->format('Y'), 'mes' => \Carbon\Carbon::parse($item->primeirolastmodified)->format('m') ]) }}" >
                  <td class="text-center">
                        {{ \Carbon\Carbon::parse($item->primeirolastmodified)->format('m/Y') }}
                      </td>
                      <td class="text-center">
                    {{ \Carbon\Carbon::parse($item->primeirolastmodified)->format('d') }} e {{ \Carbon\Carbon::parse($item->ultimolastmodified)->format('d') }}
                  </td>
                  <td class="text-center">
                    {{ $item->qtdearquivos }}
                  </td>
                  <td class="text-right" >
                    <div>{{ human_filesize($item->totalsize) }}</div>
                  </td>
                  
                </tr>
            @endforeach
                <tr >
                  <td colspan="3">
                    {{$dados ? count($dados) . ' registro(s)' : '-' }}
                  </td>
                  <td class="text-right " >
                    <div>{{ human_filesize($totalbytes) }}</div>
                  </td>
                  
                </tr>
                </tbody>
              </table>
          @endif
          
          <div style="margin: 10px 0 10px 0">
            <hr>
            <h5>Listagem de arquivos</h5>
            <form method="get" id="formsearch" name="formsearch" action="{{ route('adm.backup.list.files', ['diretorio' => $diretorio]) }}" >
              <div class="row">
                <div class="col-md-12">
                  Mês: <input type="number" name="mes" min="0" max="12" title="Mês" placeholder="Mês" value="{{request()->mes}}">
                  Ano: <input type="number" name="ano" min="2000" max="2100" title="Ano" placeholder="Ano" value="{{request()->ano}}">
                  Paginação: <input type="number" name="page_size" title="Paginação" min="0" max="999" placeholder="Paginação" value="{{isset(request()->page_size) ? request()->page_size : 15 }}">
                  <button type="submit" class="btn btn-primary" >Consultar</button>
                  <a  class="btn btn-secondary" href="#" onClick="goBack();">Voltar</a>
                  <a  class="btn btn-primary" href="{{route('adm.backup.list.sync', ['diretorio' => $diretorio])}}" >Sync</a>
                  <button onclick="send()" type="button" class="btn btn-danger" >Remover selecionados</button>
                </div>
              </div>
              </form>
          </div>
          @if(isset($dados))
            <form method="POST" id="formsel" name="formsel" action="{{ route('adm.backup.delete') }}" >
              @csrf 
              <table class="table table-sm table-striped">
                <thead>
                  <tr>
                    <th scope="col"><input type="checkbox" name="select-all" id="select-all" ></th>
                    <th scope="col">Arquivo</th>
                    <th scope="col">Bucket</th>
                    <th scope="col">Data backup/Última modificação</th>
                    <th scope="col" class="text-right"  style="min-width: 250px">Tamanho</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $totalbytes =0;
                  ?>
            @foreach ($dados as $item)
                 <?php
                  $totalbytes += $item->size;
                  ?>
                <tr class="{{$item->size < 1 ? 'table-danger' : ''}}">
                  <td><input type="checkbox" class="selecionado"  id="selecionado" name="selecionado[]" value="{{$item->md5}}"></td>
                  <td>
                    <div>
                      <a href="{{ route('adm.backup.download', ['md5' => $item->md5]) }}" target="_blank">{{ $item->shortfilename }}</a>
                    </div>
                  </td>
                  <?php
                  $tooltip = 'Data do backup: '  . ($item->lastmodified ? $item->lastmodified->format('d/m/Y H:i:s') : '') . '\n\r' .
                             'Último sincronismo: '  . ($item->lastcheck ? $item->lastcheck->format('d/m/Y H:i:s') : '');
                  ?>
                  <td>{{ $item->bucket==0 ? 'EUA' : 'BR' }}</td>
                  <td data-toggle="tooltip" data-placement="top" title="{{$tooltip}}">
                    <div>
                      {{ $item->lastmodified ? $item->lastmodified->format('d/m/Y H:i') : '-'}} - {{ $item->lastmodified ? \Carbon\Carbon::parse($item->lastmodified)->diffForHumans() : '-'}}</div>
                  </td>
                  <td class="text-right {{$item->size < 1 ? 'text-danger' : ''}}" >
                    <div>{{ human_filesize($item->size) }}</div>
                    @if($item->size < 1)
                    <h5><span class="badge badge-danger">zerado</span></h5>
                    @endif
                  </td>
                  
                </tr>
            @endforeach
                <tr >
                  <td colspan="4">
                    {{$dados ? count($dados) . ' registro(s)' : '-' }}
                  </td>
                  <td class="text-right " >
                    <div>{{ human_filesize($totalbytes) }}</div>
                    {{-- @if($item->size < 1)
                    <h5><span class="badge badge-danger">zerado</span></h5>
                    @endif --}}
                  </td>
                  
                </tr>
                </tbody>
              </table>
            </form>
            <div class="form-group row">
              <div class="col-md-12">
                <div class="text-center">
                {{ $dados->appends(request()->input())->links() }}
                </div>
              </div>
            </div>            
          @endif
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
  function send() {
    document.getElementById("formsel").submit();
  };
  function Consultar() {
    document.getElementById("formdiretorio").submit();
  };
  // Listen for click on toggle checkbox
  $('#select-all').click(function(event) {   
      if(this.checked) {
          // Iterate each checkbox
          $('.selecionado').each(function() {
              this.checked = true;                        
          });
      } else {
          $('.selecionado').each(function() {
              this.checked = false;                       
          });
      }
  });

  // Listen for click on toggle checkbox
  $('.clickable-resumo').click(function() {   
    window.location = $(this).data("href");
  });
</script>
@endsection