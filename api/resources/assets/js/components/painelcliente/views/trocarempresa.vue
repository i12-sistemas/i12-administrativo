<template>
<div>
<app-painelcliente-pageheader :title="title" :breadcrumb="breadcrumb" :breadcrumbactive="title" activegoback="false"></app-painelcliente-pageheader>
<section class="content">
  <div class="box">
    <div class="box-body">
    <div class="row">
       <div class="col-xs-12" >
        <p class="text-left text-bold">Selecione uma empresa:</p>
      </div>
    </div>
    <div class="row" v-for="(empresa, key) in empresaslist" :key="key" >
        <div class="col-xs-12" >
            <button type="button" class="btn btn-link" @click="sendSetEmpresa(empresa.codcliente)">{{empresa.nome}}</button>
        </div>
    </div>    
    </div>
  </div>
</section> 

  
</div>
</template>

<script>
import pageheader from "../template/pageheader.vue";

export default {
  components: {
    "app-painelcliente-pageheader": pageheader
  },
  data: () => ({
    title: "Troca de empresa",
    breadcrumb: [],
    breadcrumbactive: "",
    empresaslist: []
  }),
  mounted() {
    var app = this;
    app.listempresas();
  },
  methods: {
    async listempresas() {
      var app = this;
      var content = {
        idcontato: app.$root.logincliente.contato.id
      };
      var ret = await app.$helpers.sendPost(
        app,
        "/painelcliente/login/listempresas",
        content
      );
      if (ret.ok) {
        app.empresaslist = ret.rows;
      } else {
        alert(ret.msg);
        console.log(ret);
      }
    },
    async sendSetEmpresa(idempresa) {
      var app = this;
      var content = {
        idempresa: idempresa
      };
      var ret = await app.$helpers.sendPost(
        app,
        "/painelcliente/login/setempresa",
        content
      );
      if (ret.ok) {
        window.location.href = "/painelcliente";
      } else {
        alert(ret.msg);
        console.log(ret);
      }
    }
  },
  name: "view-login"
};
</script>