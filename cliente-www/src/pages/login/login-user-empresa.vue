<template>
<div class="row justify-center">
  <div class="col-xs-12 col-sm-8 col-md-6 col-lg-4">
    <div class="justify-center items-center text-center q-mt-lg">
      <img src="~assets/Logo-i12-horizontal150x50.png"  width="100%" style="max-width: 220px" >
    </div>
    <q-card class="q-ma-md q-mt-lg my-card" flat bordered  >
      <q-toolbar class="bg-grey-1 text-grey-8">
        <q-icon name="maps_home_work"  size="1.5em"/>
        <q-toolbar-title class="text-body2">
          Qual empresa deseja acessar? {{redirect}}
        </q-toolbar-title>
      </q-toolbar>
      <q-separator  />
      <q-card-section v-if="$store.state.usuario.clientes ? $store.state.usuario.clientes.length > 0 : false" class="q-px-none q-pt-none">
        <q-list  separator >
          <q-item clickable v-ripple v-for="(cliente, k) in $store.state.usuario.clientes" :key="k" @click="actRequest(cliente)">
            <q-item-section avatar>
              <q-avatar text-color="white" :color="$helpers.colorByCNPJ(cliente.doc)" >
                  {{ cliente.nome.charAt(0) }}
              </q-avatar>
            </q-item-section>
            <q-item-section>
              <q-item-label>{{cliente.nome}}</q-item-label>
              <q-item-label v-if="cliente.doc" caption>{{$helpers.mascaraCpfCnpj(cliente.doc)}}</q-item-label>
            </q-item-section>
          </q-item>
           <q-item clickable v-ripple @click="actClear">
            <q-item-section avatar>
              <q-avatar text-color="grey-10" color="grey-3"  icon="arrow_back" />
            </q-item-section>
            <q-item-section>
              <q-item-label ca>Voltar</q-item-label>
            </q-item-section>
          </q-item>
          <q-separator  />
        </q-list>
      </q-card-section>
      <q-card-section class="text-caption text-center">
        <div>Por segurança registramos seu acesso</div>
        <div>IP: {{$store.state.usuario.ip.ip_address}}</div>
        <div>{{$store.state.usuario.ip.city}}, {{$store.state.usuario.ip.region_iso_code}} - {{$store.state.usuario.ip.country}}</div>
      </q-card-section>
    </q-card>
  </div>
</div>
</template>

<script>

import axios from 'axios'
export default {
  data () {
    return {
      cliente: null,
      sending: false,
      redirect: null
    }
  },
  computed: {
    allowpost: function () {
      // var app = this
      var b = true
      try {
        // if (app.sending) throw new Error('Em loading')
        // if (app.username ? app.username === '' : true) throw new Error('username inválido')
        // if (app.password ? app.password === '' : true) throw new Error('password inválido')
        // if (app.recaptchasitekey ? app.recaptchasitekey !== '' : false) {
        //   if (!app.recaptchatoken) throw new Error('Sem recaptcha nota')
        // }
      } catch (error) {
        b = false
      }
      return b
    }
  },
  async mounted () {
    var app = this
    var usuario = app.$store.state.usuario
    if (app.$store.state.usuario.logado) app.redirectNow()
    app.$nextTick(() => {
      app.$refs.txtempresa.$el.focus()
    })
    if (app.$route.query) {
      if (app.$route.query.redirect) app.redirect = app.$route.query.redirect
    }
    if (usuario.clientes ? usuario.clientes.length === 0 : true) {
      app.$router.push({ name: 'login.usuario' })
    }
  },
  methods: {
    async redirectNow () {
      var app = this
      if (app.redirect) {
        await app.$router.push({ path: app.redirect })
      } else {
        await app.$router.push({ name: 'home' })
      }
    },
    async actClear () {
      var app = this
      await app.$store.dispatch('usuario/logout')
      app.redirectNow()
    },
    async verify (response) {
      this.recaptchatoken = response
    },
    async closeApp () {
      // var app = this
      navigator.app.exitApp()
    },
    async actRequest (pCliente) {
      var app = this
      // if (!app.allowpost) return
      app.cliente = pCliente
      app.sending = true
      let ret = await app.sendRequest()
      if (!ret.ok) {
        app.actShowError('Acesso restrito', ret.msg, 4000)
      }
      app.sending = false
    },
    actShowError (title, msg, timeoutclose) {
      var app = this
      const dialog = app.$q.dialog({
        title: title,
        message: msg
      }).onDismiss(() => {
        clearTimeout(timer)
        app.username = ''
        app.password = ''
        app.$nextTick(() => {
          app.$refs.txtempresa.$el.focus()
        })
      })

      const timer = setTimeout(() => {
        dialog.hide()
      }, timeoutclose)
    },
    async sendRequest () {
      var app = this
      try {
        if (!app.cliente) throw new Error('Selecione a empresa')
      } catch (error) {
        return { ok: false, msg: error.message }
      }
      var usuario = app.$store.state.usuario
      var params = {
        username: usuario.username,
        token: usuario.token,
        clienteid: app.cliente.id,
        clientedoc: app.cliente.doc
      }
      var req = axios.create({
        baseURL: app.$configini.API_URL,
        withCredentials: false,
        headers: {
          'Access-Control-Allow-Origin': '*',
          'Access-Control-Allow-Headers': 'Authorization',
          'Access-Control-Allow-Methods': 'GET, POST, OPTIONS, PUT, PATCH, DELETE',
          'Content-Type': 'application/json;charset=UTF-8'
        }
      })
      var ret = await req.post('v1/contato/auth2', params).then(response => {
        let data = response.data
        return data
      }
      ).catch(error => {
        let msg = error
        if (error.response) {
          msg = 'Code: ' + error.response.status + ' - ' + error.response.data.message
        } else {
          msg = error.message
        }
        return { ok: false, msg: 'Falha ao consultar servidor online: ' + msg }
      })
      if (ret.ok) {
        await app.$store.dispatch('usuario/auth', ret.data)
        if (app.$store.state.usuario.logado) app.redirectNow()
      }
      return ret
    }
  }
}
</script>

<style scoped>
.my-card {
  -webkit-box-shadow: -2px 4px 41px -14px rgb(194, 192, 192) !important;
  -moz-box-shadow:-2px 4px 41px -14px rgb(194, 192, 192) !important;
  box-shadow: -2px 4px 41px -14px rgb(194, 192, 192) !important;
}
</style>
