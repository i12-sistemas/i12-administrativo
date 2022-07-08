<template>
<div>
<app-painelcliente-pageheader :title="title" :breadcrumb="breadcrumb" :breadcrumbactive="title" :activegoback="true"></app-painelcliente-pageheader>
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-sm-8" >
          <div class="btn-group">
            <a href="/painelcliente/chamados/novo" class="btn btn-default" ><i class="fa fa-plus" ></i> <span class="hidden-xs">Novo</span></a>
            <div class="btn-group">
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                Status <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li v-bind:class="filterstatus=='A' ? 'active' : ''"><a href="javascript:void(0);" @click="setFilterStatus('A')">Em abertos</a></li>
                <li v-bind:class="filterstatus=='E' ? 'active' : ''"><a href="javascript:void(0);" @click="setFilterStatus('E')">Encerrados</a></li>
                <li v-bind:class="filterstatus=='' ? 'active' : ''"><a href="javascript:void(0);" @click="setFilterStatus('')">Todos</a></li>
                <li class="divider"></li>
                <li v-bind:class="filterstatus=='NL' ? 'active' : ''"><a class="disabled" href="javascript:void(0);" @click="setFilterStatus('NL')">Não lidos</a></li>
                <li v-bind:class="filterstatus=='PR' ? 'active' : ''"><a href="javascript:void(0);" @click="setFilterStatus('PR')">Pendente resposta</a></li>
              </ul>
            </div>
            <button class="btn" v-bind:class="mostrartodosempresa ? 'btn-info' : 'btn-default'" 
              @click="actMostrarTodos" 
              v-if="$helpers.checkpermissao($root.logincliente.contato.permissaopainel, '40000374')"
              >
                <i class="fa" v-bind:class="mostrartodosempresa ? 'fa-globe' : 'fa-user-secret'"></i> Todas da empresa
            </button>
            
            <div class="input-group col-xs-12 col-sm-4">
                <input type="text"  @change="onNewSearch()" v-model="txtsearch" class="form-control" placeholder="por número ou assunto">
                <span class="input-group-btn">

                  <button type="button" @click="onNewSearch()" class="btn btn-default btn-flat" :disabled="loading"><i class="fa fa-refresh fa-spin" v-if="loading"></i><i class="fa fa-search" v-if="!loading"></i></button>
                  <button type="button" @click="txtsearch=''; onNewSearch();" class="btn btn-default btn-flat" :disabled="loading"><i class="fa fa-times" ></i></button>
                </span>
            </div>
          </div>
        </div>
        <div class="col-xs-12 col-sm-4">
          <div class="text-right">
            <div is="uib-pagination" @change="listchamados()" :max-size="6" :items-per-page="pagesize" :total-items="paginationtotal" v-model="pagination" class="pagination-sm" style="margin:0px" first-text="<<" previous-text="<" next-text=">" last-text=">>"></div>
            <div><small>{{ $helpers.paginationInfo(pagination.currentPage, pagesize, paginationtotal) }}</small></div>
            
          </div>
        </div>        
      </div>      
    </div>
    <div class="box-body">
    <div class="row" v-if="loading">
        <div class="alert text-center text-primary h3"><i class="fa fa-refresh fa-spin"></i> <b> carregando...</b></div>
    </div>
    <div class="row" >
        <div class="col-xs-12" >
            
            <table class="table table-bordered table-striped">
              <tbody>
                <tr v-for="(chamado, key) in chamados" :key="key" >
                  <td class="tdtable">
                    <div class="row">
                      <div class="col-md-1 text-center" v-bind:class="chamado.situacao=='Aberta' ? 'text-' + getStatus(chamado.situacao, chamado.fase, chamado.naolido).cor : 'text-' + getStatus(chamado.situacao, chamado.fase, chamado.naolido).cor  ">
                        <div class="col-md-12 text-center">#{{chamado.id}}</div>
                        <div class="col-md-12 text-center" >
                          <i class="fa fa-2x" v-bind:class="chamado.situacao=='Aberta' ? 'fa-folder-open' : 'fa-check-circle'  "></i>
                        </div>
                        <div class="col-md-12 text-center">{{chamado.situacao=='Aberta' ? 'Aberto' : 'Encerrado'  }}</div>
                      </div>
                      <div class="col-md-9">
                        <div class="col-md-12">
                          <a class="link title"  v-bind:class="chamado.naolido==1 ? 'text-black' : 'text-' + getStatus(chamado.situacao, chamado.fase, chamado.naolido).cor"
                          :href="'/painelcliente/chamado/show/' + $helpers.base64enc(chamado.id) + '/' + $helpers.md5(chamado.id + chamado.dtabertura + 'OS')"
                          :title="chamado.problemareclamado">
                            <span v-bind:class="chamado.naolido==1 ?'text-bold' : ''" >{{chamado.problemareclamado.trunc(150,true)}}</span>
                        </a>
                        <span class="label bg-yellow pull-right" v-if="chamado.naolido" title="Não lido"><i class="fa fa-eye-slash fa-2x"></i></span>
                        </div>
                        <div class="col-md-12">
                            Aberto em {{$helpers.dateToBR(chamado.dtabertura)}}
                            <span v-if="(chamado.situacao!='Aberta') && chamado.dtfechamento"> - Encerrado em {{$helpers.dateToBR(chamado.dtfechamento)}}</span>
                        </div>
                        <div class="col-md-12" v-if="mostrartodosempresa">
                          Quem registrou: <span class="text-ellipsis" v-bind:class="chamado.contato.id==$root.logincliente.contato.id ? 'text-bold' : '' ">
                            {{chamado.contato.id==$root.logincliente.contato.id ? 'Eu' :  chamado.contato.nome }}
                          </span>
                        </div>                        
                        <div class="col-md-12" v-if="chamado.fase ? chamado.fase.descricaocliente : false">
                            <span v-bind:class="chamado.fase.pendentecliente=='1' ? 'bg-danger' : '' ">
                            Fase: <span>{{chamado.fase.descricaocliente }}</span>
                            </span>
                        </div>  
                      </div>
                      
                      <div class="col-md-2 text-right">
                        <div class="btn-group-vertical">
                          <a class="btn btn-block btn-social" v-bind:class="getStatus(chamado.situacao, chamado.fase, chamado.naolido).btn" :href="'/painelcliente/chamado/show/' + $helpers.base64enc(chamado.id) + '/' + $helpers.md5(chamado.id + chamado.dtabertura + 'OS')"><i class="fa fa-info"></i>Ler chamado</a>
                          <button type="button" class="btn btn-block btn-social btn-default" @click="actShowShare(chamado.id, chamado.dtabertura)" ><i class="fa fa-share-alt"></i>Compartilhar </button>
                        </div>                        
                        
                      </div>
                    </div>                    
                  </td>
                </tr>
              </tbody>
            </table>
       
       
        </div>
    </div>    
    </div>
  </div>
</section> 

  <!-- share -->
  <transition name="modal" v-if="showsharing" >
  <div class="modal modal-mask" id="modal-default"  style="display: block; padding-right: 17px;">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="onCloseShare()">
            <span aria-hidden="true">×</span></button>
          <h4 class="modal-title"><i class="fa fa-share-alt"></i> Link de compartilhamento</h4>
        </div>        
          <div class="modal-body modal-body-scroll">
              <chamadoshare :idhashmd5="avaliacaoselidhashmd5" :idbase64="avaliacaoselidbase64" v-on:closeshare="onCloseShare"/>
          </div>
          <!-- <div class="modal-footer">
              <button type="button" class="btn btn-default" @click="closeApontamentos()"> Fechar </button>
          </div> -->
      </div>
  </div>
  </div>
  </transition>    
  <!-- share -->    

  <!-- avaliacao -->
  <transition name="modal" v-if="showavaliacao" >
  <div class="modal modal-mask"  style="display: block">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-body modal-body-scroll">
              <chamadoavaliacao :idhashmd5="avaliacaoselidhashmd5" :idbase64="avaliacaoselidbase64" v-on:closeavaliacao="onCloseAvaliacao"/>
          </div>
          <!-- <div class="modal-footer">
              <button type="button" class="btn btn-default" @click="closeApontamentos()"> Fechar </button>
          </div> -->
      </div>
  </div>
  </div>
  </transition>    
  <!-- avaliacao -->    

</div>
</template>

<script>
import pageheader from "../template/pageheader.vue";
import chamadoavaliacao from "../components/chamadoavaliacao.vue";
import chamadoshare from "../components/chamadoshare.vue";

export default {
  components: {
    "app-painelcliente-pageheader": pageheader,
    chamadoavaliacao: chamadoavaliacao,
    chamadoshare: chamadoshare
  },

  data: () => ({
    txtsearch: "",
    loading: true,
    title: "Listagem de chamados",
    breadcrumb: [],
    breadcrumbactive: "",
    chamados: [],
    filterstatus: "A",
    mostrartodosempresa: false,
    filtersituacao: "Aberta",
    filterpendenteresposta: -1,
    filternaolido: -1,
    pagesize: 15,
    pagination: { currentPage: 0 },
    paginationtotal: 0,
    showavaliacao: false,
    avaliacaoselidhashmd5: null,
    avaliacaoselidbase64: null,
    showsharing: false
  }),
  mounted() {
    var app = this;

    if (this.$route.query.status) {
      app.setFilterStatus(this.$route.query.status, false);
    }

    if (this.$route.query.mostrartodosempresa) {
      app.mostrartodosempresa = this.$route.query.mostrartodosempresa == 1;
    }

    if (
      !app.$helpers.checkpermissao(
        app.$root.logincliente.contato.permissaopainel,
        "40000374"
      )
    )
      app.mostrartodosempresa = 0;

    if (this.$route.query.search) {
      app.txtsearch = this.$route.query.search;
    }

    app.listchamados();
    console.log(this.$route.query);
  },
  methods: {
    onNewSearch() {
      var app = this;
      app.pagination.currentPage = 0;
      app.listchamados();
    },
    actShowAvaliacao(id, dtabertura) {
      var app = this;
      app.avaliacaoselidhashmd5 = app.$helpers.md5(id + dtabertura + "OS");
      app.avaliacaoselidbase64 = app.$helpers.base64enc(id);
      app.showavaliacao = true;
    },
    onCloseAvaliacao(finalizado) {
      var app = this;
      app.avaliacaoselidhashmd5 = null;
      app.avaliacaoselidbase64 = null;
      app.showavaliacao = false;
      if (finalizado) app.listchamados();
    },
    actShowShare(id, dtabertura) {
      var app = this;
      app.avaliacaoselidhashmd5 = app.$helpers.md5(id + dtabertura + "OS");
      app.avaliacaoselidbase64 = app.$helpers.base64enc(id);
      app.showsharing = true;
    },
    onCloseShare() {
      var app = this;
      app.avaliacaoselidhashmd5 = null;
      app.avaliacaoselidbase64 = null;
      app.showsharing = false;
    },

    setFilterStatus(status, reload = true) {
      var app = this;
      app.filternaolido = -1;
      app.filterstatus = status;
      app.filtersituacao = "";
      app.filterpendenteresposta = -1;
      if (app.filterstatus == "A") {
        app.filtersituacao = "Aberta";
      } else if (app.filterstatus == "E") {
        app.filtersituacao = "Encerrada";
      } else if (app.filterstatus == "PR") {
        app.filtersituacao = "Aberta";
        app.filterpendenteresposta = 1;
      } else if (app.filterstatus == "NL") {
        app.filternaolido = 1;
      }

      if (reload) app.listchamados();
    },
    getStatus(situacao, fase, naolido) {
      var encerrado = situacao == "Encerrada";
      var desc = "";
      var btn = "btn-default";
      var cor = "blue";
      if (encerrado) {
        desc = "Encerrado";
        cor = "success";
         btn = "btn-success";
        if (naolido==1){
            desc = "Não lido";
            cor = "yellow";
            btn = "btn-warning";
        }               
      } else {
        if (fase) {
          if (fase.pendentecliente == 1) {
            desc = "Aguardando resposta";
            cor = "danger";
             btn = "btn-danger";
          } else {
            desc = "Aguardando atendimento";
            cor = "blue";
             btn = "btn-primary";
            if (naolido==1){
                desc = "Não lido";
                cor = "yellow";
                btn = "btn-warning";
            }             
          }
        }
      }
      

      return {
        desc: desc,
        cor: cor,
         btn : btn
      };
    },
    actMostrarAbertos() {
      var app = this;
      app.mostrarabertos = !app.mostrarabertos;
      app.listchamados();
    },
    actMostrarTodos() {
      var app = this;
      app.mostrartodosempresa = !app.mostrartodosempresa;
      app.listchamados();
    },
    async listchamados() {
      var app = this;
      Vue.nextTick(function() {
        app.loading = true;
      });
      var content = {
        naolido: app.filternaolido,
        pendenteresposta: app.filterpendenteresposta,
        search: app.txtsearch,
        mostrartodosempresa: app.mostrartodosempresa ? 1 : 0,
        idcontato: app.$root.logincliente.contato.id,
        situacao: app.filtersituacao,
        idcliente: app.$root.logincliente.cliente.codcliente,
        limit: app.pagesize
      };
      var url =
        "/api/v1/painelcliente/chamados/list" +
        (app.pagination.currentPage > 0
          ? "?page=" + app.pagination.currentPage
          : "");

      var ret = await app.$helpers.sendPost(app, url, content);

      Vue.nextTick(function() {
        app.loading = false;
      });
      if (ret.ok) {
        app.pagesize = ret.rows.per_page;
        app.pagination.currentPage = ret.rows.current_page;
        app.paginationtotal = ret.rows.total;
        app.chamados = ret.rows.data;
        console.log(app.chamados);
      } else {
        alert(ret.msg);
        console.log(ret);
      }
    },
    async sendSetEmpresa(idempresa) {
      var app = this;
      var content = {
        idcontato: idempresa
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

<style>
.tdtable{
  padding: 10px;
    border: 1px solid #33333330;
}
.title{
  
  font-size: 15px;
}
</style>