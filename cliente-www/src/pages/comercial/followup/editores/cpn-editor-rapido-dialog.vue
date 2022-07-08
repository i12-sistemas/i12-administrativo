<template>
  <q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.platform.is.mobile">
    <q-card class="q-dialog-plugin" :class="$q.platform.is.mobile ? '' : 'full-70'">
      <q-bar class="bg-primary text-white" v-if="!$q.platform.is.mobile">
        <span class="ellipsis text-subtitle2 q-ml-xs">{{title}}</span>
        <q-space />
        <q-btn dense flat icon="close" @click="actClose">
          <q-tooltip>Sair sem salvar (ESC)</q-tooltip>
        </q-btn>
      </q-bar>
      <q-toolbar class="bg-primary text-white" v-if="$q.platform.is.mobile">
        <q-toolbar-title>{{title}}</q-toolbar-title>
        <q-btn flat round dense icon="close" @click="actClose"/>
      </q-toolbar>

      <q-card-section>
        <div v-if="!loading">
          <editorrapido :lista="lista" @close="onClose" @title="setTitle" />
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import editorrapido from './cpn-editor-rapido'
export default {
  name: 'comercial.fup.editorrapido.dialog',
  components: {
    editorrapido
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
    return {
      title: 'Editor',
      loading: true,
      tevealteracao: false
    }
  },
  async mounted () {
  },
  methods: {
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
