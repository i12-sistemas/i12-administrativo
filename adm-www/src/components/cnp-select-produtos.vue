<template>
<div>
  <q-select :dense="dense" :outlined="outlined" v-model="modelselect" input-debounce="300"
        :label="label" stack-label
        :use-input="!modelselect"
        clearable
        ref="txtselectproduto" class="full-width"
        :options="options" map-options
        @filter="refreshData"
        @clear="actOnFocus"
        clear-icon="clear"
        :loading="loading || loadingdata"
      >
        <template v-slot:selected-item="scope">
            <div v-if="scope.opt ? scope.opt.id > 0 : false" class="ellipsis" >
              {{ (scope.opt.onu === '' ? '' : scope.opt.onu + ' - ') + scope.opt.nome }}
              <q-tooltip>
                <div>{{scope.opt.nome}}</div>
                <div caption v-if="scope.opt.onu != ''" >ONU: {{scope.opt.onu}}</div>
              </q-tooltip>
            </div>
        </template>
        <template v-slot:option="scope">
          <q-item v-bind="scope.itemProps" v-on="scope.itemEvents" dense class="border-bottom-separator" >
            <q-item-section>
              <q-item-label >{{scope.opt.nome}}</q-item-label>
              <q-item-label caption v-if="scope.opt.onu != ''" >ONU: {{scope.opt.onu}}</q-item-label>
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
import Produtos from 'src/mvc/collections/produtos.js'
export default {
  components: {
  },
  props: [
    'value', 'dense', 'outlined', 'label', 'clearable', 'nullabled', 'loading'
  ],
  data () {
    var dataset = new Produtos()
    return {
      dataset,
      modelselect: null,
      options: null,
      showing: false,
      loadingdata: false
    }
  },
  async mounted () {
    if (this.value) {
      if (this.value.id > 0) this.modelselect = this.value
    }
    this.actOnFocus()
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
    'value.id': async function (val) {
      var app = this
      if (!app.modelselect) return
      app.modelselect.id = val
    },
    'value.codigo_ibge': async function (val) {
      var app = this
      app.modelselect.codigo_ibge = val
    },
    'value.cidade': async function (val) {
      var app = this
      app.modelselect.cidade = val
    },
    'value.estado': async function (val) {
      var app = this
      app.modelselect.estado = val
    },
    'value.uf': async function (val) {
      var app = this
      app.modelselect.uf = val
    }
  },
  methods: {
    actOnFocus () {
      this.$nextTick(() => {
        this.$refs.txtselectproduto.$el.focus()
      })
    },
    async refreshData (val, update) {
      var app = this
      app.loadingdata = true
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
      app.loadingdata = false
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
