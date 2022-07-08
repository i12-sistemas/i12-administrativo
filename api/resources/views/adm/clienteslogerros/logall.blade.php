@extends('layouts.adm.template')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="form-group row">
            <div class="col-md-2">
              <a  class="btn btn-secondary" href="{{ route('adm.logs.clientes') }}">Por clientes</a>
            </div>
          </div>
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
                    <th style="min-width:250px">Informações</th>
                    <th style="min-width:330px">Versão</th>
                    <th style="min-width:170px">Outros</th>
                  </tr>
                </thead>
                <tbody>
            @foreach ($logs as $log)
                <tr >
                  <td>
                    <input type="checkbox" class="selecionado"  id="selecionado" name="selecionado[]" value="{{$log->id}}">
                  </td>
                  <td class="small">
                    <div class="media">
                      @if($log->filenameurl != '')
                        <a href="{{ $log->filenameurl}}" target="_blank">
                          <img src="{{ asset('assets/images/icon-image.png') }}" style="width: 30px"  class="mr-3" alt="PrintScreen">
                        </a>
                      @endif
                      <div class="media-body">
                        <div><strong>{{ $log->problema}}</strong></div>
                        @if($log->tipo == 0)
                        <div><h6><span class="badge badge-danger">Erro não tratado</span></h6></div>
                        @elseif($log->tipo == 1)
                        <div><h6><span class="badge badge-warning">Erro - Msg de alerta</span></h6></div>
                        @elseif($log->tipo == 2)
                        <div><h6><span class="badge badge-primary">Informação</span></h6></div>
                        @elseif($log->tipo == 3)
                        <div><h6><span class="badge badge-info">Somente acesso</span></h6></div>
                        @endif
                        <div>Data/hora: {{ $log->datahora->format('d/m/Y H:i')}}</div>
                        <div>ID: {{ $log->id }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="small">
                    <div>Versão App: {{ $log->versaoapp }}</div>
                    <div>Versão BD: {{ $log->versaobd }}</div>
                    @if($log->releaseapp)
                    <div>Release: {{ $log->releaseapp->format('d/m/Y H:i') }}</div>
                    @endif
                    @if($log->md5app)
                    <div>MD5: {{ $log->md5app }}</div>
                    @endif
                    <div>Hostname: {{ $log->nomecomputador}}</div>
                    <div>SO: {{ $log->so}}</div>
                    <div>User SO: {{ $log->usuarioso}}</div>
                  </td>
                  <td class="small">
                    
                    @if($log->cliente)
                    <div class="text-truncate"><strong>{{ $log->cliente->nome}}</strong></div>
                    <div class="text-truncate">{{ $log->cliente->fantasia}}</div>
                    @endif
                    @if($log->formulario)
                    <div>Form.: {{ $log->formulario}}</div>
                    @endif
                    @if($log->controle)
                    <div>Controle: {{ $log->controle}}</div>
                    @endif
                    @if($log->usuariosistema)
                    <div>Usuário: {{ $log->usuariosistema}}</div>
                    @endif
                  </td>
                  <td>
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