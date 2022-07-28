<template>
  <div>
    <lottie v-if="json" :options="{ animationData: json }"   />
  </div>
</template>
<script>
import Lottie from 'vue-lottie'
export default {
  name: 'lottie-internal',
  components: {
    lottie: Lottie
  },
  props: [
    'arquivo'
  ],
  data () {
    return {
      json: null
    }
  },
  async mounted () {
    this.json = require('src/assets/lotties/' + this.arquivo + '.json')
  }
  // watch: {
  //   arquivo: function (val) {
  //     console.log(val)
  //     this.json = require('src/assets/lotties/' + val + '.json')
  //     console.log(this.json)
  //   }
  // }
}
</script>
