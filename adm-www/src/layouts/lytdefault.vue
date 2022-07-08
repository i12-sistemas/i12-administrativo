<template>
  <q-layout view="lHh LpR lff" class="bg-grey-1" >

    <!-- menu opção geral somente mobile -->
    <q-drawer show-if-above v-model="leftDrawerOpen" side="left" bordered content-class="bg-grey-1" >
      <q-scroll-area class="fit">
        <div class="q-mt-sm" style="border-color: #e0e0e0">
          <div class="bg-white full-width text-center items-center no-wrap cursor-pointer" >
            <img src="~assets/Logo-i12-horizontal150x50.png" style="max-height: 36px;" @click="$router.push({ name: 'home' })" >
          </div>
        </div>
        <!-- usuario -->
        <div>
          <q-list>
            <q-expansion-item
              expand-separator
              icon="perm_identity"
              :label="$helpers.strEllipsis(usuariologado.nome, 15)"
            >
            <q-card class="q-ma-sm bg-white" bordered style="max-width: 280px"  >
              <q-card-section >
                <div class="" v-if="usuariologado.celular !== ''"><q-icon name="phone" /> {{usuariologado.celular}}</div>
                <div class="" v-if="usuariologado.email !== ''"><q-icon name="mail" /> {{usuariologado.email}}</div>
              </q-card-section>
              <q-separator   />
              <q-card-section >
                <div class="text-caption text-weight-bold">Empresas associadas</div>
                <div class="ellipsis text-left" v-if="usuariologado.clientescount > 0">
                  <div class="ellipsis text-caption" v-for="(cli, k) in usuariologado.clientes" :key="'cli' + k">
                    {{ cli.cnpj ? $helpers.mascaraDocCPFCNPJ(cli.cnpj) + ' :: ': ''}}
                    {{cli.razaosocial !== '' ? cli.razaosocial : cli.fantasia }}
                    <q-tooltip :delay="500">
                      {{ cli.cnpj ? $helpers.mascaraDocCPFCNPJ(cli.cnpj) + ' :: ': ''}} {{cli.razaosocial !== '' ? cli.razaosocial : cli.fantasia }}
                    </q-tooltip>
                  </div>
                </div>
                <div class="text-weight-bold" v-if="usuariologado.clientescount <= 0">Nenhuma empresa identificada</div>
              </q-card-section>
              <q-separator />
              <q-card-section class="q-pa-none">
                <q-btn label="Meu perfil" :to="({name: 'usuario.meuperfil'})" v-close-popup unelevated icon="fa fa-user-circle" class="full-width" />
                <q-btn label="Sair" :to="({name: 'logout.usuario'})" v-close-popup unelevated icon="power_settings_new" class="full-width" />
              </q-card-section>
            </q-card>
            </q-expansion-item>
          </q-list>
        </div>
        <q-separator  />
        <q-list class="text-black">
          <q-item class="header-item" v-if="(!usuariologado.permitestatusgeral) && (!usuariologado.pemitefup) && (usuariologado.followupcount < 1)">
            <q-item-section class="text-weight-bold">OPERACIONAL</q-item-section>
          </q-item>
          <!-- <q-item clickable  v-ripple :to="{ name: 'home' }" v-if="(usuariologado ? usuariologado.permitestatusgeral : false)" > -->
          <q-item clickable v-ripple :to="{ name: 'tabelaibpt' }" v-if="(usuariologado)" >
            <q-item-section avatar>
              <q-icon name="dashboard" />
            </q-item-section>
            <q-item-section>Tabela IBPT</q-item-section>
          </q-item>
          <q-item clickable  v-ripple :to="{ name: 'tabelaibpt' }" v-if="(usuariologado)" >
            <q-item-section avatar>
              <q-icon name="dashboard" />
            </q-item-section>
            <q-item-section>Etiquetas</q-item-section>
          </q-item>
          <q-item clickable  v-ripple :to="{ name: 'tabelaibpt' }" v-if="(usuariologado)" >
            <q-item-section avatar>
              <q-icon name="dashboard" />
            </q-item-section>
            <q-item-section>Licenças</q-item-section>
          </q-item>
          <q-item clickable  v-ripple :to="{ name: 'tabelaibpt' }" v-if="(usuariologado)" >
            <q-item-section avatar>
              <q-icon name="dashboard" />
            </q-item-section>
            <q-item-section>Scripts</q-item-section>
          </q-item>
          <q-item clickable  v-ripple :to="{ name: 'tabelaibpt' }" v-if="(usuariologado)" >
            <q-item-section avatar>
              <q-icon name="dashboard" />
            </q-item-section>
            <q-item-section>Erros</q-item-section>
          </q-item>
          <q-item clickable  v-ripple :to="{ name: 'tabelaibpt' }" v-if="(usuariologado)" >
            <q-item-section avatar>
              <q-icon name="dashboard" />
            </q-item-section>
            <q-item-section>Backup</q-item-section>
          </q-item>
        </q-list>
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

    <q-footer class="bg-grey-2" >
      <div class="q-mt-md text-grey-8 text-caption q-pa-xs text-center">
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
      year: 2021,
      optionsmenu: null,
      appPackage: datapackage,
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
    this.$eventbus.$on('homedashboardupdated', this.onhomedashboardupdated)
    this.$eventbus.$on('setdrawerr', this.onSetDrawerR)
    this.$eventbus.$on('notification_add', this.onAddNotificacao)
  },
  beforeDestroy: function () {
    this.$eventbus.$off('togglemenu', this.toggleMenu)
    this.$eventbus.$off('homedashboardupdated', this.onhomedashboardupdated)
    this.$eventbus.$off('notification_add', this.onAddNotificacao)
    this.$eventbus.$off('drawerr_remove', this.onRemoveDrawerR)
  },
  async mounted () {
    var app = this
    app.optionsmenu = this.$store.state.homedashboard.options
    app.permitefollowupconsulta = app.$helpers.permite('followup.consulta')
    app.year = moment().year()
    if (!app.$store.state.homedashboard.ultimaatualizacao) {
      await app.$store.dispatch('homedashboard/refresh')
    }
    app.onhomedashboardupdated()
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
    onhomedashboardupdated () {
      var app = this
      app.loading = true
      app.optionsmenu = app.$store.state.homedashboard.options
      setTimeout(() => {
        app.loading = false
      }, 100)
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
  padding-top: 80px;
  font-weight: 300;
  max-width: 900px;
  margin-left: auto;
  margin-right: auto
}
.doc-header {
  padding-top: 80px;
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
    padding-top: 45px;
  }
  .doc-header {
    padding-top: 45px;
  }
  .titletoobarcustom {
    min-width: auto;
    padding-left: 16px;
  }
}
  .header-item {
    background-color: #d6d1d1;
  }
</style>
