<template>
<div>
    <q-select :dense="dense" :outlined="outlined" v-model="modelselect" input-debounce="300"
        :label="label + (addfaster ? ((selecionadosids ? (selecionadosids.length > 0) : false) ? ' :: ' + selecionadosids.length + ' selecionado(s)' : '') : '')" stack-label
        use-input
        clearable
        input-class="text-uppercase"
        ref="txtselect" class="full-width no-wrap"
        :options="options" map-options
        @filter="refreshData"
        @clear="actOnFocus"
        :loading="loading || loadingdata"
        :readonly="readonly"
        @dblclick="actOnFocus"
      >
        <template v-slot:selected-item="scope">
            <div v-if="scope.opt ? scope.opt.id > 0 : false" class="ellipsis text-uppercase no-wrap"  >
              {{scope.opt.razaosocial + ' [' + scope.opt.id + ']'}}
              <q-tooltip>
                <div>{{scope.opt.razaosocial}}</div>
                <div caption v-if="scope.opt.fantasia != '' && scope.opt.fantasia != scope.opt.razaosocial" >{{scope.opt.fantasia}}</div>
                <div>CNPJ: {{$helpers.mascaraDocCPFCNPJ(scope.opt.cnpj) }}</div>
                <div v-if="scope.opt.endereco" >{{ scope.opt.endereco.endereconumeroecep }}</div>
                <div v-if="scope.opt.endereco.cidade" >{{ scope.opt.endereco.cidade.cidade }}</div>
                <div v-if="scope.opt.endereco.cidade" >{{ scope.opt.endereco.cidade.estado }}</div>
              </q-tooltip>
            </div>
        </template>
        <template v-slot:option="scope">
          <q-item v-if="!addfaster" v-bind="scope.itemProps" v-on="scope.itemEvents" dense class="border-bottom-separator full-width" >
            <q-item-section>
              <q-item-label class="text-uppercase" >{{scope.opt.razaosocial}}</q-item-label>
              <q-item-label caption class="text-uppercase" v-if="scope.opt.fantasia != '' && scope.opt.fantasia != scope.opt.razaosocial" >{{scope.opt.fantasia}}</q-item-label>
              <q-item-label caption v-if="scope.opt.endereco" class="ellipsis text-uppercase" >{{ scope.opt.endereco.endereconumeroecep }}</q-item-label>
              <q-item-label caption class="text-uppercase" v-if="scope.opt.endereco.cidade" >{{ scope.opt.endereco.cidade.cidade  + ' - ' + scope.opt.endereco.cidade.uf }}</q-item-label>
            </q-item-section>
            <q-item-section side top>
                <q-item-label caption class="text-uppercase">{{$helpers.mascaraDocCPFCNPJ(scope.opt.cnpj) }}</q-item-label>
                <q-item-label caption >Cód.: {{ scope.opt.id }}</q-item-label>
            </q-item-section>
          </q-item>
          <q-item v-if="addfaster" v-bind="scope.itemProps" @click="actFasterSelected(scope.opt)" dense class="border-bottom-separator full-width"
            :class="(selecionadosids ? (selecionadosids.find(element => parseInt(element) === parseInt(scope.opt.id))) : false) ? 'bg-light-blue-1' : ''"
            >
            <q-item-section avatar v-if="!processando">
              <q-icon size="24px" color="blue"  name="check_circle_outline" v-if="(selecionadosids ? (selecionadosids.find(element => parseInt(element) === parseInt(scope.opt.id))) : false)" />
              <q-icon size="24px" color="grey-7"  name="radio_button_unchecked" v-if="(selecionadosids ? !(selecionadosids.find(element => parseInt(element) === parseInt(scope.opt.id))) : true)" />
            </q-item-section>
            <q-item-section>
              <q-item-label class="text-uppercase" >{{scope.opt.razaosocial}}</q-item-label>
              <q-item-label caption class="text-uppercase" v-if="scope.opt.fantasia != '' && scope.opt.fantasia != scope.opt.razaosocial" >{{scope.opt.fantasia}}</q-item-label>
              <q-item-label caption v-if="scope.opt.endereco" class="ellipsis text-uppercase" >{{ scope.opt.endereco.endereconumeroecep }}</q-item-label>
              <q-item-label caption class="text-uppercase" v-if="scope.opt.endereco.cidade" >{{ scope.opt.endereco.cidade.cidade  + ' - ' + scope.opt.endereco.cidade.uf }}</q-item-label>
            </q-item-section>
            <q-item-section side top>
                <q-item-label caption class="text-uppercase">{{$helpers.mascaraDocCPFCNPJ(scope.opt.cnpj) }}</q-item-label>
                <q-item-label caption >Cód.: {{ scope.opt.id }}</q-item-label>
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
import Clientes from 'src/mvc/collections/clientes.js'
import Cliente from 'src/mvc/models/cliente.js'
export default {
  name: 'cpnselectclientedefault',
  components: {
  },
  props: [
    'value', 'dense', 'outlined', 'label', 'clearable', 'nullabled', 'loading', 'readonly', 'addfaster', 'selecionadosids'
  ],
  data () {
    var dataset = new Clientes()
    return {
      dataset,
      modelselect: null,
      modelold: null,
      processando: false,
      options: null,
      showing: false,
      loadingdata: false
    }
  },
  async mounted () {
    this.init()
  },
  computed: {
    selecionadoFast: function (b) {
      return b
    },
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
      if (app.readonly) return
      app.$emit('input', val)
      app.$emit('updated')
    },
    'value.id': async function (val) {
      var app = this
      app.init()
    }
    // 'value.cidade': async function (val) {
    //   var app = this
    //   app.modelselect.cidade = val
    // },
    // 'value.estado': async function (val) {
    //   var app = this
    //   app.modelselect.estado = val
    // },
    // 'value.uf': async function (val) {
    //   var app = this
    //   app.modelselect.uf = val
    // }
  },
  methods: {
    async init () {
      var app = this
      if (app.value) {
        try {
          app.modelselect = new Cliente(app.value)
          if (!app.modelselect.id) throw new Error('Nenhum cliente informado 1')
          if (!(app.modelselect.id > 0)) throw new Error('Nenhum cliente informado 2')
        } catch (error) {
          app.modelselect = null
        }
      }
    },
    async actEditCliente (pIDCliente) {
      var app = this
      var row = new Cliente()
      row.id = pIDCliente
      var ret = await row.dialogAddOrEdit(app)
      if (ret.ok) {
        // app.refreshData(null)
      } else {
        if (ret.msg ? ret.msg !== '' : false) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {
          })
        }
      }
    },
    async actNewClienteDialog () {
      var app = this
      var row = new Cliente()
      var ret = await row.dialogAddOrEdit(app)
      if (ret.ok) {
        if (ret.dataset ? ret.dataset.id > 0 : false) {
          app.modelselect = new Cliente(ret.dataset)
          app.$emit('updated')
        }
      } else {
        if (ret.msg ? ret.msg !== '' : false) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {
          })
        }
      }
    },
    async actFasterSelected (pRegistro) {
      var app = this
      app.processando = false
      app.$emit('addfasteradded', pRegistro)
      app.$nextTick(() => {
        app.processando = false
      })
    },
    async actUpdatedCliente (pRegistro) {
      var app = this
      app.modelselect = new Cliente(pRegistro)
      app.$emit('input', pRegistro)
      app.$emit('updated')
    },
    actOnFocus () {
      var app = this
      app.modelselect = null
      this.$nextTick(() => {
        app.$refs.txtselect.$el.focus()
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
