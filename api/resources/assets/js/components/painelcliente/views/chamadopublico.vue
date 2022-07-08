<template>
<div>
<section class="content">
  <div class="box no-border no-shadow">
    <div class="box-body">
        <div class="row" v-if="!loading && loadingerror==''">
          <div class="alert text-success">
            <span class="h4">Acesso através de link enviado ao e-mail <b>{{email}}</b></span>
          </div>
        </div>       
        <div class="row" v-if="!loading && loadingerror!==''">
          <div class="alert alert-danger">
            <span class="h3"><i class="fa fa-warning"></i> {{loadingerror}}</span>
          </div>
        </div>        
        <chamadoviewer v-if="idcontato && !loading" :idhashmd5="idhashmd5" :idbase64="idbase64" :idcontato="idcontato"/>
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
    loading: true,
    loadingerror: "", 
    idhashmd5: null,
    idbase64: null,
    email: null,
    emailmd5: null,
    idcontato: null
  }),
  mounted() {
    var app = this;
    app.idbase64 = this.$route.params.idbase64;
    app.idhashmd5 = this.$route.params.idhashmd5;
    app.email = this.$helpers.base64dec(this.$route.params.email);
    app.emailmd5 = this.$route.params.emailmd5;
    console.log(app.emailmd5);

    app.onLoad();
  },
  methods: {
    async onLoad() {
      var app = this;
      app.loading = true;
      var content = {
        coluna: "email",
        search: app.email,
        emailmd5: app.emailmd5
      };
      var ret = await app.$helpers.sendPost(
        app,
        "/api/v1/painelcliente/contatos/find",
        content
      );
      app.loading = false;
      if (ret.ok) {
        app.idcontato = ret.rows.id;
      } else {
        app.loadingerror = ret.msg;
        console.log(ret);
      }
    }
  }
};
</script>