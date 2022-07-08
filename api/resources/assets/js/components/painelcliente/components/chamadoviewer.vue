<template>
<div>
    <div class="row" v-if="loading">
        <div class="alert text-center text-primary h3"><i class="fa fa-refresh fa-spin"></i> <b> carregando...</b></div>
    </div>    

    <div class="row" v-if="!loading && loadingerror!==''">
        <div class="alert alert-danger">
          <i class="fa fa-warning"></i> {{loadingerror}}
        </div>
    </div>    
    

    <div class="row" v-if="!loading && chamado ? chamado.id>0 : false">
      <table class="table table-icom">
        <tbody>
        <tr>
          <td>
            <div class="row">
              <div class="col-xs-6 col-sm-2">Número do chamado</div>
              <div class="col-xs-6 col-sm-2 text-left text-bold">
                <span class="h3">#{{chamado.id}} </span> 
              </div>

              <div class="col-xs-6 col-sm-2">Aberto em</div>
              <div class="col-xs-6 col-sm-2 text-left text-bold">{{$helpers.datetimeToBR(chamado.dtabertura, false, true)}}</div>

              <div class="col-xs-6 col-sm-2">Status</div>
              <div class="col-xs-6 col-sm-2 text-left text-bold">{{chamado.situacao}}</div>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="row">
              <div class="col-sm-2">Cliente</div>
              <div class="col-sm-4 text-left text-bold">
                <div>{{chamado.cliente.fantasia}} <small>{{chamado.cliente.nome}}</small></div>
              </div>
              <div class="col-sm-2 text-left">Responsável</div>
              <div class="col-sm-4 text-left text-bold">
                {{chamado.contato.nome}}
               <!-- <div v-for="(email, keyemail) in chamado.contato.emails" :key="keyemail">{{email.email}}</div> -->
               <button type="button" class="btn btn-link text-black btn-xs"  @click="actShowShare(chamado.id, chamado.dtabertura)" ><i class="fa fa-share-alt"></i> </button>
              </div>
            </div>
          </td>
        </tr>        
        <tr>
          <td>
            <div class="row">
              <div class="col-sm-2">Status</div>
              <div class="col-sm-4 text-left text-bold">{{chamado.fase.descricaocliente}}</div>
              <div class="col-sm-2 text-left">Origem</div>
              <div class="col-sm-4 text-left text-bold">{{chamado.tipo}}</div>
            </div>
          </td>
        </tr>        
        </tbody>      
      </table>

    </div>
    <!-- assunto -->
    <div class="row" v-if="!loading && chamado ? chamado.id>0 : false">
      <table class="table table-icom table-striped table-bordered">
        <tbody>
        <tr>
          <td><b>Assunto</b></td>
        </tr>
        <tr>
          <td>
            <div class="text-left">{{chamado.problemareclamado}}</div>
          </td>
        </tr>
        </tbody>      
      </table>

    </div>     

    <!-- mensagem -->
    <div class="row" v-if="!loading && (!chamado ? false : chamado.situacao=='Aberta')">
      <input @change="uploadImage" type="file" name="photo" :accept="INTERACAO_ANEXO_MINETYPES" ref="txtfile" class="hidden" multiple>
      <table class="table table-icom table-striped table-bordered">
        <tbody>
        <tr>
          <td><b>Interagir - Nova mensagem</b></td>
        </tr>
        <tr>
          <td>
            <div class="form-group">
              <textarea class="form-control" rows="3" v-model="txtmensagem" placeholder="Digite sua mensagem..."  :disabled="posting"></textarea>
            </div>
            <div>
              <small v-if="listfiles.length==0">Você pode anexar até {{INTERACAO_ANEXO_QTDE_ARQUIVO}} arquivos de {{$helpers.bytesToHumanFileSizeString(INTERACAO_ANEXO_TAMANHO_LIMITE)}} cada</small>
              <small v-if="listfiles.length>0">{{listfiles.length}} arquivo(s)</small>
            </div>
            <!-- anexo list -->
            <div v-if="(INTERACAO_ANEXO_QTDE_ARQUIVO ? INTERACAO_ANEXO_QTDE_ARQUIVO > 0 : false) && (INTERACAO_ANEXO_TAMANHO_LIMITE ? INTERACAO_ANEXO_TAMANHO_LIMITE > 0 : false)" >
                <div v-for="(item, key ) in listfiles" :key="key">
                    <div class="button-tagged " v-bind:class="!item.ret ? (!item.posting ? 'bg-gray' : 'bg-warning') : (item.ret.ok ? 'bg-success' : 'bg-danger')">
                      <i class="fa fa-spinner fa-spin" v-if="item.posting"></i>
                      <i v-if="!item.posting && item.ret" class="fa " v-bind:class="item.ret.ok ? 'fa-check' : 'fa-times'"></i>
                      <i class="fa fa-file" v-if="!item.posting && !item.ret"></i> {{item.name}}
                      <a v-if="!posting" href="javascript:void(0);" class="link-black" @click="deleteFile(key)"><i class="fa fa-times"></i></a>
                    </div>
                </div>

            </div>       
            <!-- anexo list -->     
          </td>
        </tr>
        <tr>
          <td>
            <button class="btn btn-danger" v-if="listfiles ? listfiles.length>1 : false" :disabled="posting" @click="deleteAllFile()" ><i class="fa fa-times"></i></button>            
            <button class="btn btn-default"  
                v-if="(INTERACAO_ANEXO_QTDE_ARQUIVO ? INTERACAO_ANEXO_QTDE_ARQUIVO > 0 : false) && (INTERACAO_ANEXO_TAMANHO_LIMITE ? INTERACAO_ANEXO_TAMANHO_LIMITE > 0 : false)"
                :disabled="posting" @click="$refs.txtfile.click()" 
                :title="'Você pode anexar até ' + INTERACAO_ANEXO_QTDE_ARQUIVO + ' arquivos de ' + $helpers.bytesToHumanFileSizeString(INTERACAO_ANEXO_TAMANHO_LIMITE) + ' cada com as extensões ' + INTERACAO_ANEXO_EXTENSOES">
                <i class="fa fa-upload"></i> Anexar arquivos
            </button>
            <button class="btn btn-primary" @click="sendPostMsg()" :disabled="posting">
              <span v-if="posting"><i class="fa fa-spinner fa-spin"></i> Enviando</span>
              <span v-if="!posting"><i class="fa fa-send"></i> Enviar</span>
            </button>            
            <button class="btn btn-danger pull-right"  :disabled="posting" @click="encerrarchamado = true;"><i class="fa fa-handshake-o"></i> <span class="hidden-xs">Encerrar chamado</span></button>
          </td>
        </tr>       
        </tbody>      
      </table>

    </div>     

    <!-- alerta encerrado -->
    <div class="row" v-if="!loading && (!chamado ? false : chamado.situacao=='Encerrada')">
      <div class="alert bg-success" role="alert"> 
          <p class="h4">Chamado encerrado!</p>
          <p>Este chamado foi encerrado em {{$helpers.datetimeToBR(chamado.dtfechamento, false, true)}}.</p>
          <p>Caso ainda seja necessário atendimento/suporte efetue a abertura de um novo atendimento. Obrigado!</p>
  

        <!-- valiação -->
        <div class="box box-primary" v-if="!loading && (!chamado ? false : chamado.situacao=='Encerrada') && showavaliacao" style="margin-top: 30px">
          <div class="box-header with-border">
            <h3 class="box-title">Avalie-nos!</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" @click="onCloseAvaliacao()"><i class="fa fa-times"></i></button>
            </div>
          </div>
          <div class="box-body">
            <chamadoavaliacao :idhashmd5="idhashmd5" :idbase64="idbase64" :contato="chamado.contato" v-on:closeavaliacao="onCloseAvaliacao"/>
          </div>
        </div>    
        <!-- valiação -->
      </div>
    </div> 

    <!-- interacao -->
    <div class="row" v-if="!loading && chamado ? chamado.id>0 : false">
      <div class="table-embed-responsive">
      <table class="table table-icom table-hover table-bordered">
        <tbody>
        <tr>
          <th style="width: 120px;min-width: 120px;">
            <div class="text-left">Data/hora - Origem</div>
          </th>

          <th>
            <div class="text-left">Descritivo</div>
          </th>
        </tr>
        <tr v-for="(interacao, key) in chamado.interacoes" :key="key" v-bind:class="interacao.origem=='C' ? 'bg-info' : ''">
          <td>
            <div class="text-left">
              <div><small>{{$helpers.datetimeToBR(interacao.datahora, false, true)}}</small></div>
              <div v-if="interacao.origem=='C' && (interacao.ip ? interacao.ip!=='' : false)"><small>IP: {{interacao.ip}}</small></div>
            </div>
            <div class="text-ellipsis">

              <span class="label table-icom" v-bind:class="interacao.origem=='C' ? 'label-primary' : 'label-success'">
              <i v-bind:class="interacao.origem=='C' ? 'fa fa-user' : 'fa fa-life-ring'"></i> {{interacao.origem=='O' ? 'Operador' : 'Cliente'}}
              </span>
            </div>
          </td>
          <td>
            <div class="text-left" >
                <span class="label bg-purple pull-right" v-if="chamado.ultimaleitura && interacao.datahora ? (interacao.datahora>chamado.ultimaleitura) : true">Novo</span>
                <div style="font-size: larger !important" v-html="$helpers.linebreakToBr(interacao.texto)"></div>
                <!-- anexos -->
                <div v-if="interacao.anexos ? interacao.anexos.length>0 : false">
                  <hr>
                  <label><small>Anexos - {{interacao.anexos.length}} arquivo(s)</small></label>
                  <div class=""  v-for="(anexo, anexokey) in interacao.anexos" :key="anexokey">
                    <a href="javascript:void(0);" @click="actDownloadArquivo(anexo.md5)" target="_self" class="btn btn-link link-blue btn-xs"><i class="fa fa-paperclip"></i> {{anexo.descricao}}</a>
                  </div>
                </div>
              </div>
          </td>
        </tr>
        </tbody>      
      </table>
      </div>

    </div>    

    <!-- encerrar chamado -->
    <transition name="modal" v-if="encerrarchamado" >
    <div class="modal modal-mask"  style="display: block">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body modal-body-scroll">
                <div class="h4">Confirmar o encerramento do chamado?</div>
                <p>Após o encerramento somente nossos operadores poderão reabrir o chamado se necessário</p>
                <hr v-if="postingencerrando">
                <div class="h4 text-red" v-if="postingencerrando">Encerrando, aguarde...</div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" :disabled="postingencerrando" @click="sendPostEncerrar()">
                  <span v-if="!postingencerrando"><i class="fa fa-handshake-o"></i> Confirmar encerramento</span>
                  <span v-if="postingencerrando"><i class="fa fa-refresh fa-spin"></i> Encerrando...</span>
                </button>
                <button type="button" class="btn btn-default" :disabled="postingencerrando" @click="encerrarchamado=false"><i class="fa fa-times"></i> Sair</button>
            </div>
        </div>
    </div>
    </div>
    </transition>    
    <!-- encerrar -->    
      

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
              <chamadoshare :idhashmd5="idhashmd5" :idbase64="idbase64"  v-on:closeshare="onCloseShare"/>
          </div>
          <!-- <div class="modal-footer">
              <button type="button" class="btn btn-default" @click="closeApontamentos()"> Fechar </button>
          </div> -->
      </div>
  </div>
  </div>
  </transition>    
  <!-- share -->   

</div>
</template>

<script>
import pageheader from "../template/pageheader.vue";
import chamadoavaliacao from "./chamadoavaliacao.vue";
import moment from "moment";
import VueMomentJS from "vue-momentjs";
import chamadoshare from "../components/chamadoshare.vue";

export default {
  components: {
    "app-painelcliente-pageheader": pageheader,
    chamadoavaliacao: chamadoavaliacao,
    moment,
    VueMomentJS,
    chamadoshare
  },
  props: ["idhashmd5", "idbase64", "idcontato"],
  data: () => ({
    showsharing: false,
    encerrarchamado: false,
    postingencerrando: false,
    id: null,
    loading: true,
    loadingerror: "",
    posting: false,
    chamado: null,
    txtmensagem: "",

    intervalSec: 5,
    minutesread: 0,
    idleitura: null,
    errorCount: 0,
    showavaliacao: true,
    listfiles: [],

    INTERACAO_ANEXO_TAMANHO_LIMITE: 0,
    INTERACAO_ANEXO_EXTENSOES: [],
    INTERACAO_ANEXO_MINETYPES: [],
    INTERACAO_ANEXO_QTDE_ARQUIVO: 0
  }),
  mounted() {
    var app = this;
    setTimeout(function() {
      app.onLoad();

      setTimeout(function() {
        app.sendRead();
      }, 1000);

      app.timer = setInterval(function() {
        app.sendRead();
      }, app.intervalSec * 1000);
    }, 100);
  },
  methods: {
    actShowShare(id, dtabertura) {
      var app = this;
      app.showsharing = true;
    },
    onCloseShare() {
      var app = this;
      app.showsharing = false;
    },
    async actDownloadArquivo(md5) {
      var app = this;
      // app.postingencerrando = true;
      var content = {
        idchamado: app.id,
        md5: md5
      };
      var url = "/api/v1/painelcliente/chamados/mensagemdownloadanexo";
      var ret = await app.$helpers.sendPost(app, url, content);
      if (!ret.ok) {
        alert(ret.msg);
      } else {
        var pom = document.createElement("a");
        pom.setAttribute("href", ret.rows.url);
        pom.setAttribute("download", ret.rows.filename);
        // pom.setAttribute("target", "_self");
        pom.style.display = "none";
        document.body.appendChild(pom);
        pom.click();
        document.body.removeChild(pom);
      }
    },
    async sendPostEncerrar() {
      var app = this;
      app.postingencerrando = true;
      var content = {
        idchamado: app.id,
        idcontato: app.idcontato
      };
      var url = "/api/v1/painelcliente/chamados/encerrachamadousuario";
      var ret = await app.$helpers.sendPost(app, url, content);
      if (!ret.ok) {
        alert(ret.msg);
      } else {
        app.getdata();
        app.encerrarchamado = false;
      }
      app.postingencerrando = false;
    },
    deleteAllFile() {
      var app = this;
      app.listfiles = [];
    },
    deleteFile: function(idx) {
      var app = this;
      app.listfiles.splice(idx, 1);
    },
    addFileItem: function(file) {
      var app = this;
      var reader = new window.FileReader();
      reader.onload = function(e) {
        var item = {
          base64: reader.result,
          dtref: moment().format("YYYY-MM-DD"),
          name: file.name,
          size: file.size,
          ret: null,
          posting: false,
          postingstr: ""
        };
        if (app.listfiles.length < app.INTERACAO_ANEXO_QTDE_ARQUIVO)
          app.listfiles.push(item);
      };
      reader.onerror = function(error) {
        alert(error);
      };
      if (file) {
        reader.readAsDataURL(file);
      }
    },
    uploadImage: function() {
      var app = this;
      var alertou = false;
      var files = app.$refs.txtfile.files;
      var i = 0;

      if (
        files.length + app.listfiles.length >
        app.INTERACAO_ANEXO_QTDE_ARQUIVO
      ) {
        alert(
          "A quantidade selecionada ultrapassa o limite de " +
            app.INTERACAO_ANEXO_QTDE_ARQUIVO +
            " arquivo(s)."
        );
        return;
      }
      for (i = 0; i < files.length; i++) {
        var file = files[i];
        try {
          if (file.size > app.INTERACAO_ANEXO_TAMANHO_LIMITE)
            throw "Tamanho do arquivo excede o limite permitido\nLimite:" +
              $helpers.bytesToHumanFileSizeString(
                app.INTERACAO_ANEXO_TAMANHO_LIMITE
              );

          var extension = file.name
            .substr(file.name.lastIndexOf(".") + 1)
            .toLowerCase();
          if (app.INTERACAO_ANEXO_EXTENSOES.indexOf(extension) === -1) {
            throw "Formato de arquivo inválido. Somente arquivos  " +
              app.INTERACAO_ANEXO_EXTENSOES.join(", ") +
              " estão liberados.";
          }

          var check = app.$helpers.arrayObjectIndexOf(
            app.listfiles,
            file.name,
            "name"
          );
          if (check >= 0) {
            throw "Arquivo já está na listagem.";
          }

          app.addFileItem(file);
        } catch (err) {
          if (!alertou) alert(err);
          alertou = true;
        }
      }
    },
    onCloseAvaliacao() {
      this.showavaliacao = false;
    },
    onLoad() {
      var app = this;
      app.id = this.$helpers.base64dec(app.idbase64);
      this.getConfig();
      this.getdata();
    },
    async getConfig() {
      var app = this;
      var content = {
        FLAGIDS: JSON.stringify([
          "INTERACAO_ANEXO_EXTENSOES",
          "INTERACAO_ANEXO_TAMANHO_LIMITE",
          "INTERACAO_ANEXO_MINETYPES",
          "INTERACAO_ANEXO_QTDE_ARQUIVO"
        ])
      };
      var url = "/api/v1/painelcliente/configuracao/find";
      var ret = await app.$helpers.sendPost(app, url, content);
      if (ret.ok) {
        ret.rows;
        if (ret.rows.INTERACAO_ANEXO_TAMANHO_LIMITE) {
          app.INTERACAO_ANEXO_TAMANHO_LIMITE =
            Number(ret.rows.INTERACAO_ANEXO_TAMANHO_LIMITE.valor) * 1024;
        }
        if (ret.rows.INTERACAO_ANEXO_EXTENSOES) {
          var s = ret.rows.INTERACAO_ANEXO_EXTENSOES.valor;
          app.INTERACAO_ANEXO_EXTENSOES = s.split(";");
        }
        if (ret.rows.INTERACAO_ANEXO_QTDE_ARQUIVO) {
          app.INTERACAO_ANEXO_QTDE_ARQUIVO = Number(
            ret.rows.INTERACAO_ANEXO_QTDE_ARQUIVO.valor
          );
        }

        if (ret.rows.INTERACAO_ANEXO_MINETYPES) {
          var s = ret.rows.INTERACAO_ANEXO_MINETYPES.valor;
          app.INTERACAO_ANEXO_MINETYPES =
            ret.rows.INTERACAO_ANEXO_MINETYPES.valor == ""
              ? []
              : ret.rows.INTERACAO_ANEXO_MINETYPES.valor;
        }
      }
      return ret;
    },
    stopTimer: function() {
      if (this.errorCount > 5) {
        clearInterval(this.timer);
      }
    },
    sendRead: function() {
      this.minutesread += this.intervalSec;
      this.sendPostRead();
    },
    async sendPostRead() {
      var app = this;
      var content = {
        idchamado: app.id,
        idcontato: app.idcontato,
        idleitura: app.idleitura,
        url: window.location.href
      };
      var url = "/api/v1/painelcliente/chamados/setreadchamado";
      var ret = await app.$helpers.sendPost(app, url, content);
      if (!ret.ok) {
        app.errorCount += 1;
        app.stopTimer();
      } else {
        app.intervalSec = 5;
        app.idleitura = ret.id;
        app.errorCount = 0;
      }
    },
    async sendPostMsg() {
      var app = this;
      Vue.nextTick(function() {
        app.posting = true;
      });
      var content = {
        idchamado: app.id,
        idcontato: app.idcontato,
        mensagem: app.txtmensagem
      };

      var url = "/api/v1/painelcliente/chamados/addmessageusuario";
      var ret = await app.$helpers.sendPost(app, url, content);

      if (ret.ok) {
        app.txtmensagem = "";
        // upload files
        var i = 0;
        for (i = 0; i < app.listfiles.length; i++) {
          app.listfiles[i].posting = true;
          // app.listfiles[i].postingstr = "enviando...";
          var retUpload = await app.sendupload(ret.id, app.listfiles[i]);
          if (retUpload.ok) {
            // app.listfiles[i].postingstr = "processando...";
            // app.tevealteracao = true;
          } else {
            // teveerro = true;
          }
          app.listfiles[i].ret = retUpload;
          app.listfiles[i].posting = false;
        }

        app.listfiles = [];
        app.getdata();
      } else {
        alert(ret.msg);
        console.log(ret);
      }
      Vue.nextTick(function() {
        app.posting = false;
      });
    },
    async sendupload(idinteracao, file) {
      var app = this;
      var content = {
        idinteracao: idinteracao,
        dados: JSON.stringify(file)
      };
      var url = "/api/v1/painelcliente/chamados/mensagemuploadanexo";
      var ret = await app.$helpers.sendPost(app, url, content);
      return ret;
    },
    async getdata() {
      var app = this;
      Vue.nextTick(function() {
        app.loading = true;
      });

      try {
        if (!app.idcontato) throw "Contato/E-mail não identificado";
        //   if (!app.idcontato.id > 0) throw "Contato/E-mail não identificado";
      } catch (err) {
        Vue.nextTick(function() {
          app.loading = false;
        });
        app.loadingerror = err;
        return await {
          ok: false,
          msg: err
        };
      }

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