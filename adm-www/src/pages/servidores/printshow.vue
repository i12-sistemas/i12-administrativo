<template>
<div class="q-pa-lg">
  <q-card bordered flat class="q-pa-lg">
    <q-card-section v-if="loading || saving"  class="text-center q-pa-lg">
        <q-spinner color="accent" size="80px" />
        <div class="text-accent text-center q-pa-md" v-if="loading">Processando documento para impressão,</div>
        <div class="text-accent text-center q-pa-md text-subtitle2" v-if="loading && (ids ? ids.length > 0 : false)">{{ids.length +  ' coleta' + (ids.length === 1 ? '' : 's')}} </div>
        <div class="text-accent text-center q-pa-md text-subtitle2" v-if="loading">Aguarde...</div>
    </q-card-section>
    <q-card-section  horizontal v-if="!loading && (retorno ? (!retorno.ok) : true)" >
      <div class="full-width text-center q-pa-lg">
          <div class="text-h6 q-pa-md" v-if="retorno ? retorno.msg == '' : true">Nenhum registro encontrado!</div>
          <div class="text-h6 q-pa-md" v-if="retorno ? retorno.msg !== '' : false">{{ retorno.msg }}</div>
          <q-btn label="Fechar"  @click="actClose" unelevated color="black" />
      </div>
    </q-card-section>
  </q-card>
</div>
</template>

<script>
import Coletas from 'src/mvc/collections/coletas.js'
export default {
  name: 'coleta.print.cnp',
  components: {
  },
  data () {
    return {
      retorno: { ok: false, msg: '' },
      ids: null,
      loading: true
    }
  },
  async mounted () {
    var app = this
    await app.init()
  },
  methods: {
    async init () {
      var app = this
      app.loading = true
      app.ids = null
      if (app.$route.query) {
        if (app.$route.query.ids) {
          app.ids = app.$route.query.ids.split(',')
          if (app.ids) {
            if (app.ids.length === 0) app.ids = null
          }
        }
      }
      if (app.ids) {
        await app.refreshData()
      } else {
        app.retorno.ok = false
        app.retorno.msg = 'Nenhum número informado'
      }
    },
    actClose () {
      window.close()
    },
    async refreshData () {
      var app = this
      app.loading = true
      var t = new Coletas()
      app.retorno = await t.print(app.ids.join(','))
      if (app.retorno.ok) {
        window.location = app.retorno.msg
      } else {
        app.loading = false
        // var a = app.$helpers.showDialog(app.retorno)
        // await a.then(function () {
        // })
      }
      app.loading = false
    }
  }
}
</script>
