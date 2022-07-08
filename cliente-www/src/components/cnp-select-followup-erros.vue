<template>
<div>
  <q-select :dense="!$q.platform.is.mobile" :outlined="outlined" v-model="modelselect" input-debounce="300"
        :label="label" stack-label
        @input="sendEmit"
        use-input
        :disable="disable"
        clearable
        input-class="text-uppercase ellipsis"
        ref="txtselect" class="full-width"
        :options="options" map-options
        @filter="refreshData"
        @clear="actOnFocus"
        clear-icon="clear"
        :loading="loading || loadingdata"
        :readonly="readonly"
      >
        <template v-slot:selected-item="scope">
            <q-icon name="block" color="red" v-if="!scope.opt.ativo" />
            <div v-if="scope.opt ? scope.opt.id > 0 : false" class=" text-caption" :class="scope.opt.ativo ? '' : 'text-red'" style="max-width: 75%;" >
              <div class="ellipsis"  >{{scope.opt.descricao}}</div>
              <q-tooltip>
                <div v-if="!scope.opt.ativo">** CADASTRO INATIVO **</div>
                <div>{{scope.opt.descricao}}</div>
              </q-tooltip>
            </div>
        </template>
        <template v-slot:option="scope">
          <q-item v-bind="scope.itemProps" v-on="scope.itemEvents" dense class="border-bottom-separator" >
            <q-item-section avatar top v-if="!scope.opt.ativo">
              <q-icon name="block" color="red" />
            </q-item-section>
            <q-item-section>
              <q-item-label >{{scope.opt.descricao}}</q-item-label>
            </q-item-section>
            <q-item-section side top>
                <q-item-label caption >{{scope.opt.id}}</q-item-label>
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
import FollowupErros from 'src/mvc/collections/followuperros.js'
import FollowupErro from 'src/mvc/models/followuperro.js'
export default {
  components: {
  },
  props: [
    'value', 'tipo', 'outlined', 'label', 'clearable', 'nullabled', 'loading', 'readonly', 'disable'
  ],
  data () {
    var dataset = new FollowupErros()
    return {
      dataset,
      modelselect: new FollowupErro(),
      options: null,
      showing: false,
      loadingdata: false
    }
  },
  async mounted () {
    this.modelselect = new FollowupErro(this.value)
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
    // 'modelselect.codigo_ibge': async function (val) {
    //   var app = this
    //   app.$emit('input', app.modelselect)
    //   app.$emit('updated')
    // },
    'loading': async function (val) {
      if (!val) {
        if (this.value) {
          this.modelselect = new FollowupErro(this.value)
        }
      }
    }
  },
  methods: {
    actOnFocus () {
      this.$nextTick(() => {
        this.$refs.txtselect.$el.focus()
      })
    },
    async sendEmit () {
      var app = this
      if (this.loading) return
      app.$emit('input', app.modelselect)
      app.$emit('updated')
    },
    async refreshData (val, update) {
      var app = this
      app.loadingdata = true
      app.dataset.filter = val
      app.dataset.tipo = app.tipo
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
      } else {
        if (ret.msg) {
          if (ret.msg !== '') app.$helpers.showDialog(ret)
        }
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
