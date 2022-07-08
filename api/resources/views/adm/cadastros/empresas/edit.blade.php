{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', ' - ' . $title)

@section('content_header')

<section class="content-header">
    <h1>
        {{ $title or 'Empresa'}}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('empresas.index') }}">Empresas</a></li>
        <li class="active">
        @if (isset($empresa))
            Código: {{ $empresa->id }}
        @else
            Novo
        @endif
        </li>
    </ol>
  </section>


@stop

@section('content')


<section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body">

                @if (\Session::has('success'))
                    <div class="form-group">
                    <div class="row col-md-12">
                    <div class="alert alert-success">
                        {!! \Session::get('success') !!}
                    </div>
                    </div>
                    </div>
                @endif    
                            
            @if (count($empresa)==0)
                <div class="alert alert-success">
                    <p>Nenhuma empresa encontrada</p>
                </div>
            @else

        
                @if (isset($empresa))
                    {!! Form::model($empresa, ['route' => ['empresas.update', $empresa->id], 'class' => 'form-horizontal', 'method' => 'put']) !!}
                @else
                    {!! Form::open(['route' => 'empresas.store', 'class' => 'form']) !!}
                @endif

                <div class="form-group">
                    <label for="inputType" class="col-md-1 control-label">Status</label>
                    <div class="col-md-3">
                        {!! Form::select('situacao', ['1' => 'Ativo', '0' => 'Inativo'], null, ['class' => 'form-control']) !!}   
                    </div>           
                </div>
                <div class="form-group">    
                    <label for="inputType" class="col-md-1 control-label">Tipo</label>
                    <div class="col-md-3">
                        {!! Form::select('pessoa', ['F' => 'Pessoa Física', 'J' => 'Pessoa Jurídica'], null, ['class' => 'form-control']) !!}    
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputType" class="col-md-1 control-label">Nome</label>
                    <div class="col-md-10">
                        {!! Form::text('razao', null, ['class' => 'form-control ucase', 'placeholder' => 'Razão Social', 'maxlength' => '150']) !!}        
                        @if ($errors->has('razao'))
                        <span class="text-danger">
                            <strong>{{ $errors->first('razao') }}</strong>
                        </span>
                        @endif  
                    </div>
                </div> 
                <div class="form-group">
                    <label for="inputType" class="col-md-1 control-label">Nome Fantasia</label>
                    <div class="col-md-10">
                        {!! Form::text('fantasia', null, ['class' => 'form-control ucase', 'placeholder' => 'Apelido ou Nome Fantasia', 'maxlength' => '150']) !!}        
                        @if ($errors->has('fantasia'))
                        <span class="text-danger">
                            <strong>{{ $errors->first('fantasia') }}</strong>
                        </span>
                        @endif  
                    </div>
                </div>  
                <div class="form-group">
                    <label for="inputType" class="col-md-1 control-label">Endereço</label>
                    <div class="col-md-8">
                        {!! Form::text('endereco', null, ['class' => 'form-control ucase', 'placeholder' => 'Nome da rua, avenida e etc', 'maxlength' => '50']) !!}        
                        @if ($errors->has('endereco'))
                        <span class="text-danger">
                            <strong>{{ $errors->first('endereco') }}</strong>
                        </span>
                        @endif  
                    </div>  
                    <label for="inputType" class="col-md-1 control-label">Número</label>
                    <div class="col-md-1">
                        {!! Form::text('num', null, ['class' => 'form-control ucase', 'placeholder' => 'Nº', 'maxlength' => '20']) !!}        
                        @if ($errors->has('num'))
                        <span class="text-danger">
                            <strong>{{ $errors->first('num') }}</strong>
                        </span>
                        @endif  
                    </div>                                      
                </div>                               
                <div class="form-group">
                    <label for="inputType" class="col-md-1 control-label">CEP</label>
                    <div class="col-md-2">
                        {!! Form::text('cep', null, ['class' => 'form-control ucase', 'placeholder' => '99999999', 'maxlength' => '8']) !!}        
                        @if ($errors->has('cep'))
                        <span class="text-danger">
                            <strong>{{ $errors->first('cep') }}</strong>
                        </span>
                        @endif  
                    </div>
                     <label for="inputType" class="col-md-1 control-label">Cidade</label>
                    <div class="col-md-5">
                        {!! Form::text('cidade', null, ['class' => 'form-control ucase', 'placeholder' => 'Cidade', 'maxlength' => '50']) !!}        
                        @if ($errors->has('cidade'))
                        <span class="text-danger">
                            <strong>{{ $errors->first('cidade') }}</strong>
                        </span>
                        @endif  
                    </div>  
                    <label for="inputType" class="col-md-1 control-label">UF</label>
                    <div class="col-md-1">
                        {!! Form::text('uf', null, ['class' => 'form-control ucase', 'placeholder' => 'UF', 'maxlength' => '220']) !!}        
                        @if ($errors->has('uf'))
                        <span class="text-danger">
                            <strong>{{ $errors->first('uf') }}</strong>
                        </span>
                        @endif  
                    </div>                                      
                </div>        
                <div class="form-group">
                    <label for="inputType" class="col-md-1 control-label">Mapa</label>
                    <div class="col-md-10">
                        <div id="map" class="map"></div>
                    </div>
                </div>                          
        
                <!-- Button (Double) -->
                <div class="form-group">
                    <label class="col-md-1 control-label"></label>
                    <div class="col-md-10">
                        {!! Form::button('<i class="glyphicon glyphicon-ok-sign"></i> Salvar', ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
                        <a href='{{ URL::previous() }}' class='btn btn-default'><i class="glyphicon glyphicon-backward"></i> Voltar</a>
                    </div>
                </div>
                {!! Form::close() !!}
               
 
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif         
            @endif




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
<script>
    // This example displays a map with the language and region set
    // to Japan. These settings are specified in the HTML script element
    // when loading the Google Maps JavaScript API.
    // Setting the language shows the map in the language of your choice.
    // Setting the region biases the geocoding results to that region.
    function initMap() {
      var uluru =  {lat: {!! $empresa->mapslat !!}, lng: {!! $empresa->mapslng !!} };
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 16,
          center: uluru
        });
      var marker = new google.maps.Marker({
          position: uluru,
          map: map,
          animation: google.maps.Animation.DROP,
          title: 'Hello World!',
        });
    }
      function toggleBounce() {
        if (marker.getAnimation() !== null) {
          marker.setAnimation(null);
        } else {
          marker.setAnimation(google.maps.Animation.BOUNCE);
        }
      }   
  </script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD6kMHvafcIeCSVK7sLCpE5iyfipCd4-3M&callback=initMap&language=pt-br" async defer>
  </script>
@stop