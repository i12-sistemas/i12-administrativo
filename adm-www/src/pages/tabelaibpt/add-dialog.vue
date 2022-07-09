<template>
  <q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.platform.is.mobile">
    <q-card class="q-dialog-plugin" :class="$q.platform.is.mobile ? '' : 'full-90'">
      <q-toolbar class="bg-primary text-white">
        <q-toolbar-title>Nova tabela do IBPT</q-toolbar-title>
        <q-space />
        <q-btn dense flat icon="close" @click="actClose">
          <q-tooltip>Sair sem salvar (ESC)</q-tooltip>
        </q-btn>
      </q-toolbar>

      <q-card-section>
        <div class="col-sm-12" >
        <q-card class="bg-white" flat :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile">
          <q-card-section>
            <div class="row q-col-gutter-md">
              <div class="col-sm-12 col-md-4">
                <div class="col-sm-12 col-md-8 col-lg-6">
                  {{uf}}
                  <q-select v-model="uf" :options="uflista" label="UF" outlined emit-value stack-label class="q-mr-xs" />
                </div>
              </div>
              <div class="col-sm-12 col-md-4">
                <div class="col-sm-12 col-md-8 col-lg-6">
                  {{semestre}}
                  <q-select v-model="semestre"
                      label="UF" outlined emit-value stack-label option-value="numero" option-label="nome"  map-options class="q-mr-xs"
                      :options="[ {numero: 1, nome: '1º Semestre' }, {numero: 2, nome: '2º Semestre' } ]"
                      />
                </div>
              </div>
              <div class="col-sm-12 col-md-4">
                <div class="col-sm-12 col-md-8 col-lg-6">
                  {{ano}}
                  <q-select v-model="ano"
                      label="Ano" outlined emit-value stack-label  class="q-mr-xs"
                      :options="anos"
                      />
                </div>
              </div>
              <div class="col-sm-12">
                <div class="col-12 ">
                  {{file}}
                </div>
                <div class="col-sm-12">
                  <q-file clearable  outlined v-model="file" label="Anexar arquivo" counter
                     accept=".csv"  max-file-size="10485760"
                     @rejected="onRejected"
                    >
                    <template v-slot:prepend>
                      <q-icon name="attach_file" />
                    </template>
                    <template v-slot:hint>
                      Anexe o arquivo CSV - Limite de 10Mb por arquivo
                    </template>
                  </q-file>
                </div>
              </div>
            </div>
          </q-card-section>
          <q-card-section>
            <div class="row">
            <q-btn unelevated label="Adicionar" color="primary" icon="search"  @click="actSalvar" :loading="loading"  />
            <q-btn flat label="Sair" color="primary" icon="close"  @click="actClose" class="q-ml-md" />
            </div>
          </q-card-section>
        </q-card>
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import moment from 'moment'
import TabelasIbpt from 'src/mvc/collections/tabelasibpt.js'
export default {
  name: 'tabelaibpt.add.dialog',
  components: {
  },
  props: ['dataset'],
  data () {
    return {
      rows: [],
      semestre: null,
      uf: null,
      ano: null,
      file: null,
      title: 'Carregando...',
      loading: true,
      tevealteracao: false,
      anos: [],
      uflista: ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO']
    }
  },
  async mounted () {
    var app = this
    var anoAtual = moment().year()
    app.anos = []
    for (let index = (anoAtual - 1); index <= (anoAtual + 2); index++) {
      app.anos.push(index)
    }
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
        var dataset = new TabelasIbpt()
        ret = await dataset.upload(app.file, app.uf, app.semestre, app.ano)
        if (!ret.ok) {
          var aRet = app.$helpers.showDialog(ret)
          await aRet.then(function () {})
        } else {
          // app.onOKClick()
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
