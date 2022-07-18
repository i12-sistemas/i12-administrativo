<template>
  <q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.platform.is.mobile">
    <q-card class="q-dialog-plugin" :class="$q.platform.is.mobile ? 'q-ma-none q-pa-none' : 'full-60'">
      <q-toolbar class="bg-primary text-white">
        <q-toolbar-title>Gerar licença</q-toolbar-title>
        <q-space />
        <q-btn dense flat icon="close" @click="actClose">
          <q-tooltip>Sair sem salvar (ESC)</q-tooltip>
        </q-btn>
      </q-toolbar>

      <q-card-section :class="$q.platform.is.mobile ? 'q-ma-none q-pa-none' : ''">
        <q-card class="bg-white" flat :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile">
          <q-card-section v-if="cliente">
            <div class="row">
              <div class="col-12">
                <div>Cliente: <span class="text-weight-bold">{{cliente.nome}}</span></div>
                <div>Documento: <span class="text-weight-bold">{{$helpers.mascaraDocCPFCNPJ(cliente.doc)}}</span></div>
              </div>
            </div>
          </q-card-section>
          <q-separator spaced  />
          <q-card-section>
            <div class="row q-col-gutter-md">
              <div class="col-12">
                <q-radio dense v-model="modo" val="vencimento" label="Por vencimento" class="q-mr-md" />
                <q-radio dense v-model="modo" val="bloqueio" label="Por bloqueio" />
              </div>
              <div class="col-xs-12 col-sm-4">
                <inputdata outlined v-model="data" :label="'Data de ' + (modo === 'vencimento' ? 'vencimento' : 'bloqueio')"  :dense="!$q.platform.is.mobile"  />
              </div>
              <div class="col-xs-12 col-sm-8">
                <q-input v-model="obs" type="text" label="Observação" counter maxlength="150" outlined stack-label :dense="!$q.platform.is.mobile" />
              </div>
              <div class="col-12">
                <q-checkbox v-model="force" label="Forçar substituição da licença atual" />
              </div>
            </div>
          </q-card-section>
          <q-separator spaced  />
          <q-card-section>
            <div class="row">
            <q-btn unelevated label="Gerar licença" color="primary"  @click="actSalvar" :loading="loading"  />
            <q-btn flat label="Sair" color="primary" icon="close"  @click="actClose" class="q-ml-md" />
            </div>
          </q-card-section>
        </q-card>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import moment from 'moment'
import ClienteLicenca from 'src/mvc/models/clientelicenca.js'
import inputdata from 'src/components/cpn-input-data'
export default {
  name: 'cliente.licenca.add.dialog',
  components: {
    inputdata
  },
  props: ['cliente'],
  data () {
    return {
      rows: [],
      modo: 'vencimento',
      obs: '',
      force: false,
      data: null,
      semestre: null,
      title: 'Carregando...',
      loading: true,
      tevealteracao: false
    }
  },
  async mounted () {
    var app = this
    app.data = moment().format('YYYY/MM/DD')
  },
  methods: {
    onRejected (rejectedEntries) {
      // Notify plugin needs to be installed
      // https://quasar.dev/quasar-plugins/notify#Installation
      this.$q.notify({
        type: 'negative',
        message: `${rejectedEntries.length} arquivo não foi carregado devido a restrição de tamanho ou extensão`
      })
    },
    async actSalvar () {
      var app = this
      app.loading = true
      var ret = { ok: false }
      try {
        var data = app.$helpers.strDateToFormated(app.data, 'YYYY/MM/DD', 'YYYY-MM-DD')
        var dataset = new ClienteLicenca()
        ret = await dataset.gerarlicenca(app.cliente.doc, app.modo, data, app.obs, app.force)
        if (!ret.ok) {
          var aRet = app.$helpers.showDialog(ret)
          await aRet.then(function () {})
        } else {
          app.tevealteracao = true
          app.$q.notify({
            color: 'positive',
            icon: 'check',
            timeout: 3000,
            caption: ret.msg,
            message: 'Licença de uso'
          })
          this.actClose()
        }
      } catch (error) {
        app.loading = false
        var a = app.$helpers.showDialog({ ok: false, msg: error.message })
        await a.then(function () {})
        return false
      } finally {
        app.loading = false
      }
    },
    setTitle (val) {
      this.title = val
    },
    ontevealteracao (val) {
      this.tevealteracao = val
    },
    actClose () {
      this.onClose(this.tevealteracao)
    },
    onClose (TeveAlteracao) {
      this.$emit('ok', TeveAlteracao)
      this.hide()
    },
    async actUpdated (dataset) {
      // var app = this
      // app.dataset = dataset
    },

    // following method is REQUIRED
    // (don't change its name --> "show")
    show () {
      var app = this
      this.$refs.dialog.show()
      setTimeout(() => {
        app.loading = false
      }, 500)
    },

    // following method is REQUIRED
    // (don't change its name --> "hide")
    hide () {
      this.$refs.dialog.hide()
    },

    onDialogHide () {
      // required to be emitted
      // when QDialog emits "hide" event
      this.$emit('hide')
    },

    onOKClick () {
      // if (!this.aceite) return
      // if (!(this.justificativa.length > 5)) return
      // on OK, it is REQUIRED to
      // emit "ok" event (with optional payload)
      // before hiding the QDialog
      this.$emit('ok', true)
      // or with payload: this.$emit('ok', { ... })

      // then hiding dialog
      this.hide()
    }

  }
}
</script>
