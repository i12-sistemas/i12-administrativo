<template>
<div>
  <q-select :dense="dense" :outlined="outlined" v-model="modelselect" input-debounce="300"
        :label="label" stack-label
        :use-input="!modelselect"
        clearable :disable="disable"
        ref="txtselect" class="full-width"
        :options="options" map-options
        @filter="refreshData"
        @clear="actOnFocus"
        clear-icon="clear"
      >
        <template v-slot:before-options>
          <q-item clickable @click="actAddNovo">
            <q-item-section class="text-accent" >
              Adicionar tipo..
            </q-item-section>
            <q-item-section side avatar>
              <q-avatar size="22px" color="accent" text-color="white" icon="add" />
            </q-item-section>
          </q-item>
          <q-separator spaced inset vertical dark />
        </template>
        <template v-slot:selected-item="scope">
            <div v-if="scope.opt" class="ellipsis" :class="!scope.opt.ativo ? 'text-red' : ''">
              <q-icon name="block" v-if="!scope.opt.ativo" color="red" class="q-mr-xs"  />{{scope.opt.tipo}}
              <q-tooltip>
                <div>{{scope.opt.tipo}}</div>
                <div v-if="!scope.opt.ativo"><q-icon name="block"  color="red" class="q-mr-xs"  /> Cadastro inativo</div>
              </q-tooltip>
            </div>
        </template>
        <template v-slot:option="scope">
          <q-item v-bind="scope.itemProps" v-on="scope.itemEvents" dense class="border-bottom-separator" >
            <q-item-section>
              <q-item-label :class="scope.opt.ativo ? '' : 'text-red'" >{{scope.opt.tipo}}</q-item-label>
            </q-item-section>
            <q-item-section v-if="!scope.opt.ativo" side>
              <q-item-label ><q-badge color="red" text-color="white" label="INATIVO" /></q-item-label>
            </q-item-section>
          </q-item>
        </template>
        <template v-slot:no-option>
          <q-item>
            <q-item-section class="text-grey">
              Sem resultados
            </q-item-section>
          </q-item>
        </template>
      </q-select>
</div>
</template>

<script>
import VeiculoTipos from 'src/mvc/collections/veiculotipos.js'
import VeiculoTipo from 'src/mvc/models/veiculotipo.js'
export default {
  components: {
  },
  props: [
    'value', 'dense', 'outlined', 'label', 'clearable', 'disable'
  ],
  data () {
    var dataset = new VeiculoTipos()
    return {
      dataset,
      modelselect: null,
      options: null,
      showing: false,
      loading: false
    }
  },
  async mounted () {
    if (this.value) {
      this.modelselect = this.value
    }
  },
  computed: {
    selecao: function () {
      var app = this
      if (!app.options) return false
      var sel = null
      for (let index = 0; index < app.options.length; index++) {
        const element = app.options[index]
        if (app.registroselecionado === element.id) {
          sel = element.dados
          break
        }
      }
      return sel
    }
  },
  watch: {
    modelselect: async function (val) {
      var app = this
      app.$emit('input', val)
      app.$emit('updated')
    },
    value: async function (val) {
      var app = this
      app.modelselect = val
    }
  },
  methods: {
    actOnFocus () {
      this.$nextTick(() => {
        this.$refs.txtselect.$el.focus()
      })
    },
    async actAddNovo () {
      var app = this
      var lTipo = new VeiculoTipo()
      var ret = lTipo.edit(app)
      await ret.then(function (value) {
        if (value.ok) {
          app.$q.notify({
            message: 'Cadastro salvo!',
            color: 'positive',
            caption: value.msg,
            actions: [
              { label: 'OK', color: 'white', handler: () => { /* ... */ } }
            ]
          })
          app.$nextTick(() => {
            app.modelselect = lTipo
          })
        } else {
          if (value.msg) {
            if (value.msg !== '') app.$helpers.showDialog(value)
          }
        }
      })
    },
    async refreshData (val, update) {
      var app = this
      app.loading = true
      app.dataset.filter = val
      app.dataset.showinativos = false
      // app.usuarios.readPropsTable(props)
      var ret = await app.dataset.fetch()
      if (ret.ok) {
        update(() => {
          this.options = []
          for (let index = 0; index < app.dataset.itens.length; index++) {
            const element = app.dataset.itens[index]
            app.options.push(element)
          }
        })
      }
      app.loading = false
    }
  }
}
</script>
<style>
.border-bottom-separator {
  border-bottom: 1px solid #e4e4e4;
  padding-top: 3px;
  padding-bottom: 3px;
}
</style>
