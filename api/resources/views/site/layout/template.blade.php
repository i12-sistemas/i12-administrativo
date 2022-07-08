<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="keywords" content="Bootstrap, Parallax, Template, Registration, Landing">
    <meta name="viewport" content="minimum-scale=1.0, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width, height=device-height" />
    <meta name="author" content="i12 Sistemas">
    <meta name="description" content="i12 Sistemas - Empresa de desenvolvimento de sistemas comerciais">
    <meta property="og:url"           content="http://www.idoze.com.br" />
    <meta property="og:type"          content="website" />
    <meta property="og:title"         content="i12 Sistemas" />
    <meta property="og:description"   content="i12 Sistemas - Empresa de desenvolvimento de sistemas comerciais" />
    <meta property="og:image"         content="http://www.idoze.com.br/images/logo2.png" />    
    <title>i12 Sistemas {{isset($title) ? ' - ' . $title : ''}}</title>

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

      <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-119245133-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-119245133-1');
  </script>
  
      <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
      <!-- CSS
      ================================================== -->
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('vendor/templatesite/css/bootstrap.min.css') }}"/>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="{{ asset('vendor/templatesite/css/font-awesome.min.css') }}"/>
    <!-- Animation -->
    <link rel="stylesheet" href="{{ asset('vendor/templatesite/css/animate.css') }}" />
    <!-- Owl Carousel -->
    <link rel="stylesheet" href="{{ asset('vendor/templatesite/css/owl.carousel.css') }}"/>
    <link rel="stylesheet" href="{{ asset('vendor/templatesite/css/owl.theme.css') }}"/>
    <!-- Pretty Photo -->
    <link rel="stylesheet" href="{{ asset('vendor/templatesite/css/prettyPhoto.css') }}"/>
    <!-- Main color style -->
    <link rel="stylesheet" href="{{ asset('vendor/templatesite/css/red.css') }}"/>
    <!-- Template styles-->
    <link rel="stylesheet" href="{{ asset('vendor/templatesite/css/custom.css') }}" />
    <!-- Responsive -->
    <link rel="stylesheet" href="{{ asset('vendor/templatesite/css/responsive.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/templatesite/css/jquery.fancybox.css?v=2.1.5') }}" type="text/css" media="screen" />


    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjBsDFWE3FujoO34zdgNDD4zY2IZ_-Si8&region=GB"></script>
    <link href='http://fonts.googleapis.com/css?family=Lato:400,300' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,300,500' rel='stylesheet' type='text/css'>
  </head>
<body data-spy="scroll" data-target=".navbar-fixed-top">
         
    @if(!(Request::query('hidetoolbar')=='1'))
    <header id="section_header" class="navbar-fixed-top main-nav" role="banner">
    	<div class="container">
    		<!-- <div class="row"> -->
                 <div class="navbar-header ">
                     <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="{{route('site.home')}}">
                            <img src="{{ asset('vendor/templatesite/images/logo2.png') }}" alt="">
                            
                        </a>
                 </div><!--Navbar header End-->
                 	<nav class="collapse navbar-collapse navigation" id="bs-example-navbar-collapse-1" role="navigation">
                        <ul class="nav navbar-nav navbar-right ">
                           	<li class="active"> <a href="#slider_part" class="page-scroll">Home </a></li>

                            <li>

                                <a href="{{route('site.suporte')}}" >Suporte</a>



							</li>
              <li><a href="{{route('site.teamviewer')}}" >Dowload Suporte</a></li>
                        </ul>
                     </nav>
                </div><!-- /.container-fluid -->
    </header>
    @endif


@yield('content')

<!-- Footer Area Start -->
@if(!(Request::query('hidetoolbar')=='1'))
<section id="footer">
    <div class="footer_top">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-4 col-xs-12">
                    <h3 class="menu_head">Contato</h3>
                    <div class="footer_menu">
                        <ul>
                            <li><i class="fa fa-home"></i>
                                <span> Rua das Rosas, 265 - Jardim Marina </span>
                            </li>
                            <li><span> CEP 14640-000 - Morro Agudo - SP </span></li>
                            <hr>
                            <li><a href="tel:+551638516983"><i class="fa fa-phone"></i><span> +55 (16) 3851-6983</span></a></li>
                            <li><a href="tel:+551638513621"><i class="fa fa-phone"></i><span> +55 (16) 3851-3621</span></a></li>
                            <li><a href="https://api.whatsapp.com/send?phone=5516991624912" target="_blank"><i class="fa fa-whatsapp"></i><span> +55 (16) 9 9162-4912</span></a></li>
                            <hr>
                            <li><i class="fa fa-clock-o"></i>
                                <span> Expediente </span>
                            </li>
                            <li><span>Segunda à Sexta das 08:00h às 18:00h</span>
                            <li><span>Sábado das 08:00h às 12:00h</span></li>

                        </ul>
                    </div>
                </div>
                <div class="col-md-3 col-sm-4 col-xs-12">
                    <h3 class="menu_head">Links Utéis</h3>
                    <div class="footer_menu">
                        <ul>
                            <li><a href="http://www.nfe.fazenda.gov.br/portal/principal.aspx">Nota Fiscal Eletrônica</a></li>
                            <li><a href="http://www.sintegra.gov.br">Sintegra</a></li>
                            <li><a href="http://www.fazenda.sp.gov.br/sat/">SEFAZ/SP</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-3 col-sm-4 col-xs-12">
                    <h3 class="menu_head">Redes Sociais</h3>
                    <div class="footer_menu">
                        <ul>
                            <li><a href="https://www.facebook.com/i12sistemas" target="_blank">Facebook</a></li>
                            <li><a href="https://www.linkedin.com/company/i12sistemas/"  target="_blank">LinkedIn</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-3 col-sm-12 col-xs-12 text-right">
                   <div class="fb-page" data-href="https://www.facebook.com/i12sistemas/" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/i12sistemas/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/i12sistemas/">I12 Sistemas</a></blockquote></div>
                </div>

            </div>
        </div>
    </div>

    <div class="footer_b">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="footer_bottom">
                        <p class="text-block"> &copy; Direitos Reservados</p>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="footer_mid pull-right">
                        <ul class="social-contact list-inline">
                            <li> <a href="https://www.facebook.com/i12sistemas/"><i class="fa fa-facebook"></i></a></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<!-- Footer Area End -->



    <!-- Back To Top Button -->
    <div id="back-top">
        <a href="#slider_part" class="scroll" data-scroll>
            <button class="btn btn-primary" title="Back to Top"><i class="fa fa-angle-up"></i></button>
        </a>
    </div>
    <!-- End Back To Top Button -->
    @endif

    <!-- Javascript Files
        ================================================== -->
        <!-- initialize jQuery Library -->

        <!-- initialize jQuery Library -->
            <!-- Main jquery -->
            <script type="text/javascript" src="{{ asset('vendor/templatesite/js/jquery.js') }}"></script>
            <!-- Bootstrap jQuery -->
            <script src="{{ asset('vendor/templatesite/js/bootstrap.min.js') }}"></script>
            <!-- Owl Carousel -->
            <script src="{{ asset('vendor/templatesite/js/owl.carousel.min.js') }}"></script>
            <!-- Isotope -->
            <script src="{{ asset('vendor/templatesite/js/jquery.isotope.js') }}"></script>
            <!-- Pretty Photo -->
            <script type="text/javascript" src="{{ asset('vendor/templatesite/js/jquery.prettyPhoto.js') }}"></script>
            <!-- SmoothScroll -->
            <script type="text/javascript" src="{{ asset('vendor/templatesite/js/smooth-scroll.js') }}"></script>
            <!-- Image Fancybox -->
            <script type="text/javascript" src="{{ asset('vendor/templatesite/js/jquery.fancybox.pack.js?v=2.1.5') }}"></script>
            <!-- Counter  -->
            <script type="text/javascript" src="{{ asset('vendor/templatesite/js/jquery.counterup.min.js') }}"></script>
            <!-- waypoints -->
            <script type="text/javascript" src="{{ asset('vendor/templatesite/js/waypoints.min.js') }}"></script>
            <!-- Bx slider -->
            <script type="text/javascript" src="{{ asset('vendor/templatesite/js/jquery.bxslider.min.js') }}"></script>
            <!-- Scroll to top -->
            <script type="text/javascript" src="{{ asset('vendor/templatesite/js/jquery.scrollTo.js') }}"></script>
            <!-- Easing js -->
            <script type="text/javascript" src="{{ asset('vendor/templatesite/js/jquery.easing.1.3.js') }}"></script>
          <!-- PrettyPhoto -->
            <script src="{{ asset('vendor/templatesite/js/jquery.singlePageNav.js') }}"></script>
            <!-- Wow Animation -->
            <script type="js/javascript" src="{{ asset('vendor/templatesite/js/wow.min.js') }}"></script>
          <!-- Custom js -->
            <script src="{{ asset('vendor/templatesite/js/custom.js') }}"></script>

            <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.7";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>



    <script type="text/javascript">
      var Tawk_API=Tawk_API||{
      }, Tawk_LoadStart=new Date();
      
    @if( !(Request::url()== url('/suporte')) || !(Request::url()== url('/chatintegrado')))  
        Tawk_API.embedded='tawk_5a6a20d44b401e45400c65e9';
    @endif
      (function(){
        var s1=document.createElement("script"),
        s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/5a6a20d44b401e45400c65e9/1c4net4jg';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
      })();

    
    Tawk_API.onLoad = function(){
        @if(!($tawkto->hash==''))
          Tawk_API.setAttributes({
              'name'  : '{!! $tawkto->nome !!}',
              'email' : '{!! $tawkto->email  !!}',
              'hash'  : '{!! $tawkto->hash !!}'
          }, function(error){});

          
            Tawk_API.addTags([
                @foreach ($tawkto->tags as $tag)
                    "{{$tag}}",
                @endforeach  
            ], function(error){ console.log("tags=" + error) });

        @endif

        @if( !(Request::url()== url('/suporte')) || !(Request::url()== url('/chatintegrado')))
          Tawk_API.minimize();
        @endif
    };
   </script>


@yield('js')


  </body>
</html>