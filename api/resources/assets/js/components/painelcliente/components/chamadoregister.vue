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
    

    <div class="row" v-if="!loading && loadingerror==''">
      <table class="table table-icom">
        <tbody>
        <tr>
          <td>
            <div class="row">
              <div class="col-xs-4 col-sm-2">Empresa</div>
              <div class="col-xs-8 col-sm-10 text-left text-bold">
                <span class="h3">{{$root.logincliente.cliente.nome}}</span> 
              </div>
              <div class="col-xs-4 col-sm-2">Responsável</div>
              <div class="col-xs-8 col-sm-10 text-left text-bold">{{$root.logincliente.contato.nome}}</div>

            </div>
          </td>
        </tr>
             
        </tbody>      
      </table>

    </div>
  

    <!-- mensagem -->
    <div class="row" v-if="!loading && loadingerror==''">
      <input @change="uploadImage" type="file" name="photo" :accept="INTERACAO_ANEXO_MINETYPES" ref="txtfile" class="hidden" multiple>
      <table class="table table-icom table-striped table-bordered">
        <tbody>
        <tr>
          <td><b>Assunto (breve informação sobre a solicitação/problema)</b></td>
        </tr>   
        <tr>
          <td><input type="text" class="form-control" placeholder="Assunto..." maxlength="255"  :disabled="posting" v-model="txtassunto" /></td>
        </tr>                
        <tr>
          <td><b>Detalhe sua solicitação ou problema</b></td>
        </tr>
        <tr>
          <td>
            <div class="form-group">
              <textarea class="form-control" rows="5" v-model="txtmensagem" placeholder="Digite sua mensagem..."  :disabled="posting"></textarea>
            </div>
            <div v-if="(INTERACAO_ANEXO_QTDE_ARQUIVO ? INTERACAO_ANEXO_QTDE_ARQUIVO > 0 : false) && (INTERACAO_ANEXO_TAMANHO_LIMITE ? INTERACAO_ANEXO_TAMANHO_LIMITE > 0 : false)">
              <small v-if="listfiles.length==0">Você pode anexar até {{INTERACAO_ANEXO_QTDE_ARQUIVO}} arquivos de {{$helpers.bytesToHumanFileSizeString(INTERACAO_ANEXO_TAMANHO_LIMITE)}} cada</small>
              <small v-if="listfiles.length>0">{{listfiles.length}} arquivo(s)</small>
            </div>
            <!-- anexo list -->
            <div>
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
            <button class="btn btn-default"  :disabled="posting" @click="$refs.txtfile.click()" 
                  v-if="(INTERACAO_ANEXO_QTDE_ARQUIVO ? INTERACAO_ANEXO_QTDE_ARQUIVO > 0 : false) && (INTERACAO_ANEXO_TAMANHO_LIMITE ? INTERACAO_ANEXO_TAMANHO_LIMITE > 0 : false)"
                  :title="'Você pode anexar até ' + INTERACAO_ANEXO_QTDE_ARQUIVO + ' arquivos de ' + $helpers.bytesToHumanFileSizeString(INTERACAO_ANEXO_TAMANHO_LIMITE) + ' cada com as extensões ' + INTERACAO_ANEXO_EXTENSOES">
                  <i class="fa fa-upload"></i> Anexar arquivos
            </button>
            <button class="btn btn-primary" @click="sendChamado()" :disabled="posting">
              <span v-if="posting"><i class="fa fa-spinner fa-spin"></i> Enviando</span>
              <span v-if="!posting"><i class="fa fa-send"></i> Enviar</span>
            </button>
            
          </td>
        </tr>       
        </tbody>      
      </table>

    </div>     






</div>
</template>

<script>
import moment from "moment";
import VueMomentJS from "vue-momentjs";

export default {
  components: {
    moment,
    VueMomentJS
  },
  data: () => ({
    config: null,
    loading: true,
    loadingerror: "",
    posting: false,
    txtassunto: "",
    txtmensagem: "",
    INTERACAO_ANEXO_TAMANHO_LIMITE: 0,
    INTERACAO_ANEXO_EXTENSOES: [],
    INTERACAO_ANEXO_MINETYPES: [],
    INTERACAO_ANEXO_QTDE_ARQUIVO: 0,
    listfiles: []
  }),
  mounted() {
    var app = this;
    setTimeout(function() {
      app.onLoad();
    }, 100);
  },
  methods: {
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
              app.$helpers.bytesToHumanFileSizeString(
                app.INTERACAO_ANEXO_TAMANHO_LIMITE
              );

          var extension = file.name
            .substr(file.name.lastIndexOf(".") + 1)
            .toLowerCase();
          if (app.INTERACAO_ANEXO_EXTENSOES.indexOf(extension) === -1) {
            throw "Formato de arquivo inválido [" +
              extension +
              "]. Somente arquivos  " +
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
    onLoad() {
      var app = this;
      app.loading = false;
      this.getConfig();
    },
    async getConfig() {
      var app = this;
      app.loading = true;
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
      } else {
        alert(ret.msg);
        console.log(ret);
      }
      Vue.nextTick(function() {
        app.loading = false;
      });
    },

    async sendChamado() {
      var app = this;
      Vue.nextTick(function() {
        app.posting = true;
      });
      var content = {
        idcliente: app.$root.logincliente.cliente.codcliente,
        idcontato: app.$root.logincliente.contato.id,
        mensagem: app.txtmensagem,
        assunto: app.txtassunto
      };

      var url = "/api/v1/painelcliente/chamados/addchamado";
      var ret = await app.$helpers.sendPost(app, url, content);

      if (ret.ok) {
        app.txtmensagem = "";
        // upload files
        var i = 0;
        for (i = 0; i < app.listfiles.length; i++) {
          app.listfiles[i].posting = true;
          // app.listfiles[i].postingstr = "enviando...";
          var retUpload = await app.sendupload(
            ret.rows.idinteracao,
            app.listfiles[i]
          );
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
        var url =
          app.$root.base_url +
          "/chamado/show/" +
          ret.rows.idchamadobase64 +
          "/" +
          ret.rows.tokenchamado;

        window.location.href = url;
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
    }
  }
};
</script>