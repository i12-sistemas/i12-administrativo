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

    <link rel="stylesheet" type="text/css" href="{{ asset('css/i12site.css') }}">

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
    <link rel="stylesheet" href="{{ asset('vendor/templatesite/css/responsive.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/templatesite/css/jquery.fancybox.css?v=2.1.5') }}" type="text/css" media="screen" />

    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjBsDFWE3FujoO34zdgNDD4zY2IZ_-Si8&region=GB"></script>
    <link href='http://fonts.googleapis.com/css?family=Lato:400,300' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,300,500' rel='stylesheet' type='text/css'>
  </head>
<body class="bodychat">
         
         

@yield('content')



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
        s1.src='https://embed.tawk.to/5a6a20d44b401e45400c65e9/1cd3oe401';
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