<template>
  <q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.platform.is.mobile">
    <q-card class="q-dialog-plugin" :class="$q.platform.is.mobile ? '' : 'full-70'">
      <q-bar class="bg-primary text-white" v-if="!$q.platform.is.mobile">
        <span class="ellipsis text-subtitle2 q-ml-xs">Coleta :: {{ (!loading ? (adding ? 'Novo cadastro' : (dataset ? '#' + dataset.id : '?')) : 'Consultando')}}</span>
        <q-space />
        <q-btn dense flat icon="close" @click="actClose">
          <q-tooltip>Sair sem salvar (ESC)</q-tooltip>
        </q-btn>
      </q-bar>
      <q-toolbar class="bg-primary text-white" v-if="$q.platform.is.mobile">
        <q-toolbar-title>
          Coleta :: {{ (!loading ? (adding ? 'Novo' : (dataset ? '#' + dataset.id : '?')) : 'Consultando')}}
        </q-toolbar-title>
        <q-btn flat round dense icon="close" @click="actClose"/>
      </q-toolbar>
      <q-card-section class="no-padding scroll" style="max-height: 80vh">
        <div v-if="!loading">
          <formedit :adding="adding" :idstart="dataset.id" @updated="actUpdated" @close="onClose" mode="dialog" ref="txtform" @saving="onSaving" />
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import formedit from './edit-cnp'
export default {
  name: 'coleta.edit.dialog',
  components: {
    formedit
  },
  props: {
    adding: {
      type: Boolean,
      default: function () {
        return true
      }
    },
    dataset: {
      type: Object,
      default: function () {
        return null
      }
    }
  },
  data () {
    return {
      saving: false,
      loading: true
    }
  },
  async mounted () {
  },
  methods: {
    onSaving (val) {
      this.saving = val
    },
    actClose () {
      this.onClose(false)
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
    actSave () {
      this.$refs.txtform.actSave()
    },

    onDialogHide () {
      // required to be emitted
      // when QDialog emits "hide" event
      this.$emit('hide')
    },

    onOKClick () {
      if (!this.aceite) return
      if (!(this.justificativa.length > 5)) return
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
