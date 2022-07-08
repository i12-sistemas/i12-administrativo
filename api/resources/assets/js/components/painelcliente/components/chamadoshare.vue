<template>
<div>
    <div v-if="loading">
        <div class="alert text-center text-primary h3"><i class="fa fa-refresh fa-spin"></i> <b> carregando...</b></div>
    </div>      

    <div v-if="!loading && (chamado ? chamado.id<=0 : true )">
      <div>Nenhum chamado encontrado.</div>
      <hr>
      <p class="text-green" @click="onClose()"><button class="btn btn-default"><i class="fa fa-times"></i> Clique aqui para sair</button></p>
    </div>   

    <transition name="fade">
    <div v-if="!loading && (chamado ? chamado.id>0 : false )">
      <div class="row">
        <div class="col-xs-4">Número do chamado</div>
        <div class="col-xs-8"><b>{{chamado.id}}</b></div>
      </div>
      <div class="row">
        <div class="col-xs-4">Responsável</div>
        <div class="col-xs-8"><b>{{chamado.contato.nome}}</b></div>
      </div>      
      
      <table class="table table-striped" style="margin: 20px 0">
        <tr>
          <th>E-mail</th>
          <th style="width: 160px;min-width: 160px;"></th>
        </tr>
        <tr v-for="(email, key) in chamado.contato.emails" :key="key">
          <td>{{email.email}}</td>
          <td>
            <button :ref="'btnsend' + key" class="btn btn-default btn-xs" @click="sendMail(email.email, key)" :disabled="posting">
              <span><i class="fa fa-envelope"></i> E-mail</span>
            </button>
            <button class="btn btn-default btn-xs" v-clipboard:copy="email.urlpublico" v-clipboard:success="clipboard_onCopy" v-clipboard:error="clipboard_onError"><i class="fa fa-clone"></i> Copiar url</button>
          </td>
          
        </tr>
      </table>
 


        <!-- <tr v-if="perguntas[perguntaordem].permiteresplivre==1">
          <td colspan="2">
            <div class="form-group">
              <textarea class="form-control" rows="2" v-model="txtmensagem" 
                :placeholder="(perguntas[perguntaordem].obrigatorioresplivre==1 ?'**Obrigatório** ': 'Opcional - ') + 'Descreva livremente sua opinião aqui...'"  :disabled="posting"></textarea>
            </div>
          </td>
        </tr>         -->
        
    </div>
    </transition>

  
  
    <!-- assunto -->
    <div class="row" v-if="!loading && (chamado ? chamado.id>0 : false )">
      <div class="col-xs-12">
        <button class="btn btn-default pull-right" @click="onClose()"><i class="fa fa-times"></i> Fechar</button>
      </div>

    </div>     




   
</div>
</template>

<script>
import pageheader from "../template/pageheader.vue";

export default {
  components: {
    "app-painelcliente-pageheader": pageheader
  },
  props: ["idhashmd5", "idbase64"],
  data: () => ({
    posting: false,
    currentrating: 0,
    ratingscore: 0,
    perguntaordem: 0,
    id: null,
    loading: true,
    perguntas: null,
    chamado: null
  }),
  mounted() {
    var app = this;
    setTimeout(function() {
      app.onLoad();
    }, 100);
  },
  methods: {
    async sendMail(email, key) {
      var app = this;
      app.posting = true;

      var bt = this.$refs["btnsend" + key];
      jQuery(bt)
        .addClass("btn-warning")
        .removeClass("btn-default")
        .html('<span><i class="fa fa-spinner fa-spin"></i> Enviando</span>');

      var content = {
        id: app.id,
        hash: app.idhashmd5,
        email: email
      };

      var url = "/api/v1/painelcliente/chamados/sendmailchamado";
      var ret = await app.$helpers.sendPost(app, url, content);
      if (!ret.ok) {
        jQuery(bt)
          .addClass("btn-default")
          .removeClass("btn-warning")
          .html('<span><i class="fa fa-envelope"></i> E-mail</span>');
        alert(ret.msg);
        console.log(ret);
      } else {
        jQuery(bt)
          .addClass("btn-success")
          .removeClass("btn-warning")
          .html('<span><i class="fa fa-check"></i> Enviado</span>');
      }
      app.posting = false;
    },
    clipboard_onCopy: function(e) {
      alert("URL copiada para área de transferência.\n" + e.text);
    },
    clipboard_onError: function(e) {
      alert("Falha ao copiar!.\n" + e.text);
    },
    onClose() {
      this.$emit("closeshare");
    },

    onLoad() {
      var app = this;
      app.id = this.$helpers.base64dec(app.idbase64);
      this.getdata();
    },

    async getdata() {
      var app = this;
      Vue.nextTick(function() {
        app.loading = true;
      });

      var content = {
        id: app.id,
        hash: app.idhashmd5
      };
      var url = "/api/v1/painelcliente/chamados/showdetalhe";
      var ret = await app.$helpers.sendPost(app, url, content);
      Vue.nextTick(function() {
        app.loading = false;
      });
      if (ret.ok) {
        app.chamado = ret.rows;
        if (app.chamado) {
          if (app.chamado.id > 0) {
            app.showavaliacao =
              app.chamado.situacao == "Encerrada" && app.chamado.avaliado == 0;
          }
        }
      } else {
        app.loadingerror = ret.msg;
        alert(ret.msg);
        console.log(ret);
      }
    }
  }
};
</script>