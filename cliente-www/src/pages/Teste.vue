<template>
  <q-page class="q-pa-md">
    <div class="row q-col-gutter-xs q-mt-lg">
      <div class="col-12">
        <q-btn color="primary" icon="check" label="OK" @click="onClick" />
        <q-checkbox left-label v-model="showall" label="Show All" />
      </div>
      <div class="col-sm-12 col-md-4">
      </div>
      <div class="col-12">
        Teste
      </div>
    </div>
  </q-page>
</template>

<script>

export default {
  name: 'Teste',
  components: {
  },
  data () {
    return {
      cliente: null,
      showall: false
    }
  },
  async mounted () {
    // var app = this
  //   this.$store.dispatch('usuario/doLogout')
  },
  methods: {
    onSelect (model) {
      this.modelo = model
    },
    onClose () {
      this.showcateg = false
    },
    onClick () {
      this.showcateg = true
    }

  }
}
</script>
