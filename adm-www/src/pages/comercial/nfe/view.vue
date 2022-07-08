<template>
<div>
  <q-page class="">
    <div class="row doc-page" :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-pa-md'" >
      <div class="col-sm-12 full-width" :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-pt-md'">
          <div v-if="!loading">
            <formedit :chave="chave" />
          </div>
      </div>
    </div>
  </q-page>
</div>
</template>

<script>
import formedit from './edit-cpn.vue'

export default {
  name: 'nfe.view',
  components: {
    formedit
  },
  data () {
    return {
      loading: true,
      chave: null
    }
  },
  async mounted () {
    await this.init()
  },
  methods: {
    // async actUpdated (dataset) {
    //   var app = this
    //   app.dataset = dataset
    //   if ((app.adding) && (app.dataset.id > 0)) {
    //     app.adding = false
    //     app.idstart = app.dataset.id
    //     app.loading = false
    //   }
    // },
    async init () {
      var app = this
      app.loading = true
      app.chave = null
      if (app.$route.query.chave) app.chave = app.$route.query.chave
      app.loading = false
    }
  }
}
</script>
