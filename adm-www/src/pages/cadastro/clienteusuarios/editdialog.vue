<template>
  <q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.platform.is.mobile">
    <q-card class="q-dialog-plugin" :class="$q.platform.is.mobile ? '' : 'full-70'">
      <q-bar class="bg-primary text-white" v-if="!$q.platform.is.mobile">
        <span class="ellipsis text-subtitle2 q-ml-xs">Cliente :: {{ (!loading ? (adding ? 'Novo cadastro' : (localdataset ? localdataset.razaosocial_old +  ' [id: ' + localdataset.id + ']' : '?')) : 'Consultando')}}</span>
        <q-space />
        <q-btn dense flat icon="close" @click="actClose">
          <q-tooltip>Sair sem salvar (ESC)</q-tooltip>
        </q-btn>
      </q-bar>
      <q-toolbar class="bg-primary text-white" v-if="$q.platform.is.mobile">
        <q-toolbar-title>
          Cliente :: {{ (!loading ? (adding ? 'Novo' : (localdataset ? localdataset.razaosocial_old : '?')) : 'Consultando')}}
        </q-toolbar-title>
      </q-toolbar>
      <q-card-section>
        <div v-if="!loading" class="no-padding scroll" style="max-height: 80vh">
          <formedit :adding="adding" :idstart="dataset.id" @updated="actUpdated" @close="onClose" mode="dialog" />
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import formedit from './edit-cpn.vue'
import Cliente from 'src/mvc/models/cliente.js'
export default {
  name: 'cliente.edit.dialog',
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
    var localdataset = new Cliente()
    return {
      loading: true,
      localdataset
    }
  },
  async mounted () {
    if (this.dataset) this.localdataset = new Cliente(this.dataset)
  },
  methods: {
    actClose () {
      this.onClose(false)
    },
    onClose (TeveAlteracao) {
      this.$emit('ok', { ok: TeveAlteracao, dataset: TeveAlteracao ? this.localdataset : null })
      this.hide()
    },
    async actUpdated (dataset) {
      var app = this
      await app.localdataset.cloneFrom(dataset)
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
      this.$emit('ok', { ok: true, dataset: this.localdataset })
      this.hide()
    }

  }
}
</script>
