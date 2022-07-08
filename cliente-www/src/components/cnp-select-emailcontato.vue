<template>
<div>
  <q-select :dense="dense" :outlined="outlined" v-model="modelselect" input-debounce="0"
        :label="label" stack-label use-input use-chips hide-dropdown-icon new-value-mode="add-unique"
        clearable multiple
        input-class="text-lowercase"
        ref="txtselect" class="full-width no-wrap"
        :options="optionsfiltered" map-options
        :loading="loading"
        :readonly="readonly"
        @filter="filterFn"
        :option-label="(item) => ((item.nome ? item.nome !== '' : false) ? item.nome + ' :: ' : '') + item.email"
        @new-value="createValue"
        :error="erroremail ? erroremail !== '' : false"
        :error-message="erroremail"
      >
        <template v-slot:option="scope">
          <q-item v-bind="scope.itemProps" v-on="scope.itemEvents" dense class="border-bottom-separator full-width" >
            <q-item-section>
              <q-item-label class="text-lowercase" >{{scope.opt.email}}</q-item-label>
              <q-item-label caption class="text-uppercase" v-if="scope.opt.nome ? scope.opt.nome != ''  : false" >{{scope.opt.nome}}</q-item-label>
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
import EmailGeneric from 'src/mvc/models/email-generic.js'
export default {
  components: {
  },
  props: [
    'value', 'dense', 'outlined', 'label', 'clearable', 'nullabled', 'readonly', 'sugestao'
  ],
  data () {
    return {
      erroremail: null,
      modelselect: [],
      options: null,
      optionsfiltered: null,
      loading: true
    }
  },
  async mounted () {
    var app = this
    this.options = []
    if (app.value) {
      for (let index = 0; index < app.value.length; index++) {
        const element = app.value[index]
        app.modelselect.push(element)
      }
    }
    if (app.sugestao) {
      for (let index = 0; index < app.sugestao.length; index++) {
        const element = app.sugestao[index]
        app.options.push(element)
      }
    }
    app.loading = false
  },
  watch: {
    modelselect: async function (val) {
      var app = this
      if (app.readonly) return
      app.$emit('input', val)
      app.$emit('updated')
    }
    // 'value.id': async function (val) {
    //   var app = this
    //   app.init()
    // }
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
    createValue (val, done) {
      var app = this
      try {
        if (!this.$helpers.validaEmail(val)) throw new Error('E-mail ' + val + ' inválido')

        var contato = new EmailGeneric()
        contato.email = val
      } catch (error) {
        this.erroremail = error.message
        setTimeout(() => {
          app.erroremail = null
        }, 3000)
        return false
      }
      // specific logic to eventually call done(...) -- or not
      done(contato, 'add-unique')
      // done callback has two optional parameters:
      //  - the value to be added
      //  - the behavior (same values of new-value-mode prop,
      //    and when it is specified it overrides that prop –
      //    if it is used); default behavior (if not using
      //    new-value-mode) is to add the value even if it would
      //    be a duplicate
    },
    filterFn (val, update) {
      var app = this
      update(() => {
        if (val === '') {
          app.optionsfiltered = app.options
        } else {
          const needle = val.toLowerCase()
          app.optionsfiltered = app.options.filter(
            v => (v.email.toLowerCase().indexOf(needle) > -1) || (v.nome.toLowerCase().indexOf(needle) > -1)
          )
        }
      })
    }
  }
}
</script>

<style scoped>
.border-bottom-separator {
  border-bottom: 1px solid #e4e4e4;
  padding-top: 3px;
  padding-bottom: 3px;
}
</style>
