<template>
<div>
  <q-page class="" >
    <div class="full-width header-top-bg bg-grey-9 shadow-up-21"></div>
    <div class="row doc-header full-width" style="padding-bottom: 70px" >
      <div class="col-xs-12 col-md-10 offset-md-1 col-lg-8 offset-lg-2 " :class="$q.screen.lt.sm ? '' : 'q-pa-lg'" >

        <q-card class="bg-white " flat bordered  :class="$q.platform.is.mobile ? 'q-ma-sm' : ''" >
          <q-card-section>
            <div class="row">
              <div class="col-xs-12 col-md-9">
                <div class="q-pa-md  text-h6">Olá, <b>{{$store.state.usuario.usuario.nome}}!</b></div>
              </div>
              <div class="col-xs-12 col-md-3 text-right" v-if="!$q.platform.is.mobile">
                <q-avatar size="50px" font-size="30px" text-color="grey-9" color="grey-3" icon="security" />
              </div>
            </div>
          </q-card-section>
          <q-card-section class="text-center">
              <div class="text-subtitle2">
                <div class="q-pa-md">Para acessar esse conteúdo é necessário liberação da perfil <span class="bg-grey-3 text-primary rounded-borders q-pa-sm">{{idpermissao}}</span>.</div>
                <div class="q-pa-md" v-if="redirect ? redirect !== '' : false">Página <span class="bg-grey-3 text-primary rounded-borders q-pa-sm">{{redirect}}</span></div>
                <div class="q-pa-md">Se necessário, faça contato com seu o superior para solicitar essa liberação permissão.</div>
              </div>
          </q-card-section>
          <q-card-section>
            <div class="row q-gutter-y-sm">
            <q-btn color="grey-9" icon="home"  style="width:200px;" :to="{ name: 'home' }" label="Home" unelevated :class="$q.platform.is.mobile ? 'full-width' : 'q-mr-md'"  />
            <q-btn color="grey-9"  style="width:200px;" @click="$router.go(-1)" label="Voltar" unelevated :class="$q.platform.is.mobile ? 'full-width' : 'q-mr-md'" />

            </div>
          </q-card-section>
        </q-card>
  </div>
  <q-page-sticky position="top" expand>
    <q-toolbar class="bg-grey-9 text-white">
      <q-btn flat round icon="arrow_back" @click="$router.go(-1)"/>
      <q-toolbar-title>Acesso negado</q-toolbar-title>
    </q-toolbar>
  </q-page-sticky>
  </div>
  </q-page>
</div>
</template>

<script>
export default {
  name: 'acesso.negado',
  data () {
    return {
      redirect: null,
      idpermissao: null
    }
  },
  async activated () {
    var app = this
    app.redirect = app.$route.query.redirect ? app.$route.query.redirect : '/'
    app.idpermissao = app.$route.query.idpermissao ? app.$route.query.idpermissao : '<Não identificada>'
  },
  methods: {
  }
}
</script>
