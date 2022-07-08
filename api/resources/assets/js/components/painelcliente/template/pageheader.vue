<template>
<div>
<section class="content-header">
  <h1>
    <button type="button" class="btn btn-link btn-sm link-black" v-if="activegoback" @click="goback"><i class="fa fa-arrow-left"></i></button> 
    {{title}}
    <small v-if="subtitle!==''">{{subtitle}}</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="/painelcliente">home</a></li>
    <li v-for="(item, key) in breadcrumb" :key="key"><a :href="item.url" v-html="item.desc"></a></li>
    <li class="active" v-if="breadcrumbactive!==''"><span v-html="breadcrumbactive"></span></li>
  </ol>
</section>
</div>
</template>

<script>
export default {
  props: [
    "title",
    "subtitle",
    "breadcrumb",
    "breadcrumbactive",
    "activegoback"
  ],
  data: () => ({
    ano: 2018
  }),
  mounted() {
    if (!this.activegoback) this.activegoback = false;
    if (!this.breadcrumbactive) this.breadcrumbactive = "";
  },
  methods: {
    goback() {
      this.$router.go(-1);
      console.log("goback2");
      this.$emit("goback");
    }
  },
  name: "app-painelcliente-pageheader"
};
</script>