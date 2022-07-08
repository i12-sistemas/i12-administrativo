<template>
<div>
    
<section id="contact" class="section-padding">
    <div class="container">
      <div class="row">
        <div class="header-section text-center">
          <h2>Contato</h2>
          <p>Nos informe sobre dúvidas, sugestões de melhoria ou reclamações.</p>
          <p>Sua opinião é muito importante para melhoria constante dos nossos processos.</p>
          <hr class="bottom-line">
        </div>
        <div id="sendmessage">Sua mensagem foi registrada.<br>Faremos contato pelo e-mail ou telefone informados.<br>Obrigado!</div>
        <div id="errormessage"></div>
        <form v-on:submit.prevent="onSubmit" method="post" role="form" class="contactForm">
          <div class="col-md-6 col-sm-6 col-xs-12 left">
            <div class="form-group">
              <input v-model="nome" type="text"  :disabled="posting" name="name" class="form-control form" id="name" data-rule="minlen:3" placeholder="Seu nome"  data-msg="Por favor escreva seu nome" />
              <div class="validation"></div>
            </div>
            <div class="form-group">
              <input type="email" v-model="email"  :disabled="posting" class="form-control" name="email" id="email" placeholder="Seu e-mail"  data-msg="Por favor escreva seu e-mail"  />
              <div class="validation"></div>
            </div>
            <div class="form-group">
                <input type="phone" v-model="telefone"  :disabled="posting" class="form-control" name="phone" id="phone"  data-rule="minlen:4"  placeholder="Seu telefone"  data-msg="Por favor escreva seu telefone (mínimo 4 número)" />
                <div class="validation"></div>
              </div>
            <div class="form-group">
              <input type="text" class="form-control"  :disabled="posting" v-model="assunto" name="subject" id="subject" data-rule="minlen:3" placeholder="Assunto"  data-msg="Por favor escreva o assunto (mínimo 3 caracteres)" />
              <div class="validation"></div>
            </div>
          </div>

          <div class="col-md-6 col-sm-6 col-xs-12 right">
            <div class="form-group">
              <textarea class="form-control" name="message"  :disabled="posting" v-model="mensagem" rows="8" data-rule="required" minlen="5" data-msg="Por favor escreva algo pra nós!" placeholder="Mensagem"></textarea>
              <div class="validation"></div>
            </div>
          </div>

          <div class="col-xs-12">
            <!-- Button -->
            <button type="submit" id="submit" name="submit" :disabled="posting" class="form contact-form-button light-form-button oswald light"><i class="fa fa-spinner fa-spin" aria-hidden="true"  v-if="posting"></i> {{posting ? 'ENVIANDO...' : 'ENVIAR MENSAGEM'}}</button>
            <span style="font-size:smaller;">Informar corretamente os dados de contato, ele é o nosso canal de comunicação.</span>
          </div>
        </form>

      </div>
    </div>
  </section>
    
</div> 

</template>

<script>
    export default {
        data: function () {
            return {
                mensagem: '',
                nome: '',
                telefone: '',
                email: '',
                assunto: '',
                mensagem: '',
                posting: false,

            }
        },
        mounted() {
            console.log('tste');
        },
        methods: {
            onSubmit(){
                var app = this;
                // app.erros = [];
                app.posting = true;
                app.erros = '';
                app.mensagemenviada = false;


                var f =  jQuery('form.contactForm').find('.form-group'),
                ferror = false,
                emailExp = /^[^\s()<>@,;:\/]+@\w[\w\.-]+\.[a-z]{2,}$/i;

                f.children('input').each(function(){ // run all inputs

                    var i = jQuery(this); // current input
                    var rule = i.attr('data-rule');

                    if( rule !== undefined ){
                    var ierror=false; // error flag for current input
                    var pos = rule.indexOf( ':', 0 );
                    if( pos >= 0 ){
                        var exp = rule.substr( pos+1, rule.length );
                        rule = rule.substr(0, pos);
                    }else{
                        rule = rule.substr( pos+1, rule.length );
                    }
                    
                    switch( rule ){
                        case 'required':
                        if( i.val()==='' ){ ferror=ierror=true; }
                        break;
                        
                        case 'minlen':
                        if( i.val().length<parseInt(exp) ){ ferror=ierror=true; }
                        break;

                        case 'email':
                        if( !emailExp.test(i.val()) ){ ferror=ierror=true; }
                        break;

                        case 'checked':
                        if( !i.attr('checked') ){ ferror=ierror=true; }
                        break;
                        
                        case 'regexp':
                        exp = new RegExp(exp);
                        if( !exp.test(i.val()) ){ ferror=ierror=true; }
                        break;
                    }
                        i.next('.validation').html( ( ierror ? (i.attr('data-msg') !== undefined ? i.attr('data-msg') : 'wrong Input') : '' ) ).show('blind');
                    }
                });
                f.children('textarea').each(function(){ // run all inputs

                    var i = jQuery(this); // current input
                    var rule = i.attr('data-rule');

                    if( rule !== undefined ){
                    var ierror=false; // error flag for current input
                    var pos = rule.indexOf( ':', 0 );
                    if( pos >= 0 ){
                        var exp = rule.substr( pos+1, rule.length );
                        rule = rule.substr(0, pos);
                    }else{
                        rule = rule.substr( pos+1, rule.length );
                    }
                    
                    switch( rule ){
                        case 'required':
                        if( i.val()==='' ){ ferror=ierror=true; }
                        break;
                        
                        case 'minlen':
                        if( i.val().length<parseInt(exp) ){ ferror=ierror=true; }
                        break;
                    }
                        i.next('.validation').html( ( ierror ? (i.attr('data-msg') != undefined ? i.attr('data-msg') : 'wrong Input') : '' ) ).show('blind');
                    }
                });

                if( ferror ) {
                    app.posting = false;
                    return false; 
                }



                // else var str = $(this).serialize();		
                //     $.ajax({
                //         type: "POST",
                //         url: "contactform/contactform.php",
                //         data: str,
                //         success: function(msg){
                //         // alert(msg);
                //             if(msg == 'OK') {

                //             }
                //             else {

                //             }
                            
                //         }
                //     });
                // return false;
        
                        

                // if(!app.permite('relatorio-desk-statusreport-cli')){
                //     app.share.hash = null;
                //     app.share.id = null;
                //     app.flash('Sem permissão de acesso', 'error', {timeout: 5000});
                //     return;
                // }
                var content = { nome: app.nome,
                                telefone: app.telefone,
                                email: app.email,
                                assunto: app.assunto,
                                mensagem: app.mensagem
                            };

                app.axios.post('/api/v1/site/contato/add', content)
                    .then(function (resp) {
                        let ret = resp.data;
                        console.log(ret);
                        if(ret.ok){
                            app.posting = false;
                            jQuery("#sendmessage").addClass("show");			
                            jQuery("#errormessage").removeClass("show");	                            
                        }else{
                            console.log(ret.msg);
                            app.posting = false;
                            $("#sendmessage").removeClass("show");
                            $("#errormessage").addClass("show");
                            $('#errormessage').html(ret.msg);                            
                        }
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        $("#sendmessage").removeClass("show");
                        $("#errormessage").addClass("show");
                        $('#errormessage').html(resp);                            
                        app.posting = false;
                    });
            }                      

        }
    }
</script>