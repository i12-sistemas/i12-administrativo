<template>
  <q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.screen.lt.sm">
    <q-card class="q-dialog-plugin" bordered :style="$q.screen.lt.sm ? '' : 'min-width: 50vw'">
      <q-bar class="bg-primary text-white" v-if="!$q.screen.lt.sm">
        <span class="ellipsis text-subtitle2 q-ml-xs">{{title}}</span>
        <q-space />
        <q-btn dense flat icon="sync" @click="actRefresh" :loading="loading" />
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
      <q-card-section class="scroll" style="max-height: 50vh" >
        <div class="fit row wrap full-width">
          <div class="col-12">
            <q-table :data="rows" :columns="[{ name: 'id', align: 'left', label: 'Cliente', field: 'id' }]" dense bordered flat :loading="loading"
              id="sticky-table" row-key="id" :rows-per-page-options="[0]"
              selection="multiple" :selected.sync="selected_rows"
            >
            </q-table>
          </div>
        </div>
      </q-card-section>
      <q-card-actions>
        <q-btn :label="'Ok'  + ((selected_rows ? selected_rows.length > 0 :false) ? ' [' + selected_rows.length + ']' : '')" color="primary" unelevated @click="onOKClick" :class="$q.screen.lt.sm ? 'full-width' : ''" :size="$q.screen.lt.sm ? 'lg' : 'md'"/>
        <q-btn label="Limpar tudo" color="primary" flat @click="actClearAll" :class="$q.screen.lt.sm ? 'full-width' : ''" :size="$q.screen.lt.sm ? 'lg' : 'md'"/>
        <q-btn label="Fechar" color="primary" flat  @click="actClose" :class="$q.screen.lt.sm ? 'full-width q-mt-md' : 'q-ml-sm'" :size="$q.screen.lt.sm ? 'lg' : 'md'" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
import FollowUps from 'src/mvc/collections/followups.js'
export default {
  name: 'dialog.followup.filter.clientefupid',
  components: {
  },
  props: {
    config: {
      type: Object,
      default: function () {
        return {
          value: null,
          title: 'Clientes por ID'
        }
      }
    }
  },
  data () {
    var dataset = new FollowUps()
    return {
      rows: [],
      selected_rows: [],
      dataset,
      loading: true,
      title: 'Clientes por ID'
    }
  },
  async mounted () {
    var app = this
    if (app.config.title) app.title = app.config.title

    if (app.config.value ? app.config.value.length > 0 : false) {
      app.selected_rows = []
      for (let index = 0; index < app.config.value.length; index++) {
        app.selected_rows.push({ id: app.config.value[index] })
      }
    } else {
      app.value = []
    }
    await app.onRefresh()
  },
  methods: {
    async onRefresh () {
      var app = this
      app.loading = true
      var ret = await app.dataset.fetchlistclientesfupid()
      if (ret.ok) {
        app.rows = []
        for (let index = 0; index < ret.data.length; index++) {
          const element = ret.data[index]
          app.rows.push({ id: element })
        }
      }
      app.loading = false
    },
    actRefresh () {
      this.onRefresh()
    },
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
        if (app.selected_rows ? app.selected_rows.length > 0 : false) {
          for (let index = 0; index < app.selected_rows.length; index++) {
            const element = app.selected_rows[index]
            dados.push(element.id)
          }
        }
        if (dados ? dados.length === 0 : true) dados = null
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
