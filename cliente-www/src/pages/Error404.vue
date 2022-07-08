<template>
<div>
  <div class="justify-center full-height full-width text-center q-pa-lg">
    <img src="~assets/Logo-i12-horizontal150x50.png" style="max-height: 30px;" @click="$router.push({ name: 'home' })" >
  </div>
  <div class="q-pt-lg text-center">
    <p>
      <q-avatar size="100px" font-size="52px" color="primary" text-color="white" icon="fa fa-dizzy" />
    </p>
    <p class="text-faded">Desculpe, está página não existe...<strong>(404)</strong></p>
    <q-btn color="grey-9" style="width:200px;" to="/" label="Voltar para home" unelevated />
  </div>
</div>
</template>

<script>
export default {
  name: 'Error404'
}
</script>
