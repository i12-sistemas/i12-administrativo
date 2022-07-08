<?php
//include_once('conexaobd.php');
include_once('functions.php');
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
    <!-- Basic Page Needs
    ================================================== -->
        <meta charset="utf-8">
        <title>i12 Sistemas</title>
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
        <meta name="description" content="i12 Sistemas - Empresa de desenvolvimento de sistemas comerciais">

        <meta property="og:url"           content="http://www.idoze.com.br" />
        <meta property="og:type"          content="website" />
        <meta property="og:title"         content="i12 Sistemas" />
        <meta property="og:description"   content="i12 Sistemas - Empresa de desenvolvimento de sistemas comerciais" />
        <meta property="og:image"         content="http://www.idoze.com.br/images/logo2.png" />
        <!-- Mobile Specific Metas
    ================================================== -->
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
         <!-- CSS
         ================================================== -->
        <!-- Bootstrap -->
        <link rel="stylesheet" href="css/bootstrap.min.css"/>
        <!-- FontAwesome -->
        <link rel="stylesheet" href="css/font-awesome.min.css"/>
        <!-- Animation -->
        <link rel="stylesheet" href="css/animate.css" />
        <!-- Owl Carousel -->
        <link rel="stylesheet" href="css/owl.carousel.css"/>
        <link rel="stylesheet" href="css/owl.theme.css"/>
        <!-- Pretty Photo -->
        <link rel="stylesheet" href="css/prettyPhoto.css"/>
        <!-- Main color style -->
        <link rel="stylesheet" href="css/red.css"/>
        <!-- Template styles-->
        <link rel="stylesheet" href="css/custom.css" />
        <!-- Responsive -->
        <link rel="stylesheet" href="css/responsive.css" />
        <link rel="stylesheet" href="css/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />


        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjBsDFWE3FujoO34zdgNDD4zY2IZ_-Si8&region=GB"></script>
        <link href='http://fonts.googleapis.com/css?family=Lato:400,300' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Raleway:400,300,500' rel='stylesheet' type='text/css'>
    </head>

 <body data-spy="scroll" data-target=".navbar-fixed-top">
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->


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
                        <a class="navbar-brand" href="../">
                            <img src="images/logo2.png" alt="">
                            <div class="fb-like" data-href="https://www.facebook.com/i12sistemas/" data-layout="button_count" data-action="like" data-size="small" data-show-faces="true" data-share="true"></div>
                        </a>
                 </div><!--Navbar header End-->
                 	<nav class="collapse navbar-collapse navigation" id="bs-example-navbar-collapse-1" role="navigation">
                        <ul class="nav navbar-nav navbar-right ">
                           	<li class="active"> <a href="#slider_part" class="page-scroll">Home </a></li>


                            <li>
<?php

        $retChat = GetStatusChati12();
        if($retChat["online"]){

          echo '<a href="javascript:janelaSecundaria(\'http://idoze.mysuite1.com.br/atendimento.php?inf=\')" ><img src="images\chat32.png" border="0" style="
    padding-right: 9px;">Suporte Online</img></a>';

        }else {
          echo '<a href="javascript:janelaSecundaria(\'http://idoze.mysuite1.com.br/atendimento.php?inf=\')" ><img src="images\chatoffline32.png" border="0" style="
    padding-right: 9px;">Suporte Offline</img></a>';
        };
?>


							</li>
              <li><a href="https://get.teamviewer.com/i12suporte" target="_blank">Dowload Suporte</a></li>
                        </ul>
                     </nav>
                </div><!-- /.container-fluid -->
</header>





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
                                    <h4>(16) 3851-3621</h4>
                                    <p>Expediente:</p>
                                    <p>Segunda à Sexta das 08:00h às 18:00h</p>
                                    <p>Sábado das 08:00h às 12:00h</p>
                                </div>
                            </div>
                        <div class="col-md-4 col-xs-12 col-sm-6">
                                <div class="feature_content">
<?php
if($retChat["online"]){
?>
  <i class="fa fa-comments"></i>
  <h5 >Chat Online</h5>
  <a href="javascript:janelaSecundaria('http://idoze.mysuite1.com.br/atendimento.php?inf=')"  >Suporte Online</a>

<?php
}else {
?>
  <i class="fa fa-envelope"></i>
  <h5>Chat offline<br>Contato por e-mail</h5>
  <p id="emailcontato"></p>
<?php
};
?>



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
<!-- Footer Area Start -->

<section id="footer">
    <div class="footer_top">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <h3 class="menu_head">Contato</h3>
                    <div class="footer_menu_contact">
                        <ul>
                            <li> <i class="fa fa-home"></i>
                                <span> Rua das Rosas, 265 - Jardim Marina </span></li>
                            <li><span> CEP 14640-000 - Morro Agudo - SP </span></li>
                            <li><i class="fa fa-globe"></i>
                                <span> +55 (16) 3851-3621</span></li>
                            <li><i class="fa fa-phone"></i>
                                <span> contato@idoze.com.br</span></li>
                            <li><i class="fa fa-map-marker"></i>
                            <span> www.idoze.com.br</span></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <h3 class="menu_head">Links Utéis</h3>
                    <div class="footer_menu">
                        <ul>
                            <li><a href="http://www.nfe.fazenda.gov.br/portal/principal.aspx">Nota Fiscal Eletrônica</a></li>
                            <li><a href="http://www.sintegra.gov.br">Sintegra</a></li>
                            <li><a href="http://www.fazenda.sp.gov.br/sat/">SEFAZ/SP</a></li>
                        </ul>
                    </div>
                </div>


                <div class="col-md-6 col-sm-6 col-xs-12 text-right">
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

<!--Inicio mySuite Chat Script-->
<script type="text/javascript">
window.$mysuite||function(t,e){var c=$mysuite=function(t){c._.push(t)},s=c.s=t.createElement(e),a=t.getElementsByTagName(e)[0];c.set=function(t){c.set._.push(t)},c._=[],c.set._=[],s.async=!0,s.setAttribute("charset","utf-8"),s.src="http://idoze.mysuite1.com.br/client/cf/?h=593ef1e10f3c2c919f7b5f7653ee572c",c.t=+new Date,s.type="text/javascript",a.parentNode.insertBefore(s,a)}(document,"script");
</script>
<!--Fim mySuite Chat Script-->



<!-- Javascript Files
    ================================================== -->
    <!-- initialize jQuery Library -->

		<!-- initialize jQuery Library -->
        <!-- Main jquery -->
		    <script type="text/javascript" src="js/jquery.js"></script>
        <!-- Bootstrap jQuery -->
         <script src="js/bootstrap.min.js"></script>
        <!-- Owl Carousel -->
        <script src="js/owl.carousel.min.js"></script>
        <!-- Isotope -->
        <script src="js/jquery.isotope.js"></script>
        <!-- Pretty Photo -->
		    <script type="text/javascript" src="js/jquery.prettyPhoto.js"></script>
        <!-- SmoothScroll -->
        <script type="text/javascript" src="js/smooth-scroll.js"></script>
        <!-- Image Fancybox -->
        <script type="text/javascript" src="js/jquery.fancybox.pack.js?v=2.1.5"></script>
        <!-- Counter  -->
        <script type="text/javascript" src="js/jquery.counterup.min.js"></script>
        <!-- waypoints -->
        <script type="text/javascript" src="js/waypoints.min.js"></script>
        <!-- Bx slider -->
        <script type="text/javascript" src="js/jquery.bxslider.min.js"></script>
        <!-- Scroll to top -->
        <script type="text/javascript" src="js/jquery.scrollTo.js"></script>
        <!-- Easing js -->
        <script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
   		 <!-- PrettyPhoto -->
        <script src="js/jquery.singlePageNav.js"></script>
      	<!-- Wow Animation -->
        <script type="js/javascript" src="js/wow.min.js"></script>
        <!-- Google Map  Source -->
        <script type="text/javascript" src="js/gmaps.js"></script>
			 <!-- Custom js -->
        <script src="js/custom.js"></script>
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
          icon: "images/map1.png"
        }
		);
      	</script>

        <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.7";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

    </body>
</html>
