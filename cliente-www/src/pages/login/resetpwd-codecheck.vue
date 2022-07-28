<template>
<div>
  <q-card class="my-card full-width" flat :bordered="!$q.platform.is.mobile" :class="($q.platform.is.mobile ? '' : 'bg-gradiente')">
    <q-card-section class="q-pa-none">
      <div class="row">
        <div class="q-pa-sm rounded-borders" :class="($q.screen.gt.sm ? 'col-sm-6 col-md-5' : 'col-12') + ' ' +  ($q.platform.is.mobile ? '' : '')">
          <div class="row flex-center full-width" >
            <q-card flat class="full-width"  style="border: 6px solid rgba(255, 255, 255, 0.541);" >
              <q-card-section class="bg-white justify-center items-center text-center">
                <img src="~assets/Logo-i12-horizontal150x50.png"  width="100%" style="max-width: 300px" >
              </q-card-section>
              <q-separator spaced inset  />
              <q-card-section class="q-pa-none q-ma-none">
                <q-card flat class="full-width" >
                  <q-card-section class="q-pa-none" v-if="!checado">
                    <q-form ref="myForm" @submit.prevent="actCheckCode"  >
                    <div class="row flex-center q-pb-md q-px-md full-width" >
                      <q-card flat :bordered="false" class="full-width q-pa-none" >
                        <q-card-section class="q-pa-none q-pt-sm ">
                          <div class="row justify-center content-start text-subtitle1">
                            <q-icon name="email" color="primary" size="24px" class="q-pr-sm" v-if="tipo === 'email'" />
                            <q-icon name="whatsapp" color="green" size="24px" class="q-pr-sm" v-if="tipo === 'whatsapp'"/>
                            Confira o código enviado para {{tipo === 'email' ? 'e-mail' : 'WhatsApp'}}
                          </div>
                          <div class="row wrap justify-center q-pb-lg content-start text-subtitle1 text-weight-bold">
                            <div v-if="tipo === 'email'">{{ $helpers.emailhide(username)}}</div>
                            <div v-if="tipo === 'whatsapp'">{{$helpers.emailphone(username)}}</div>
                          </div>
                          <div class="row wrap justify-start content-start text-body" v-if="tipo === 'email'">
                            <div>Acesse sua caixa de e-mail e siga as instruções para concluir a alteração de senha!</div>
                            <div class="text-caption text-left">* Não esqueça de conferir na sua caixa de Spam.</div>
                          </div>
                        </q-card-section>
                        <q-card-section class="text-weight-bold text-center rounded-borders bg-light-green-1 text-green q-my-md q-pa-none q-py-sm">
                            O código expira em 1 hora!
                        </q-card-section>
                        <q-card-section>
                          <q-input outlined color="primary" bg-color="white" label-color="grey-10" v-model="codenumber" label="Código" stack-label type="number" ref="txtcodenumber" class="codenumber" />
                        </q-card-section>
                        <q-card-section class="text-center q-py-md" v-if="(recaptchasitekey ? recaptchasitekey !== '' : false)">
                          <div class="full-width row justify-center items-center">
                            <div v-if="recaptchaloading">carregando...</div>
                            <vue-recaptcha ref="recaptcha" :sitekey="recaptchasitekey" @verify="verify" :loadRecaptchaScript="true" @render="rendered" ></vue-recaptcha>
                          </div>
                        </q-card-section>
                        <q-card-actions align="center">
                          <q-btn class="full-width q-py-sm" unelevated color="primary" text-color="white" :loading="sending" type="submit" label="Validar código" />
                        </q-card-actions>
                        <q-card-actions align="center">
                          <q-btn class="full-width" unelevated color="grey-3" text-color="black" :to="{ name: 'login.usuario' }" :disable="sending" label="Login de acesso" />
                        </q-card-actions>
                        <q-card-section>
                          <p class="text-caption text-center">
                            Seu IP esta sendo registrado!
                          </p>
                        </q-card-section>
                      </q-card>
                    </div>
                    </q-form>
                  </q-card-section>

                  <q-card-section class="q-pa-none" v-if="checado">
                    <q-form ref="myFormChecado" @submit.prevent="actChangePwd" >
                    <div class="row flex-center q-pb-md q-px-md full-width">
                      <q-card flat :bordered="false" class="full-width" >
                        <q-card-section class="q-pa-none">
                          <div class="row justify-center q-pt-sm content-start text-subtitle1">
                            <q-icon name="check_circle" color="positive" size="24px" class="q-pr-sm" />
                            Código verificado para a conta
                          </div>
                          <div class="row wrap justify-center q-pt-lg content-start text-subtitle1 text-weight-bold">
                            {{datatoken.username}}
                          </div>
                          <div class="row wrap justify-center content-start text-body">
                            Informe sua nova senha
                          </div>
                        </q-card-section>
                        <q-card-section>
                          <q-input outlined color="primary" bg-color="white" label-color="grey-10" v-model="password1" ref="password1" :loading="sending" :disable="sending" label="Senha" stack-label type="password"/>
                        </q-card-section>
                        <q-card-section>
                          <q-input outlined color="primary" bg-color="white" label-color="grey-10" v-model="password2" ref="password2" :loading="sending" :disable="sending" label="Confirmação de senha" stack-label type="password"/>
                        </q-card-section>
                        <q-card-actions align="center" class="row wrap justify-between q-px-md content-start">
                          <q-btn class="full-width" unelevated color="primary" text-color="white" :loading="sending" type="submit" label="Alterar senha" />
                        </q-card-actions>
                        <q-card-actions align="center" class="row wrap justify-between q-px-md content-start">
                          <q-btn class="full-width" color="grey-2" text-color="black" icon="arrow_back" unelevated :to="{ name: 'login.usuario' }" label="Login de acesso" :disable="sending"/>
                        </q-card-actions>
                        <q-card-section>
                          <p class="text-caption text-center">
                            Seu IP esta sendo registrado!
                          </p>
                        </q-card-section>
                      </q-card>
                    </div>
                    </q-form>
                  </q-card-section>
                </q-card>
              </q-card-section>
            </q-card>
          </div>
        </div>
        <div class="col-sd-6 col-md-7 q-pa-sm" v-if="$q.screen.gt.sm">
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
  components: {
    VueRecaptcha
  },
  data () {
    return {
      tipo: '',
      username: '',
      codenumber: '',
      token: '',
      checado: false,
      datatoken: null,
      recaptchaloading: true,
      recaptchatoken: null,
      recaptchasitekey: null,
      sending: false,
      password1: '',
      password2: ''
    }
  },
  computed: {
  },
  async mounted () {
    var app = this
    app.recaptchasitekey = app.$configini.RECAPTCHA_KEY ? app.$configini.RECAPTCHA_KEY : null
    if (app.recaptchasitekey ? app.recaptchasitekey === '' : true) app.recaptchasitekey = null
    if (app.$route.query) {
      if (app.$route.query.username) {
        app.username = atob(app.$route.query.username)
        app.tipo = app.$helpers.validaEmail(app.username) ? 'email' : 'whatsapp'
      }
      if (app.$route.query.code) app.codenumber = app.$route.query.code
      if (app.$route.query.token) app.token = app.$route.query.token
    }
    // if ((app.username !== '') && (app.codenumber !== '')) {
    //   setTimeout(() => {
    //     app.actCheckCode()
    //   }, 500)
    // }
  },
  methods: {
    rendered (id) {
      this.recaptchaloading = false
    },
    async verify (response) {
      this.recaptchatoken = response
    },
    async actChangePwd (e) {
      var app = this
      if (app.password1 === '') {
        app.$refs.txtpassword1.$el.focus()
        return
      }
      if (app.password2 === '') {
        app.$refs.password2.$el.focus()
        return
      }
      app.sending = true
      let ret = await app.sendChangePwd()
      if (!ret.ok) {
        app.actShowError('Acesso restrito', ret.msg, 4000)
      } else {
        if (ret.msg !== '') {
          app.$q.notify({
            message: 'Senha alterada!',
            color: 'positive',
            actions: [
              { label: 'OK', color: 'white', handler: () => { /* ... */ } }
            ]
          })
        }
        app.$router.push({ name: 'login.usuario' })
      }
      app.sending = false
    },
    async sendChangePwd () {
      var app = this
      try {
        if (!app.datatoken) throw new Error('Código não checado')
        if (!app.checado) throw new Error('Código não checado')
        if (app.datatoken.username === '') throw new Error('Usuário inválido')
        if (app.datatoken.codenumber === '') throw new Error('Código não informado')
        if (app.datatoken.token === '') throw new Error('Token não informado')

        if (app.password1 === '') throw new Error('Senha inválida')
        if (app.password2 === '') throw new Error('Confirmação de senha inválida')
        if (!(app.password2 === app.password1)) throw new Error('Senha e confirmação estão diferentes')
      } catch (error) {
        return { ok: false, msg: error.message }
      }
      let params = {
        username: app.datatoken.username,
        codenumber: app.datatoken.codenumber,
        token: app.datatoken.token,
        pwd: app.password1
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
      var ret = await req.post('v1/contato/auth/pwd/reset/changepwd', params).then(response => {
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
      return ret
    },
    async closeApp () {
      // var app = this
      navigator.app.exitApp()
    },
    async actCheckCode (e) {
      var app = this
      if (app.code === '') {
        app.$refs.txtcodenumber.$el.focus()
        return
      }
      app.sending = true
      let ret = await app.sendCheckCode()
      if (!ret.ok) {
        app.actShowError('Acesso restrito', ret.msg, 4000)
      } else {
        app.checado = true
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
        app.codenumber = ''
      })

      const timer = setTimeout(() => {
        dialog.hide()
      }, timeoutclose)
    },
    async sendCheckCode () {
      var app = this
      try {
        if (app.username === '') throw new Error('Usuário inválido')
        if (app.token === '') throw new Error('Token não informado')
        if (app.codenumber === '') throw new Error('Código não informado')
      } catch (error) {
        return { ok: false, msg: error.message }
      }
      let params = {
        username: app.username,
        token: app.token,
        codenumber: app.codenumber
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
      var ret = await req.post('v1/contato/auth/pwd/reset/check', params).then(response => {
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
        app.checado = true
        app.datatoken = ret.data
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

.codenumber {
    font-size:1.5rem;
    line-height:1.5rem;
}
</style>
