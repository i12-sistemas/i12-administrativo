<template>
<div>
<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
        <div class="box-header">
            <div class="box-title" v-if="protocolo">
                    Nº protocolo: <b>{{protocolo.protocolo}}</b>
                    <span class="label label-success margin-left h3" v-if="protocolo.status=='r'">Encerrado</span>
            </div>
            <div class="box-title" v-if="loading">
                <div class="alert text-center text-primary h3"><i class="fa fa-refresh fa-spin"></i> <b> carregando...</b></div>
            </div>
        </div>            
        <!-- SEM PROTOCOLO  -->
        <div class="box-body"  v-if="(!protocolo) && (erros.length>0)">
            <div class="row">
                <div class="col-xs-12  col-md-12">     
                    <div class="text-danger">
                        <p v-for="(erro, key) in erros" :key="key">{{erro}}</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- SEM PROTOCOLO -->
        <div class="box-body" v-if="protocolo">
            <div class="row">
                <div class="col-xs-12  col-md-12">
                    <div class="col-xs-6 col-md-3">Abertura: <b>{{$helpers.datetimeToBR(protocolo.dh, false, true)}}</b></div>
                    <div class="col-xs-6 col-md-3">Última mensagem: <b>{{$helpers.datetimeToBR(protocolo.dhultupdate, false, true)}}</b></div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <div class="col-xs-12 col-md-3">Assunto</div>
                    <div class="col-xs-12 col-md-9"><b>{{protocolo.assunto}}</b></div>
                </div>
            </div>     
            <hr>       
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <div class="col-xs-12 col-md-3">Mensagem</div>
                    <div class="col-xs-12 col-md-9"><b>{{protocolo.mensagenspublicas[0].msg}}</b></div>
                </div>
            </div>
            <!-- lista de msg -->
            <hr v-if="(protocolo.mensagenspublicas ? protocolo.mensagenspublicas.length>1 : false)">
            <div class="row" v-if="(protocolo.mensagenspublicas ? protocolo.mensagenspublicas.length>1 : false)">
                <div class="direct-chat-messages" style="height: auto !important;">  
                <div class="col-xs-12" v-for="(msg, key) in protocolo.mensagenspublicas" :key="key" v-if="key>0">
                    
                    <!-- msg dir usuario  -->
                    <div class="direct-chat-msg right" v-if="msg.origem=='user'">
                        <div class="direct-chat-info clearfix">
                        <span class="direct-chat-name pull-right">Eu</span>
                        <span class="direct-chat-timestamp pull-left">{{ $helpers.datetimeToBR(msg.dhacao, true) }}</span>
                        </div>
                        <!-- /.direct-chat-info -->
                        <img class="direct-chat-img" :src="msg.fotourl" alt="Minha foto">
                        <div class="direct-chat-text">
                            <p v-html="$helpers.nl2br(msg.msg)"></p>
                            <h4 v-if="msg.status=='r'" ><span class="label label-success"><i class="fa fa-check-circle" aria-hidden="true"></i> Assunto resolvido até {{$helpers.datetimeToBR(msg.dhacao, true)}}</span></h4>
                            <h4 v-if="msg.status=='p'" ><span class="label label-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Assunto como pendente - {{$helpers.datetimeToBR(msg.dhacao, true)}}</span></h4>
                        </div>
                    </div>
                    <!-- msg dir usuario  -->           
                    <!-- msg esquerda admin -->
                    <div class="direct-chat-msg"  v-if="msg.origem=='admin'">
                        <div class="direct-chat-info clearfix">
                            <span class="direct-chat-name pull-left"> {{ msg.usuarioadmin.nome }}</span>
                            <span class="direct-chat-timestamp pull-right">{{$helpers.datetimeToBR(msg.dhacao, true)}}</span>
                        </div>
                        <i class="direct-chat-img fa fa-user fa-3x text-gray-dark" aria-hidden="true"></i>
                        <div class="direct-chat-text">
                            <p v-html="$helpers.nl2br(msg.msg)"></p>
                            <h4 v-if="msg.status=='r'" ><span class="label label-success"><i class="fa fa-check-circle" aria-hidden="true"></i> Assunto resolvido até {{$helpers.datetimeToBR(msg.dhacao, true)}}</span></h4>
                            <h4 v-if="msg.status=='p'" ><span class="label label-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Assunto como pendente - {{$helpers.datetimeToBR(msg.dhacao, true)}}</span></h4>
                        </div>
                    </div>
                    <!-- msg esquerda  -->

                </div>
                </div>
            </div>
            <!-- lista de msg -->
            <!-- nenhum msg -->
            <hr v-if="(protocolo.mensagenspublicas ? protocolo.mensagenspublicas.length<=1 : true)">
            <div class="row" v-if="(protocolo.mensagenspublicas ? protocolo.mensagenspublicas.length<=1 : true)">
                <div class="col-xs-12 text-center text-blue">
                    Nenhuma mensagem ainda!
                </div>
            </div>            
            <!-- nenhum msg -->
            <hr>  
            <!-- novo mensagem -->
            <div class="row" v-if="protocolo.status=='a'">
                <div class="col-xs-12 col-md-12">
                    <label for="txtmsg" class="col-xs-12 col-form-label">Mensagem</label>
                    <div class="col-xs-12 margin-bottom"><textarea class="form-control" id="txtmsg" rows="3" placeholder="Digite aqui sua mensagem" v-model="txtmsg" :disabled="posting"></textarea></div>

                    <div class="col-xs-12 margin-bottom text-danger"  v-if="(erros.length>0)">
                        <p class="text-bold">Mensagem não foi adicionada!</p>
                        <p v-for="(erro, key) in erros" :key="key">{{erro}}</p>
                    </div>
                
                    <div class="col-xs-12">
                        <button type="button" class="btn btn-primary" @click="sendMsg" :disabled="posting">
                            <i class="fa fa-spinner fa-spin" aria-hidden="true"  v-if="posting"></i> {{posting ? 'Enviando...' : 'Enviar'}}
                        </button>
                    </div>
                </div>
            </div>            
            <!-- novo mensagem -->
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
        props: ['numprotocolo'],
        data: function () {
            return {
                protocolo: null,
                novoprotocolo: false,
                txtmsg: '',
                TeveAlteracao: false,
                posting: false,
                erros:[],
                loading:true,
            }
        },
        mounted() {
            
            this.getData();
        },
        methods: {
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

                var content = { protocolo: app.numprotocolo,
                                idassociado: this.$root.userlogged.id,
                              };

                app.axios.post('/api/v1/usuario/protocolos/show', content)
                    .then(function (resp) {
                        let dados = resp.data;
                        if(!dados.id){
                            console.log(dados.msg);
                            app.erros.push(dados.msg);
                            app.loading = false;
                        }else{
                            app.protocolo = dados;
                            app.loading = false;
                        }
                    })
                    .catch(function (resp) {
                        console.log('Falha ao carregar dados');
                        console.log(resp);
                        alert('Falha ao consultar dados!.\n' + resp);
                        app.loading = false;
                    });
            },  
            sendMsg(){
                var app = this;
                app.erros = [];
                app.posting = true;

                // if(!app.permite('relatorio-desk-statusreport-cli')){
                //     app.share.hash = null;
                //     app.share.id = null;
                //     app.flash('Sem permissão de acesso', 'error', {timeout: 5000});
                //     return;
                // }
                var content = { idprotocolo: app.protocolo.id,
                                origem: 'user',
                                mensagem: app.txtmsg,
                                idassociado: this.$root.userlogged.id,
                            };

                app.axios.post('/api/v1/usuario/protocolos/msg/novo', content)
                    .then(function (resp) {
                        let ret = resp.data;
                        if(ret.ok){
                            app.TeveAlteracao = true;
                            app.txtmsg = '';
                            app.posting = false;
                            app.getData();
                            
                        }else{
                            console.log(ret.msg);
                            app.erros.push(ret.msg);
                            app.posting = false;
                        }
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        app.erros.push(resp);
                        app.posting = false;
                    });
            }                      

        }
    }
</script>