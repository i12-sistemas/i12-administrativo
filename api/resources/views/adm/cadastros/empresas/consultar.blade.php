{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', ' - ' . $title)

@section('content_header')

<section class="content-header">
    <h1>
        {{ $title or 'Listagem de empresas'}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('empresas.consultar') }}"><i class="fa fa-dashboard"></i> Home</a></li>
    </ol>
  </section>


@stop

@section('content')


<section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <form action="{{ route('empresas.consultar') }}" method="GET" class="form form-inline">
          <div class="box-header">
            
            <div class="box-tools pull-left" style="left: 10px; right: 0px !important;">
              <div class="form-group">
              <label for="searchall" class="label-control ">Consultar: </label>
              <div class="input-group input-group-sm" style="width: 300px;">
                
                <input type="text" id="searchall" name="searchall" class="form-control ucase" placeholder="Pesquisar empresas" value="{{ Request::get('searchall')  }}" required autofocus maxlength="65">
                <div class="input-group-btn">
                  <button type="submit" class="btn btn-default" title="Consultar"><i class="fa fa-search"></i></button>
                  <a href="{{ route('empresas.consultar') }}" class="btn btn-default" title="Limpar filtros"><i class="glyphicon glyphicon-erase"></i></a>
                </div>
              </div>
              </div>
            </div>
            

          </div>
          </form>
          <!-- /.box-header -->
          <div class="box-body table-responsive">
            <table class="table table-hover">
              <tbody>
                @foreach($empresas as $empresa)
                <tr>
                    <td width="100px">
                      <div class="btn-group-vertical">
                        <a class="btn btn-default" href="{{ route('empresas.edit', $empresa->id) }}" title="Editar cadastro"><span class="fa fa-edit"></span></a>
                        @if ($empresa->logerros->count() > 0)
                          <a href="{{ route('logs.index', ["searchcnpj" => $empresa->cnpj] ) }}" class="btn btn-default" title="Ver erros reportados"><span class="fa fa-plus-square"></span> {{ $empresa->logerros->count() }}</a>    
                        @endif
                        @if ($empresa->licencaativa->count()>0 )
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne{{ $empresa->id }}"  class="btn btn-{{ (($empresa->licencaativa->first()->vencimento < Carbon\Carbon::now()) ? "danger" : "primary") }}">
                        <span class="fa fa-arrow-right"></span>
                        </a>                        
                        @endif
                      </div>
                        
                    </td>                
                    <td>
                      <div class="h4 font-weight-bold">{{ $empresa->fantasia }}</div>
                      <div class="font-weight-bold">{{ $empresa->razao }}</div>
                      <div> <small>CNPJ: {{ $empresa->cnpj }}</small> | <small>Código: {{ $empresa->id }}</small></div>
                      @if ($empresa->licencaativa->count()>0 )
                        
                        <div id="collapseOne{{ $empresa->id }}" class="panel-collapse collapse" aria-expanded="true" style="">
                          <div class="panel box box-{{ (($empresa->licencaativa->first()->vencimento < Carbon\Carbon::now()) ? "danger" : "primary") }}">
                          <div class="box-body">
                            <dl class="dl-horizontal">
                              <dt>Chave de licença</dt>
                              <dd> {{ $empresa->licencaativa->first()->licenca }} </dd>
                              <dt>Data emissão</dt>
                              <dd>{{ $empresa->licencaativa->first()->dataemissao->format('d/m/Y') }}</dd>
                              <dt>Data de vencimento</dt>
                              <dd>
                                {{ $empresa->licencaativa->first()->vencimento->format('d/m/Y') }}
                                @if ($empresa->licencaativa->first()->vencimento < Carbon\Carbon::now() ) 
                                  <span class="badge text-white bg-red font-weight-bold">EXPIRADO</span>
                                @endif                                
                              </dd>
                              @if ($empresa->licencaativa->first()->vencimento < Carbon\Carbon::now() ) 
                                <dt>Data de bloqueio</dt>
                                <dd>{{ $empresa->licencaativa->first()->bloqueio->format('d/m/Y') }}
                                @if ($empresa->licencaativa->first()->bloqueio < Carbon\Carbon::now() ) 
                                  <span class="badge text-white bg-red font-weight-bold">BLOQUEADO</span>
                                @endif
                                </dd>
                              @endif
                              @if (isset($empresa->licencaativa->first()->dtativadaonline)) 
                                <hr>
                                <dt>Data ativação online</dt>
                                <dd>{{ $empresa->licencaativa->first()->dtativadaonline->format('d/m/Y h:m:s') }}
                                </dd>
                              @endif                              
                            </dl>
                          </div>
                          </div>
                        </div>
                      @endif
                      
                    </td>
                </tr>
                @endforeach
            </tbody></table>

            {{ $empresas->links() }}
            
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
    </div>

</section>
@stop

@section('css')
    <link rel="stylesheet" href="/css/sapv.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop

