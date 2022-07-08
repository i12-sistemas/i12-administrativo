@extends('layouts.adm.template')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="row">
          <div class="col-sm-3">
            <div class="card border-primary">
              <div class="card-header bg-primary text-white"><i class="far fa-money-bill-alt"></i> Nível pago</div>
              <div class="card-body">
                <dl class="row">
                  
                  <dt class="col-sm-5">Uso</dt>
                  <dd class="col-sm-7">
                    <div class="text-right h4">{{human_filesize( $dados->nivelpago->total)}}</div>
                    <div class="text-right small">{{ $dados->nivelpago->qtdearquivos }} arquivos</div>
                    <div class="text-right small">{{human_filesize( $dados->nivelpago->mediaarquivoscliente)}} / cliente</div>
                  </dd>

                  <dt class="col-sm-8">Nº de clientes</dt>
                  <dd class="col-sm-4"><div class="text-right">{{$dados->nivelpago->qtdeclientes}}</div></dd>

                  <dt class="col-sm-7 text-right"><dt class="col-sm-7">Consumo</dt>
                  <dd class="col-sm-5"><div class="text-right">{{$dados->nivelpago->mediageral}}%</div></dd>
              </dl>
                  <div class="progress">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{round($dados->nivelpago->mediageral,0)}}%" aria-valuenow="{{round($dados->nivelpago->mediageral,0)}}" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <hr/>
                  <div class="text-center">
                    <a href="{{route('adm.backup.list',  ['nivel' => '2'])}}" class="card-link text-primary">Listagem completa</a>
                  </div>
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="card border-info">
              <div class="card-header bg-info text-white"><i class="fas fa-universal-access"></i> Nível gratuito</div>
              <div class="card-body">
                <dl class="row">
                  
                  <dt class="col-sm-5">Uso</dt>
                  <dd class="col-sm-7">
                    <div class="text-right h4">{{human_filesize( $dados->nivelgratuito->total)}}</div>
                    <div class="text-right small">{{ $dados->nivelgratuito->qtdearquivos }} arquivos</div>
                    <div class="text-right small">{{human_filesize( $dados->nivelgratuito->mediaarquivoscliente)}} / cliente</div>
                  </dd>
                  <dt class="col-sm-8">Nº de clientes</dt>
                  <dd class="col-sm-4"><div class="text-right">{{$dados->nivelgratuito->qtdeclientes}}</div></dd>
                  <dt class="col-sm-7 text-right"><dt class="col-sm-7">Consumo</dt>
                  <dd class="col-sm-5"><div class="text-right">{{$dados->nivelgratuito->mediageral}}%</div></dd>
              </dl>
              <div class="progress">
                <div class="progress-bar bg-info" role="progressbar" style="width: {{round($dados->nivelgratuito->mediageral,0)}}%" aria-valuenow="{{round($dados->nivelgratuito->mediageral,0)}}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <hr/>
              <div class="text-center">
                <a href="{{route('adm.backup.list',  ['nivel' => '1'])}}" class="card-link text-info">Listagem completa</a>
              </div>
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="card border-danger">
              <div class="card-header bg-danger text-white">Inativos</div>
              <div class="card-body">
                <dl class="row">
                  <dt class="col-sm-5">Uso</dt>
                  <dd class="col-sm-7">
                    <div class="text-right h4">{{human_filesize( $dados->inativos->total)}}</div>
                    <div class="text-right small">{{ $dados->inativos->qtdearquivos }} arquivos</div>
                  </dd>

                  <dt class="col-sm-8">Nº de clientes</dt>
                  <dd class="col-sm-4"><div class="text-right">{{$dados->inativos->qtdeclientes}}</div></dd>

                  <dt class="col-sm-5">Período</dt>
                  <dd class="col-sm-7 small">
                    @if($dados->inativos->minlastmodified)
                    <div class="text-right">de {{$dados->inativos->minlastmodified->format('d/m/Y')}}</div>
                    @endif
                    @if($dados->inativos->maxlastmodified)
                    <div class="text-right">até {{ $dados->inativos->maxlastmodified->format('d/m/Y') }}</div>
                    @endif
                  </dd>

                  @if($dados->inativos->qtdesemclientes > 0)
                    <dt class="col-sm-8">Sem identificação</dt>
                    <dd class="col-sm-4 small">
                      <div class="text-right">{{$dados->inativos->qtdesemclientes}} arq.</div>
                    </dd>                  
                  @endif

                  <dt class="col-sm-7">Consumo</dt>
                  <dd class="col-sm-5"><div class="text-right">{{$dados->inativos->mediageral}}%</div></dd>
                </dl>
                <div class="progress">
                  <div class="progress-bar bg-danger" role="progressbar" style="width: {{round($dados->inativos->mediageral,0)}}%" aria-valuenow="{{round($dados->inativos->mediageral,0)}}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <hr/>
                <div class="text-center">
                  <a href="{{route('adm.backup.list',  ['inativos' => 'on'])}}" class="card-link text-danger">Listagem completa</a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="card border-black">
              <div class="card-header bg-grey text-black">Total S3</div>
              <div class="card-body">
                <dl class="row">
                  <dt class="col-sm-5">Uso</dt>
                  <dd class="col-sm-7">
                    <div class="text-right h4">{{human_filesize( $dados->total->size)}}</div>
                    <div class="text-right small">{{ $dados->total->qtde }} arquivos</div>
                  </dd>

                  <dt class="col-sm-8">Nº de clientes</dt>
                  <dd class="col-sm-4"><div class="text-right">{{$dados->total->qtdeclientes}}</div></dd>

                  <dt class="col-sm-5">Média</dt>
                  <dd class="col-sm-7 small">
                    <div class="text-right">{{human_filesize($dados->total->mediaarquivos)}}/arquivo</div>
                    <div class="text-right">{{human_filesize($dados->total->mediacliente)}}/cliente</div>
                  </dd>
                </dl>
                <hr/>
                <div class="text-center">
                  <a href="{{route('adm.backup.list')}}" class="card-link text-black">Listagem completa</a>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <div class="row justify-left">
      <div class="row">
        <div class="col-sm-2">
          <a  class="btn btn-primary" href="{{route('adm.backup.report.alert')}}" >Alertas</a>
        </div>
      </div>
    </div>
</div>
@endsection