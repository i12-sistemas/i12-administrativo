<template>
<div>
  <q-select :dense="dense" :outlined="outlined" v-model="modelinterno" input-debounce="0"
        :label="(label ? label : 'UF') + (modeshow=='estado' ? (model ? ': ' + model : '') : '') " stack-label :clearable="clearable"
        ref="txtselect" class="full-width"
        :options="options" map-options use-input hide-selected fill-input
        option-value="uf"
        :disable="disable"
        :option-label="modeshow ? (modeshow=='estado' ? 'estado' : 'uf') : 'uf'"
        clear-icon="clear"
         @filter="filterFnAutoselect"
        @filter-abort="abortFilterFn"
        lazy-rules :rules="[ val => val || 'Obrigatório informar o estado.']"
      >
      <q-tooltip v-if="model">
        {{modelinterno.estado + ' - ' + modelinterno.uf}}
      </q-tooltip>
    </q-select>
</div>
</template>

<script>
export default {
  components: {
  },
  props: [
    'value', 'dense', 'outlined', 'label', 'clearable', 'modeshow', 'disable'
  ],
  data () {
    return {
      modelinterno: null,
      model: '',
      ufs: [
        { uf: 'AC', estado: 'Acre' },
        { uf: 'AL', estado: 'Alagoas' },
        { uf: 'AP', estado: 'Amapá' },
        { uf: 'AM', estado: 'Amazonas' },
        { uf: 'BA', estado: 'Bahia' },
        { uf: 'CE', estado: 'Ceará' },
        { uf: 'DF', estado: 'Distrito Federal' },
        { uf: 'ES', estado: 'Espírito Santo' },
        { uf: 'GO', estado: 'Goiás' },
        { uf: 'MA', estado: 'Maranhão' },
        { uf: 'MT', estado: 'Mato Grosso' },
        { uf: 'MS', estado: 'Mato Grosso do Sul' },
        { uf: 'MG', estado: 'Minas Gerais' },
        { uf: 'PA', estado: 'Pará' },
        { uf: 'PB', estado: 'Paraíba' },
        { uf: 'PR', estado: 'Paraná' },
        { uf: 'PE', estado: 'Pernambuco' },
        { uf: 'PI', estado: 'Piauí' },
        { uf: 'RJ', estado: 'Rio de Janeiro' },
        { uf: 'RN', estado: 'Rio Grande do Norte' },
        { uf: 'RS', estado: 'Rio Grande do Sul' },
        { uf: 'RO', estado: 'Rondônia' },
        { uf: 'RR', estado: 'Roraima' },
        { uf: 'SC', estado: 'Santa Catarina' },
        { uf: 'SP', estado: 'São Paulo' },
        { uf: 'SE', estado: 'Sergipe' },
        { uf: 'TO', estado: 'Tocantins' }
      ],
      options: null,
      showing: false,
      loading: false
    }
  },
  async mounted () {
    var app = this
    if (app.value) {
      app.modelinterno = await app.ufs.find(function (element) {
        return element.uf.toUpperCase() === app.value.toUpperCase()
      })
      app.model = app.modelinterno.uf
    }
  },
  watch: {
    modelinterno: async function (val) {
      var app = this
      app.$emit('input', val.uf)
      app.$emit('updated')
    },
    value: async function (val) {
      var app = this
      app.model = val
    }
  },
  methods: {
    filterFnAutoselect (val, update, abort) {
      var app = this
      // call abort() at any time if you can't retrieve data somehow
      setTimeout(() => {
        update(() => {
          if (val === '') {
            this.options = app.ufs
          } else {
            const needle = val.toLowerCase()
            this.options = app.ufs.filter(function (element) {
              return (element.estado.toLowerCase().indexOf(needle) > -1) || (element.uf.toLowerCase().indexOf(needle) > -1)
            })
          }
        },
        // next function is available in Quasar v1.7.4+;
        // "ref" is the Vue reference to the QSelect
        ref => {
          if (val !== '' && ref.options.length > 0 && ref.optionIndex === -1) {
            ref.moveOptionSelection(1, true) // focus the first selectable option and do not update the input-value
            ref.toggleOption(ref.options[ref.optionIndex], true) // toggle the focused option
          }
        })
      }, 300)
    },
    abortFilterFn () {
    },
    actOnFocus () {
      this.$nextTick(() => {
        this.$refs.txtselect.$el.focus()
      })
    }
  }
}
</script>
