<template>
<div>
  <q-select :dense="dense" :outlined="outlined" v-model="modelselect" input-debounce="300"
        :label="label" stack-label
        use-input clearable
        ref="txtselect" class="full-width"
        :options="options" map-options
        @filter="refreshData"
        @clear="actOnFocus"
        clear-icon="clear"
        :loading="loading || loadingdata"
        :readonly="readonly"
      >
        <template v-slot:append>
          <q-btn color="grey-8" flat icon="edit" dense round v-if="modelselect ? modelselect.id > 0 : false" @click="actEditUsuario(modelselect.id)">
            <q-tooltip>Editar cadastro {{modelselect.razaosocial}}</q-tooltip>
          </q-btn>
        </template>
        <template v-slot:selected-item="scope">
            <div v-if="scope.opt ? scope.opt.id > 0 : false" class="ellipsis" >
              {{scope.opt.nome}}
              <q-tooltip>
                <div v-if="scope.opt.email ? scope.opt.email !== '' : false">{{scope.opt.email}}</div>
                <div v-if="scope.opt.login ? ((scope.opt.login !== '') && (scope.opt.login !== scope.opt.nome)) : false">{{scope.opt.login}}</div>
              </q-tooltip>
            </div>
        </template>
        <template v-slot:option="scope">
          <q-item v-bind="scope.itemProps" v-on="scope.itemEvents" dense class="border-bottom-separator" >
            <q-item-section>
              <q-item-label >{{scope.opt.nome}}</q-item-label>
              <q-item-label caption v-if="scope.opt.login ?  ((scope.opt.login !== '') && (scope.opt.login !== scope.opt.nome)) : false" >{{scope.opt.login}}</q-item-label>
              <q-item-label caption v-if="scope.opt.email ? scope.opt.email != '' : false" >{{scope.opt.email}}</q-item-label>
            </q-item-section>
            <q-item-section side top>
                <q-item-label caption >ID.: {{ scope.opt.id }}</q-item-label>
                <q-item-label v-if="!scope.opt.ativo" > <q-chip  color="grey-3" text-color="red" label="Inativo" size="12px" icon="block" /> </q-item-label>
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

  <q-dialog v-model="showeditusuario"  :maximized="$q.platform.is.mobile"  >
    <q-card style="width: 80vw; max-width: 80vw;" >
        <q-card-section class="bg-primary text-white q-py-0 q-px-md">
          <div class="row items-center no-wrap">
            <div class="col">
              <div class="text-h6">Cadastro de usuário</div>
            </div>
            <div class="col-auto">
              <q-btn color="white" round flat icon="clear" @click="actUsuarioEditClose" >
                <q-tooltip>Fechar [ESC]</q-tooltip>
              </q-btn>
            </div>
          </div>
        </q-card-section>
      <q-card-section class="q-pt-md scroll" style="max-height: 80vh" >
        <formedit :adding="false" :idstart="editusuarioid" @updated="actUpdatedUsuario" mode="dialog" @close="actUsuarioEditClose" />
      </q-card-section>
    </q-card>
  </q-dialog>
</div>
</template>

<script>
import Usuarios from 'src/mvc/collections/usuarios.js'
import Usuario from 'src/mvc/models/usuario.js'
import formedit from 'src/pages/cadastro/usuarios/edit-cpn.vue'
export default {
  components: {
    formedit
  },
  props: [
    'value', 'dense', 'outlined', 'label', 'clearable', 'nullabled', 'loading', 'readonly'
  ],
  data () {
    var dataset = new Usuarios()
    return {
      dataset,
      modelselect: null,
      options: null,
      showing: false,
      loadingdata: false,
      showeditusuario: false,
      editusuarioid: null
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
    actEditUsuario (pIDUsuario) {
      var app = this
      app.editusuarioid = pIDUsuario
      app.showeditusuario = true
    },
    actUsuarioEditClose () {
      var app = this
      app.editusuarioid = null
      app.showeditusuario = false
    },
    async actUpdatedUsuario (pRegistro) {
      var app = this
      app.modelselect = new Usuario(pRegistro)
      app.$emit('input', pRegistro)
      app.$emit('updated')
    },
    actOnFocus () {
      this.$nextTick(() => {
        this.$refs.txtselect.$el.focus()
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
