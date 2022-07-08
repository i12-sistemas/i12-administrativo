<template>
<div>
<div class="row">
    <div class="col-md-12">
        <!-- box -->
        <div class="box box-primary">
            <div class="box-header"><h3 class="box-title">Novo protocolo</h3></div>
            <div class="box-body">

                <!-- Default form contact -->
                <div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Assunto</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="txtassunto" placeholder="Assunto" v-model="txtassunto">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputPassword3" class="col-sm-2 col-form-label">Mensagem</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="txtmsg" rows="3" placeholder="Digite aqui sua mensagem" v-model="txtmsg"></textarea>
                        </div>
                    </div>

                    <div class="row" v-if="erros.length > 0">
                        <div class="col-sm-12">
                            <div class="alert alert-danger">
                                <ul>
                                    <li v-for="(erro, key) in erros" :key="key">{{erro}}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    

                    <div class="row">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-primary" @click="sendProtocolo" :disabled="posting">
                                <i class="fa fa-spinner fa-spin" aria-hidden="true"  v-if="posting"></i> {{posting ? 'Enviando...' : 'Abrir protocolo'}}
                            </button>
                            <button type="button" class="btn btn-default" @click="onClose"><i class="fa fa-close"></i> Fechar</button>
                        </div>
                    </div>
                </div>
                <!-- Default form contact -->
            </div>
        </div>
        <!-- box -->
    </div>            
</div>    
</div> 
</template>
<script>

    export default {
        data: function () {
            return {
                protocolos: [],
                TeveAlteracao: false, 
                txtassunto: '',
                txtmsg: '', 
                erros: [],
                posting: false,

            }
        },
        props: [''],
        mounted() {
            // this.getData();
        },
        methods: {
            onClose: function(){
                this.$emit('closenewprotocolo', this.TeveAlteracao);
            },
            sendProtocolo(){
                var app = this;
                app.erros = [];
                app.posting = true;

                // if(!app.permite('relatorio-desk-statusreport-cli')){
                //     app.share.hash = null;
                //     app.share.id = null;
                //     app.flash('Sem permissão de acesso', 'error', {timeout: 5000});
                //     return;
                // }
                var content = { idassociado: this.$root.userlogged.id,
                                origem: 'user',
                                mensagem: app.txtassunto,
                                assunto: app.txtmsg
                    };

                app.axios.post('/api/v1/usuario/protocolos/novo', content)
                    .then(function (resp) {
                        let ret = resp.data;
                        if(ret.ok){
                            app.TeveAlteracao = true;
                            app.posting = false;
                            app.onClose();
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