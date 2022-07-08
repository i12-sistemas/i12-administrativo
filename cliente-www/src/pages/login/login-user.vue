<template>
<q-card flat class="full-width" >
  <q-card-section>
    <q-btn-toggle v-model="tipouser" spread class="my-custom-toggle" no-caps rounded unelevated toggle-color="grey-9" color="grey-2" text-color="grey-9"
      :options="[
        {label: 'E-mail', value: 'email'},
        {label: 'Celular', value: 'celular'}
      ]"
    />
  </q-card-section>
  <q-card-section>
    <q-input outlined color="primary" bg-color="white" label-color="grey-10" ref="txtuser" v-model="username" :loading="sending" :disable="sending"
      :label="tipouser === 'celular' ? 'Telefone' : 'E-mail'"
      :type="tipouser === 'celular' ? 'phone' : 'email'"
      :maxlength="tipouser === 'celular' ? '20' : '255'"
      :hint="tipouser === 'celular' ? 'Informe somente número' : ''"
      :counter="tipouser === 'celular'"
      @keyup.13="actRequest"
      >
          <template v-slot:prepend>
          <q-icon name="mail" v-if="tipouser === 'email'" />
          <q-icon name="phone_iphone" v-if="tipouser === 'celular'" />
          </template>
    </q-input>
  </q-card-section>
  <q-card-section>
    <q-input outlined  color="primary" bg-color="white" label-color="grey-10"  v-model="password" ref="txtpwd"
      :loading="sending" :disable="sending" label="Senha" type="password"
      @keyup.13="actRequest"
      />
  </q-card-section>
  <q-card-actions align="center" class="row wrap justify-between q-px-md content-start">
    <q-btn class="full-width q-py-sm" unelevated color="primary" text-color="white" icon="far fa-keyboard" label="Acessar"
      :loading="sending" @click="actRequest"
      />
  </q-card-actions>
  <q-card-actions align="center" class="row wrap justify-between q-px-md content-start">
    <q-btn class="full-width" unelevated :to="{ name: 'login.resetpwd.request' }" label="Esqueci minha senha" no-caps
      color="grey-2" text-color="black" :disable="sending" />
  </q-card-actions>
  <!-- <q-separator  />
  <q-card-actions class="row full-width">
    <q-btn  :to="{ name: 'publico.carga.rastreabilidade' }" label="Rastreamento rápido de carga" no-caps icon="travel_explore"
      color="grey-9" flat :disable="sending"  class="full-width"/>
  </q-card-actions> -->
  <q-separator  />
  <q-card-section>
    <p class="text-caption text-center">
      Seu IP esta sendo registrado!
    </p>
  </q-card-section>
</q-card>
</template>

<script>

import { mapActions } from 'vuex'
import axios from 'axios'
export default {
  data () {
    return {
      tipouser: 'email',
      username: '',
      password: '',
      sending: false,
      redirect: null
    }
  },
  computed: {
    fichacessarallowaativa () {
      return ((this.username !== '') && (this.password !== ''))
    }
  },
  async mounted () {
    var app = this
    if (app.$route.query) {
      if (app.$route.query.redirect) app.redirect = app.$route.query.redirect
    }
    if (app.$store.state.usuario.logado) app.redirectNow()
    app.$nextTick(() => {
      app.$refs.txtuser.$el.focus()
    })
  },
  methods: {
    ...mapActions('authmotorista', [
      'logout'
    ]),
    async redirectNow () {
      var app = this
      if (app.redirect) {
        await app.$router.push({ path: app.redirect })
      } else {
        await app.$router.push({ name: 'entregas.consulta.rapida' })
      }
    },
    async closeApp () {
      // var app = this
      navigator.app.exitApp()
    },
    async actRequest (e) {
      var app = this
      if (app.username === '') {
        app.$refs.txtuser.$el.focus()
        return
      }
      if (app.password === '') {
        app.$refs.txtpwd.$el.focus()
        return
      }
      if (!app.fichacessarallowaativa) return
      app.sending = true
      let ret = await app.sendRequest(app.tipouser)
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
          app.$refs.txtuser.$el.focus()
        })
      })

      const timer = setTimeout(() => {
        dialog.hide()
      }, timeoutclose)
    },
    async sendRequest (tipo) {
      var app = this
      try {
        if (app.username === '') {
          this.$refs.txtuser.$el.focus()
          throw new Error('Usuário inválido')
        }
        if (app.password === '') {
          this.$refs.txtpwd.$el.focus()
          throw new Error('Senha inválida')
        }

        if (tipo === 'celular') {
          app.username = app.username.toString().trim()
          if (app.username.length < 8) throw new Error('Número celular inválido.\n\rInforme o número com DDD.')
        }
        if (tipo === 'email') {
          if (!app.$helpers.validaEmail(app.username)) throw new Error('E-mail inválido.')
        }
      } catch (error) {
        return { ok: false, msg: error.message }
      }

      var credentials = 'Basic ' + btoa(app.username + ':' + app.password)
      var req = axios.create({
        baseURL: app.$configini.API_URL,
        withCredentials: false,
        headers: {
          'x-auth-uuid': app.$store.state.usuario.uuid,
          'Authorization': credentials,
          'Access-Control-Allow-Origin': '*',
          'Access-Control-Allow-Headers': 'Authorization',
          'Access-Control-Allow-Methods': 'GET, POST, OPTIONS, PUT, PATCH, DELETE',
          'Content-Type': 'application/json;charset=UTF-8'
        }
      })
      var ret = await req.post('v1/painelcliente/login/usuario/auth').then(response => {
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
        await app.$store.dispatch('usuario/setlocalstorage', ret.data)
        if (app.$store.state.usuario.logado) app.redirectNow()
      }
      return ret
    }
  }
}
</script>
