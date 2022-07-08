@extends('layouts.adm.template')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <form method="get" id="formdiretorio" name="formconsulta" action="{{ route('adm.backup.report.alert') }}" >
          <div class="form-group row">
            <div class="col-md-1">
              <a  class="btn btn-secondary" href="#" onClick="goBack();">Voltar</a>
            </div>
            <div class="col-md-4">
              <div class="form-group row" >
                <input type="text" class="form-control" value="{{request()->q}}" onchange="Consultar()" name="q" placeholder="Digite aqui para consultar">
              </div>
            </div>
            <div class="col-md-7">
              <a  class="btn btn-secondary" href="{{ route('adm.backup.report.alert') }}">Todos</a>
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
                    <th scope="col">Tipo</th>
                    <th scope="col" class="text-right"  style="min-width: 250px">Tamanho arquivos</th>
                  </tr>
                </thead>
                <tbody>
            @if($dados)
            @foreach ($dados as $item)
            <?php
              $class = '';
              $btnclass = 'btn-primary';
              if (($item->horasatras>72) && ($item->horasatras<=96)) {
                $class = 'text-warning';
                $btnclass = 'btn-warning';
              } else if (($item->horasatras>96) || !($item->horasatras>0)) {
                $class = 'text-danger';
                $btnclass = 'btn-danger';
              }
            ?>
                <tr  >
                  <td>
                    <div class="text-center">
                      <a href="{{route('adm.backup.list.files', ['diretorio' => $item->doc14char ])}}" class="btn btn-sm {{$btnclass}}">
                        <i class="fas fa-file-medical-alt"></i>
                        
                      </a>
                    </div>
                  </td>                  
                  <td>
                    <a href="{{route('adm.backup.list.files', ['diretorio' => $item->doc14char ])}}" class="text-black">
                    <div>
                     
                      {{ $item->nome }}
                    </div>
                    <div class="small">
                    @if($item->fantasia != $item->nome)
                    {{ $item->fantasia}} - 
                    @endif
                    {{ $item->cidade }}</div>
                    </a>
                  </td>
                  <td>
                    <div>{{ $item->lastmodified ? \Carbon\Carbon::parse($item->lastmodified)->format('d/m/Y H:m') : '-'}} - {{ $item->lastmodified ? \Carbon\Carbon::parse($item->lastmodified)->diffForHumans() : '-'}}</div>
                  </td>
                  <td class="text-right" >
                    @if($item->i12controlabkp == 1)
                    <i class="far fa-hdd"></i>
                    @elseif($item->i12controlabkp == 2)
                    <i class="fab fa-aws"></i>
                    @endif
                  </td>
                  <td class="text-right" >
                    <div>{{ human_filesize($item->totalsize) }}</div>
                    <div class="small">{{ $item->qtdearquivos}} arquivo(s)</div>
                  </td>
                </tr>
            @endforeach
            @endif
                </tbody>
              </table>
            </form>
            <div class="form-group row">
              <div class="col-md-12">
                <div class="text-center">
                {{-- {{ $dados->appends(request()->input())->links() }} --}}
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