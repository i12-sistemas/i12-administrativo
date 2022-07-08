<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{$title or 'Status Report do cliente'}}</title>
    <meta charset="utf-8">
    <link href="{{ asset('/assets/favicon.ico') }}" rel="shortcut icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <link href="{{ asset('css/statusreport.css') }}" rel="stylesheet"> 
     
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-4.0.0-alpha.6-dist/css/bootstrap.min.css') }}">
    <script src="{{ asset('vendor/bootstrap-4.0.0-alpha.6-dist/js/jquery-3.2.1.slim.min.js') }}" ></script>
    <script src="{{ asset('vendor/bootstrap-4.0.0-alpha.6-dist/js/popper.min.js') }}" ></script>
    <script src="{{ asset('vendor/bootstrap-4.0.0-alpha.6-dist/js/tether.min.js') }}" ></script>
    <script src="{{ asset('vendor/bootstrap-4.0.0-alpha.6-dist/js/bootstrap.min.js') }}" ></script>
    <link rel="stylesheet" href="{{ asset('vendor/font-awesome-4.7.0/css/font-awesome.min.css') }}">
</head>
<body >
<div id="appcontent">
    {{-- topo --}}
    <div class="header">
        <div class="container">
            <div class="row" >
            <div class="col-12" >
        <span class="text-bold text-white"><span style="font-size: x-large;font-weight: bold;">SAPV</span> <small> Sistema de pré-venda e projetos</small></span>
        <span class="text-white pull-right text-bold">Status Report do cliente  </span>

            
        </div>
        </div>
        </div>
    </div>
    {{-- topo --}}

<div class="container" id="topo">

    {{-- body --}}    
    <div class="row" >
        <div class="col-12" id="main">
        
      <main role="main">

        @if((!$projeto) or (count($errors)>0))
            <div class="col-12 text-center" style=" margin-top: 40px;">
                <div class="alert alert-danger" role="alert">
           <div class="error-page">
            <div class="error-content">
                <h2 style="padding: 20px"><i class="fa fa-meh-o text-red"></i> Oops!</h2>
                @foreach($errors as $error)
                    <p><i class="fa fa-close" aria-hidden="true"></i> {!!$error!!}</p>
                @endforeach


            </div>
            </div>


                </div>
            </div>



        {{-- validação if exists projeto --}}
        @else

            <div class="logo">
                <img src="{{ asset('/assets/images/logos/' . $projeto->empresa->nomecurto . '.jpg' ) }}" class="image image-img-responsive img-fluid"  />
            </div>
            

                <router-view token="{!!$token!!}" hash="{!!$hash!!}" numprojeto="{!!$projeto->numproposta!!}"></router-view>
                <div class="card border-light">
                    <div class="card-block">
                        <div class="row projeto-content">
                            <div class="col-sx-12 col-md-12"> 
                                <div><span class="caption">Projeto</span></div>
                                <div class="card-title titulo">{{ $projeto->nomeprojeto }}</div>
                                <div><span class="caption">Cliente</span></div>
                                <div class="card-text cliente"> {{ $projeto->cliente->fantasia=='' ? $projeto->cliente->razao : $projeto->cliente->fantasia }}</div>
                            </div>
                        </div>
                          <div class="row">
                            <div class="col-sx-12 col-sm-12 col-md-12 col-lg-4">  
                                <div class="row">
                                    <dt class="col-4"><span class="caption">Nº projeto</span></dt>
                                    <dd class="col-8"><b>{{ $projeto->numproposta }}</b></dd>
                                     @if($projeto->temdataplanejamento)
                                        <dt class="col-4"><span class="caption">Início</span></dt>
                                        <dd class="col-8"><b>{{ $projeto->dtinicioprojeto->format('d/m/Y') }}</b></dd>    
                                        <dt class="col-4"><span class="caption">Término</span></dt>
                                        <dd class="col-8"><b>{{ $projeto->dtencerramentoprojeto->format('d/m/Y') }}</b></dd>   
                                    @endif     
                                    <dt class="col-4"><span class="caption">Responsável</span></dt>
                                    <dd class="col-8"><b>{{ $projeto->usuariotecnico->nome }}</b></dd>                                                             
                                </div>

                               
                            


                            </div> 
                            <div class="col-sx-12 col-sm-12 col-md-12 col-lg-8">      
                                <div class="row">
                                    {{-- dashboard planejamento --}}
                                    @if($projeto->temdataplanejamento)
                                    <div class="col-sx-12 col-md-4" style="margin-bottom: 10px !important;">
                                        <div class="card bg-light" >
                                            <div class="card-body">

                                                <div class="row" >
                                                    <div class="col-12">
                                                    <div class="text-bold"><span class="caption">Planejado</span><span class="pull-right">{{$projeto->diasdeprojetoperc}}%</span></div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{$projeto->diasdeprojetoperc}}%" aria-valuenow="{{$projeto->diasdeprojetoperc}}" aria-valuemin="0" aria-valuemax="100">{{$projeto->diasdeprojetoperc}}%</div>
                                                    </div>   
                                                    </div>   
                                                </div>
                                                <div class="row" style="margin-top: 10px !important;">
                                                    
                                                    <div class="col-6">
                                                        <div class="card text-center">
                                                        <div class="card bg-light" >
                                                            <div class="card-body  card-body-mini">
                                                                <h5 class="card-title">{{$projeto->diasdeprojeto}} <small>d</small></h5>
                                                                <p class="card-text"><span class="caption" style="font-size:x-small">Previsto</span></p>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="card text-center">
                                                        <div class="card bg-light" >
                                                            <div class="card-body  card-body-mini">
                                                                <h5 class="card-title">{{$projeto->diasdecorridosprojeto}} <small>d</small></h5>
                                                                <p class="card-text"><span class="caption" style="font-size:x-small">Decorrido</span></p>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    
                                                </div> 
                                                
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    {{-- dashboard planejamento --}}

                                    {{-- dashboard realizado --}}
                                    <div class="col-sx-12 col-md-{{ $projeto->temdataplanejamento ? '8' : '12' }}" style="margin-bottom: 10px !important;">
                                        <div class="card bg-light" >
                                            <div class="card-body">

                                                <div class="row" >
                                                    <div class="col-12">
                                                    <div class="text-bold"><span class="caption">Realizado</span><span class="pull-right">{{ round( $projeto->gestao_percconcluido, 2) }}%</span></div>
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" style="width: {{ round( $projeto->gestao_percconcluido, 2) }}%" aria-valuenow="{{ round( $projeto->gestao_percconcluido, 2) }}" aria-valuemin="0" aria-valuemax="100">{{ round( $projeto->gestao_percconcluido, 2) }}%</div>
                                                    </div>   
                                                    </div>   
                                                </div>
                                                <div class="row" style="margin-top: 10px !important;">
                                                    <div class="{{ $projeto->atividades->where('apontretrabalho', 1)->count()>0 ? 'col-xs-6 col-md-3' : 'col-4' }}">
                                                        <div class="text-center">
                                                        <div class="card border-primary " >
                                                            <div class="card-body card-body-mini text-primary">
                                                                <h5 class="card-title">{{ $projeto->atividades->count() }}</h5>
                                                                <p class="card-text"><i class="fa fa-tasks" aria-hidden="true"></i></p>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>                                                    
                                                    <div class="{{ $projeto->atividades->where('apontretrabalho', 1)->count()>0 ? 'col-xs-6 col-md-3' : 'col-4' }}">
                                                        <div class="text-center">
                                                        <div class="card border-success" >
                                                            <div class="card-body card-body-mini text-success">
                                                                <h5 class="card-title">
                                                                    {{ 
                                                                        ($projeto->atividades->where('apontencerrado',  100)->count() +
                                                                        ($projeto->atividades->where('apontencerrado',  50)->count()  / 2 ))
                                                                    }}
                                                                </h5>
                                                                <p class="card-text"><i class="fa fa-check" aria-hidden="true"></i></p>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    <div class="{{ $projeto->atividades->where('apontretrabalho', 1)->count()>0 ? 'col-xs-6 col-md-3' : 'col-4' }}">
                                                        <div class="text-center">
                                                        <div class="card border-danger" title="Realizados" >
                                                            <div class="card-body card-body-mini text-danger">
                                                                <h5 class="card-title">
                                                                    {{ 
                                                                        ($projeto->atividades->where('apontencerrado',  0)->count() +
                                                                        ($projeto->atividades->where('apontencerrado',  50)->count()  / 2 ))
                                                                    }}
                                                                </h5>
                                                                <p class="card-text"><i class="fa fa-circle-o-notch" aria-hidden="true"></i></p>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    @if($projeto->atividades->where('apontretrabalho', 1)->count()>0)
                                                    <div class="{{ $projeto->atividades->where('apontretrabalho', 1)->count()>0 ? 'col-xs-6 col-md-3' : 'col-4' }}">
                                                        <div class="text-center">
                                                        <div class="card border-danger" >
                                                            <div class="card-body card-body-mini  text-danger">
                                                                <h5 class="card-title">{{ $projeto->atividades->where('apontretrabalho', 1)->count() }}</h5>
                                                                <p class="card-text"><i class="fa fa-recycle" aria-hidden="true"></i></p>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>                                                    
                                                    @endif
                                                    
                                                </div> 
                                                
                                            </div>
                                        </div>
                                    </div>
                                    {{-- dashboard realizado --}}
                                
                            </div>
                        </div>                         
                    </div>
                </div>  
                
 
        
        <?php $n = 1 ?>     
        @foreach ($fases as $key => $fase)
            {{-- <p>
            <a class="btn btn-primary" data-toggle="collapse" href="#fase{{ $n }}" role="button" aria-expanded="false" aria-controls="fase{{ $n }}">
            {{$key}}
            </a>
            </p>
            <div class="collapse" id="fase{{ $n }}">
            <div class="card card-body">
            Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.
            </div>
            </div>         --}}
            <div class="card card-atividade">
                <div class="card-header text-black card-header-atividade">
                    <div class="row" data-target="#fase{{ $n }}" data-toggle="collapse" aria-expanded="false" aria-controls="fase{{ $n }}">
                        <div class="col-7">
                        {{$key}}
                        </div>
                        <div class="col-5 text-right">
                            <span class="badge badge-default text-primary"><i class="fa fa-tasks" aria-hidden="true"></i> {{ count($fase) }} </span>
                            @if(($fase->where('apontencerrado', 100)->count() + ($fase->where('apontencerrado', 50)->count()/2))>0)
                            <span class="badge badge-default text-success"><i class="fa fa-check" aria-hidden="true"></i> {{ $fase->where('apontencerrado', 100)->count() + ($fase->where('apontencerrado', 50)->count()/2) }} </span>
                            @endif
                            @if(( $fase->where('apontencerrado', 0)->count() + ($fase->where('apontencerrado', 50)->count()/2) ) >0)
                                <span class="badge badge-default text-danger"><i class="fa fa-circle-o-notch" aria-hidden="true"></i> {{ $fase->where('apontencerrado', 0)->count() + ($fase->where('apontencerrado', 50)->count()/2) }} </span>
                            @endif
                            @if($fase->where('apontretrabalho', 1)->count()>0)
                                <span class="badge badge-danger"><i class="fa fa-recycle" aria-hidden="true"></i> {{ $fase->where('apontretrabalho', 1)->count() }} </span>
                            @endif
                        </div>

                    </div>
                </div>
                <div class="collapse" id="fase{{ $n }}" >
                <div class="card-block">
                    
                    <table class="table table-hover table-condensed">
                        <tbody>
                        @foreach ($fase as $keyatividade => $atividade)
                            <tr>
                                @if($atividade->apontencerrado<=0)
                                    <td class="item-atividade-danger" >
                                @elseif($atividade->apontencerrado==50)
                                    <td class="item-atividade-warning" >
                                @elseif($atividade->apontencerrado>=100)
                                    <td class="item-atividade-success" >
                                @endif                            
                                <div class="row row-ajuste">
                                    <div class="col-xs-12">
                                        <div class="col-xs-12"><span class="badge badge-light">{{ $atividade->nitem }}</span> - <span class="text-bold">{{$atividade->atividade}}</span></div>
                                        <div class="col-xs-12">Responsável: {{($atividade->dependenciacliente==1 ? 'Cliente' : $atividade->empresa->nomecurto) . (trim($atividade->responsavel)=='' ? '' : ' - ' . $atividade->responsavel) }} </div>
                                        @if(count($atividade->consultoresnomes())>=1)
                                        <div class="col-xs-12">
                                            @if(count($atividade->consultoresnomes())==1)
                                                Consultor: {{ $atividade->consultoresnomes()[0] }}
                                            @elseif(count($atividade->consultoresnomes())>1)
                                                Consultores: {{ implode(', ', $atividade->consultoresnomes() ) }}
                                            @endif
                                            
                                        </div>
                                        @endif
                                        <div class="col-xs-12">
                                            <div class="h4">
                                            @if($atividade->apontencerrado<=0)
                                                <span class="text-danger"><i class="fa fa-circle" aria-hidden="true"></i> Em aberto</span> 
                                            @elseif($atividade->apontencerrado==50)
                                                <span class="text-warning"><i class="fa fa-circle-o-notch" aria-hidden="true"></i> Parcialmente concluido</span>
                                            @elseif($atividade->apontencerrado>=100)
                                                <span class="text-success"><i class="fa fa-check" aria-hidden="true"></i> Concluido</span>
                                            @endif
                                            </div>

                                            @if($atividade->apontretrabalho==1)
                                            <div class="h4">
                                                <p class="text-danger"><i class="fa fa-recycle" aria-hidden="true"></i> <small>Atividade com retrabalho</small></p>
                                            
                                            </div>
                                        @endif
                                    </div>  
                                    </div>
                                </div>
                            </td></tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                </div>
                </div>
            </div>   
            <?php $n = $n+1 ?>   
        @endforeach


        @endif
        {{-- fim validação if exists projeto --}}
      </main>
      </div>
    </div>
    {{-- body --}}    

</div>


<footer class="footer">
    <div class="container">
    <span class="text-muted"><small>{{ $projeto->empresa->fantasia or '' }}</small></span>

    <a href="http://www.idoze.com.br/sapv" target="_blank" class="link link-black">
        <span class="text-muted pull-right"><small>SAPV - Sistema de pré-venda e projetos.</small></span>
    </a>
    </div>
</footer>


</div>      

<script>
    var userlogged = [];
    var base_url = "{!! url('/') !!}";    
</script>
@if(config('app.env')=='production')
<script src="{{ asset('vendor/vuejs/vue.min.js') }}"></script>
@else
<script src="{{ asset('vendor/vuejs/vue.js') }}"></script>
@endif
<script src="{{ asset('js/app.js') }}"></script>


</body>
</html>