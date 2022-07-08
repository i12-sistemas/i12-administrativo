@extends('layouts.adm.template')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          {{-- <form method="get" id="formdiretorio" name="formconsulta" action="{{ route('api.logclientes.listagem') }}" > --}}
          <form method="get" id="formdiretorio" name="formconsulta" action="#" >
          <div class="form-group row">
            <div class="col-md-3">
              <div class="form-group row" >
                {{-- <input type="text" class="form-control" value="{{$q}}" onchange="Consultar()" name="q" placeholder="Digite aqui para consultar"> --}}
              </div>
            </div>
            <div class="col-md-6">
              {{-- <a  class="btn btn-secondary" href="{{ route('api.logclientes.listagem') }}">Todos</a> --}}
            </div>
          </div>
        </form>
          <div class="form-group row">
            <div class="col-md-4">
              <h6>Logs de Erro</h6>
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

          @if(isset($logs))
            <form method="POST" id="formsel" name="formsel" action="#" >
              @csrf 
              <table class="table table-sm table-striped">
                <thead>
                  <tr>
                    <th scope="col"><input type="checkbox" name="select-all" id="select-all" ></th>
                    <th scope="col">#</th>
                    {{-- <th scope="col">CNPJ</th> --}}
                    <th style="width:250px">Problema</th>
                    <th scope="col">Data</th>
                    <th scope="col">Versão App</th>
                    <th scope="col">Módulo App</th>
                    <th scope="col">Versão BD</th>
                    <th scope="col">Formulário</th>
                    <th scope="col">Controle</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Usuário SO</th>
                    <th scope="col">Usuário iCom</th>
                    <th scope="col">Computador</th>
                    <th scope="col">SO</th>
                    {{-- <th scope="col">Filename</th> --}}
                    <th scope="col">Size</th>
                    <th scope="col">Anexo</th>
                    <th scope="col">Tratado</th>
                  </tr>
                </thead>
                <tbody>
            @foreach ($logs as $log)
                <tr >
                  <td><input type="checkbox" class="selecionado"  id="selecionado" name="selecionado[]" value="{{$log->id}}"></td>
                  <td>{{$log->id}}</td>
                  {{-- <td>{{$log->cnpj}}</td> --}}
                  <td>{{ $log->problema}}</td>
                  <td><div>{{($log->datahora)->format('d/m/Y')}}</div></td>
                  <td><div>{{ $log->versaoapp}}</div></td>
                  <td><div>{{ $log->moduloapp}}</div></td>
                  <td><div>{{ $log->veraobd}}</div></td>
                  <td><div>{{ $log->formulario}}</div></td>
                  <td><div>{{ $log->controle}}</div></td>
                  <td><div>{{ $log->tipo}}</div></td>
                  <td><div>{{ $log->usuarioso}}</div></td>
                  <td><div>{{ $log->usuariosistema}}</div></td>
                  <td><div>{{ $log->nomecomputador}}</div></td>
                  <td><div>{{ $log->so}}</div></td>
                  {{-- <td><div>{{ $log->filename}}</div></td> --}}
                  @if ($log->filename)
                  <td><div>{{ $log->size}}</div></td>
                  <td>
                    <div class="alert alert-info" role="alert">
                       <a href="{{ $log->filenameurl}}" class="alert-link">Anexo</a>
                    </div>
                  </td>
                  @else
                  <td><div> -- </div></td>
                  <td><div> -- </div></td>
                  @endif 
                  <td class="text-center">
                    <i class="fa fa-times-circle" style="color:red"></i>
                  </td>
                </tr>
            @endforeach
                </tbody>
              </table>
            </form>
            <div class="form-group row">
              <div class="col-md-12">
                <div class="text-center">
                {{ $logs->links() }}
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