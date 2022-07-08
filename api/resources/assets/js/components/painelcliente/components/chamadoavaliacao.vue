<template>
<div>
    <div v-if="loading">
        <div class="alert text-center text-primary h3"><i class="fa fa-refresh fa-spin"></i> <b> carregando...</b></div>
    </div>      
    <div v-if="!loading && !finalizado && (perguntas ? perguntas.length>0 : false )">
      <div><b>{{contato.nome}}</b>, sua avaliação é muito importante, como ela melhoramos nossos processos de atendimento e solução.</div>

    </div>
    <div v-if="!loading && !finalizado && (perguntas ? perguntas.length<=0 : true )">
      <div>Nenhuma pergunta de avaliação disponível.</div>
      <hr>
      <p class="text-green" @click="onClose()"><button class="btn btn-default"><i class="fa fa-times"></i> Clique aqui para sair</button></p>
    </div>   

    <transition name="fade">
    <div v-if="finalizado">
      <p class="h1 text-green"><i class="fa fa-check-circle"></i> Muito obrigado por sua avaliação!</p>
      <hr>
      <p class="text-green" @click="onClose()"><button class="btn btn-success"><i class="fa fa-times"></i> Clique aqui para sair</button></p>
    </div>
    <div v-if="mostrapergunta && !finalizado">
      <table class="table table-icom" v-if="perguntas ? perguntas.length>0 : false && perguntaordem>0">
        <tbody>
        <tr>
          <td>
            <div class="row">
              <div class="col-xs-12 col-sm-5">
                <div style="font-size: larger;font-weight: bold;color: rgb(51, 51, 51);">{{perguntas[perguntaordem].pergunta }}</div>
              </div>
              <div class="col-xs-12 col-sm-4">
                <div @mouseleave="oncurrentrating(ratingscore)">
                <star-rating v-bind:increment="1" v-bind:max-rating="perguntas[perguntaordem].qtdeopcao" v-model="ratingscore"
                inactive-color="#ccc" active-color="#eece48" v-bind:star-size="40"
                @rating-selected="onratingselected"
                @current-rating="oncurrentrating" :rounded-corners="true" :show-rating="false" />
                
                </div>
              </div>
              <div class="col-xs-12 col-sm-3">
                <div style="font-size: x-large;font-weight: bold;color: #333;padding: 7px 4px 0px 7px;">{{perguntas[perguntaordem]['opcao'+currentrating]}}</div>
              </div>
            </div>
              
          </td>

        </tr>        
        <tr v-if="perguntas[perguntaordem].permiteresplivre==1">
          <td colspan="2">
            <div class="form-group">
              <textarea class="form-control" rows="2" v-model="txtmensagem" 
                :placeholder="(perguntas[perguntaordem].obrigatorioresplivre==1 ?'**Obrigatório** ': 'Opcional - ') + 'Descreva livremente sua opinião aqui...'"  :disabled="posting"></textarea>
            </div>
          </td>
        </tr>        
        </tbody>      
      </table>
    </div>
    </transition>

  
  
    <!-- assunto -->
    <div class="row" v-if="!loading && (!finalizado) && (perguntas ? perguntas.length >0 : false )">
      <div class="col-xs-12">
        <button class="btn btn-primary" @click="actNextQuestion"><i class="fa fa-arrow-circle-right"></i> Continuar {{(perguntaordem+1) + ' de ' + perguntas.length   }}</button>
        <button class="btn btn-default pull-right" @click="onClose()"><i class="fa fa-times"></i> Fechar</button>
      </div>

    </div>     




   
</div>
</template>

<script>
import pageheader from "../template/pageheader.vue";
// https://vuejsfeed.com/blog/star-rating-component-for-vue-2
import StarRating from "vue-star-rating";

export default {
  components: {
    "app-painelcliente-pageheader": pageheader,
    StarRating
  },
  props: ["idhashmd5", "idbase64", "contato"],
  data: () => ({
    mostrapergunta: false,
    currentrating: 0,
    ratingscore: 0,
    perguntaordem: 0,
    id: null,
    loading: true,
    posting: false,
    perguntas: null,
    txtmensagem: ""
  }),
  computed: {
    finalizado() {
      var app = this;
      var r = false;
      if (app.perguntas) {
        if (app.perguntas.length > 0) {
          r = app.perguntaordem > app.perguntas.length - 1;
        }
      }
      return r;
    }
  },
  mounted() {
    var app = this;
    setTimeout(function() {
      app.onLoad();
    }, 100);
  },
  methods: {
    onClose() {
      this.$emit("closeavaliacao", this.finalizado);
    },
    onratingselected: function(rating) {
      var app = this;
      if (
        app.perguntas[app.perguntaordem].obrigatorioresplivre == 1 &&
        app.txtmensagem == ""
      )
        return;

      app.actNextQuestion();
    },
    oncurrentrating: function(rating) {
      var app = this;
      app.currentrating = rating;
    },
    async onLoad() {
      var app = this;
      app.id = this.$helpers.base64dec(app.idbase64);
      var ret = await app.getdata();
      if (ret.ok) {
        if (app.perguntas.length <= 0) app.onClose();
      }
    },
    async actNextQuestion() {
      var app = this;

      if (!app.perguntas) return;
      if (app.perguntas.length <= 0) return;
      if (app.finalizado) return;
      // se for obrigatorio
      if (
        app.perguntas[app.perguntaordem].obrigatorioresplivre == 1 &&
        app.txtmensagem == ""
      ) {
        alert("Descreva sua avaliação.");
        return;
      }
      if (app.ratingscore <= 0) {
        alert("Por favor clique na estrela para fazer sua avaliação.");
        return;
      }

      var ret = await app.sendPostAvaliacao();
      if (ret.ok) {
        app.mostrapergunta = false;
        app.perguntaordem += 1;
        app.txtmensagem = "";
        app.ratingscore = 0;
        app.currentrating = 0;

        setTimeout(function() {
          if (app.finalizado) {
            app.mostrapergunta = false;
          } else {
            app.mostrapergunta = true;
          }
        }, 100);
      } else {
        alert(ret.msg);
      }
    },

    async sendPostAvaliacao() {
      var app = this;
      Vue.nextTick(function() {
        app.posting = true;
      });

      var content = {
        idchamado: app.id,
        idcontato: app.contato.id,
        idpergunta: app.perguntas[app.perguntaordem].id,
        valorresp: app.ratingscore,
        resplivre: app.txtmensagem,
        finalizar: app.perguntaordem == app.perguntas.length - 1 ? 1 : 0
      };
      var url = "/api/v1/painelcliente/chamados/avaliachamado";
      var ret = await app.$helpers.sendPost(app, url, content);
      Vue.nextTick(function() {
        app.posting = false;
      });
      return ret;
    },
    async getdata() {
      var app = this;
      Vue.nextTick(function() {
        app.loading = true;
      });
      var content = {
        modo: "OS",
        idchamado: app.id
      };
      var url = "/api/v1/painelcliente/chamados/avaliaperguntalist";
      var ret = await app.$helpers.sendPost(app, url, content);
      Vue.nextTick(function() {
        app.loading = false;
        app.perguntaordem = 0;
        app.mostrapergunta = true;
      });
      if (ret.ok) {
        app.perguntas = ret.rows;
      } else {
        console.log(ret);
      }
      return ret;
    }
  }
};
</script>