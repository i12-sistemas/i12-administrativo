<template>
<div class="navbar-custom-menu">
    <ul class="nav navbar-nav">
    <li class="dropdown messages-menu">
    <!-- Menu toggle button -->
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-envelope-o"></i>
        <span class="label label-danger" v-if="protocolos">{{protocolos.length}}</span>
    </a>
        <ul class="dropdown-menu">
            <li class="header" v-if="protocolos">Você tem {{protocolos.length + (protocolos.length==1 ? ' mensagem' : ' mensagens')}}</li>
            <li class="header" v-if="!protocolos">Você não tem protocolos e mensagens</li>
            <li>
                <ul class="menu">
                    <li v-for="(protocolo, key) in protocolos" :key="key">

                    <a :href="'/painelcliente/protocolos/show/' + protocolo.protocolo" target="_self">
                        <div class="pull-left">
                        <img :src="protocolo.associado.fotourlsmall" class="img-circle" alt="Foto do usuário">
                        </div>
                        <h4>
                        {{protocolo.associado.firstname}}
                        <small><i class="fa fa-clock-o"></i> {{ $helpers.datetimeRelativeToday(protocolo.dhultupdate) }}</small>
                        </h4>
                        <p v-if="protocolo.mensagens ? protocolo.mensagens.length > 0 : false">Protocolo: <b>{{protocolo.protocolo}}</b></p>
                        <p v-if="protocolo.mensagens ? protocolo.mensagens.length > 0 : false">{{ protocolo.mensagens[protocolo.mensagens.length-1].msg}}</p>
                    </a>
                    </li>
                
                </ul>
            </li>
        </ul>
    </li>
    

    <!-- User Account Menu -->
    <li class="dropdown user user-menu" v-if="usuario">
        <!-- Menu Toggle Button -->
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <!-- The user image in the navbar-->
        <!-- hidden-xs hides the username on small devices so only the image appears. -->
        <img :src="usuario.fotourlsmall" class="user-image" alt="User Image">
        <span class="hidden-xs">{{usuario.nome}}</span>
        </a>
        <ul class="dropdown-menu">
        <!-- The user image in the menu -->
        <li class="user-header">
            <img :src="usuario.fotourlsmall" class="img-circle" alt="User Image">
            <p>{{usuario.nome}}<small>{{usuario.email}}</small></p>
        </li>
        <!-- Menu Footer-->
        <li class="user-footer">
            <div class="pull-left">
            <a :href="'/painelcliente/cadastros/usuario/' + userloggedid" class="btn btn-default btn-flat"><i class="fa fa-user-circle-o" aria-hidden="true"></i> Meus dados</a>
            </div>
            <div class="pull-right">
            <a href="/painelcliente/logout" class="btn btn-default btn-flat"><i class="fa fa-power-off" aria-hidden="true"></i> Sair</a>
            </div>
        </li>
        </ul>
    </li>             
    <!-- @yield('menu-top-right') -->
    </ul>
</div>
</template>

<script>
export default {
  data: function() {
    return {
      userloggedid: 0,
      protocolos: null,
      usuario: null
    };
  },
  mounted() {
    var app = this;
    app.userloggedid = this.$root.userlogged.id;

    this.$bus.$on("toolbarchanged", () => {
      app.getDataProtocolos();
    });
    this.getDataUsuario();
    this.getDataProtocolos();
  },
  methods: {
    permite: function(permissao) {
      return this.$helpers.checkpermissao(
        this.$root.userlogged.permissoes,
        permissao
      );
    },
    getDataProtocolos() {
      var app = this;

      // if(!app.permite('relatorio-desk-statusreport-cli')){
      //     app.share.hash = null;
      //     app.share.id = null;
      //     app.flash('Sem permissão de acesso', 'error', {timeout: 5000});
      //     return;
      // }

      var content = {
        idusuario: app.userloggedid,
        hidesemusuario: 0,
        status: "a"
      };

      app.axios
        .post("/api/v1/admin/protocolos/list", content)
        .then(function(resp) {
          app.protocolos = resp.data;
        })
        .catch(function(resp) {
          console.log("Falha ao carregar dados");
          console.log(resp);
          app.flash("Falha ao consultar dados!.\n" + resp, "error", {
            timeout: 7000
          });
        });
    },
    getDataUsuario() {
      var app = this;
      var content = { id: app.userloggedid };

      app.axios
        .post("/api/v1/admin/cadastro/usuario/show", content)
        .then(function(resp) {
          app.usuario = resp.data;
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