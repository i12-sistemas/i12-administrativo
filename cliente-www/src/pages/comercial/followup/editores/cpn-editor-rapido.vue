<template>
<div>
   <q-form @submit="actSave" v-if="!loading"  >
      <div class="fit row wrap full-width">
        <div class="fit row wrap q-col-gutter-md">
          <!-- status de agendamento -->
          <div class="col-md-4 col-xs-12">
            <q-card bordered flat>
              <q-card-section class="q-px-md q-py-xs bg-grey-3">
                <div class="text-weight-bold">Status agendamento</div>
              </q-card-section>
              <q-card-section class="no-padding">
                <div class="fit row wrap q-col-gutter-sm q-pa-sm">
                  <div class="col-6">
                    <inputdata outlined v-model="dataset.datasolicitacao" label="Data solicitação" stacklabel :dense="!$q.platform.is.mobile" />
                  </div>
                  <div class="col-6">
                    <inputdata outlined v-model="dataset.dataagendamentocoleta" label="Data agenda coleta" stacklabel :dense="!$q.platform.is.mobile" />
                  </div>
                  <div class="col-12">
                    <q-card bordered flat>
                      <q-card-section class="q-px-md q-py-xs bg-grey-3">
                        <div class="text-weight-bold text-caption">Status</div>
                      </q-card-section>
                      <q-card-section class="q-pa-sm">
                        <div>
                          <q-option-group v-model="dataset.erroagendastatus.value" type="radio" color="primary" :options="optionsstatusagendamento" size="sm" inline />
                        </div>
                        <div v-if="dataset.erroagendastatus.value === '2'">
                          <selectfollowuperros outlined label="Erro" nullabled tipo="agenda"  v-model="dataset.erroagenda" class="full-width" ref="txtmotorista" />
                        </div>
                      </q-card-section>
                    </q-card>
                  </div>
                </div>
              </q-card-section>
            </q-card>
          </div>
          <!-- status de agendamento -->
          <!-- Inicio de follow up -->
          <div class="col-md-2 col-xs-12">
            <q-card bordered flat>
              <q-card-section class="q-px-md q-py-xs bg-grey-3">
                <div class="text-weight-bold">Início de Follow Up</div>
              </q-card-section>
              <q-card-section class="no-padding">
                <div class="fit row wrap q-col-gutter-sm q-pa-sm">
                  <div class="col-12">
                    <q-option-group v-model="dataset.iniciofollowup.value" type="radio" color="primary" size="xs" :options="optionsstatusiniciofup" />
                  </div>
                </div>
              </q-card-section>
            </q-card>
          </div>
          <!-- Inicio de follow up -->
          <!-- Inicio de Agendamento até promessa -->
          <div class="col-md-3 col-xs-12">
            <q-card bordered flat>
              <q-card-section class="q-px-md q-py-xs bg-grey-3">
                <div class="text-weight-bold">Agendamento até promessa</div>
              </q-card-section>
              <q-card-section class="no-padding">
                <div class="fit row wrap q-col-gutter-sm q-pa-sm">
                  <div class="col-12">
                    <q-option-group v-model="dataset.errodtpromessastatus.value" type="radio" color="primary" size="xs" :options="optionsstatusagendamento" />
                  </div>
                  <div class="col-12 q-mt-sm" v-if="dataset.errodtpromessastatus.value === '2'">
                    <selectfollowuperros outlined label="Erro" nullabled tipo="dtpromessa"  v-model="dataset.errodtpromessa" class="full-width" />
                  </div>
                </div>
              </q-card-section>
            </q-card>
          </div>
          <!-- Inicio de Agendamento até promessa -->
          <!-- observação -->
          <div class="col-md-3 col-xs-12">
            <q-card bordered flat>
              <q-card-section class="q-px-md q-py-xs bg-grey-3">
                <div class="text-weight-bold">Observação</div>
              </q-card-section>
              <q-card-section class="no-padding">
                <div class="fit row wrap q-col-gutter-sm q-pa-sm">
                  <div class="col-12">
                    <q-input v-model="dataset.observacao" outlined type="textaread" input-class="text-caption" :dense="!$q.platform.is.mobile" autogrow />
                  </div>
                </div>
              </q-card-section>
            </q-card>
          </div>
          <!-- observação -->
          <!-- status de coleta -->
          <div class="col-md-9 col-xs-12">
            <q-card bordered flat>
              <q-card-section class="q-px-md q-py-xs bg-grey-3">
                <div class="text-weight-bold">Coleta</div>
              </q-card-section>
              <q-card-section class="no-padding">
                <div class="fit row wrap q-col-gutter-sm q-pa-sm">
                  <div class="col-md-4 col-sm-12">
                    <div class="fit row wrap q-col-gutter-sm q-pa-sm">
                      <div class="col-12">
                        <inputdata outlined v-model="dataset.dataconfirmacao" label="Data confirmação" stacklabel :dense="!$q.platform.is.mobile" />
                      </div>
                      <div class="col-12">
                        <q-card bordered flat>
                          <q-card-section class="q-px-md q-py-xs bg-grey-3">
                            <div class="text-weight-bold text-caption">Status da confirmação</div>
                          </q-card-section>
                          <q-card-section class="no-padding">
                            <q-option-group v-model="dataset.statusconfirmacaocoleta.value" type="radio" color="primary" :options="optionsstatusagendamento" size="sm" />
                          </q-card-section>
                        </q-card>
                      </div>
                    </div>
                  </div>
                  <div class="col-3">
                      <div class="fit row wrap q-col-gutter-sm q-pa-sm">
                        <div class="col-12">
                          <q-input v-model="dataset.notafiscal" outlined type="number" label="Nota Fiscal"  stack-label :dense="!$q.platform.is.mobile" />
                        </div>
                        <div class="col-12">
                          <q-input v-model="dataset.coletaid" outlined type="number" label="Número Coleta"  stack-label :dense="!$q.platform.is.mobile" />
                        </div>
                        <div class="col-12">
                          <inputdata outlined v-model="dataset.datacoleta" label="Data coleta" stacklabel :dense="!$q.platform.is.mobile" />
                        </div>
                      </div>
                  </div>
                  <div class="col-md-5 col-sm-12">
                    <q-card bordered flat>
                      <q-card-section class="q-px-md q-py-xs bg-grey-3">
                        <div class="text-weight-bold text-caption">Status da coleta</div>
                      </q-card-section>
                      <q-card-section class="q-pa-sm">
                        <div>
                          <q-option-group v-model="dataset.errocoletastatus.value" type="radio" color="primary" :options="optionsstatusagendamento" size="sm" />
                        </div>
                        <div v-if="dataset.errocoletastatus.value === '2'">
                          <selectfollowuperros outlined label="Erro" nullabled tipo="coleta"  v-model="dataset.errocoleta" class="full-width"  />
                        </div>
                      </q-card-section>
                    </q-card>
                  </div>
                </div>
              </q-card-section>
            </q-card>
          </div>
          <!-- status de agendamento -->
          <div class="col-md-3 col-xs-12">
            <div class="fit row wrap full-width content-end q-gutter-sm">
              <div class="col-12">
                <q-btn label="Confirmar" type="submit" color="primary" unelevated class="full-width"/>
              </div>
              <div class="col-12">
                <q-btn label="Fechar" color="primary" flat  @click="actClose" class="full-width" />
              </div>
            </div>
          </div>
        </div>
      </div>
  </q-form>
</div>
</template>

<script>

import selectfollowuperros from 'src/components/cnp-select-followup-erros'
import inputdata from 'src/components/cpn-input-data'
import FollowUp from 'src/mvc/models/followup.js'
import FollowUps from 'src/mvc/collections/followups.js'
export default {
  name: 'comercial.fup.editorrapido.cpn',
  components: {
    inputdata,
    selectfollowuperros
  },
  props: {
    lista: {
      type: Array,
      default: function () {
        return []
      }
    }
  },
  data () {
    var dataset = new FollowUp()
    return {
      multi: false,
      title: 'Editor',
      dataset,
      loading: true,
      model: null,
      optionsstatusiniciofup: [
        {
          label: 'Sem status',
          value: '0',
          color: 'grey'
        },
        {
          label: 'Conecta',
          value: '1',
          color: 'green'
        },
        {
          label: 'Fornecedor',
          value: '2',
          color: 'red'
        }
      ],
      optionsstatusagendamento: [
        {
          label: 'Sem status',
          value: '0',
          color: 'grey'
        },
        {
          label: 'OK',
          value: '1',
          color: 'green'
        },
        {
          label: 'Com erro',
          value: '2',
          color: 'red'
        }
      ]
    }
  },
  async mounted () {
    var app = this
    if (app.lista) {
      if (app.lista.length > 1) app.multi = true
      if (app.lista.length > 0) {
        await app.dataset.cloneFrom(app.lista[0])
      }
    }

    app.setTitle(app.multi ? 'Multi-edição de ' + app.lista.length + ' registros' : 'Editando :: id ' + app.dataset.id + ' - ' + app.dataset.cliente.fantasia_followup + ' >> ' + app.dataset.fornecrazao)

    app.loading = false
    document.onkeydown = function (e) {
      if (e.key === 'Escape') {
        app.actClose()
      }
    }
  },
  watch: {
  },
  methods: {
    setTitle (val) {
      this.title = val
      this.$emit('title', this.title)
    },
    actRemove (key) {
      this.lista.splice(key, 1)
    },
    async actAdd () {
      var app = this
      if (app.model) {
        var idx = -1
        for (let index = 0; index < app.lista.length; index++) {
          const element = app.lista[index]
          if (parseInt(element.id) === parseInt(app.model.id)) {
            idx = index
            break
          }
        }
        if (idx < 0) {
          // var reg = new Cidade(this.model)
          // app.lista.push(reg)
        }
      }
      this.model = null
    },
    actClose () {
      this.$emit('close', false)
    },
    async actSave (e) {
      var app = this
      try {
        // var permite = Vue.prototype.$helpers.permite('operacional.coletas.save')
        // if (!permite.ok) throw new Error(permite.msg)

        var dialog = app.$q.dialog({
          message: 'Salvando, aguarde...',
          progress: true, // we enable default settings
          color: 'blue',
          persistent: true, // we want the user to not be able to close it
          ok: false // we want the user to not be able to close it
        })
        var plista = []
        for (let index = 0; index < app.lista.length; index++) {
          const row = app.lista[index]
          var item = new FollowUp(row)
          item.datasolicitacao = app.dataset.datasolicitacao
          item.dataagendamentocoleta = app.dataset.dataagendamentocoleta
          item.erroagendastatus.value = app.dataset.erroagendastatus.value
          item.erroagenda = app.dataset.erroagenda

          item.iniciofollowup.value = app.dataset.iniciofollowup.value

          item.errodtpromessastatus.value = app.dataset.errodtpromessastatus.value
          item.errodtpromessa = app.dataset.errodtpromessa

          item.observacao = app.dataset.observacao

          item.dataconfirmacao = app.dataset.dataconfirmacao
          item.statusconfirmacaocoleta.value = app.dataset.statusconfirmacaocoleta.value
          item.notafiscal = app.dataset.notafiscal
          item.coletaid = app.dataset.coletaid
          item.datacoleta = app.dataset.datacoleta
          item.errocoletastatus.value = app.dataset.errocoletastatus.value
          item.errocoleta = app.dataset.errocoleta

          plista.push(item)
        }

        var senddata = new FollowUps()
        var ret = await senddata.savemass(plista)
        if (!ret.ok) {
          dialog.hide()
          app.$helpers.showDialog(ret)
          throw new Error('')
        } else {
          app.$q.notify({
            message: 'Atualizado com sucesso!',
            color: 'positive',
            caption: ret.msg,
            actions: [
              { label: 'OK', color: 'white', handler: () => { /* ... */ } }
            ]
          })
          app.$emit('update', app.dataset)
          app.$emit('close', true)
          dialog.hide()
        }
      } catch (error) {
        dialog.hide()
        if (error.message !== '') app.$helpers.showDialog({ ok: false, msg: error.message, warning: true })
        return false
      }
    }
  }
}
</script>
