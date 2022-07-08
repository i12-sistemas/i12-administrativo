<template>
<div>
  <div class="q-pt-lg text-center">
    <p>
      <q-avatar size="100px" font-size="52px" text-color="grey-9" color="grey-3" icon="security" />
    </p>
    <div class="text-faded q-pa-lg text-subtitle2">
      <div class="q-pa-md  text-h6">Olá, <b>{{$store.state.usuario.user.nome}}!</b></div>
      <div class="q-pa-md">Para acessar esse conteúdo é necessário liberação da permissão <span class="bg-grey-3 text-primary rounded-borders q-pa-sm">{{idpermissao}}</span>.</div>
      <div class="q-pa-md">Se necessário, faça contato com seu o superior para solicitar essa liberação permissão.</div>
    </div>
    <q-btn color="grey-9"  style="width:200px;" :to="{ path: redirect }" label="Voltar" unelevated />
  </div>
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
  async mounted () {
    var app = this
    app.redirect = app.$route.query.redirect ? app.$route.query.redirect : '/'
    app.idpermissao = app.$route.query.idpermissao ? app.$route.query.idpermissao : '<Não identificada>'
  },
  methods: {
  }
}
</script>
