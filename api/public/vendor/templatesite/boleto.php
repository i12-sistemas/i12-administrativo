<?php
require('header.php');
?>

<?php
$cnpj = '';
if(isset($_GET["cnpj"])){
  $cnpj=$_GET["cnpj"];
}


$fatura = '';
if(isset($_GET["fatura"])){
  $fatura=$_GET["fatura"];
}

$token = '';
if(isset($_GET["token"])){
  $token=$_GET["token"];
}
echo '<input type="hidden" id="cnpj" name="cnpj" value="' . $cnpj . '">';
echo '<input type="hidden" id="fatura" name="fatura" value="' . $fatura . '">';
echo '<input type="hidden" id="token" name="token" value="' . $token . '">';
?>


<section id="about-details">
  <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="feature_header text-center">
                            <h3 class="feature_title">DOWNLOAD DE <b>BOLETO</b></h3>

                            <h4 class="feature_sub" id="dadosboleto">
                              <div class="row">
                                  <div class="col-md-7 col-md-offset-5"><div class="loader centered" id="loading"></div></div>

                              </div>
                              <br>
                              <div class="row">Consultando dados do boleto...<div>

                            </h4>
                        </div>
                    </div>  <!-- Col-md-12 End -->
                </div>
</section>




		<!-- initialize jQuery Library -->
        <!-- Main jquery -->
		    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <!-- Bootstrap jQuery -->
         <script src="js/bootstrap.min.js"></script>
         <script src="js/boleto.js"></script>



     <?php
     require('bottom.php');
     ?>
