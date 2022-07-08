<template>
<div>
    <q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.platform.is.mobile">
      <q-card class="q-dialog-plugin" bordered>
        <q-bar class="bg-primary text-white" v-if="!$q.platform.is.mobile">
          <span class="ellipsis text-subtitle2 q-ml-xs">{{(title ? title !== '' : false) ? title : 'Configuração de colunas'}}</span>
          <q-space />
          <q-btn dense flat :icon="tudomarcado ? 'remove_done' : 'done_all'" :label="tudomarcado ? 'Descarmar tudo' : 'Marcar tudo'" @click="checkAll">
            <q-tooltip>Selecionar tudo</q-tooltip>
          </q-btn>
          <q-btn dense flat icon="close" @click="actClose">
            <q-tooltip>Sair (ESC)</q-tooltip>
          </q-btn>
        </q-bar>
        <q-toolbar class="bg-primary text-white" v-if="$q.platform.is.mobile">
          <q-toolbar-title>
            {{(title ? title !== '' : false) ? title : 'Configuração de colunas'}}
          </q-toolbar-title>
          <q-btn dense flat :icon="tudomarcado ? 'remove_done' : 'done_all'" :label="tudomarcado ? 'Descarmar tudo' : 'Marcar tudo'" @click="checkAll">
            <q-tooltip>Selecionar tudo</q-tooltip>
          </q-btn>
          <q-btn flat round dense icon="close" @click="actClose"/>
        </q-toolbar>
        <q-card-section class="no-padding scroll" style="max-height: 80vh">
            <q-list>
              <div id="itemsmovesorted">
                <div v-for="(col, k) in columns" :key="k" :data-id="col.name">
                  <q-item dense >
                    <q-item-section avatar>
                      <q-toggle v-if="!(col.required ? col.required === true : false)" v-model="visibleColumns" :val="col.name" dense />
                      <q-icon v-if="col.required ? col.required : false" name="adjust" color="primary" >
                        <q-tooltip :delay="700">Coluna fixa</q-tooltip>
                      </q-icon>
                    </q-item-section>
                    <q-item-section>{{col.label ? (col.label != '' ? col.label : label.name) : col.name}}</q-item-section>
                    <q-item-section avatar id="draghandle"  style="cursor: move">
                      <q-icon name="drag_handle" />
                      <q-tooltip :delay="700">Clique e mova para reordernar</q-tooltip>
                    </q-item-section>
                  </q-item>
                  <q-separator inset />
                </div>
              </div>
            </q-list>
        </q-card-section>
        <q-card-actions >
          <q-btn unelevated color="primary" label="OK" @click="onOKClick" class="full-width" />
        </q-card-actions>
      </q-card>
    </q-dialog>
</div>
</template>

<script>
import Sortable from 'sortablejs'
export default {
  components: {
  },
  props: [
    'colvisible',
    'columns',
    'title'
  ],
  data () {
    return {
      visibleColumns: [],
      colsreordenado: [],
      sortable: null,
      loading: true
    }
  },
  mounted () {
    var app = this
    app.loading = true
    if (typeof app.colvisible !== 'undefined') {
      if (app.colvisible.length > 0) app.visibleColumns = app.colvisible
    }
  },
  computed: {
    tudomarcado () {
      if (!this.columns) return false
      if (!this.visibleColumns) return false
      return (this.columns.length === this.visibleColumns.length)
    },
    usuariologado: function () {
      var app = this
      let u = null
      if (app.$store.state.usuario.logado) {
        if (app.$store.state.usuario.user) {
          u = app.$store.state.usuario.user
        }
      }
      return u
    }
  },
  methods: {
    actClose () {
      // this.$emit('ok', null)
      this.hide()
    },
    checkAll () {
      var app = this
      app.loading = true
      var list = app.sortable.toArray()
      var colVisible = []
      if (!app.tudomarcado) {
        list.forEach((element, key) => {
          colVisible.push(element)
        })
      }
      app.visibleColumns = colVisible
      app.checkMove()
      setTimeout(() => {
        app.loading = false
      }, 100)
    },
    checkMove: function (evt) {
      var app = this
      app.loading = true
      var list = app.sortable.toArray()
      var colVisible = []
      app.colsreordenado = []
      list.forEach((element, key) => {
        for (let index = 0; index < app.columns.length; index++) {
          const col = app.columns[index]
          // find na lista visivel
          var idxvisible = -1
          for (let nidx = 0; nidx < app.visibleColumns.length; nidx++) {
            const colname = app.visibleColumns[nidx]
            if (element === colname) {
              idxvisible = nidx
              break
            }
          }

          if (col.name === element) {
            app.colsreordenado.push(col)
            if ((col.required ? col.required : false) || (idxvisible >= 0)) colVisible.push(element)
          }
        }
      })
      app.visibleColumns = colVisible
      setTimeout(() => {
        app.loading = false
      }, 300)
    },
    show () {
      var app = this
      this.$refs.dialog.show()
      setTimeout(() => {
        var el = document.getElementById('itemsmovesorted')
        app.sortable = Sortable.create(el, {
          dragoverBubble: true,
          animation: 150,
          dataIdAttr: 'data-id',
          handle: '#draghandle',
          easing: 'cubic-bezier(1, 0, 0, 1)',
          onEnd: app.checkMove
        })
        app.loading = false
      }, 500)
    },
    hide () {
      this.$refs.dialog.hide()
    },
    onDialogHide () {
      this.$emit('hide')
    },
    actClearAll () {
      this.$emit('ok', null)
      this.hide()
    },
    async onOKClick () {
      var app = this
      await app.checkMove()
      this.$emit('ok', { visible: app.visibleColumns, cols: app.colsreordenado })
      this.hide()
    }
  }
}
</script>
