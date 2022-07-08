<template>
<div>
<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
        <div class="box-header">
            <div v-if="!novoprotocolo">
                <button type="button" class="btn btn-default" v-on:click="actNovoProtocolo" ><i class="fa fa-plus"></i> Novo</button>
                <button type="button" class="btn" v-bind:class="showEncerrados ? 'btn-info' : ' btn-default'" v-on:click="changeStatus" title="Mostrar encerrados"><i class="fa fa-circle-o" v-if="!showEncerrados"></i><i class="fa fa-check-circle" v-if="showEncerrados"></i> Encerrados</button>
                <button type="button" class="btn btn-default" v-on:click="getData"><i class="fa fa-refresh"></i></button>
            </div>

            <!-- novo -->
            <div class="row margin-top" v-if="novoprotocolo">
                <div class="col-md-12">
                    <protocolosNew v-on:closenewprotocolo="actCloseNovoProtocolo"></protocolosNew>
                </div>
            </div>    
            <!-- novo -->


        </div>            
        <div class="box-body" v-if="!novoprotocolo">

            

            <table class="table table-hover">
                <thead>
                <tr>
                    <th>
                        <div class="row">
                            <div class="col-sm-2 col-xs-5">Nº protocolo</div>
                            <div class="col-sm-5 col-xs-7">Assunto</div>
                            <div class="col-sm-1 col-xs-3">Msgs</div>
                            <div class="col-sm-4 col-xs-9 text-right">Datas</div>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>
                    <tr v-if="loading"><td>
                        <div class="alert text-center text-primary h3"><i class="fa fa-refresh fa-spin"></i> <b> carregando...</b></div>
                    </td></tr>
                    <tr v-for="(protocolo, key) in protocolos" :key="key">
                        <td>
                            <a :href="getEditLink(protocolo.protocolo)"  v-bind:class="(protocolo.dhultupdate>protocolo.dhultleituraassociado && protocolo.status=='a') ? 'text-red' : 'text-black'"  >
                            <div class="row">
                                <div class="col-sm-2 col-xs-5"><i class="fa fa-check-circle text-info fa-1x" v-if="protocolo.status=='r'"> </i>{{protocolo.protocolo}}</div>
                                <div class="col-sm-5 col-xs-7">{{protocolo.assunto}}</div>
                                <div class="col-sm-1 col-xs-3">{{protocolo.mensagenspublicas.length}}</div>
                                <div class="col-sm-4 col-xs-9 text-right">
                                    <div class="col-xs-12">Abertura: <span class="text-bold text-right">{{$helpers.datetimeToBR(protocolo.dh, false, true)}}</span></div>
                                    <div class="col-xs-12">Última msg: <span class="text-bold text-right">{{$helpers.datetimeToBR(protocolo.dhultupdate, false, true)}}</span></div>
                                    <div class="col-xs-12">Última leitura: <span class="text-bold text-right">{{$helpers.datetimeToBR(protocolo.dhultleituraassociado, false, true)}}</span></div>
                                </div>

                            </div>
                            </a>
                        </td>
                    </tr>
                </tbody>   
            </table>                
        </div>
        </div>
    </div>
</div>    

</div> 

</template>

<script>
    import ProtocolosNew from './ProtocolosNew.vue';
    export default {
        components: {
            protocolosNew: ProtocolosNew
        },
        data: function () {
            return {
                protocolos: [],
                novoprotocolo: false,
                showEncerrados: false,
                loading: true,
            }
        },
        props: [''],
        mounted() {
            this.getData();
        },
        methods: {
            changeStatus: function(){
                this.showEncerrados=!this.showEncerrados;
                this.getData();
            },
            getEditLink: function(numero){
                return this.$root.base_url + '/protocolos/show/' + numero;
            },
            actCloseNovoProtocolo: function(TeveAlteracao){
                this.novoprotocolo = false;
                if(TeveAlteracao){
                    this.getData();
                }
            },
            actNovoProtocolo: function() {
                this.novoprotocolo = true;
            },
            getData(){
                var app = this;
                app.loading = true;

                // if(!app.permite('relatorio-desk-statusreport-cli')){
                //     app.share.hash = null;
                //     app.share.id = null;
                //     app.flash('Sem permissão de acesso', 'error', {timeout: 5000});
                //     return;
                // }

                var content = { idassociado: this.$root.userlogged.id, 
                                status: app.showEncerrados ? 'r' : 'a'
                            };

                app.axios.post('/api/v1/usuario/protocolos/list', content)
                    .then(function (resp) {
                        app.protocolos = resp.data;
                        app.loading = false;
                    })
                    .catch(function (resp) {
                        console.log('Falha ao carregar dados');
                        console.log(resp);
                        alert('Falha ao consultar dados!.\n' + resp);
                        app.loading = false;
                    });
            },            

        }
    }
</script>