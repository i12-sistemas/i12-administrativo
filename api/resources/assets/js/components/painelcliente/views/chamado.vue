<template>
<div>
  <app-painelcliente-pageheader :title="title" :breadcrumb="breadcrumb" :breadcrumbactive="title" :activegoback="true"></app-painelcliente-pageheader>
<section class="content">
  <div class="box no-border no-shadow">
    <div class="box-body">
        <chamadoviewer v-if="idcontato" :idhashmd5="idhashmd5" :idbase64="idbase64" :idcontato="idcontato"/>
    </div>
  </div>
</section> 

  
</div>
</template>

<script>
import chamadoviewer from "../components/chamadoviewer.vue";
import pageheader from "../template/pageheader.vue";

export default {
  components: {
    chamadoviewer: chamadoviewer,
    "app-painelcliente-pageheader": pageheader
  },
  data: () => ({
    title:"Detalhe do chamado",
    breadcrumb: [{ desc: "chamados", url: "/painelcliente/chamados" }],
    breadcrumbactive: "",    
    idhashmd5: null,
    idbase64: null,
    idcontato: null
  }),
  mounted() {
    var app = this;
    app.idbase64 = this.$route.params.idbase64;
    app.idhashmd5 = this.$route.params.idhashmd5;
    app.idcontato = app.$root.logincliente.contato.id;
  }
};
</script>