<template>
<div>
  <q-select :dense="dense" :outlined="outlined" v-model="modelselect" input-debounce="300"
        :label="label" stack-label
        :clearable="clearable"
        ref="txtselect" class="full-width"
        :options="options" map-options
        :loading="loading || loadingdata"
        :readonly="readonly"
      >
        <template v-slot:selected-item="scope">
            <div class="ellipsis q-pt-xs" v-if="!enfasecnpj" >
              <span>{{ scope.opt.fantasia }}</span>
              <span v-if="scope.opt.cnpj">{{ ' - ' +  $helpers.mascaraDocCPFCNPJ(scope.opt.cnpj) }}</span>
            </div>
            <div class="ellipsis q-pt-xs" v-if="enfasecnpj" >
              <span >{{ (scope.opt.cnpj ? $helpers.mascaraDocCPFCNPJ(scope.opt.cnpj): '*') + ' - ' + scope.opt.fantasia }}</span>
            </div>
            <q-tooltip :delay="700">
              <div caption >{{scope.opt.fantasia}}</div>
              <div caption v-if="scope.opt.razaosocial != '' && scope.opt.razaosocial != scope.opt.fantasia" >{{scope.opt.razaosocial}}</div>
              <div caption v-if="scope.opt.cnpj" >{{ $helpers.mascaraDocCPFCNPJ(scope.opt.cnpj) }}</div>
              <div caption v-if="scope.opt.cidade" >{{scope.opt.cidade.cidade + ' - ' + scope.opt.cidade.uf }}</div>
            </q-tooltip>
        </template>
        <template v-slot:option="scope">
          <q-item v-bind="scope.itemProps" v-on="scope.itemEvents" dense class="border-bottom-separator" >
            <q-item-section>
              <q-item-label class="text-weight-bold" v-if="scope.opt.cnpj && enfasecnpj" >{{ $helpers.mascaraDocCPFCNPJ(scope.opt.cnpj)}}</q-item-label>
              <q-item-label caption >{{scope.opt.fantasia}}</q-item-label>
              <q-item-label caption v-if="scope.opt.razaosocial != '' && scope.opt.razaosocial != scope.opt.fantasia" >{{scope.opt.razaosocial}}</q-item-label>
              <q-item-label caption v-if="scope.opt.cnpj && !enfasecnpj" >{{ $helpers.mascaraDocCPFCNPJ(scope.opt.cnpj)}}</q-item-label>
              <q-item-label caption v-if="scope.opt.cidade" >{{scope.opt.cidade.cidade + ' - ' + scope.opt.cidade.uf }}</q-item-label>
            </q-item-section>
            <q-item-section avatar top v-if="(scope.opt.fantasia_followup ? scope.opt.fantasia_followup !== '' : false) && (scope.opt.followupid ? scope.opt.followupid !== '' : false)">
              <q-avatar color="white" text-color="red" icon="block" font-size="22px" />
                <q-tooltip :delay="700">Cadastro inativo</q-tooltip>
            </q-item-section>
            <q-tooltip :delay="700">
                <div caption >{{scope.opt.fantasia}}</div>
                <div caption v-if="scope.opt.razaosocial != '' && scope.opt.razaosocial != scope.opt.fantasia" >{{scope.opt.razaosocial}}</div>
                <div caption v-if="scope.opt.cnpj" >{{ $helpers.mascaraDocCPFCNPJ(scope.opt.cnpj) }}</div>
                <div caption v-if="scope.opt.cidade" >{{scope.opt.cidade.cidade + ' - ' + scope.opt.cidade.uf }}</div>
              </q-tooltip>
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
export default {
  components: {
  },
  props: [
    'value', 'dense', 'outlined', 'label', 'clearable', 'nullabled', 'loading', 'readonly', 'lista',
    'enfasecnpj'
  ],
  data () {
    return {
      modelselect: null,
      options: [],
      showing: false,
      loadingdata: false
    }
  },
  async mounted () {
    var app = this
    this.options = []
    if (app.lista ? app.lista.length > 0 : false) {
      for (let index = 0; index < app.lista.length; index++) {
        const element = app.lista[index]
        app.options.push(element)
      }
    }
    if (app.value) {
      app.modelselect = app.value
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
    'value.id': async function (val) {
      if (this.value) {
        this.modelselect = this.value
      } else {
        this.modelselect = null
      }
    }
  },
  methods: {
    actOnFocus () {
      this.$nextTick(() => {
        this.$refs.txtselect.$el.focus()
      })
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
