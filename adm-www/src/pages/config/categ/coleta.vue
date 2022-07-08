<template>
  <div>
    <q-card class="q-ma-sm" flat  >
      <q-card-section>
        <div class="fit row wrap q-col-gutter-md q-pb-md">

          <div class="col-12" v-if="loading">
            <q-card class="" flat >
              <q-card-section class="no-padding">
                <div class="col-12" >
                  <q-card class="" flat >
                    <q-card-section  class="full-width text-center">
                      <q-spinner color="blue" size="3rem" :thickness="5" />
                      <div class="text-blue q-pa-md">Carregando...</div>
                    </q-card-section>
                  </q-card>
                </div>
              </q-card-section>
            </q-card>
          </div>

          <!-- Quadro de informações para impressão do "Relatório de Viagem" -->
          <div class="col-12" v-if="!loading">
            <q-card class="" flat >
              <q-card-section class="no-padding">
                <q-toolbar class="bg-grey-3 text-black" si>
                  <q-toolbar-title class="text-subtitle2">
                    Quadro de informações para impressão da coleta com produtos perigosos
                  </q-toolbar-title>
                </q-toolbar>
                <div class="col-12" >
                  <q-card class="" flat bordered >
                    <q-card-section >
                      <q-input v-model="configuracoes.itens.coleta_info_printprodperigosos.valor" type="textarea" outlined label="Informações para impressão da coleta com produtos perigosos" autogrow :dense="!$q.platform.is.mobile"
                       hint="Evite muitas linhas para não ocorrer quebra de página na impressão"/>
                    </q-card-section>
                  </q-card>
                </div>
              </q-card-section>
            </q-card>
          </div>
          <!-- Quadro de informações para impressão do "Relatório de Viagem" -->

        </div>
      </q-card-section>
    </q-card>
  </div>
</template>

<script>
import Configuracoes from 'src/mvc/collections/configuracoes.js'
export default {
  name: 'config.categ.acertoviagem',
  components: {
  },
  data () {
    var configuracoes = new Configuracoes()
    return {
      configuracoes,
      loading: true
    }
  },
  mounted () {
    var app = this
    app.setLoading(true)
    // app.configuracoes.additem (id, tipo, defaultvalue)
    app.configuracoes.additem('coleta_info_printprodperigosos', 'mediumtext', '')

    app.refreshdata()
  },
  methods: {
    async savedata () {
      var app = this
      app.setLoading(true)
      app.$emit('saving', true)
      var ret = await app.configuracoes.save()
      app.$emit('saved', ret)
      if (ret.ok) {
        app.refreshdata()
      } else {
        app.setLoading(false)
      }
      app.$emit('saving', false)
    },
    setLoading (val) {
      this.loading = val
      this.$emit('loading', this.loading)
    },
    async refreshdata () {
      var app = this
      app.setLoading(true)
      await app.configuracoes.fetch()
      app.setLoading(false)
    }
  }

}
</script>
