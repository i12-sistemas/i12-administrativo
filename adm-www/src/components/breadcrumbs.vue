<template>
  <div class="q-px-md q-py-sm q-gutter-sm">
    <q-breadcrumbs gutter="sm" :active-color="color ? color : 'primary'">
      <q-breadcrumbs-el v-if="showhome"  >
        <template v-slot:default>
          <q-btn :color="color ? color : 'primary'" icon="home" :to="{ name: 'home' }"  flat dense />
          <q-tooltip>Volta para o menu principal</q-tooltip>
        </template>
      </q-breadcrumbs-el>
      <q-breadcrumbs-el v-if="!hideback"  >
        <template v-slot:default>
          <q-btn :color="color ? color : 'primary'" icon="arrow_back"  @click="$router.go(-1)" flat dense />
          <q-tooltip>Volta para a tela anterior</q-tooltip>
        </template>
      </q-breadcrumbs-el>
      <q-breadcrumbs-el v-for="(item, key) in modelos" :key="'breadcrumb' + key"
          :label="item.label"
          :icon="item.icon"
          :to="item.to" />
      <q-breadcrumbs-el v-if="showactive" :label="$helpers.strEllipsis(showactive.label,30)" :icon="showactive.icon" >
          <q-tooltip>{{showactive.label}}</q-tooltip>
      </q-breadcrumbs-el>
    </q-breadcrumbs>
  </div>
</template>
<script>
export default {
  name: 'i12-breadcrumbs',
  props: [
    'value', 'showhome', 'showactive', 'hideback', 'color'
  ],
  data () {
    return {
      modelos: []
    }
  },
  async mounted () {
    if (this.value) {
      this.modelos = this.value
    }
  },
  watch: {
    value: function (val) {
      this.modelos = val
    }
  }
}
</script>
