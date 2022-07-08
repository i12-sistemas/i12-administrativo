<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{$title or ''}}</title>
  <meta name="description" content="{{ $site_metatags ? $site_metatags->description : 'Sistema de Gestão de Transporte'}}">
  <meta name="keywords" content="{{ $site_metatags ? $site_metatags->keywords : 'ISTU gestão Transporte'}}">
  <meta name="author" content="i12 Sistemas - www.idoze.com.br">

  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans|Candal|Alegreya+Sans">
  <link rel="stylesheet" type="text/css" href="{{ asset('vendor/Mentor/css/font-awesome.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('vendor/Mentor/css/bootstrap.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('vendor/Mentor/css/imagehover.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('vendor/Mentor/css/style.css') }}">
  <!-- =======================================================
    Theme Name: Mentor
    Theme URL: https://bootstrapmade.com/mentor-free-education-bootstrap-theme/
    Author: BootstrapMade.com
    Author URL: https://bootstrapmade.com
    Muito obrigado!
  ======================================================= -->

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="user" content="''">

  <script src='https://www.google.com/recaptcha/api.js' async defer></script>

  
  <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/favicon/apple-icon-57x57.png') }}">
  <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('assets/favicon/apple-icon-60x60.png') }}">
  <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/favicon/apple-icon-72x72.png') }}">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/favicon/apple-icon-76x76.png') }}">
  <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/favicon/apple-icon-114x114.png') }}">
  <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('assets/favicon/apple-icon-120x120.png') }}">
  <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('assets/favicon/apple-icon-144x144.png') }}">
  <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets/favicon/apple-icon-152x152.png') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon/apple-icon-180x180.png') }}">
  <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('assets/favicon/android-icon-192x192.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/favicon/favicon-96x96.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon/favicon-16x16.png') }}">
  <link rel="manifest" href="{{ asset('assets/favicon/manifest.json') }}">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="{{ asset('assets/favicon/ms-icon-144x144.png') }}">
  <meta name="theme-color" content="#ffffff">

</head>

<body>
<div id="appusercontent">
  <!--Navigation bar-->
  <nav class="navbar navbar-default navbar-fixed-top hidden-print">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      <a class="navbar-brand" href="{{route('site.home')}}">i<span>Stu</span></a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav navbar-right">
            <li><a href="{{route('site.home')}}"><i class="fa fa-globe"></i> Ir para o site</a></li>
            @if($ajuda<>'')
            <li><a href="{{route('site.prematricula.ajuda')}}"><i class="fa fa-bookmark"></i> Saiba como funciona</a></li>
            @endif
       </ul>
      </div>
    </div>
  </nav>
  <!--/ Navigation bar-->
 <div class="container">
  @yield('content')
 </div>
   <!--Footer-->
  <footer id="footer" class="footer hidden-print">
    <div class="container text-center">

      
      
      <div class="credits">
        <!--
          All the links in the footer should remain intact.
          You can delete the links only if you purchased the pro version.
          Licensing information: https://bootstrapmade.com/license/
          Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/buy/?theme=Mentor
        -->
       <p>i<b>Stu</b> - Sistema de Transporte de Estudantes</p>
        <p style="font-size: small"><a href="http://www.idoze.com.br" target="_blank">i12 Sistemas.</a></p> 
        <p style="font-size: xx-small;"><small>©2016 Mentor Theme. All rights reserved</small></p>
      </div>
      
    </div>
  </footer>
  <!--/ Footer-->
</div>


<script src="{{ asset('vendor/Mentor/js/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/Mentor/js/jquery.easing.min.js') }}"></script>
<script src="{{ asset('vendor/Mentor/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor/Mentor/js/custom.js') }}"></script>
@if(config('app.env')=='production')
<script src="{{ asset('vendor/vuejs/vue.min.js') }}"></script>
@else
<script src="{{ asset('vendor/vuejs/vue.js') }}"></script>
@endif
<script>
  var userlogged = {
                    id: 0,
                    permissoes: []
                    };
  var base_url = "{!! url('/') !!}";   
</script>

<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>