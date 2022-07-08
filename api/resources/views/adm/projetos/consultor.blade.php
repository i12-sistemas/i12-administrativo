{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', ' - ' . $title)

@section('content_header')

<section class="content-header">
    <h1>
        {{ $title or 'Projetos'}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('projetos.consultor') }}"><i class="fa fa-dashboard"></i> Home</a></li>
    </ol>
</section>
@stop

@section('content')


<section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          
          <div class="box-header">
            <form action="{{ route('projetos.consultor') }}" method="GET" class="form form-inline">
            <div class="box-tools">
                                          
              <input type="text" id="searchall" name="searchall" class="form-control ucase input-sm" placeholder="Pesquisar logs" value="{{ Request::get('searchall')  }}"  maxlength="65">
              <div class="form-group">
                <div class="input-group input-group-sm" style="width: 300px;">
                
                  <div class="input-group-btn">
                    <button type="submit" class="btn btn-default" title="Consultar"><i class="fa fa-search"></i> Consultar</button>
                    <a href="{{ route('projetos.consultor') }}" class="btn btn-default" title="Limpar filtros"><i class="glyphicon glyphicon-erase"></i> Limpar</a>
                  </div>
                </div>
              </div>
              
            </div>
            </form> 

          </div>
         
          <!-- /.box-header -->
          <div class="box-body ">
            <table class="table table-hover table-responsive">
              <tbody>
                @foreach($projetos as $projeto)
                <tr>
                    <td width="50px">
                        <a class="btn btn-default" href="{{ route('projetos.show', $projeto->numproposta) }}" title="Ver projeto"><span class="fa fa-edit"></span></a>
                    </td>                
                    <td>
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="row text-header">
                            <a href="{{ route('projetos.show', $projeto->numproposta) }}">
                              <div class="col-sm-3 col-md-3 col-lg-3 text-black text-left no-padding"><strong>{{ $projeto->numproposta }}</strong></div>
                              <div class="col-sm-9 col-md-9 col-lg-9 text-black  text-left no-padding">{{ $projeto->nomeprojeto }}
                                <span class="pull-right-container"><small class="label pull-right text-black">{!! $projeto->statusprojetodescricao(true) !!}</small></span>
                              </div>
                            </a>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="row text-description">
                            {{ $projeto->cliente->nomeexibicao }}
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-12">
                          
                          @if ($projeto->atividades->count() > 0)
                          <p class="text-black">
                              <span class="text-align-left">Realizado {{ $projeto->gestao_percconcluido }}%</span>
                              <span class="pull-right">{{ ($projeto->atividadesencerrado + ($projeto->atividadesparcial/2)) }} de {{ $projeto->atividades->count()  }}</span>
                          </p> 
                          <div class="progress" >
                            <div class="progress-bar progress-bar-success" role="progressbar" style="width:{{ getPerc($projeto->atividadesencerrado, $projeto->atividades->count()) }}%">
                              {{ getPerc($projeto->atividadesencerrado, $projeto->atividades->count()) }}% - Encerrado [{{ $projeto->atividadesencerrado }}]
                            </div>
                            <div class="progress-bar progress-bar-warning" role="progressbar" style="width:{{ getPerc($projeto->atividadesparcial/2, $projeto->atividades->count()) }}%">
                              {{ getPerc($projeto->atividadesparcial/2, $projeto->atividades->count()) }}% - Parcial [{{ $projeto->atividadesparcial }}]
                            </div>
                            <div class="progress-bar progress-bar-danger" role="progressbar" style="width:{{ getPerc($projeto->atividadesemaberto + ($projeto->atividadesparcial/2), $projeto->atividades->count()) }}%">
                              {{ getPerc($projeto->atividadesemaberto + ($projeto->atividadesparcial/2), $projeto->atividades->count()) }}% - Em aberto [{{ $projeto->atividadesemaberto }}] 
                            </div>
                          </div>
                          @else
                            <p class="text-red">
                              <span class="text-align-left">Nenhuma atividade</span>
                          </p> 
                          @endif                          
                        </div>
                         </div>
                      </div>
                      
                    </td>
                </tr>
                @endforeach
            </tbody></table>
            <div class="text-center">
            {{ $projetos->links() }}
            </div>
            
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
  @if(config('app.env')=='production')
  <script src="{{ asset('vendor/vuejs/vue.min.js') }}"></script>
  @else
  <script src="{{ asset('vendor/vuejs/vue.js') }}"></script>
  @endif
    <script src="{{ asset('js/app.js') }}"></script>
@stop