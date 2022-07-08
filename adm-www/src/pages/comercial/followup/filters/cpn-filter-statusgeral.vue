<template>
  <q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.screen.lt.sm">
    <q-card class="q-dialog-plugin" bordered :style="$q.screen.lt.sm ? '' : 'min-width: 50vw'">
      <q-bar class="bg-primary text-white" v-if="!$q.screen.lt.sm">
        <span class="ellipsis text-subtitle2 q-ml-xs">{{title}}</span>
        <q-space />
        <q-btn dense flat icon="close" @click="actClose">
          <q-tooltip>Sair (ESC)</q-tooltip>
        </q-btn>
      </q-bar>
      <q-toolbar class="bg-primary text-white" v-if="$q.screen.lt.sm">
        <q-toolbar-title>
          {{title}}
        </q-toolbar-title>
        <q-btn flat round dense icon="close" @click="actClose"/>
      </q-toolbar>
      <q-card-section>
         <q-card bordered flat>
          <q-card-section class="bg-grey-3 q-pa-sm">
            <div class="text-subtitle2">Status</div>
          </q-card-section>
          <q-card-section class="q-pa-none q-ma-none" >
              <div class="q-pa-sm">
              <q-option-group v-model="status" type="checkbox" color="primary" :options="config.options" size="sm" inline />
              </div>
          </q-card-section>
         </q-card>
      </q-card-section>
      <q-card-actions>
        <q-btn label="Ok" color="primary" unelevated @click="onOKClick" :class="$q.screen.lt.sm ? 'full-width' : ''" :size="$q.screen.lt.sm ? 'lg' : 'md'"/>
        <q-btn label="Limpar tudo" color="primary" flat @click="actClearAll" :class="$q.screen.lt.sm ? 'full-width' : ''" :size="$q.screen.lt.sm ? 'lg' : 'md'"/>
        <q-btn label="Fechar" color="primary" flat  @click="actClose" :class="$q.screen.lt.sm ? 'full-width q-mt-md' : 'q-ml-sm'" :size="$q.screen.lt.sm ? 'lg' : 'md'" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>

export default {
  name: 'dialog.followup.filter.statusgeral',
  components: {
  },
  props: {
    config: {
      type: Object,
      default: function () {
        return {
          options: [],
          value: null,
          title: 'Itens'
        }
      }
    }
  },
  data () {
    return {
      loading: true,
      title: 'Itens',
      modelerro: null,
      status: []
    }
  },
  computed: {
  },
  async mounted () {
    var app = this
    if (app.config.title) app.title = app.config.title
    app.status = []
    if (app.config.value ? app.config.value.length > 0 : false) {
      for (let index = 0; index < app.config.value.length; index++) {
        app.status.push(app.config.value[index])
      }
    }
  },
  methods: {
    actClose () {
      // this.$emit('ok', null)
      this.hide()
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
    actClearAll () {
      this.$emit('ok', null)
      this.hide()
    },
    async onOKClick () {
      try {
        var app = this
        var dados = []
        if (app.status) {
          for (let index = 0; index < app.status.length; index++) {
            dados.push(app.status[index])
          }
        }
        if (dados.length === 0) dados = null
      } catch (error) {
        app.error = error.message
        return
      }
      this.$emit('ok', dados)
      // or with payload: this.$emit('ok', { ... })

      // then hiding dialog
      this.hide()
    }

  }
}
</script>
