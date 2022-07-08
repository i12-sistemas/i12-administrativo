<template>
<div>
  <q-select :dense="dense" :outlined="outlined" v-model="modelselect" input-debounce="300"
        :label="label" stack-label :use-input="!modelselect" clearable
        ref="txtselect" class="full-width"
        :options="options" map-options
        @filter="refreshData"
        @clear="actOnFocus"
        clear-icon="clear"
      >
        <template v-slot:selected-item="scope">
            <div v-if="scope.opt" class="ellipsis" :class="!scope.opt.ativo ? 'text-red' : ''">
              <q-icon name="block" v-if="!scope.opt.ativo" color="red" class="q-mr-xs"  />{{scope.opt.nome}}
              <q-tooltip>
                <div>{{scope.opt.nome}}</div>
                <div>{{scope.opt.profissao}}</div>
                <div v-if="!scope.opt.ativo"><q-icon name="block"  color="red" class="q-mr-xs"  /> Cadastro inativo</div>
              </q-tooltip>
            </div>
        </template>
        <template v-slot:option="scope">
          <q-item v-bind="scope.itemProps" v-on="scope.itemEvents" dense class="border-bottom-separator" >
            <q-item-section>
              <q-item-label :class="scope.opt.ativo ? '' : 'text-red'" >{{scope.opt.nome}}</q-item-label>
              <q-item-label caption v-if="scope.opt.profissao ? scope.opt.profissao !== '' : false" >
                {{scope.opt.profissao}}
              </q-item-label>
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
import Colaboradores from 'src/mvc/collections/colaboradores.js'
export default {
  components: {
  },
  props: [
    'value', 'dense', 'outlined', 'label', 'clearable'
  ],
  data () {
    var colaboradores = new Colaboradores()
    return {
      colaboradores,
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
    async refreshData (val, update) {
      var app = this
      app.loading = true
      app.colaboradores.filter = val
      app.colaboradores.showinativos = false
      // app.colaboradores.readPropsTable(props)
      var ret = await app.colaboradores.fetch()
      if (ret.ok) {
        update(() => {
          this.options = []
          for (let index = 0; index < app.colaboradores.itens.length; index++) {
            const colaborador = app.colaboradores.itens[index]
            app.options.push(colaborador)
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
