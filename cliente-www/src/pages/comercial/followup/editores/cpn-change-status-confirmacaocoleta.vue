<template>
<div>
  <div class="no-padding" style="width: 400px;">
    <div class="fit row wrap q-col-gutter-sm q-pa-sm">
      <div class="col-12" v-if="!loading">
        <div>
          <q-option-group v-model="status" type="radio" color="primary" :options="optionsstatus" size="sm" inline @input="actSendInput" />
        </div>
        <div v-if="retornoerro ? retornoerro.ok : false" class="text-caption text-red">
          {{retornoerro.msg}}
        </div>

      </div>
    </div>
      </div>
</div>
</template>

<script>
export default {
  props: [ 'value' ],
  data () {
    return {
      retornoerro: { ok: false, msg: '' },
      loading: true,
      status: '0',
      optionsstatus: [
        {
          label: 'Sem status',
          value: '0',
          color: 'grey'
        },
        {
          label: 'Ok',
          value: '1',
          color: 'green'
        },
        {
          label: 'Erro',
          value: '2',
          color: 'erro'
        }
      ]
    }
  },
  computed: {
    hasError () {
      return this.retornoerro.ok
    }
  },
  async mounted () {
    var app = this
    if (typeof app.value !== 'undefined') app.status = app.value
    app.checkError()
    app.loading = false
  },
  methods: {
    checkError () {
      var app = this
      try {
        if (typeof app.status === 'undefined') throw new Error('Selecione um status')
        if (!((app.status === '0') || (app.status === '1') || (app.status === '2'))) throw new Error('Selecione um status')
        app.retornoerro.ok = false
        app.retornoerro.msg = ''
        return false
      } catch (error) {
        app.retornoerro.ok = true
        app.retornoerro.msg = error.message
        return true
      }
    },
    async actSendInput (e) {
      var app = this
      try {
        await app.checkError()
        // if (temerro) return
        this.$emit('input', app.status)
      } catch (error) {
        return false
      }
    }
  }
}
</script>
