@extends('layouts.adm.template')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <form method="get" id="formdiretorio" name="formconsulta" action="{{ route('adm.backup.list') }}" >
          <div class="form-group row">
            <div class="col-md-3">
              <div class="form-group row" >
                <input type="text" class="form-control" value="{{request()->q}}" onchange="Consultar()" name="q" placeholder="Digite aqui para consultar">
              </div>
            </div>
            <div class="col-md-3">
              <label for="ckinativos"> Mostrar inativos</label>
              <input type="checkbox" name="inativos" id="ckinativos" onchange="Consultar()" {{request()->inativos ? 'checked' : ''}} >
            </div>
            <div class="col-md-6">
              <a  class="btn btn-secondary" href="{{ route('adm.backup.dash') }}">Todos</a>
            </div>
          </div>
        </form>

          <div class="row">
            <div class="col-md-12">
              <h6>Listagem de backups</h6>
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
          @if(isset($dados))
            <form method="POST" id="formsel" name="formsel" action="{{ route('adm.etiquetas.delete.multi') }}" >
              @csrf 
              <table class="table table-sm table-striped">
                <thead>
                  <tr>
                    <th scope="col">.</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">Último backup</th>
                    <th scope="col" class="text-right"  style="min-width: 250px">Tamanho arquivos</th>
                  </tr>
                </thead>
                <tbody>
            @foreach ($dados as $item)
                <tr >
                  <td>
                    <div class="text-center">
                      <a href="{{route('adm.backup.list.files', ['diretorio' => $item->cnpj ])}}" class="btn btn-primary btn-sm"><i class="fas fa-file-medical-alt"></i></a>
                    </div>
                  </td>                  
                  <td>
                    <a href="{{route('adm.backup.list.files', ['diretorio' => $item->cnpj ])}}" class="text-black">
                    <div>
                      {{ $item->cliente ?  $item->cliente->nome : $item->cnpj}}
                      @if( $item->cliente ? $item->cliente->ativo != 1 : true)
                        <span class="badge badge-danger">Inativo</span>
                      @endif
                    </div>
                    @if($item->cliente ? $item->cliente->fantasia != $item->cliente->nome : false)
                    <div class="small">{{ $item->cliente->fantasia}}</div>
                    @endif
                    @if($item->cliente ? $item->cliente->cidade : false)
                    <div class="small">{{ $item->cliente->cidade }}</div>
                    @endif
                    </a>
                  </td>
                  <td>
                    <div>{{ $item->ultimolastmodified ? \Carbon\Carbon::parse($item->ultimolastmodified)->diffForHumans() : '-'}}</div>
                    <div class="small">{{ $item->ultimolastmodified ? \Carbon\Carbon::parse($item->ultimolastmodified)->format('d/m/Y H:m') : '-'}}</div>
                  </td>
                  <td class="text-right" >
                    <div>{{ human_filesize($item->totalsize) }}</div>
                    <div class="small">{{ $item->qtdearquivos}} arquivo(s)</div>
                  </td>
                </tr>
            @endforeach
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
</script>
@endsection