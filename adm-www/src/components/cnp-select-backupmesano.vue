<template>
<div>
  <q-select :dense="dense" :outlined="outlined" v-model="modelselect" input-debounce="300"
        :label="label" stack-label
        clearable counter
        :disable="disable"
        ref="txtselect" class="full-width"
        :options="options" map-options emit-value
        :option-value="opt => Object(opt) === opt && 'primeirolastmodified' in opt ? $helpers.datetimeFormat(opt.primeirolastmodified, 'YYYY-MM') : null"

        clear-icon="clear"
        :loading="loading || loadingdata"
      >
        <template v-slot:append>
          <q-btn color="grey-6" flat round dense icon="sync"  @click="refreshData" :loading="loading" />
        </template>
        <template v-slot:selected-item="scope">
          <div v-if="scope.opt" class="ellipsis" >
            <span v-if="scope.opt.primeirolastmodified" >{{ $helpers.datetimeFormat(scope.opt.primeirolastmodified, 'MMM/YYYY') }}</span>
            <span v-if="scope.opt.primeirolastmodified !== scope.opt.ultimolastmodified">{{ ' dias ' + $helpers.datetimeFormat(scope.opt.primeirolastmodified, 'DD') + ' a ' + $helpers.datetimeFormat(scope.opt.ultimolastmodified, 'DD') }}</span>
            <span v-else>{{ ' dia ' + $helpers.datetimeFormat(scope.opt.primeirolastmodified, 'DD')}}</span>
            <span class="q-ml-sm text-caption text-grey">{{  $helpers.formatRS(scope.opt.qtdearquivos, '', 0) + ' arquivo(s) em ' + $helpers.bytesToHumanFileSizeString(scope.opt.totalsize) }}</span>
          </div>
        </template>
        <template v-slot:option="scope">
          <q-item v-bind="scope.itemProps" v-on="scope.itemEvents" :dense="dense" class="border-bottom-separator" >
            <q-item-section avatar>
              <q-avatar color="primary" text-color="white" font-size="12px" size="28px" >{{scope.opt.qtdearquivos}}</q-avatar>
            </q-item-section>
            <q-item-section >
              <q-item-label v-if="scope.opt.primeirolastmodified" style="width: 70px">{{ $helpers.datetimeFormat(scope.opt.primeirolastmodified, 'MMM/YYYY') }}</q-item-label>
            </q-item-section>
            <q-item-section >
              <q-item-label v-if="scope.opt.primeirolastmodified" class="text-center" >
                <span v-if="scope.opt.primeirolastmodified !== scope.opt.ultimolastmodified">{{ $helpers.datetimeFormat(scope.opt.primeirolastmodified, 'DD') + ' a ' + $helpers.datetimeFormat(scope.opt.ultimolastmodified, 'DD') }}</span>
                <span v-else>{{ $helpers.datetimeFormat(scope.opt.primeirolastmodified, 'DD')}}</span>
              </q-item-label>
            </q-item-section>
            <q-item-section side>
              <q-item-label v-if="scope.opt.totalsize" >{{ $helpers.bytesToHumanFileSizeString(scope.opt.totalsize) }}</q-item-label>
            </q-item-section>
          </q-item>
        </template>
        <template v-slot:counter>
          {{  $helpers.formatRS(totalarquivos, '', 0) + ' arquivo(s) em ' + $helpers.bytesToHumanFileSizeString(totalbytes) }}
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
import Backups from 'src/mvc/collections/backups.js'
export default {
  components: {
  },
  props: [
    'doc', 'value', 'dense', 'outlined', 'label', 'clearable', 'nullabled', 'loading', 'disable'
  ],
  data () {
    var dataset = new Backups()
    return {
      dataset,
      modelselect: null,
      options: null,
      showing: false,
      loadingdata: false
    }
  },
  async mounted () {
    await this.refreshData()
    if (this.value) this.modelselect = this.value
  },
  computed: {
    totalbytes: function () {
      var app = this
      if (!app.options) return 0
      var total = 0
      for (let index = 0; index < app.options.length; index++) {
        const element = app.options[index]
        total = parseInt(total + parseInt(element.totalsize))
      }
      return total
    },
    totalarquivos: function () {
      var app = this
      if (!app.options) return 0
      var total = 0
      for (let index = 0; index < app.options.length; index++) {
        const element = app.options[index]
        total = parseInt(total + parseInt(element.qtdearquivos))
      }
      return total
    }
  },
  watch: {
    doc: async function (val) {
      this.refreshData()
      this.modelselect = null
    },
    modelselect: async function (val) {
      var app = this
      app.$emit('input', val)
      app.$emit('updated')
    }
    // 'value.id': async function (val) {
    //   var app = this
    //   if (!app.modelselect) return
    //   app.modelselect.id = val
    // },
    // 'value.codigo_ibge': async function (val) {
    //   var app = this
    //   app.modelselect.codigo_ibge = val
    // },
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
    actOnFocus () {
      this.$nextTick(() => {
        this.$refs.txtselect.$el.focus()
      })
    },
    async refreshData () {
      var app = this
      app.loadingdata = true
      var ret = await app.dataset.fetchGroupMesAno(app.doc)
      if (ret.ok) {
        app.options = ret.data
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
