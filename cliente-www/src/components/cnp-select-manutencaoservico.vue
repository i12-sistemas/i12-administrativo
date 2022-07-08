<template>
<div>
  <q-select :dense="dense" :outlined="outlined" v-model="modelselect" input-debounce="300"
        :readonly="readonly"
        :label="label" stack-label
        use-input clearable
        ref="txtselect" class="full-width"
        :options="options" map-options
        @filter="refreshData"
        @clear="actOnFocus"
        clear-icon="clear"
        :loading="loading || loadingdata"
      >
        <template v-slot:selected-item="scope">
            <div v-if="scope.opt ? scope.opt.id > 0 : false" class="ellipsis" >
              {{scope.opt.descricao}}
              <q-tooltip :delay="500" v-if="(scope.opt.proxmanut_km > 0) || (scope.opt.proxmanut_dias > 0)">
                <div side v-if="(scope.opt.proxmanut_km > 0) || (scope.opt.proxmanut_dias > 0)">
                  Validade:
                  <span v-if="scope.opt.proxmanut_km > 0" class="text-weight-bold q-pr-xs">{{ $helpers.formatRS(scope.opt.proxmanut_km, false, 0)  + ' Km'}}</span>
                  <span v-if="(scope.opt.proxmanut_km > 0) && (scope.opt.proxmanut_dias > 0)"> ou </span>
                  <span v-if="scope.opt.proxmanut_dias > 0" class="text-weight-bold q-pr-xs">{{ scope.opt.proxmanut_dias + ' dias'}}</span>
                </div>
              </q-tooltip>
            </div>
        </template>
        <template v-slot:option="scope">
          <q-item v-bind="scope.itemProps" v-on="scope.itemEvents" dense class="border-bottom-separator" >
            <q-item-section >
              <q-item-label >{{scope.opt.descricao}}</q-item-label>
            </q-item-section>
            <q-item-section side >
              <q-item-label caption>
                id: {{scope.opt.id}}
              </q-item-label>
              <q-item-label caption v-if="(scope.opt.proxmanut_km > 0) || (scope.opt.proxmanut_dias > 0)">
                Validade:
                <span v-if="scope.opt.proxmanut_km > 0" class="text-weight-bold q-pr-xs">{{ $helpers.formatRS(scope.opt.proxmanut_km, false, 0)  + ' Km'}}</span>
                <span v-if="(scope.opt.proxmanut_km > 0) && (scope.opt.proxmanut_dias > 0)"> ou </span>
                <span v-if="scope.opt.proxmanut_dias > 0" class="text-weight-bold q-pr-xs">{{ scope.opt.proxmanut_dias + ' dias'}}</span>
              </q-item-label>
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
import ManutencoesServicos from 'src/mvc/collections/manutencoesservicos.js'
export default {
  components: {
  },
  props: [
    'value', 'dense', 'outlined', 'label', 'clearable', 'nullabled', 'loading', 'readonly'
  ],
  data () {
    var dataset = new ManutencoesServicos()
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
      app.loadingdata = true
      app.dataset.filter = val
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
