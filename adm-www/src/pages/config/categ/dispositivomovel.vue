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

          <!-- dados de base -->
          <div class="col-12" v-if="!loading">
            <q-card class="" flat >
              <q-card-section class="no-padding">
                <div class="col-12" >
                  <q-card class="" flat bordered >
                    <q-card-section >
                      <div class="fit row wrap q-col-gutter-sm">

                        <div class="col-md-3 col-xs-12 self-center">
                          E-mail padrão
                        </div>
                        <div class="col-md-9 col-xs-12">
                          <q-input outlined v-model="configuracoes.itens.email_padrao_liberacao_dispositivo.valor" label="E-mail" stack-label
                            :dense="!$q.platform.is.mobile" type="email" hint="E-mail padrão para liberação de novos dispositivos" />
                        </div>
                      </div>
                    </q-card-section>
                  </q-card>
                </div>
              </q-card-section>
            </q-card>
          </div>
          <!-- dados de base -->

        </div>
      </q-card-section>
    </q-card>
  </div>
</template>

<script>
import Configuracoes from 'src/mvc/collections/configuracoes.js'
export default {
  name: 'config.categ.manutencaoveiculo',
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
    app.configuracoes.additem('email_padrao_liberacao_dispositivo', 'string', '')

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
      var ret = await app.configuracoes.fetch()
      if (ret.ok) {
        app.setLoading(false)
      }
    }
  }
}
</script>
