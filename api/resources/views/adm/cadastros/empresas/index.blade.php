{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', ' - ' . $title)

@section('content_header')

<section class="content-header">
    <h1>
        {{ $title or 'Listagem de empresas'}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('empresas.index') }}"><i class="fa fa-dashboard"></i> Home</a></li>
    </ol>
  </section>


@stop

@section('content')


<section class="content">
    <div class="row">


        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-primary">
            <div class="inner">
              <h3>{{ $qtde or ''}}</h3>

              <p>Empresas ativas</p>
            </div>
            <div class="icon">
              <i class="fa fa-heart"></i>
            </div>
            <a href="{{ route('empresas.consultar') }}" class="small-box-footer">Ver todos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3>53<sup style="font-size: 20px">%</sup></h3>

              <p>Bounce Rate</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>44</h3>

              <p>User Registrations</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3>65</h3>

              <p>Unique Visitors</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
      

    </div>
	  
    
    <div class="row">
      <div class="col-xs-6">
        <div class="box">
          <form action="{{ route('empresas.consultar') }}" method="GET" class="form form-inline">
          <div class="box-header">
            <div class="box-title">Últimas empresas alteradas</div>
            <div class="box-tool pull-right">
              <div class="input-group input-group-sm" style="width: 200px;">
                <input type="text" id="searchall" name="searchall" class="form-control ucase" placeholder="Pesquisar mais empresas" value="{{ Request::get('searchall')  }}" required autofocus maxlength="65">
                <div class="input-group-btn">
                  <button type="submit" class="btn btn-default" title="Consultar"><i class="fa fa-search"></i></button>
                </div>
              </div>

            </div>
            
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif  
          </div>
          </form>

          <!-- /.box-header -->
          <div class="box-body table-responsive">
            <table class="table table-hover">
              <tbody>
                @foreach($empresas as $empresa)
                <tr>
                    <td>
                      <a href="{{ route('empresas.edit', $empresa->id) }}" >
                      <div class="font-weight-bold">{!! $empresa->nomeexibicao !!}</div>
                      </a>
                    </td>
                    <td style="width = 150px"><a href="{{ route('logs.index', ["searchcnpj" => $empresa->cnpj] ) }}" class="btn btn-default btn-sm" title="Ver erros reportados"><span class="fa fa-plus-square"></span></a></td>
                </tr>
                @endforeach
            </tbody></table>


            
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

