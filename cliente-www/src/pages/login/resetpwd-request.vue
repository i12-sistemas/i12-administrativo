<template>
<div>
  <q-card class="my-card full-width" flat :bordered="!$q.platform.is.mobile" :class="($q.platform.is.mobile ? '' : 'bg-gradiente')">
    <q-card-section class="q-pa-none">
      <div class="row">
        <div class="q-pa-sm rounded-borders" :class="($q.screen.gt.sm ? 'col-md-5' : 'col-12') + ' ' +  ($q.platform.is.mobile ? '' : '')">
          <div class="row flex-center full-width" >
            <q-card flat class="full-width"  style="border: 6px solid rgba(255, 255, 255, 0.541);" >
              <q-card-section class="bg-white justify-center items-center text-center">
                <img src="~assets/Logo-i12-horizontal150x50.png"  width="100%" style="max-width: 300px" >
              </q-card-section>
              <q-separator spaced inset  />
              <q-card-section class="q-pa-none q-ma-none">
                <q-card flat class="full-width" >
                  <q-card-section v-if="solicitado">
                    <div class="row wrap justify-center q-pt-lg content-start">
                      <q-icon name="check_circle" color="positive" size="60px" />
                    </div>
                    <div class="row justify-center q-pa-sm content-start text-body">
                      Link enviado ao e-mail informado.
                    </div>
                    <div class="row wrap justify-center content-start text-body">
                      Acesse sua caixa de e-mail e siga as instruções para concluir a alteração de senha!
                    </div>
                    <div class="row wrap justify-center q-pt-md content-start text-body">
                      * Não esquece de conferir na sua caixa de Spam.
                    </div>
                    <div class="row wrap justify-center content-start text-caption">
                      O link expira em 1 hora!
                    </div>
                  </q-card-section>
                  <q-card-section v-if="!solicitado">
                    <q-input outlined color="primary" bg-color="white" label-color="grey-10" ref="txtuser" v-model="username" :loading="sending" :disable="sending"
                      label="E-mail / WhatsApp" type="email" maxlength="255" hint="E-mail ou número de WhatsApp" counter @keyup.13="actRequest" >
                        <template v-slot:prepend>
                          <q-icon name="person"/>
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
                    <q-btn class="full-width q-py-sm"  unelevated color="primary" text-color="white" icon="send" label="Solicitar alteração"
                      :loading="sending" @click="actRequest"
                      :disable="solicitado"
                      />
                  </q-card-actions>
                  <q-card-actions align="center" class="row wrap justify-between q-px-md content-start">
                    <q-btn class="full-width" unelevated :to="{ name: 'login.usuario' }" label="Voltar" no-caps
                      color="grey-2" text-color="black" icon="arrow_back" :disable="sending" />
                  </q-card-actions>
                  <q-card-section>
                    <p class="text-caption text-center">
                      Seu IP esta sendo registrado!
                    </p>
                  </q-card-section>
                </q-card>
              </q-card-section>
            </q-card>
          </div>
        </div>
        <div class="col-md-7 q-pa-sm" v-if="$q.screen.gt.sm">
          <lottieinternal arquivo="login" :loop="false"/>
        </div>
      </div>
    </q-card-section>
  </q-card>
</div>

</template>

<script>
import VueRecaptcha from 'vue-recaptcha'
import axios from 'axios'
export default {
  data () {
    return {
      username: 'weber@i12.com.br',
      solicitado: false,
      recaptchaloading: true,
      recaptchatoken: null,
      recaptchasitekey: null,
      sending: false
    }
  },
  components: {
    VueRecaptcha
  },
  computed: {
  },
  async mounted () {
    var app = this
    app.recaptchasitekey = app.$configini.RECAPTCHA_KEY ? app.$configini.RECAPTCHA_KEY : null
    if (app.recaptchasitekey ? app.recaptchasitekey === '' : true) app.recaptchasitekey = null
    if (app.$route.query) {
      if (app.$route.query.redirect) app.redirect = app.$route.query.redirect
    }
    app.$refs.txtuser.$el.focus()
  },
  methods: {
    async redirectNow () {
      var app = this
      if (app.redirect) {
        app.$router.push({ path: app.redirect })
      } else {
        app.$router.push({ name: 'home' })
      }
    },
    async closeApp () {
      // var app = this
      navigator.app.exitApp()
    },
    rendered (id) {
      this.recaptchaloading = false
    },
    async verify (response) {
      this.recaptchatoken = response
    },
    async actRequest (e) {
      var app = this
      try {
        if (app.username === '') {
          app.$refs.txtuser.$el.focus()
          return
        }
      } catch (error) {
        if (error.message) {
          app.actShowError('Validação', error.message, 4000)
        } else {
          return
        }
      }
      app.sending = true
      let ret = await app.sendRequest()
      if (!ret.ok) {
        app.recaptchatoken = null
        if (app.recaptchasitekey ? app.recaptchasitekey !== '' : false) {
          app.$refs.recaptcha.reset()
        }
        app.actShowError('Acesso restrito', ret.msg, 4000)
      } else {
        if (ret.msg !== '') {
          app.$q.notify({
            message: ret.msg,
            color: 'positive',
            actions: [
              { label: 'OK', color: 'white', handler: () => { /* ... */ } }
            ]
          })
        }
        app.$router.push({ name: 'login.resetpwd.change', query: { username: ret.data.username, token: ret.data.token } })
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
        app.email = ''
        app.username = ''
        app.$refs.txtuser.$el.focus()
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
      } catch (error) {
        return { ok: false, msg: error.message }
      }

      var params = {
        username: app.username
      }
      if (app.recaptchasitekey ? app.recaptchasitekey !== '' : false) {
        params['g-recaptcha-response'] = app.recaptchatoken
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
      var ret = await req.post('v1/contato/auth/pwd/reset/request', params).then(response => {
        let data = response.data
        var ret = { ok: false, msg: '' }
        if (data) {
          ret.msg = data.msg ? data.msg : ''
          ret.ok = data.ok
          ret.data = data.data
        }
        return ret
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
        app.solicitado = true
      }
      return ret
    }
  }
}
</script>

<style scoped>
.min-width {
  min-width: 450px !important;
}
</style>
