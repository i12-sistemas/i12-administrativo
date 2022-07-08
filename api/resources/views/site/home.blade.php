@extends('site.layout.template')

@section('title', '')

@section('menu-top-right')                    
@stop


@section('banner')
  <div class="container">      
    <div class="row justify-content-md-center">
      <div class="col-md-10">
        <div class="contents text-center">
          <h1 class="wow fadeInDown" data-wow-duration="1000ms" data-wow-delay="0.3s">Mate - Free Parallax Website Template</h1>
          <p class="lead  wow fadeIn" data-wow-duration="1000ms" data-wow-delay="400ms">Lorem ipsum dolor sit amet, consectetur adipiscing elit. </p>
          <a href="#" class="btn btn-common wow fadeInUp" data-wow-duration="1000ms" data-wow-delay="400ms">Download</a>
        </div>
      </div>
    </div> 
  </div>   
@stop

@section('content')

<section id="about-details">
  <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="feature_header text-center">
                            <h3 class="feature_title">Outros <b>contatos</b></h3>
                            <h4 class="feature_sub">Caso prefira temos outras formas de atendimento</h4>
                        </div>
                    </div>  <!-- Col-md-12 End -->
                </div>
                <div class="row">
                    <div class="main_feature text-center">
                        <div class="col-md-4 col-xs-12 col-sm-6">
                                <div class="feature_content">
                                    <i class="fa fa-phone"></i>
                                    <h5>Telefone</h5>
                                    <h4>(16) 3851-6983</h4>
                                    <h4>(16) 3851-3621</h4>
                                    <h4>(16) 9 9162-4912 <small>(WhatsApp)</small></h4>
                                    <p class="text-bold">Expediente</p>
                                    <p>Segunda à Sexta das 08:00h às 18:00h</p>
                                    <p>Sábado das 08:00h às 12:00h</p>
                                </div>
                            </div>
                        <div class="col-md-4 col-xs-12 col-sm-6">
                                <div class="feature_content">

  <i class="fa fa-comments"></i>
  <h5 >Chat Online</h5>
  <a href="{{route('site.suporte')}}" >Suporte Online</a>




                                </div>
                        </div> <!-- Col-md-4 Single_feature End -->
                        <div class="col-md-4 col-xs-12 col-sm-6">
                                <div class="feature_content">
                                    <i class="fa fa-cog"></i>
                                    <h5>Suporte Remoto</h5>
                                    <p>Este procedimento requer o download do aplicativo Team Viewer</p>
                                    <a href="https://get.teamviewer.com/i12suporte" target="_blank">Download TeamViewer</a>
                                </div>
                        </div> <!-- Col-md-4 Single_feature End -->
                        <!-- <button class="btn btn-main"> Read More</button> -->
                    </div>
            </div>  <!-- Row End -->
        </div>  <!-- Container End -->
</section>




<div id="g-map" class="no-padding">
	<div class="container-fluid">
		<div class="row">
			<div class="map" id="map" ></div>
    </div>
	</div>
</div>

@stop


@section('js')
<script type="text/javascript" src="{{ asset('vendor/templatesite/js/gmaps.js') }}"></script>
<script>
// Google Map - with support of gmaps.js
var map;
map = new GMaps({
    div: '#map',
    lat: -20.7178827,
    lng: -48.053392,
zoom: 0,
    scrollwheel: false,
    panControl: false,
    zoomControl: false,
});
map.addMarker({
lat: -20.7178827,
    lng: -48.053392,
    title: 'i12 Sistemas',
    infoWindow: {
    content: '<p>Rua das Rosas, 265 - Jardim Marina<br>CEP 14640-000 - Morro Agudo - SP</p>'
    },
    icon: "{!! asset('vendor/templatesite/images/map1.png') !!}"
}
);
</script>
@stop
