<template>
<div>
<footer class="main-footer no-padding">
    <div class="container">
      <div class="row">
        <div class="col-sm-6">
          <a href="/painelcliente/trocarempresa" class="btn btn-link" v-if="$root.logincliente.ok" >{{$root.logincliente.cliente.nome}}</a>

        </div>

        <div class="text-right col-sm-6">
          <strong>iCom</strong>
          <strong>&copy; 2008-{{ano}} <a href="http://www.idoze.com.br" target="_self" >i12 Sistemas</a></strong> |

          
          <strong><a href="http://www.idoze.com.br/suporte.php" target="_self" class="btn btn-link text-white"><i class="fa fa-support"></i> </a></strong>
        </div>        

        
      </div>
    </div>
</footer>
</div>
</template>

<script>
import moment from "moment";
import VueMomentJS from "vue-momentjs";
export default {
  components: {
    moment,
    VueMomentJS
  },
  data: () => ({
    ano: 2018
  }),
  mounted() {
    this.ano = moment().format("Y");
  },
  methods: {},
  name: "app-painelcliente-footer"
};
</script>