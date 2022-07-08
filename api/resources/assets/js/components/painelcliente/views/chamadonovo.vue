<template>
<div>
<app-painelcliente-pageheader :title="title" :breadcrumb="breadcrumb" :breadcrumbactive="title" :activegoback="true"></app-painelcliente-pageheader>
<section class="content">
  <div class="box no-border no-shadow">
    <div class="box-body">
        <chamadoregister/>
    </div>
  </div>
</section> 

  
</div>
</template>

<script>
import chamadoregister from "../components/chamadoregister.vue";
import pageheader from "../template/pageheader.vue";

export default {
  components: {
    "app-painelcliente-pageheader": pageheader,
    chamadoregister,

  },
  data: () => ({

        title: "Registro de novo chamado",
    breadcrumb: [],
    breadcrumbactive: "",
    "app-painelcliente-pageheader": pageheader
  }),
  mounted() {
    var app = this;
  }
};
</script>