<template>
<div>
    <nav class="navbar navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <a href="/painelcliente" class="navbar-brand" style="margin-top: -5px;">
            <img :src="$root.url + '/assets/favicon/favicon-32x32.png'" class="img img-responsive" />
          </a>
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
            <i class="fa fa-bars"></i>
          </button>
        </div>
        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="/painelcliente/chamados/novo" aria-expanded="true"><i class="fa fa-plus" aria-hidden="true"></i> Novo chamado </a></li>
            <li><a href="/painelcliente/chamados" aria-expanded="true"><i class="fa fa-address-book" aria-hidden="true"></i> Chamados </a></li>
            <!-- <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-address-book" aria-hidden="true"></i> Faturas <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#"><i class="fa fa-address-book-o" aria-hidden="true"></i> A vencer</a></li>
                <li><a href="#"><i class="fa fa-address-book-o" aria-hidden="true"></i> Vencidos</a></li>
                <li><a href="#"><i class="fa fa-address-book-o" aria-hidden="true"></i> Pagas</a></li>
              </ul>              
            </li> -->
            <!--<li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-user" aria-hidden="true"></i> Conta<span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#"><i class="fa fa-file-text" aria-hidden="true"></i> Licenca de uso</a></li>
                <li><a href="#"><i class="fa fa-file-text" aria-hidden="true"></i> Contrato vigente</a></li>
              </ul>              
            </li>-->
            
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-support" aria-hidden="true"></i> Suporte<span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="http://www.idoze.com.br/suporte.php" target="_blank"><i class="fa fa-support" aria-hidden="true"></i> Suporte técnico</a></li>
                <li class="divider"></li>
                <li><a href="#"><i class="fa fa-envelope" aria-hidden="true"></i> suporte@idoze.com.br</a></li>
                <li><a href="tel:+551638513621"><i class="fa fa-phone" aria-hidden="true"></i> (16) 3851-3621</a></li>
                <li><a href="tel:+551638516983"><i class="fa fa-phone" aria-hidden="true"></i> (16) 3851-6983</a></li>
                <li><a href="https://api.whatsapp.com/send?phone=5516991624912" target="_blank"><i class="fa fa-whatsapp" aria-hidden="true"></i> (16) 9 9162-4912</a></li>
              </ul>
            </li>            
            <li><a href="/painelcliente/logout"><i class="fa fa-power-off" aria-hidden="true"></i> Sair</a></li>
          </ul>        
        </div>
      </div>
    </nav>
</div>
</template>
<script>
export default {
  data: () => ({}),
  mounted() {},
  methods: {},
  name: "app-painelcliente-navbar"
};
</script>