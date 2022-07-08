<template>
<div>
    <q-select v-model="usuarioselecionado" class="full-width"
      use-input use-chips multiple clearable input-debounce="0"
      emit-value
      map-options
      label="Filtrar por usuário" :options="options"
      @filter="onfilter"
    >
      <template v-slot:prepend>
        <q-icon :name="$icons.pessoas.usuarios" />
      </template>
      <template v-slot:append>
        <q-btn flat dense round icon="sync" @click="refreshListaUsuario(true)" :disable="loading" :loading="loading" >
          <q-tooltip>Atualizar listagem</q-tooltip>
        </q-btn>
      </template>
      <template v-slot:selected-item="scope">
        <q-chip removable dense @remove="scope.removeAtIndex(scope.index)" :tabindex="scope.tabindex"
          color="white" :text-color="scope.opt.dados.usuarioresponsavel.ativo === false ? 'red' : 'primary'" class="q-ma-none">
          <q-avatar  v-if="(scope.opt.dados.usuarioresponsavel.fotoiconurl) && (scope.opt.dados.usuarioresponsavel.ativo === true)" >
            <img :src="scope.opt.dados.usuarioresponsavel.fotoiconurl" />
          </q-avatar>
          <q-avatar  v-if="scope.opt.dados.usuarioresponsavel.ativo === false" icon="block" text-color="white" color="red" >
          </q-avatar>
          {{$helpers.strEllipsis(scope.opt.dados.usuarioresponsavel.nome, $q.platform.is.mobile ? 10 : 20) }}
        </q-chip>
      </template>
      <template v-slot:option="scope">
          <q-item v-bind="scope.itemProps" v-on="scope.itemEvents" >
          <q-item-section avatar>
            <q-avatar  v-if="(scope.opt.dados.usuarioresponsavel.fotoiconurl)" >
              <img :src="scope.opt.dados.usuarioresponsavel.fotoiconurl"/>
              <q-badge floating color="red" v-if="scope.opt.dados.usuarioresponsavel.ativo == false"><q-icon name="block" /></q-badge>
            </q-avatar>
            <q-avatar :color="scope.opt.dados.usuarioresponsavel.ativo == false ? 'grey' : 'primary'" text-color="white" v-if="(!scope.opt.dados.usuarioresponsavel.fotoiconurl)" >
              {{ scope.opt.dados.usuarioresponsavel.nome.charAt(0) }}
              <q-badge floating  color="red" v-if="scope.opt.dados.usuarioresponsavel.ativo == false"><q-icon name="block" /></q-badge>
            </q-avatar>
          </q-item-section>
          <q-item-section>
            <q-item-label >
              <div :class="scope.opt.dados.usuarioresponsavel.ativo == false ? 'text-red' : ''"> {{ scope.opt.dados.usuarioresponsavel.nome }}</div>
            </q-item-label>
            <q-item-label caption class="text-red" v-if="scope.opt.dados.usuarioresponsavel.ativo == false">Usuário inativo!</q-item-label>
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
  name: 'ordemselectusuario',
  components: {
  },
  props: [
    'value'
  ],
  data () {
    var ordens = new OrdensServico()
    return {
      ordens,
      usuarioselecionado: null,
      listagem: null,
      options: null,
      loading: false
    }
  },
  async mounted () {
    if (this.value) {
      let l = await this.refreshListaUsuario(true)
      this.options = l
      this.listagem = l
      this.usuarioselecionado = this.value
    }
  },
  watch: {
    usuarioselecionado: async function (val) {
      var app = this
      app.$emit('input', val)
      app.$emit('updated')
    },
    value: async function (val) {
      var app = this
      if (!app.listagem) {
        let l = await app.refreshListaUsuario(true)
        app.options = l
        app.listagem = l
      }
      app.usuarioselecionado = val
    }
  },
  methods: {
    async refreshListaUsuario (pForce) {
      var app = this
      app.loading = true
      var l = null
      var ret = await app.ordens.listUsuarioOS()
      ret.ok = true
      var promises = app.ordens.listusuariosos.reduce(function (filtered, option) {
        if (option) {
          filtered.push({ dados: option, value: option.usuarioresponsavel.codusuario })
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
        let l = await app.refreshListaUsuario(true)
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
            return el.dados.usuarioresponsavel.nome.toLowerCase().indexOf(needle) > -1
          })
        }
      })
    }
  }
}
</script>
