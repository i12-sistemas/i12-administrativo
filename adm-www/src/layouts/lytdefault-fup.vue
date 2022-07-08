<template>
  <q-layout view="hHh lpR lFf" class="bg-grey-3" >
    <q-header bordered class="bg-grey-2 text-indigo">
      <q-toolbar>
        <q-btn flat dense round @click="actShowMenu" aria-label="Menu" icon="menu" />
        <q-toolbar-title shrink class="row items-center no-wrap cursor-pointer"  >
          <img src="~assets/logo-conecta-fup.png" style="max-height: 30px;" @click="$router.push({ name: 'followup.consulta' })" >
        </q-toolbar-title>
        <q-btn color="indigo" icon="home" :to="{ name: 'home' }" flat round dense class="q-mr-sm"/>
        <q-btn flat round dense class="q-mr-sm" color="green" :to="{ name: 'followup.consulta' }">
          <q-avatar size="22px">
            <img src="~assets/fup-icon.png" >
          </q-avatar>
          <q-tooltip :delay="700">Consulta principal</q-tooltip>
        </q-btn>
        <q-separator vertical  inset v-if="!$q.platform.is.mobile" class="q-mx-sm"/>
        <div v-if="!$q.platform.is.mobile">
          <q-btn color="indigo" v-for="(item, key) in menulateral.menu" :key="'item' + key" :label="item.categoria" flat  >
            <q-menu >
              <q-list dense style="min-width: 100px">
                <div v-for="(link, key) in item.itens" :key="'linksOperacional' + key" >
                  <menuitemprincipalbtn :item="link" v-if="!link.separator" />
                  <q-separator v-if="link.separator"/>
                </div>
              </q-list>
            </q-menu>
          </q-btn>
        </div>
        <q-space />
        <div class="q-gutter-sm row items-center no-wrap">
          <q-btn round dense unelevated icon="notifications" :color="(notifications ? notifications.length > 0 : false) ? 'blue' : 'grey-4'" :outline="(notifications ? notifications.length > 0 : false)">
            <q-badge color="red" text-color="white" floating v-if="notificationsnoread > 0">{{notificationsnoread}}</q-badge>
            <q-tooltip v-if="notificationsnoread === 0">Nenhuma notificação</q-tooltip>
            <q-tooltip v-else>
              <div v-if="notificationsnoread > 0">{{notificationsnoread}} notificações não lidas</div>
              <div v-if="notifications ? notifications.length > 0 : false">{{notifications.length}} notificações</div>
            </q-tooltip>
            <q-menu auto-close anchor="bottom right" self="top end" >
              <q-list style="min-width: 300px" >
                <div v-for="(item, key) in notifications" :key="key">
                  <q-item clickable v-close-popup @click="actClickNotificacao(item)">
                    <q-item-section>
                      <q-item-label  v-if="item.title ? item.title != '' : false">{{item.title}}</q-item-label>
                      <q-item-label caption lines="2" v-if="item.msg ? item.msg != '' : false">{{item.msg}}</q-item-label>
                      <q-item-label caption>5 min ago</q-item-label>
                    </q-item-section>

                    <q-item-section top avatar v-if="item.icon ? item.icon != '' : false">
                      <q-avatar :text-color="(item.color ? item.color !== '' : false) ? item.color : 'primary'" color="grey-2" :icon="item.icon" size="30px" font-size="24px" />
                    </q-item-section>
                  </q-item>
                  <q-separator />
                </div>
                <q-item clickable v-close-popup v-if="notifications ? notifications.length > 0 : false">
                  <q-item-section>Marcar como lidas</q-item-section>
                </q-item>
                <q-item clickable v-close-popup v-if="notifications ? !(notifications.length > 0) : true">
                  <q-item-section>Nenhuma notificação!</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
          <q-space />
          <q-btn color="indigo" text-color="white" v-if="usuariologado" unelevated :round="$q.platform.is.mobile">
            <div class="row items-center no-wrap">
              <div class="text-right q-pr-md" v-if="!$q.platform.is.mobile">
                <div>{{$helpers.strEllipsis(usuariologado.login,15)}}</div>
              </div>
              <q-avatar size="40px" v-if="$q.platform.is.mobile && !usuariologado.fotourl" round>
                {{usuariologado.login.charAt(0)}}
              </q-avatar>
              <q-avatar size="40px" v-if="usuariologado.fotourl">
                <q-img :src="usuariologado.fotourl" contain />
              </q-avatar>
            </div>
            <q-menu anchor="bottom right" self="top right" >
              <q-card class="my-card"   >
                <q-card-section horizontal >
                  <q-card-section class="col-4" v-if="usuariologado.fotourl">
                    <q-avatar size="100px" >
                      <q-img :src="usuariologado.fotourl" contain />
                    </q-avatar>
                  </q-card-section>
                  <q-card-section class="col" >
                    <div class="text-h6 text-right">{{usuariologado.login}}</div>
                    <div class="text-subtitle2 text-right" >{{usuariologado.nome}}</div>
                    <div class="text-right" v-if="usuariologado.email !== ''">E-mail: {{usuariologado.email}}</div>
                  </q-card-section>
                </q-card-section>
                <q-separator spaced inset />
                <q-card-actions align="right">
                  <q-btn label="Meu perfil" :to="({name: 'usuario.meuperfil'})" v-close-popup unelevated color="grey-2" text-color="black" icon="fa fa-user-circle" />
                  <q-btn label="Sair" :to="({name: 'logout.usuario'})" v-close-popup unelevated color="grey-2" text-color="black" icon="power_settings_new" />
                </q-card-actions>
              </q-card>
            </q-menu>
          </q-btn>
        </div>
        <q-btn flat dense round @click="rightDrawerOpen = !rightDrawerOpen" icon="apps" v-if="currentRComponent" />
      </q-toolbar>
    </q-header>

    <!-- menu opção geral somente mobile -->
    <q-drawer v-model="leftDrawerOpen" show-if-above bordered content-class="bg-grey-2" behavior="mobile" v-if="$q.platform.is.mobile"   >
      <q-scroll-area class="fit">
        <!-- <q-list v-if="usuariologado"> -->
        <q-list>
          <div v-for="(item, key) in menulateral.menu" :key="'item' + key" >
            <q-item-label header class="text-weight-bold text-uppercase q-pt-0 text-primary" :dense="!$q.platform.is.mobile" v-if="item.itens ? (item.itens.length > 0) : false" >
              {{item.categoria}}
            </q-item-label>
            <div v-for="(link, key) in item.itens" :key="'linksOperacional' + key" >
              <menuitemprincipal :item="link" v-if="!link.separator" />
              <q-separator v-if="link.separator"/>
            </div>
          </div>
          <q-separator space/>
          <div class="q-py-md q-px-md text-grey-9">
            <div class="row items-center q-gutter-x-sm q-gutter-y-xs">
              <a v-for="button in buttons2" :key="button.text" class="YL__drawer-footer-link" :href="button.href"  target="_blank" >
                {{ button.text }}
              </a>
            </div>
          </div>
        </q-list>
      </q-scroll-area>
    </q-drawer>
    <!-- menu opção geral somente mobile -->

    <q-drawer v-if="leftDrawerOpen && (currentRComponent ? currentRComponent !== '' : false) && !$q.platform.is.mobile" side="left"
      bordered content-class="bg-grey-3" behavior="desktop" ref="drawerLeftComponent" >
      <q-scroll-area class="fit q-pt-sm" >
        <component v-bind:is="currentRComponent"></component>
      </q-scroll-area>
    </q-drawer>

    <q-drawer v-model="rightDrawerOpen" v-if="currentRComponent && $q.platform.is.mobile" side="right" show-if-above bordered content-class="bg-grey-1" behavior="mobile"  >
      <q-scroll-area class="fit q-pt-sm">
        <component v-bind:is="currentRComponent"></component>
      </q-scroll-area>
    </q-drawer>

    <q-page-container class="">
      <transition name="fade">
        <!-- <keep-alive :include="['produtos', 'grupo.favoritos', 'comanda.pedido.add']"> -->
          <router-view />
        <!-- </keep-alive> -->
      </transition>
    </q-page-container>
  </q-layout>
</template>

<script>

import menuitemprincipal from 'src/components/menu/menu-item'
import menuitemprincipalbtn from 'src/components/menu/menu-item-btn.vue'
import MenuLateral from 'src/assets/menulateralprincipal-fup.js'
import moment from 'moment'

export default {
  name: 'MyLayout',
  components: {
    menuitemprincipal,
    menuitemprincipalbtn
  },
  data () {
    let menulateral = new MenuLateral()
    return {
      menulateral,
      notificationsnoread: 0,
      // padrao de notificacao
      // { id: 'xxxx', title: 'Titulo da notificação', msg: 'teste', icon: 'sync', color: 'positive', created_at: '2020-01-20 14:50:55', read: false, url: '' },
      notifications: [],
      currentRComponent: null,
      leftDrawerOpen: false,
      rightDrawerOpen: false,
      search: '',
      buttons2: [
        { text: 'i12 Sistemas', href: 'https://www.i12.com.br' }
      ],
      menuitens: [
        {
          categoria: 'Coletas em aberto',
          classid: 'emaberto',
          id: 'total',
          badge: '99',
          badgecolor: 'primary',
          badgetextcolor: 'white',
          itens: [
            { classid: 'emaberto', id: 'revisaoorcamento', text: 'Revisar dados', badge: '99', badgecolor: 'indigo', badgetextcolor: 'white', icon: 'warning', query: { situacao: '0', origem: '2', t: new Date().getTime() } },
            { classid: 'emaberto', id: 'naoliberado', text: 'Não liberados', badge: '99', badgecolor: 'red', badgetextcolor: 'white', icon: 'warning', query: { situacao: '0,1' } },
            { classid: 'emaberto', id: 'semmotorista', text: 'Sem motorista', badgecolor: 'orange', badgetextcolor: 'white', icon: 'airline_seat_recline_normal', query: { situacao: '0,1', semmotorista: '1' } },
            { classid: 'emaberto', id: 'cargaurgente', text: 'Urgentes', badge: '99', badgecolor: 'red-10', badgetextcolor: 'white', icon: 'flash_on', query: { situacao: '0,1', cargaurgente: '1' } },
            { separator: true },
            { classid: 'emaberto', id: 'atrasado', text: 'Em atraso', badge: '99', badgecolor: 'red-2', badgetextcolor: 'black', query: { situacao: '0,1', dhcoletaf: moment().subtract(2, 'days').format('YYYY/MM/DD') } },
            { classid: 'emaberto', id: 'ontem', text: 'Ontem', badge: '99', badgecolor: 'yellow-6', badgetextcolor: 'black', query: { situacao: '0,1', dhcoletai: moment().subtract(1, 'days').format('YYYY/MM/DD'), dhcoletaf: moment().subtract(1, 'days').format('YYYY/MM/DD') } },
            { classid: 'emaberto', id: 'hoje', text: 'Hoje', badge: '99', badgecolor: 'green-5', badgetextcolor: 'white', query: { situacao: '0,1', dhcoletai: moment().format('YYYY/MM/DD'), dhcoletaf: moment().format('YYYY/MM/DD') } },
            { classid: 'emaberto', id: 'futuro', text: 'Futuramente', badge: '99', badgecolor: 'blue-5', badgetextcolor: 'white', query: { situacao: '0,1', dhcoletai: moment().add(1, 'days').format('YYYY/MM/DD') } }
          ]
        },
        {
          categoria: 'Encerradas recentemente',
          itens: [
            { classid: 'encerrados', id: 'hoje', text: 'Hoje', badgecolor: 'green-5', query: { situacao: '2', dhbaixai: moment().format('YYYY/MM/DD'), dhbaixaf: moment().format('YYYY/MM/DD') } },
            { classid: 'encerrados', id: 'semana', text: 'Esta semana', badgecolor: 'green-5', query: { situacao: '2', dhbaixai: moment().startOf('week').format('YYYY/MM/DD'), dhbaixaf: moment().format('YYYY/MM/DD') }, tooltip: moment().startOf('week').format('YYYY/MM/DD') },
            { classid: 'encerrados', id: 'mes', text: 'Este mês', badgecolor: 'green-5', query: { situacao: '2', dhbaixai: moment().startOf('month').format('YYYY/MM/DD'), dhbaixaf: moment().format('YYYY/MM/DD') } }
          ]
        },
        {
          categoria: 'Cancelados recentemente',
          itens: [
            { classid: 'cancelados', id: 'hoje', text: 'Hoje', badgecolor: 'red-5', query: { situacao: '3', dhbaixai: moment().format('YYYY/MM/DD'), dhbaixaf: moment().format('YYYY/MM/DD') } },
            { classid: 'cancelados', id: 'semana', text: 'Esta semana', badgecolor: 'red-5', query: { situacao: '3', dhbaixai: moment().startOf('week').format('YYYY/MM/DD'), dhbaixaf: moment().format('YYYY/MM/DD') }, tooltip: moment().startOf('week').format('YYYY/MM/DD') },
            { classid: 'cancelados', id: 'mes', text: 'Este mês', badgecolor: 'red-5', query: { situacao: '3', dhbaixai: moment().startOf('month').format('YYYY/MM/DD'), dhbaixaf: moment().format('YYYY/MM/DD') } }
          ]
        }
      ]
    }
  },
  created: function () {
    this.$eventbus.$on('updatemenulateral', this.onUpdateMenuLateral)
    this.$eventbus.$on('setdrawerr', this.onSetDrawerR)
    this.$eventbus.$on('drawerr_remove', this.onRemoveDrawerR)
    this.$eventbus.$on('notification_add', this.onAddNotificacao)
  },
  beforeDestroy: function () {
    this.$eventbus.$off('setdrawerr', this.onUpdateMenuLateral)
    this.$eventbus.$off('updatemenulateral', this.onUpdateMenuLateral)
    this.$eventbus.$off('drawerr_remove', this.onRemoveDrawerR)
    this.$eventbus.$off('notification_add', this.onAddNotificacao)
  },
  async mounted () {
    this.$eventbus.$emit('updatemenulateral')
  },
  computed: {
    usuariologado: function () {
      var app = this
      let u = null
      if (app.$store.state.usuario.logado) {
        if (app.$store.state.usuario.user) {
          u = app.$store.state.usuario.user
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
    async onUpdateMenuLateral () {
      // if (this.$store.state.usuario.usuario) {
      //   await this.menulateral.processar(this.$store.state.usuario.usuario)
      await this.menulateral.processar()
      // }
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
  padding: 10px 20px;
  font-weight: 300;
  max-width: 90vw;
  margin-left: auto;
  margin-right: auto
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
@media (max-width: 800px) {
  .doc-page {
    padding: 7px 10px;
    max-width: 100%;
  }
  .titletoobarcustom {
    min-width: auto;
    padding-left: 16px;
  }
}
</style>
