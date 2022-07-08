<template>
<div>
<div class="box box-primary direct-chat direct-chat-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Mensagem recentes</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" v-on:click="getData" ><i class="fa fa-refresh"></i></button>
        </div>
    </div>    
          
    <div class="box-body">
        <div v-if="mensagens.length==0" class="text-center h3">Nenhuma mensagem</div>
        <!-- Conversations are loaded here -->
        <div class="direct-chat-messages" ref="refmessages" v-bind:style="startheight>0 ? 'height: auto !important;' : 'height: 40vh' ">
            <div v-for="(msg, key) in mensagens" :key="key">
            
                <!-- msg esquerda admin -->
                <div class="direct-chat-msg"  v-if="msg.origem=='admin'">
                    <div class="direct-chat-info clearfix">
                        <span class="direct-chat-name pull-left"> {{ msg.usuarioadmin.nome }}</span>
                        <span class="direct-chat-timestamp pull-right">{{$helpers.datetimeRelativeToday(msg.dhacao)}}</span>
                    </div>
                    <!-- /.direct-chat-info -->
                    <i class="direct-chat-img fa fa-user fa-3x text-gray-dark" aria-hidden="true"></i>
                    <div class="direct-chat-text">
                        <p v-html="$helpers.nl2br(msg.msg)"></p>
                        <h4 v-if="msg.status=='r'" ><span class="label label-success"><i class="fa fa-check-circle" aria-hidden="true"></i> Assunto resolvido até {{$helpers.datetimeToBR(msg.dhacao, true)}}</span></h4>
                        <h4 v-if="msg.status=='p'" ><span class="label label-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Assunto como pendente - {{$helpers.datetimeToBR(msg.dhacao, true)}}</span></h4>
                    </div>
                </div>
                <!-- msg esquerda  -->


                <!-- msg dir usuario  -->
                <div class="direct-chat-msg right" v-if="msg.origem=='user'">
                    <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-right">Eu</span>
                    <span class="direct-chat-timestamp pull-left">{{ $helpers.datetimeRelativeToday(msg.dhacao) }}</span>
                    </div>
                    <!-- /.direct-chat-info -->
                    <img class="direct-chat-img" :src="msg.fotourl" alt="Minha foto">
                    <div class="direct-chat-text">
                        <p v-html="$helpers.nl2br(msg.msg)"></p>
                        <h4 v-if="msg.status=='r'" ><span class="label label-success"><i class="fa fa-check-circle" aria-hidden="true"></i> Assunto resolvido até {{$helpers.datetimeToBR(msg.dhacao, true)}}</span></h4>
                        <h4 v-if="msg.status=='p'" ><span class="label label-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Assunto como pendente - {{$helpers.datetimeToBR(msg.dhacao, true)}}</span></h4>
                    </div>
                </div>
                <!-- msg dir usuario  -->
            </div>
        </div>
        <!--/.direct-chat-messages-->
    </div>

    <div class="box-footer">
            <div class="col-xs-12 text-left margin-bottom">
                <textarea class="form-control" rows="3" placeholder="Digite sua mensagem..." v-model="txtMsg"></textarea>
            </div>            
            <div class="col-xs-12 text-right">
                <button type="button" class="btn btn-primary" v-on:click="sendMsg('a')"><i class="fa fa-send"></i> Enviar</button>
                <button type="button" class="btn btn-success" v-on:click="sendMsg('r')"><i class="fa fa-check-circle"></i> Resolvido</button>
                <button type="button" class="btn btn-danger" v-on:click="sendMsg('p')"><i class="fa fa-exclamation-circle"></i> Pendente</button>
            </div>
    </div>
</div>    
<flash-message class="flash-message"></flash-message>  
</div> 

</template>

<script>
export default {
  data: function() {
    return {
      mensagens: [],
      txtMsg: ""
    };
  },
  props: ["startheight"],
  mounted() {
    jQuery(".direct-chat-messages").slimScroll({
      position: "right",
      height: this.startheight > 0 ? "50vh" : "30vh",
      railVisible: true,
      alwaysVisible: true,
      color: "#3c8dbc"
    });

    this.getData();
  },
  methods: {
    sendMsg(status) {
      var app = this;

      // if(!app.permite('relatorio-desk-statusreport-cli')){
      //     app.share.hash = null;
      //     app.share.id = null;
      //     app.flash('Sem permissão de acesso', 'error', {timeout: 5000});
      //     return;
      // }

      var content = {
        idassociado: this.$root.userlogged.id,
        origem: "user",
        status: status,
        categ: "",
        msg: app.txtMsg
      };

      app.axios
        .post("/api/v1/usuario/mensagens/add", content)
        .then(function(resp) {
          let ret = resp.data;
          if (ret.ok) {
            app.getData();
            app.txtMsg = "";
          } else {
            console.log(ret.msg);
            app.flash("Mensagem não foi enviada!.\n" + ret.msg, "error", {
              timeout: 7000
            });
          }
        })
        .catch(function(resp) {
          console.log(resp);
          app.flash("Mensagem não foi enviada!.\n" + resp, "error", {
            timeout: 7000
          });
        });
    },
    getData() {
      var app = this;

      // if(!app.permite('relatorio-desk-statusreport-cli')){
      //     app.share.hash = null;
      //     app.share.id = null;
      //     app.flash('Sem permissão de acesso', 'error', {timeout: 5000});
      //     return;
      // }

      var content = { idassociado: this.$root.userlogged.id };

      app.axios
        .post("/api/v1/usuario/mensagens", content)
        .then(function(resp) {
          app.mensagens = resp.data;

          var itemscroll = app.$refs.refmessages;
          var iScrollHeight = jQuery(itemscroll).prop("scrollHeight");
          app.$nextTick(() =>
            jQuery(itemscroll).scrollTop(iScrollHeight + 100)
          );
        })
        .catch(function(resp) {
          console.log("Falha ao carregar dados");
          console.log(resp);
          app.flash("Falha ao consultar dados!.\n" + resp, "error", {
            timeout: 7000
          });
        });
    }
  }
};
</script>