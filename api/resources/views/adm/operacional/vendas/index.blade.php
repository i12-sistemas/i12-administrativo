@extends('layouts.admin.template')

@section('title', $title)

@section('menu-top')
@stop

@section('menu-top-right')                    
@stop

@section('content-header')
<section class="content-header">
    <h1>Gestão de Vendas</h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.home')}}" >Home</a></li>
        <li class="active">Vendas</b></li>
        <li> <a href="{{route('admin.operacional.vendas.consulta')}}" > Consultar</a></li>
        <li> <a href="{{route('admin.operacional.vendas.mensal.novo')}}" >Nova venda</a></li>
    </ol>
    </section>
@stop

@section('content')
<section class="content">
    
<div class="row">
    <div class="col-sx-12 col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Vendas recentes (10 últimas)</h3>


                <div class="box-tools">
                    <form action="{{ route('admin.operacional.vendas.consulta') }}" method="GET">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="search" name="str" class="form-control pull-right" placeholder="Consultar..." >
                            <div class="input-group-btn">
                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    <form>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table class="table table-bordered">
                <tbody>
                @foreach ($vendasrecentes as $venda)
                
                <tr>
                    <td>
                        <a href="{{route('admin.operacional.vendas.consulta', ['str' => $venda->associado->nome, 'dti' => $venda->min, 'dtf' => $venda->max]) }}" >
                        <div class="row">
                            <div class="col-sx-8 col-md-8">{{$venda->associado->nome}}</div>
                            <div class="col-sx-4 col-md-4">
                                <div class="text-right">{{formatRS($venda->total + $venda->vendasfilho->sum('total'))}}</div>
                            </div>
                        </div>
                        </a>    
                    </td>
                </tr>
                @endforeach
                </tbody></table>
            </div>
        </div>        
    </div>
    <div class="col-sx-12 col-md-6">
            <div class="row">
                @if( $vendasbloqueadas)
                <div class=" col-xs-12 col-md-6">
                    <div class="info-box">
                    <a href="{{route('admin.operacional.vendas.consulta', ['status' => 0, 'dti' => $vendasbloqueadas->min, 'dtf' => $vendasbloqueadas->max ] )}}"  class="text-white">
                        <span class="info-box-icon bg-red"><i class="fa fa-warning"></i></span>
                    </a>
        
                    <div class="info-box-content">
                        <span class="info-box-text">Vendas bloqueadas</span>
                    <span class="info-box-number"><small>R$ </small>{{formatRS($vendasbloqueadas->total, 0, false)}}</span>
                    <span class="info-box-text">{{$vendasbloqueadas->qtde}} <small>vendas</small>
                        <span class="pull-right"><a href="{{route('admin.operacional.vendas.consulta', ['status' => 0, 'dti' => $vendasbloqueadas->min, 'dtf' => $vendasbloqueadas->max ] )}}"  class="btn-link text-red">verificar</a></span>
                    </span>
                    </div>
                    <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                
                <!-- /.col -->
                <div class=" col-xs-12 col-md-6">
                    <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-user"></i></span>
                    <div class="info-box-content">
                    
                        <span class="info-box-text">Vendas bloqueadas</span>
                        <span class="info-box-number">{{$vendasbloqueadas->qtdeassociados}}<small>associados</small></span>
                        <span class="info-box-text"><a href="{{route('admin.operacional.vendas.consulta', ['status' => 0, 'dti' => $vendasbloqueadas->min, 'dtf' => $vendasbloqueadas->max ] )}}"  class="btn-link text-red pull-right">verificar</a></span>
                    </div>
                    <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                @endif
                    
                
                
            </div>        
    </div>
</div>

{{--  2 section  --}}
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Vendas nos últimos 12 meses - {{formatRS($vendasanual->sum('total'))}}</h3>
            <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="chart">
                <canvas id="areaChart" style="height: 252px; width: 553px;" width="553" height="252"></canvas>
            </div>
            <br>
            @if($vendasanual)
            <div class="row margin">
                <div class="col-xs-12 col-md-9">

                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <th>
                                    <div class="col-xs-3">Mês</div>
                                    <div class="col-xs-3 text-right">Total</div>
                                    <div class="col-xs-6">% no período</div>                                    
                                </th>
                            </tr>
                            <tfoot>
                                <th>
                                    <div class="col-xs-3">{{$vendasanual->count()}} mês(es)</div>
                                    <div class="col-xs-3 text-right">{{formatRS($vendasanual->sum('total'))}}</div>
                                </th>
                            </tfoot>                            
                            @foreach($vendasanual as $item)
                            <tr>
                                <td>
                                    <div class="col-xs-3">
                                        <a href="{{route('admin.operacional.vendas.consulta', ['dti' => $item->min, 'dtf' => $item->max ] )}}"  class="btn-link text-black">
                                        <i class="fa fa-external-link"></i> {{$item->mesano}}
                                        </a>

                                    </div>
                                    <div class="col-xs-3 text-right">{{formatRS($item->total)}}</div>
                                    <div class="col-xs-2">{{round(($item->total / $vendasanual->sum('total'))*100,2)}}%</div>
                                    <div class="col-xs-4">
                                        <div class="progress progress-xs">
                                            <div class="progress-bar progress-bar-primary" style="width: {{round(($item->total / $vendasanual->sum('total'))*100,2)}}%"></div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                            @endforeach   
                            </tbody></table>
                            
                            
                
                            
                </div>

                <div class="col-xs-12 col-md-3">
                    <div class="pad box-pane-right bg-primary" style="min-height: 280px">
                        <div class="description-block margin-bottom">
                        <div class="sparkbar pad" data-color="#fff"><canvas width="34" height="30" style="display: inline-block; width: 34px; height: 30px; vertical-align: top;"></canvas></div>
                        <h5 class="description-header">{{round($vendasanual->sum('qtdeassociados') / $vendasanual->count()) }}</h5>
                        <span class="description-text">associados/mês</span>
                        </div>
                        <!-- /.description-block -->
                        <div class="description-block margin-bottom">
                        <div class="sparkbar pad" data-color="#fff"><canvas width="34" height="30" style="display: inline-block; width: 34px; height: 30px; vertical-align: top;"></canvas></div>
                        <h5 class="description-header">{{round($vendasanual->sum('qtdevenda') / $vendasanual->count()) }}</h5>
                        <span class="description-text">vendas/mês</span>
                        </div>
                        <!-- /.description-block -->
                        <div class="description-block">
                        <div class="sparkbar pad" data-color="#fff"><canvas width="34" height="30" style="display: inline-block; width: 34px; height: 30px; vertical-align: top;"></canvas></div>
                        <h5 class="description-header">{{formatRS($vendasanual->sum('total') / $vendasanual->count() )}}</h5>
                        <span class="description-text">Valor médio/mês</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                </div>                
            </div>
            @endif   

        </div>
        </div>
    </div>


    
      
     
</div>
{{--  2 section  --}}
</section>
@stop







@section('js')
<script>

  jQuery(function () {
    /* ChartJS
     * -------
     * Here we will create a few charts using ChartJS
     */

    //--------------
    //- AREA CHART -
    //--------------

    // Get context with jQuery - using jQuery's .get() method.
    var areaChartCanvas = jQuery('#areaChart').get(0).getContext('2d')
    // This will get the first returned node in the jQuery collection.
    var areaChart       = new Chart(areaChartCanvas)

    var areaChartData = {
      labels  : [
        @if($vendasanual)
        @foreach($vendasanual->pluck('mesano') as $mesano)
              '{{$mesano}}',
        @endforeach        
        @endif
      ],
      datasets: [
        {
          label               : 'Tickets registrados',
          fillColor           : 'rgba(60,141,188,0.3)',
          strokeColor         : 'rgba(60,141,188,0.8)',
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : [
            @if($vendasanual)
            @foreach($vendasanual->pluck('total') as $number)
              {{$number}},
            @endforeach
            @endif
            ]
        }
      ]
    }

    var areaChartOptions = {
      //Boolean - If we should show the scale at all
      showScale               : true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines      : true,
      //String - Colour of the grid lines
      scaleGridLineColor      : 'rgba(0,0,0,.05)',
      //Number - Width of the grid lines
      scaleGridLineWidth      : 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines  : true,
      //Boolean - Whether the line is curved between points
      bezierCurve             : false,
      //Number - Tension of the bezier curve between points
      bezierCurveTension      : 0.3,
      //Boolean - Whether to show a dot for each point
      pointDot                : true,
      //Number - Radius of each point dot in pixels
      pointDotRadius          : 4,
      //Number - Pixel width of point dot stroke
      pointDotStrokeWidth     : 1,
      //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
      pointHitDetectionRadius : 20,
      //Boolean - Whether to show a stroke for datasets
      datasetStroke           : true,
      //Number - Pixel width of dataset stroke
      datasetStrokeWidth      : 2,
      //Boolean - Whether to fill the dataset with a color
      datasetFill             : true,
      showDatasetLabels : true,


      //String - A legend template
      legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].lineColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
      //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
      maintainAspectRatio     : true,
      //Boolean - whether to make the chart responsive to window resizing
      responsive              : true
    }
    //Create the line chart
    areaChart.Line(areaChartData, areaChartOptions)

   
   
  })

  


</script>
@stop