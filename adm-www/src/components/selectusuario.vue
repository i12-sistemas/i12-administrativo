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
        <template v-slot:append v-if="showme && ((!modelselect) || (eu ? eu.codusuario !== modelselect.codusuario : false))">
          <q-btn round dense flat @click="modelselect = eu">
            <q-avatar  v-if="(eu.fotoiconurl)" >
              <img :src="eu.fotoiconurl"/>
              <q-badge floating color="red" v-if="eu.ativo == false"><q-icon name="block" /></q-badge>
            </q-avatar>
            <q-avatar :color="eu.ativo == false ? 'grey' : 'primary'" text-color="white" v-if="(!eu.fotoiconurl)" >
              {{ eu.nome.charAt(0) }}
              <q-badge floating  color="red" v-if="eu.ativo == false"><q-icon name="block" /></q-badge>
            </q-avatar>
            <q-tooltip>Selecionar-me</q-tooltip>
          </q-btn>
        </template>
        <template v-slot:prepend v-if="modelselect">
          <q-avatar  v-if="(modelselect.fotoiconurl)" >
            <img :src="modelselect.fotoiconurl"/>
            <q-badge floating color="red" v-if="modelselect.ativo == false"><q-icon name="block" /></q-badge>
          </q-avatar>
          <q-avatar :color="modelselect.ativo == false ? 'grey' : 'primary'" text-color="white" v-if="(!modelselect.fotoiconurl)" >
            {{ modelselect.nome.charAt(0) }}
            <q-badge floating  color="red" v-if="modelselect.ativo == false"><q-icon name="block" /></q-badge>
          </q-avatar>
        </template>
        <template v-slot:selected-item="scope">
            <div v-if="scope.opt" class="ellipsis" :class="!scope.opt.ativo ? 'text-red' : ''">
              <q-icon name="block" v-if="!scope.opt.ativo" color="red" class="q-mr-xs"  />{{scope.opt.nome}}
              <q-tooltip>
                <div>{{scope.opt.nome}}</div>
                <div>{{scope.opt.email}}</div>
                <div v-if="!scope.opt.ativo"><q-icon name="block"  color="red" class="q-mr-xs"  /> Cadastro inativo</div>
              </q-tooltip>
            </div>
        </template>
        <template v-slot:option="scope">
          <q-item v-bind="scope.itemProps" v-on="scope.itemEvents" dense class="border-bottom-separator" >
          <q-item-section avatar>
            <q-avatar  v-if="(scope.opt.fotoiconurl)" >
              <img :src="scope.opt.fotoiconurl"/>
              <q-badge floating color="red" v-if="scope.opt.ativo == false"><q-icon name="block" /></q-badge>
            </q-avatar>
            <q-avatar :color="scope.opt.ativo == false ? 'grey' : 'primary'" text-color="white" v-if="(!scope.opt.fotoiconurl)" >
              {{ scope.opt.nome.charAt(0) }}
              <q-badge floating  color="red" v-if="scope.opt.ativo == false"><q-icon name="block" /></q-badge>
            </q-avatar>
          </q-item-section>
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
import Usuarios from 'src/mvc/collections/usuarios.js'
export default {
  components: {
  },
  props: [
    'value', 'dense', 'outlined', 'label', 'clearable', 'showme'
  ],
  data () {
    var usuarios = new Usuarios()
    return {
      usuarios,
      modelselect: null,
      options: null,
      showing: false,
      loading: false,
      eu: this.$store.state.usuario.usuario
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
      app.usuarios.filter = val
      app.usuarios.showinativos = false
      // app.usuarios.readPropsTable(props)
      var ret = await app.usuarios.fetch()
      if (ret.ok) {
        update(() => {
          this.options = []
          for (let index = 0; index < app.usuarios.itens.length; index++) {
            const usuario = app.usuarios.itens[index]
            app.options.push(usuario)
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
