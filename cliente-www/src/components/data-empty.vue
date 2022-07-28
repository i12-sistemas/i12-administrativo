<template>
<div>
  <q-slide-transition>
      <q-card class="text-center " bordered flat :class="((color ? color !== '' : false) ? 'text-' + color : 'text-grey')">
        <q-card-section class="text-center">
          <q-icon name="search_off" size="5em" />
          <div class="text-body1">Nenhum registro encontrado</div>
        </q-card-section>
      </q-card>
  </q-slide-transition>
</div>
</template>

<script>
export default {
  props: [
    'bgcolor', 'color', 'message'
  ],
  data: () => ({
  }),
  mounted () {
  },
  methods: {
  }
}
</script>
