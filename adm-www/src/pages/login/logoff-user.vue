<template>
<div></div>
</template>

<script>
export default {
  async mounted () {
    var app = this
    app.$store.dispatch('usuario/logout')
  }
}
</script>
