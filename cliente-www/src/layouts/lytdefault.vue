<template>
  <q-layout view="lHh LpR lff" class="bg-grey-1" >

    <!-- menu opção geral somente mobile -->
    <q-drawer show-if-above v-model="leftDrawerOpen" side="left" bordered content-class="bg-grey-1" >
      <q-scroll-area class="fit">
        <div class="q-pa-md bg-white"  >
          <div class=" full-width text-center items-center no-wrap cursor-pointer" >
            <img src="~assets/Logo-i12-horizontal150x50.png" style="max-height: 40px;" @click="$router.push({ name: 'home' })" >
          </div>
        </div>
        <!-- usuario -->
        <div>
          <q-separator />
          <q-list separator class="bg-grey-2">
            <q-expansion-item expand-separator default-opened icon="perm_identity" :label="$helpers.strEllipsis(usuariologado.nome, 15)" >
              <q-separator />
              <q-list class="bg-white" style="border-left: 5px solid #027be3">
                <q-item  v-if="usuariologado">
                  <q-item-section>
                    <q-item-label caption  class="row q-gutter-xs">
                      <q-badge color="black"  text-color="white" label="Administrador" v-if="usuariologado.ehadministrador" />
                      <q-badge color="green" text-color="white" label="Financeiro" v-if="usuariologado.ehfinanceiro && !usuariologado.ehadministrador" />
                      <q-badge color="blue-10" text-color="white" label="Operador" v-if="usuariologado.ehoperador && !usuariologado.ehadministrador" />
                      <q-badge color="cyan" text-color="white" label="Contador" v-if="usuariologado.ehcontador && !usuariologado.ehadministrador" />
                    </q-item-label>
                    <q-item-label >{{usuariologado.permissao}}</q-item-label>
                  </q-item-section>
                </q-item>
                <q-item  v-if="usuariologado.whatsapp ? usuariologado.whatsapp !== '' : false">
                  <q-item-section avatar>
                    <q-icon name="whatsapp" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label> {{usuariologado.whatsapp}}</q-item-label>
                  </q-item-section>
                </q-item>
                <q-item  v-if="usuariologado.email ? usuariologado.email !== '' : false">
                  <q-item-section avatar>
                    <q-icon name="mail" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label> {{usuariologado.email}}</q-item-label>
                  </q-item-section>
                </q-item>
                <q-item clickable v-ripple :to="({name: 'usuario.meuperfil'})" disable>
                  <q-item-section avatar>
                    <q-icon name="edit" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>Meu perfil</q-item-label>
                    <q-item-label caption>Gerenciar meu perfil</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-expansion-item>
            <q-expansion-item default-opened icon="attach_money" label="Financeiro" caption="Contratos e serviços" v-if="usuariologado.ehfinanceiro">
              <q-separator />
              <q-list class="bg-white" style="border-left: 5px solid #027be3">
                <q-item clickable v-ripple :to="{ name: 'financeiro.mensalidades' }" disable>
                  <q-item-section avatar>
                    <q-icon name="search"  />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>Faturas</q-item-label>
                    <q-item-label caption>Gerenciar suas faturas</q-item-label>
                  </q-item-section>
                </q-item>
                <q-item clickable v-ripple :to="{ name: 'financeiro.contratos' }" disable>
                  <q-item-section avatar>
                    <q-icon name="add" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>Contratos</q-item-label>
                    <q-item-label caption>Contratos e vigências</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-expansion-item>
            <q-expansion-item default-opened icon="support" label="Suporte" caption="Abertura de tickets e ajuda" v-if="usuariologado.ehoperador">
              <q-separator />
              <q-list class="bg-white" style="border-left: 5px solid #027be3">
                <q-item clickable v-ripple :to="{ name: 'atendimentos' }">
                  <q-item-section avatar>
                    <q-icon name="search" v-if="!usuariodashloading" />
                    <q-spinner size="1.5em" :thickness="5" v-if="usuariodashloading" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>Meus atendimentos</q-item-label>
                    <q-item-label caption>Gerenciar meus atendimentos</q-item-label>
                  </q-item-section>
                  <q-item-section avatar side v-if="usuariologado.dashboard_atendimento ? typeof usuariologado.dashboard_atendimento.emabertos !== 'undefined' : false">
                    <q-avatar size="28px" font-size="13px" color="light-blue-3" text-color="black">{{usuariologado.dashboard_atendimento.emabertos > 99 ? '+99' : usuariologado.dashboard_atendimento.emabertos}}</q-avatar>
                  </q-item-section>
                </q-item>
                <q-item clickable v-ripple :to="{ name: 'atendimentos.novo' }">
                  <q-item-section avatar>
                    <q-icon name="add" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>Novo atendimento</q-item-label>
                    <q-item-label caption>Registrar novo atendimento</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-expansion-item>
            <q-separator  />
            <q-expansion-item default-opened icon="receipt_long" label="Contabilidade" caption="Gestão de documentos fiscais" v-if="usuariologado.ehcontador">
              <q-separator />
              <q-list class="bg-white" style="border-left: 5px solid #027be3">
                <q-item clickable v-ripple :to="{ name: 'atendimentos' }" disable>
                  <q-item-section avatar>
                    <q-icon name="search" v-if="!usuariodashloading" />
                    <q-spinner size="1.5em" :thickness="5" v-if="usuariodashloading" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>NF-e</q-item-label>
                    <q-item-label caption>Em desenvolvimento</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-expansion-item>
            <q-separator  />
            <q-item clickable  v-ripple :to="({name: 'logout.usuario'})" v-if="(usuariologado)" >
              <q-item-section avatar>
                <q-icon name="power_settings_new" />
              </q-item-section>
              <q-item-section>Sair</q-item-section>
            </q-item>
          </q-list>
        </div>
      </q-scroll-area>
    </q-drawer>
    <!-- menu opção geral somente mobile -->

    <q-page-container >
      <transition name="fade">
        <!-- <keep-alive :include="['entregas.consulta', 'rastrear.entrega', 'comanda.pedido.add']"> -->
        <keep-alive>
          <router-view />
        </keep-alive>
      </transition>
    </q-page-container>

    <q-footer :class="$q.platform.is.mobile ? 'bg-primary text-grey-1' : 'bg-grey-2 text-grey-8'"  bordered v-if="$route.name === 'home'">
      <div class="q-mt-md  text-caption q-pa-xs text-center">
        <div><span class="text-weight-bold">{{appPackage.productName}} :: {{appPackage.description}}</span> | Versão {{appPackage.version}} release {{$helpers.datetimeToBR(appPackage.releasedatetime)}}</div>
        <div>© Copyright {{year ? year : ''}} :: <a href="https://www.i12.com.br" target="_blank">i12.com.br</a></div>
      </div>
    </q-footer>
  </q-layout>
</template>

<script>
import datapackage from '../../package.json'
import moment from 'moment'

export default {
  name: 'MyLayout',
  components: {
  },
  data () {
    return {
      title: 'i12 Sistemas',
      year: 2021,
      optionsmenu: null,
      appPackage: datapackage,
      usuariodashloading: false,
      loading: false,
      notificationsnoread: 0,
      // padrao de notificacao
      // { id: 'xxxx', title: 'Titulo da notificação', msg: 'teste', icon: 'sync', color: 'positive', created_at: '2020-01-20 14:50:55', read: false, url: '' },
      notifications: [],
      currentRComponent: null,
      leftDrawerOpen: false,
      rightDrawerOpen: false,
      permitefollowupconsulta: null,
      search: ''
    }
  },
  created: function () {
    this.$eventbus.$on('togglemenu', this.toggleMenu)
    this.$eventbus.$on('usuariodash', this.onusuariodash)
    this.$eventbus.$on('usuariodash_updated', this.onusuariodash_updated)
    this.$eventbus.$on('setdrawerr', this.onSetDrawerR)
    this.$eventbus.$on('notification_add', this.onAddNotificacao)
  },
  beforeDestroy: function () {
    this.$eventbus.$off('togglemenu', this.toggleMenu)
    this.$eventbus.$off('usuariodash', this.onusuariodash)
    this.$eventbus.$off('usuariodash_updated', this.onusuariodash_updated)
    this.$eventbus.$off('notification_add', this.onAddNotificacao)
    this.$eventbus.$off('drawerr_remove', this.onRemoveDrawerR)
  },
  meta () {
    return {
      title: this.title
    //   description: { name: 'description', content: 'Page 1' },
    //   keywords: { name: 'keywords', content: 'Quasar website' },
    //   equiv: { 'http-equiv': 'Content-Type', content: 'text/html; charset=UTF-8' },
    //   // note: for Open Graph type metadata you will need to use SSR, to ensure page is rendered by the server
    //   ogTitle: {
    //     name: 'og:title',
    //     // optional; similar to titleTemplate, but allows templating with other meta properties
    //     template (ogTitle) {
    //       return `${ogTitle} - My Website`
    //     }
    //   }
    }
  },
  async mounted () {
    var app = this
    if (app.clientelogado) app.title = app.clientelogado.nome + ' :: i12 Sistemas'
    this.$q.addressbarColor.set('#a2e3fa')
    // app.optionsmenu = this.$store.state.homedashboard.options
    // app.permitefollowupconsulta = app.$helpers.permite('followup.consulta')
    app.year = moment().year()
    if (app.usuariologado ? !app.usuariologado.dashboard_atendimento : false) app.$eventbus.$emit('usuariodash', false)
  },
  computed: {
    usuariologado: function () {
      var app = this
      let u = null
      if (app.$store.state.usuario.logado) {
        if (app.$store.state.usuario.usuario) {
          u = app.$store.state.usuario.usuario
        }
      }
      return u
    },
    clientelogado: function () {
      var app = this
      let u = null
      if (app.$store.state.usuario.logado) {
        if (app.$store.state.usuario.cliente) {
          u = app.$store.state.usuario.cliente
        }
      }
      return u
    }
    // permissoes: function () {
    //   var app = this
    //   let u = null
    //   if (app.$store.state.usuario.logado) {
    //     if (app.$store.state.usuario.usuario) {
    //       if (app.$store.state.usuario.usuario.codusuario > 0) u = app.$store.state.usuario.usuario.permissoes
    //     }
    //   }
    //   return u
    // },
    // empresalogada: function () {
    //   var app = this
    //   let u = null
    //   if (app.$store.state.usuario.logado) {
    //     if (app.$store.state.usuario.empresa) {
    //       if (app.$store.state.usuario.empresa.codempresa > 0) u = app.$store.state.usuario.empresa
    //     }
    //   }
    //   return u
    // }
  },
  methods: {
    toggleMenu () {
      this.leftDrawerOpen = !this.leftDrawerOpen
    },
    onSetDrawerR (pComponent, pStartOpened = true) {
      var app = this
      app.leftDrawerOpen = pStartOpened
      app.currentRComponent = pComponent
      if (app.leftDrawerOpen) {
        app.$nextTick(() => {
          app.$refs.drawerLeftComponent.show()
        })
      }
    },
    onRemoveDrawerR () {
      this.leftDrawerOpen = false
      this.currentRComponent = null
    },
    async onusuariodash (ShowNotification = false) {
      var app = this
      if (app.usuariodashloading) return
      app.usuariodashloading = true
      console.log('iniciado')
      await app.$store.dispatch('usuario/refreshDashboard', ShowNotification)
    },
    async onusuariodash_updated () {
      var app = this
      app.usuariodashloading = false
    },
    actAbrirLink (status) {
      var app = this
      if (app.$route.name === 'coletas.consulta') {
        app.$eventbus.$emit('coletasconsultaupdated', status)
      } else {
        app.$router.push({ name: 'coletas.consulta', query: { customfilter: status, t: new Date().getTime() } })
      }
    },
    actClickNotificacao (pNotificacao) {
      var app = this
      app.notificationsnoread = 0
      if (pNotificacao.url ? pNotificacao.url !== '' : false) {
        app.$helpers.href(pNotificacao.url, ((pNotificacao.urltarget ? pNotificacao.urltarget !== '' : false) ? pNotificacao.urltarget : '_self'))
      }
    },
    onAddNotificacao (pNotificacao) {
      var app = this
      if (!pNotificacao) return
      if (!app.notifications) app.notifications = []
      app.notifications.push(pNotificacao)
      app.notificationsnoread = parseInt(app.notificationsnoread) + 1
      var actions = []
      var url = null
      if (pNotificacao.url ? pNotificacao.url !== '' : false) {
        url = {
          label: (pNotificacao.urllabel ? pNotificacao.urllabel !== '' : false) ? pNotificacao.urllabel : 'Abrir para a página',
          color: 'white',
          handler: () => {
            app.$helpers.href(pNotificacao.url, ((pNotificacao.urltarget ? pNotificacao.urltarget !== '' : false) ? pNotificacao.urltarget : '_self'))
          }
        }
        actions.push(url)
      }
      app.$q.notify({
        color: (pNotificacao.color ? pNotificacao.color !== '' : false) ? pNotificacao.color : 'blue',
        icon: (pNotificacao.icon ? pNotificacao.icon !== '' : false) ? pNotificacao.icon : 'circle_notifications',
        position: 'bottom',
        multiLine: true,
        timeout: 5000,
        message: (pNotificacao.title ? pNotificacao.title !== '' : false) ? pNotificacao.title : 'Nova notificação',
        caption: pNotificacao.msg,
        actions: actions
      })
    },
    actShowMenu () {
      var app = this
      app.leftDrawerOpen = !app.leftDrawerOpen
      if ((app.currentRComponent) && (app.leftDrawerOpen)) {
        app.$nextTick(() => {
          app.$refs.drawerLeftComponent.show()
        })
      }
    }
  }
}
</script>

<style>
.meuusuariodesktop {
  min-width: 450px
}
.gradientToolbar {
  background-color: #0271d0; /* For browsers that do not support gradients */
  background-image: linear-gradient(to right, #f05b41 , #f05b41); /* Standard syntax (must be last) */
}
.doc-page {
  padding: 16px 46px;
  font-weight: 300;
  max-width: 900px;
  margin-left: auto;
  margin-right: auto
}
.doc-header {
  padding-top: 35px;
}
.dialogfullheightdesktop {
  width: 700px;
  max-width: 80vw;
}
.titletoobarcustom {
  min-width: 900px;
  padding-left: 46px;
  margin-left: auto;
  margin-right: auto
}
.doc-page>div,.doc-page>pre {
  margin-bottom: 22px
 }
@media (max-width: 600px) {
  .doc-page {
    padding: 0px;
    padding-top: 35px;
  }
  .doc-header {
    padding-top: 55px;
  }
  .titletoobarcustom {
    min-width: auto;
    padding-left: 16px;
  }
}
  .header-item {
    background-color: #d6d1d1;
  }

.header-top-bg {
  height: 110px;
  position: absolute;
  top: 0;
  left: 0;
  border-radius: 0 0 8px 8px;
}
</style>
