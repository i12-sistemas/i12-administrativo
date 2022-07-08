<template>
  <q-layout view="hHh lpR lFf" class="bg-grey-1" >
    <q-page-container class="">
      <transition name="fade">
        <!-- <keep-alive :include="['produtos', 'grupo.favoritos', 'comanda.pedido.add']"> -->
          <router-view />
        <!-- </keep-alive> -->
      </transition>
    </q-page-container>
  </q-layout>
</template>

<script>

export default {
  name: 'MyLayout-NoToolbar',
  components: {
  },
  data () {
    return {
      search: ''
    }
  },
  async mounted () {
  },
  methods: {
  }
}
</script>

<style>

.meuusuariodesktop {
  min-width: 450px
}
.gradientToolbar {
  background-color: #0271d0; /* For browsers that do not support gradients */
  background-image: linear-gradient(to right, #f05b41 , #f05b41); /* Standard syntax (must be last) */
}
.doc-page {
  padding: 16px 46px;
  font-weight: 300;
  max-width: 900px;
  margin-left: auto;
  margin-right: auto
}
.dialogfullheightdesktop {
  width: 700px;
  max-width: 80vw;
}
.titletoobarcustom {
  min-width: 900px;
  padding-left: 46px;
  margin-left: auto;
  margin-right: auto
}
.doc-page>div,.doc-page>pre {
  margin-bottom: 22px
 }
@media (max-width: 600px) {
  .doc-page {
    padding: 0px
  }
  .titletoobarcustom {
    min-width: auto;
    padding-left: 16px;
  }
}
</style>
