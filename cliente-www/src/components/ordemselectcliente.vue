<template>
<div>
    <q-select v-model="registroselecionado" class="full-width"
      use-input use-chips multiple clearable input-debounce="0"
      emit-value map-options
      label="Filtrar por cliente" :options="options"
      :option-label="(item) => item === null ? 'Null value' : JSON.stringify(item.nome)"
      @filter="onfilter"
    >
      <template v-slot:prepend>
        <q-icon :name="$icons.pessoas.clientes" />
      </template>
      <template v-slot:append>
        <q-btn flat dense round icon="sync" @click="refreshLista(true)" :disable="loading" :loading="loading" >
          <q-tooltip>Atualizar listagem</q-tooltip>
        </q-btn>
      </template>
      <template v-slot:selected-item="scope" v-if="registroselecionado">
        <q-chip removable dense @remove="scope.removeAtIndex(scope.index)" :tabindex="scope.tabindex"
          color="white" :text-color="scope.opt.dados.cliente.ativo === false ? 'red' : 'primary'" class="q-ma-none">
          <q-avatar  v-if="(scope.opt.dados.cliente.fotoiconurl) && (scope.opt.dados.cliente.ativo === true)" >
            <img :src="scope.opt.dados.cliente.fotoiconurl" />
          </q-avatar>
          <q-avatar  v-if="scope.opt.dados.cliente.ativo === false" icon="block" text-color="white" color="red" >
          </q-avatar>
          {{$helpers.strEllipsis(scope.opt.dados.cliente.nome, $q.platform.is.mobile ? 15 : (registroselecionado.length === 1 ? 70 : registroselecionado.length === 2 ? 30 : 15)) }}
        </q-chip>
      </template>
      <template v-slot:option="scope">
          <q-item v-bind="scope.itemProps" v-on="scope.itemEvents" >
          <q-item-section avatar>
            <q-avatar  v-if="(scope.opt.dados.cliente.fotoiconurl)" >
              <img :src="scope.opt.dados.cliente.fotoiconurl"/>
              <q-badge floating color="red" v-if="scope.opt.dados.cliente.ativo == false"><q-icon name="block" /></q-badge>
            </q-avatar>
            <q-avatar :color="scope.opt.dados.cliente.ativo == false ? 'grey' : 'primary'" text-color="white" v-if="(!scope.opt.dados.cliente.fotoiconurl)" >
              {{ scope.opt.dados.cliente.nome.charAt(0) }}
              <q-badge floating  color="red" v-if="scope.opt.dados.cliente.ativo == false"><q-icon name="block" /></q-badge>
            </q-avatar>
          </q-item-section>
          <q-item-section>
            <q-item-label >
              <div :class="scope.opt.dados.cliente.ativo == false ? 'text-red' : ''"> {{ scope.opt.dados.cliente.nome }}</div>
            </q-item-label>
            <q-item-label caption class="text-red" v-if="scope.opt.dados.cliente.ativo == false">Usuário inativo!</q-item-label>
            <q-item-label caption>{{ (scope.opt.dados.cliente.fantasia === '' ? '' : scope.opt.dados.cliente.fantasia) }}</q-item-label>
            <q-item-label caption>
              {{ (scope.opt.dados.qaberta > 0 ? 'Em aberto: ' + scope.opt.dados.qaberta : 'Nenhum em aberto') }}
              {{ (scope.opt.dados.qencerrada > 0 ? ' - Encerradas há um mês atrás: ' + scope.opt.dados.qencerrada : '') }}
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
import OrdensServico from 'src/mvc/collections/ordensservicos.js'
export default {
  components: {
  },
  props: [
    'value'
  ],
  data () {
    var ordens = new OrdensServico()
    return {
      ordens,
      registroselecionado: null,
      listagem: null,
      options: null,
      loading: false
    }
  },
  async mounted () {
    if (this.value) {
      let l = await this.refreshLista(true)
      this.options = l
      this.listagem = l
      this.registroselecionado = this.value
    }
  },
  watch: {
    registroselecionado: async function (val) {
      var app = this
      app.$emit('input', val)
      app.$emit('updated')
    },
    value: async function (val) {
      var app = this
      if (!app.listagem) {
        let l = await app.refreshLista(true)
        app.options = l
        app.listagem = l
      }
      app.registroselecionado = val
    }
  },
  methods: {
    async refreshLista (pForce) {
      var app = this
      app.loading = true
      var l = null
      var ret = await app.ordens.listClientesOS()
      ret.ok = true
      var promises = app.ordens.listclientesos.reduce(function (filtered, option) {
        if (option) {
          filtered.push({ dados: option, value: option.cliente.codcliente })
        }
        return filtered
      }, [])
      await Promise.all(promises).then(function (results) {
        l = results
        app.loading = false
      })
      app.loading = false
      return l
    },
    async onfilter (val, update) {
      var app = this
      if (!app.listagem) {
        let l = await app.refreshLista(true)
        app.options = l
        app.listagem = l
        update()
        return
      }
      update(async () => {
        if (val === '') {
          app.options = app.listagem
          update()
        } else {
          const needle = val.toLowerCase()
          this.options = app.listagem.filter(function (el) {
            return (el.dados.cliente.nome.toLowerCase().indexOf(needle) > -1) || (el.dados.cliente.fantasia.toLowerCase().indexOf(needle) > -1)
          })
        }
      })
    }
  }
}
</script>
