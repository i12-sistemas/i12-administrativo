<template>
<div>
  <q-page class="">
    <div class="row doc-page"  :class="$q.platform.is.mobile ? 'q-pa-md' : 'q-pa-md'"  >
      <div class="col-sm-12 q-pa-md">
        <div :class="$q.platform.is.mobile ? 'q-gutter-md' : 'q-gutter-lg'">
          <q-btn size="lg" unelevated
            v-for="(btn, key) in botoes" :key="key"
            :label="btn.label"
            :to="{ name: btn.routename }"
            :icon="btn.icon"
            :color="btn.color ? btn.color : 'secondary'"
            :text-color="btn.textcolor ? btn.textcolor : 'primary'"
            stack
            class="q-pa-lg"
          />
        </div>
      </div>
    </div>
  </q-page>
</div>
</template>

<script>
export default {
  name: 'cadastros',
  components: {
  },
  data () {
    return {
      botoes: [
        { label: 'Cidades', icon: 'fas fa-map-marker-alt', routename: 'cadastro.cidades' },
        { label: 'Regiões', icon: 'fas fa-layer-group', routename: 'cadastro.regioes' },
        { label: 'Regiões', routename: 'cadastro.regioes' }
      ],
      loading: false,
      posting: false,
      msgError: ''
    }
  },
  async mounted () {
  },

  methods: {
  }
}
</script>
