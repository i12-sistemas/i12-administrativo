<template>
<div>
<section class="content">
  <div class="box no-border no-shadow">
      <h2><small>Olá,</small> <b>{{$root.logincliente.contato.nome}}</b></h2>
      <p>Bem vindo ao painel de controle</p>
      <p>Você esta logado pela empresa <b>{{$root.logincliente.cliente.nome}}</b></p>


    <div class="row">
       <h3><i class="fa fa-user"></i> Meus chamados</h3>
    </div>
    <div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box " v-bind:class="!contadores ? 'bg-gray-active' : (contadores.meuschamados.emabertos>0 ? 'bg-blue' : 'bg-gray-active')">
            <div class="inner">
              <h3>{{!contadores ? '-' : contadores.meuschamados.emabertos}}</h3>
              <p>Em abertos</p>
            </div>
            <div class="icon">
              <i class="fa fa-folder-open"></i>
            </div>
            <div v-if="!contadores ? true : contadores.meuschamados.emabertos <= 0" class="small-box-footer" >Nenhum chamado</div>
            <a href="painelcliente/chamados?status=A" v-if="!contadores ? false : contadores.meuschamados.emabertos > 0" class="small-box-footer">Saiba mais <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>


      
        <div class="col-lg-3 col-xs-6">

          <div class="small-box" v-bind:class="!contadores ? 'bg-gray-active' : (contadores.meuschamados.pendenteresposta>0 ? 'bg-red' : 'bg-gray-active')">
            <div class="inner">
              <h3>{{!contadores ? '-' : contadores.meuschamados.pendenteresposta}}</h3>
              <p>Pendente resposta</p>
            </div>
            <div class="icon">
              <i class="fa fa-commenting"></i>
            </div>
            <div v-if="!contadores ? true : contadores.meuschamados.pendenteresposta <= 0" class="small-box-footer" >Nenhum chamado</div>
            <a href="painelcliente/chamados?status=PR" v-if="!contadores ? false : contadores.meuschamados.pendenteresposta > 0" class="small-box-footer">Ver chamados <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow" >
            <div class="inner">
              <h3>{{!contadores ? '-' : contadores.meuschamados.naolidos}}</h3>
              <p>Não lidos</p>
            </div>
            <div class="icon">
              <i class="fa fa-eye-slash"></i>
            </div>
            <div v-if="!contadores ? true : contadores.meuschamados.naolidos <= 0" class="small-box-footer" >Nenhum chamado</div>
            <a href="painelcliente/chamados?status=NL" v-if="!contadores ? false : contadores.meuschamados.naolidos > 0" class="small-box-footer" >Ver chamados <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
 
    </div> 


    <div class="row" v-if="$helpers.checkpermissao($root.logincliente.contato.permissaopainel, '40000374')">
       <h3><i class="ion ion-stats-bars"></i> Chamados da {{$root.logincliente.cliente.nome}}</h3>
    </div>
    <div class="row" v-if="$helpers.checkpermissao($root.logincliente.contato.permissaopainel, '40000374')">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box" v-bind:class="!contadores ? 'bg-gray-active' : (contadores.empresachamados.emabertos>0 ? 'bg-primary' : 'bg-gray-active')">
            <div class="inner">
              <h3>{{!contadores ? '-' : contadores.empresachamados.emabertos}}</h3>
              <p>Em abertos</p>
            </div>
            <div class="icon">
              <i class="fa fa-folder-open"></i>
            </div>
            <div v-if="!contadores ? true : contadores.empresachamados.emabertos <= 0" class="small-box-footer" >Nenhum chamado</div>
            <a href="painelcliente/chamados?status=A&mostrartodosempresa=1" v-if="!contadores ? false : contadores.empresachamados.emabertos > 0"  class="small-box-footer">Saiba mais <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-xs-6">
          <div class="small-box "  v-bind:class="!contadores ? 'bg-gray-active' : (contadores.empresachamados.pendenteresposta>0 ? 'bg-red' : 'bg-gray-active')">
            <div class="inner">
              <h3>{{!contadores ? '-' : contadores.empresachamados.pendenteresposta}}</h3>
              <p>Pendente resposta</p>
            </div>
            <div class="icon">
              <i class="fa fa-commenting"></i>
            </div>
            <div v-if="!contadores ? true : contadores.empresachamados.pendenteresposta <= 0" class="small-box-footer" >Nenhum chamado</div>
            <a href="painelcliente/chamados?status=PR&mostrartodosempresa=1" v-if="!contadores ? false : contadores.empresachamados.pendenteresposta > 0" class="small-box-footer">Ver chamados <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>{{!contadores ? '-' : contadores.empresachamados.naolidos}}</h3>
              <p>Não lidos</p>
            </div>
            <div class="icon">
              <i class="fa fa-eye-slash"></i>
            </div>
            <div v-if="!contadores ? true : contadores.empresachamados.naolidos <= 0" class="small-box-footer" >Nenhum chamado</div>
            <a href="painelcliente/chamados?status=NL&mostrartodosempresa=1" v-if="!contadores ? false : contadores.empresachamados.naolidos > 0"  class="small-box-footer">Ver chamados <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
   
    </div> 
  </div>
</section>    
</div> 

</template>

<script>
export default {
  data: function() {
    return {
      breadcrumb: [{ desc: "configuração", url: "#" }],
      breadcrumbactive: "",
      mensagens: [],
      txtMsg: "",
      userloggedid: 0,
      areaselecionada: null,
      title: "",
      contadores: null
    };
  },
  mounted() {
    this.getDataContadores();
  },
  methods: {
    actgoback() {},
    async getDataContadores() {
      var app = this;
      Vue.nextTick(function() {
        app.loading = true;
      });
      var content = {
        pendenteresposta: app.filterpendenteresposta,
        search: app.txtsearch,
        idcontato: app.mostrartodosempresa
          ? -1
          : app.$root.logincliente.contato.id,
        situacao: app.filtersituacao,
        idcliente: app.$root.logincliente.cliente.codcliente,
        limit: app.pagesize
      };
      var url = "/api/v1/painelcliente/chamados/contadores";

      var ret = await app.$helpers.sendPost(app, url, content);

      Vue.nextTick(function() {
        app.loading = false;
      });
      if (ret.ok) {
        app.contadores = ret.rows;
      } else {
        alert(ret.msg);
        console.log(ret);
      }
    }
  }
};
</script>