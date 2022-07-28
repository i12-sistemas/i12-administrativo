<template>
<q-card flat class="full-width" >
  <q-card-section>
    <q-input outlined color="primary" bg-color="white" label-color="grey-10" ref="txtuser" v-model="username" :loading="sending" :disable="sending"
      label="Usuário" type="email" maxlength="255" hint="Informe o nome de usuário" counter @keyup.13="actRequest" >
        <template v-slot:prepend>
          <q-icon name="person"/>
        </template>
  </q-input>
  </q-card-section>
  <q-card-section>
    <q-input outlined  color="primary" bg-color="white" label-color="grey-10"  v-model="password" ref="txtpwd"
      :loading="sending" :disable="sending" label="Senha" type="password"
      @keyup.13="actRequest"
      >
      <template v-slot:prepend>
        <q-icon name="lock"/>
      </template>
    </q-input>
  </q-card-section>
  <q-card-section class="text-center q-py-md" v-if="(recaptchasitekey ? recaptchasitekey !== '' : false)">
    <div class="full-width row justify-center items-center">
      <div v-if="recaptchaloading">carregando...</div>
      <vue-recaptcha ref="recaptcha" :sitekey="recaptchasitekey" @verify="verify" :loadRecaptchaScript="true" @render="rendered" ></vue-recaptcha>
    </div>
  </q-card-section>
  <q-card-actions align="center" class="row wrap justify-between q-px-md content-start">
    <q-btn class="full-width q-py-sm" unelevated  text-color="white" icon="far fa-keyboard" label="Acessar"
      :loading="sending" @click="actRequest" color="primary"
      />
  </q-card-actions>
  <q-card-section>
    <p class="text-caption text-center">
      Seu IP esta sendo registrado!
    </p>
  </q-card-section>
</q-card>
</template>

<script>

import VueRecaptcha from 'vue-recaptcha'
import { mapActions } from 'vuex'
import axios from 'axios'
export default {
  data () {
    return {
      username: '',
      password: '',
      recaptchaloading: true,
      recaptchatoken: null,
      recaptchasitekey: null,
      sending: false,
      redirect: null
    }
  },
  components: {
    VueRecaptcha
  },
  computed: {
    allowpost: function () {
      var app = this
      var b = true
      try {
        if (app.sending) throw new Error('Em loading')
        if (app.username ? app.username === '' : true) throw new Error('username inválido')
        if (app.password ? app.password === '' : true) throw new Error('password inválido')
        if (app.recaptchasitekey ? app.recaptchasitekey !== '' : false) {
          if (!app.recaptchatoken) throw new Error('Sem recaptcha nota')
        }
      } catch (error) {
        b = false
      }
      return b
    }
  },
  async mounted () {
    var app = this
    app.recaptchasitekey = app.$configini.RECAPTCHA_KEY ? app.$configini.RECAPTCHA_KEY : null
    if (app.recaptchasitekey ? app.recaptchasitekey === '' : true) app.recaptchasitekey = null
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
        await app.$router.push({ name: 'home' })
      }
    },
    rendered (id) {
      this.recaptchaloading = false
    },
    async verify (response) {
      this.recaptchatoken = response
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
      if (!app.allowpost) return
      app.sending = true
      let ret = await app.sendRequest()
      if (!ret.ok) {
        app.recaptchatoken = null
        if (app.recaptchasitekey ? app.recaptchasitekey !== '' : false) {
          app.$refs.recaptcha.reset()
        }
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
    async sendRequest () {
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

        app.username = app.username.toString().trim()
        if (app.username.length <= 1) throw new Error('Usuário inválido')
      } catch (error) {
        return { ok: false, msg: error.message }
      }

      var params = {}
      if (app.recaptchasitekey ? app.recaptchasitekey !== '' : false) {
        params['g-recaptcha-response'] = app.recaptchatoken
      }
      var credentials = 'Basic ' + btoa(app.username + ':' + app.password)
      var req = axios.create({
        baseURL: app.$configini.API_URL,
        withCredentials: false,
        headers: {
          // 'x-auth-uuid': app.$store.state.usuario.uuid,
          'Authorization': credentials,
          'Access-Control-Allow-Origin': '*',
          'Access-Control-Allow-Headers': 'Authorization',
          'Access-Control-Allow-Methods': 'GET, POST, OPTIONS, PUT, PATCH, DELETE',
          'Content-Type': 'application/json;charset=UTF-8'
        }
      })
      var ret = await req.post('v1/auth', params).then(response => {
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
