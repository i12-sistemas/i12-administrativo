<?php
//include_once('conexaobd.php');
include_once('functions.php');

$activepage = $_SERVER['PHP_SELF'];


$suporte_tags = array();
if(isset($_GET["suporte_tags"])){
  $suporte_tags=$_GET["suporte_tags"];
  if ($suporte_tags<>''){
    $suporte_tags=explode(";", $suporte_tags);
  }
}

$suporte_nome = '';
if(isset($_GET["suporte_nome"])){
  $suporte_nome=$_GET["suporte_nome"];
}

$suporte_email = '';
if(isset($_GET["suporte_email"])){
  $suporte_email=$_GET["suporte_email"];
}


$suporte_hash='';
if (!($suporte_email=='')) {
  $suporte_apikey = 'c37cb8ec5ea03087afbec3b25fdb0aac38846ebe';
  $suporte_hash = hash_hmac("sha256", $suporte_email, $suporte_apikey);
  if ($suporte_nome=='') {
      $suporte_nome = $suporte_email;
  }
}

$suporte_telefones = array();
if(isset($_GET["suporte_telefones"])){
  $suporte_telefones= $_GET["suporte_telefones"];
  $suporte_telefones=explode(";", $suporte_telefones);
}

$suporte_razaosocial = '';
if(isset($_GET["suporte_razaosocial"])){
  $suporte_razaosocial=$_GET["suporte_razaosocial"];
}

$suporte_cnpj = '';
if(isset($_GET["suporte_cnpj"])){
  $suporte_cnpj=$_GET["suporte_cnpj"];
}

if (!($suporte_razaosocial=='')){
  $suporte_tags[] =  $suporte_razaosocial;
}
if (!($suporte_cnpj=='')){
  $suporte_tags[] =  $suporte_cnpj;
}

if (count($suporte_telefones)>0) {
  foreach ($suporte_telefones as $key => $telefone) {
    if ($telefone<>'') {
        $suporte_tags[] = $telefone;
    }
  }
}





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
        <meta name="viewport" content="minimum-scale=1.0, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width, height=device-height" />

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

                                <a href="/suporte.php" >Suporte Online</a>



							</li>
              <li><a href="https://get.teamviewer.com/i12suporte" target="_blank">Dowload Suporte</a></li>
                        </ul>
                     </nav>
                </div><!-- /.container-fluid -->
</header>
