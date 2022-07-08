<template>
<div>
<div class="container">
    <div class="login-box">
        <div class="login-logo">
            <a href="route('admin.home') ">  <img :src="$root.url + '/assets/favicon/Logo-i12-horizontal.png'" class="img img-responsive" />  </a>
            <h3>Painel do cliente</h3>
        </div>
       

        <div class="login-box-body" style="box-shadow: 2px 2px 15px #00000038;">
            <div v-if="etapa==1">
                <p class="login-box-msg">Entre com seu usuário e senha para iniciar</p>
                    <div class="form-group has-feedback has-error" v-if="false">
                    <span class="text-danger">
                        <strong>error</strong>
                    </span>
                    </div>

                    
                    <div class="form-group has-feedback has-error">
                        <input type="text" name="login" class="form-control" v-model="username"
                            placeholder="Usuário">
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        
                            <span class="help-block" v-if="false">
                                <strong>ERRRRRRRRR</strong>
                            </span>
                    </div>
                    <div class="form-group has-feedback has-error">
                        <input type="password" name="password" class="form-control" v-model="senha"
                            placeholder="Senha">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            <span class="help-block" v-if="false">
                                <strong> ERR SENHA</strong>
                            </span>
                    </div>
                        
                    <div class="row">
                        <div class="col-xs-4">
                            <button type="button" @click="actLogin" class="btn btn-primary btn-block btn-flat">Acessar</button>
                        </div>
                    </div>
                
                <div class="auth-links">
                    <a href="#" class="text-center" >Esqueci minha senha</a>
                    <br>
                </div>
            </div>
            <div v-if="etapa==2">
                <p class="login-box-msg">Olá, <strong>{{$root.logincliente.contato.nome}}</strong></p>
                <p class="login-box-msg">selecione uma empresa para continuar</p>
                <div class="row" v-for="(empresa, key) in empresaslist" :key="key" >
                    <div class="col-xs-12" >
                        <button type="button" class="btn btn-app text-ellipsis text-center" 
                            style="max-width: 290px; width: 290px;"
                              @click="sendSetEmpresa(empresa.codcliente)"><i class="fa fa-check-circle-o"></i> {{empresa.nome}}
                        </button>
                    </div>
                </div>    
            </div>            
        </div>


    </div>
</div>
   
</div>
</template>

<script>
export default {
  data: () => ({
    username: "",
    senha: "",
    idempresa: null,
    etapa: 1,
    empresaslist: []
  }),
  mounted() {
    var app = this;
    if (app.$root.logincliente.contato) {
      if (app.$root.logincliente.contato.id > 0) {
        app.etapa = 2;
        app.listempresas();
      }
    }
  },
  methods: {
    async actLogin() {
      var app = this;
      var content = {
        login: app.username,
        password: app.senha
      };
      var ret = await app.$helpers.sendPost(
        app,
        "/painelcliente/login",
        content
      );
      if (ret.ok) {
        app.etapa = 2;
        app.$root.logincliente.contato = ret.rows;
        app.listempresas();
      } else {
        alert(ret.msg);
        console.log(ret);
      }
    },
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
        if (ret.rows.length == 1) {
          app.sendSetEmpresa(ret.rows[0].codcliente);
        } else {
          app.empresaslist = ret.rows;
        }
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