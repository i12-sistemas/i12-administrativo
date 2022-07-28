<template>
<div>
  <q-slide-transition>
  <div class="full-width text-body text-center q-pa-sm rounded-borders"
      :class="((bgcolor ? bgcolor !== '' : false) ? 'bg-' + bgcolor : '')"
      >
      <q-spinner-oval :color="((color ? color !== '' : false) ? color : 'primary')" size="3em"/>
      <div class="full-width q-my-sm" v-if="message ? message !== '' : false"
        :class="((color ? color !== '' : false) ? 'text-' + color : 'text-primary')"
        >
        {{message}}
      </div>
    </div>
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
