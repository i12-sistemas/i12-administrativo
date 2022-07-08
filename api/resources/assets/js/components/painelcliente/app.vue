<template>
<div>
<div class="wrapper">
  <header class="main-header" >
      <app-painelcliente-navbar v-if="$root.logincliente.ok"></app-painelcliente-navbar>
  </header>
  <div class="content-wrapper">
    <div class="container">
      <router-view></router-view>
    </div>
  </div>
  <app-painelcliente-footer ></app-painelcliente-footer>
</div>        
</div>
</template>
<script>
import footer from "./template/footer.vue";
import navbar from "./template/navbar.vue";

export default {
  components: {
    "app-painelcliente-footer": footer,
    "app-painelcliente-navbar": navbar
  },
  data: function() {
    return {
      ipubActive: false
    };
  },
  mounted() {},
  watch: {
    // $route(to, from) {
    //   console.log(to.name);
    //   this.ipubActive = false;
    //   if (to.name == "admin.vendas.ipub") {
    //     this.ipubActive = true;
    //   }
    // }
  }
};
</script>