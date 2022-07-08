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

                        <div class="col-md-4 col-xs-12 self-center">
                          Mínimo de dias para alerta
                        </div>
                        <div class="col-md-8 col-xs-12">
                          <q-input outlined v-model="manutencao_alerta_minimo_dias" label="Alerta em dias" stack-label :dense="!$q.platform.is.mobile" type="number" style="max-width: 180px" />
                        </div>

                        <div class="col-md-4 col-xs-12 self-center">
                          KM mínimo para alerta
                        </div>
                        <div class="col-md-8 col-xs-12">
                          <q-input outlined v-model="manutencao_alerta_minimo_km" label="Alerta em KM" stack-label :dense="!$q.platform.is.mobile"
                            mask="#,##" fill-mask="0" reverse-fill-mask input-class="text-left" suffix="KM" style="max-width: 180px" />
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
import Vue from 'vue'
export default {
  name: 'config.categ.manutencaoveiculo',
  components: {
  },
  data () {
    var configuracoes = new Configuracoes()
    return {
      configuracoes,
      loading: true,
      manutencao_alerta_minimo_km: '0',
      manutencao_alerta_minimo_dias: 0
    }
  },
  mounted () {
    var app = this
    app.setLoading(true)
    // app.configuracoes.additem (id, tipo, defaultvalue)
    app.configuracoes.additem('manutencao_alerta_minimo_dias', 'integer', 0)
    app.configuracoes.additem('manutencao_alerta_minimo_km', 'double', 0)

    app.refreshdata()
  },
  methods: {
    async savedata () {
      var app = this
      app.setLoading(true)
      app.$emit('saving', true)
      app.configuracoes.itens.manutencao_alerta_minimo_km.valor = Vue.prototype.$helpers.strToCurr(app.manutencao_alerta_minimo_km)
      app.configuracoes.itens.manutencao_alerta_minimo_dias.valor = app.manutencao_alerta_minimo_dias
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
        app.manutencao_alerta_minimo_dias = app.configuracoes.itens.manutencao_alerta_minimo_dias.valor
        app.manutencao_alerta_minimo_km = app.configuracoes.itens.manutencao_alerta_minimo_km.valorAsDouble(2)
        app.setLoading(false)
      }
    }
  }
}
</script>
