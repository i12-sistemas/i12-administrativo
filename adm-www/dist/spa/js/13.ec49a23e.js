(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([[13],{"15e3":function(e,t,a){"use strict";a.r(t);var r=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("q-card",{staticClass:"full-width",attrs:{flat:""}},[a("q-card-section",{staticClass:"text-center"},[a("span",{staticClass:"text-h6"},[a("q-icon",{attrs:{name:"vpn_key",color:"grey"}}),e._v(" Alteração de senha")],1)]),e.solicitado?a("q-card-section",[a("div",{staticClass:"row wrap justify-center q-pt-lg content-start"},[a("q-icon",{attrs:{name:"check_circle",color:"positive",size:"60px"}})],1),a("div",{staticClass:"row justify-center q-pa-sm content-start text-body"},[e._v("\r\n      Link enviado ao e-mail informado.\r\n    ")]),a("div",{staticClass:"row wrap justify-center content-start text-body"},[e._v("\r\n      Acesse sua caixa de e-mail e siga as instruções para concluir a alteração de senha!\r\n    ")]),a("div",{staticClass:"row wrap justify-center q-pt-md content-start text-body"},[e._v("\r\n      * Não esquece de conferir na sua caixa de Spam.\r\n    ")]),a("div",{staticClass:"row wrap justify-center content-start text-caption"},[e._v("\r\n      O link expira em 1 hora!\r\n    ")])]):e._e(),e.solicitado?e._e():a("q-card-section",[a("q-input",{ref:"txtuser",attrs:{outlined:"",color:"primary","bg-color":"white","label-color":"grey-10",loading:e.sending,disable:e.sending,label:"E-mail ou celular",maxlength:"255"},model:{value:e.username,callback:function(t){e.username=t},expression:"username"}})],1),a("q-card-actions",{staticClass:"row wrap justify-between q-px-md content-start",attrs:{align:"center"}},[a("q-btn",{staticClass:"full-width",attrs:{unelevated:"",color:"primary","text-color":"white",icon:"send",label:"Solicitar alteração",loading:e.sending,disable:e.solicitado},on:{click:e.actRequest}})],1),a("q-card-actions",{staticClass:"row wrap justify-between q-px-md content-start",attrs:{align:"center"}},[a("q-btn",{staticClass:"full-width",attrs:{unelevated:"",to:{name:"login.usuario"},label:"Voltar","no-caps":"",color:"grey-2","text-color":"black",icon:"arrow_back",disable:e.sending}})],1),a("q-card-section",[a("p",{staticClass:"text-caption text-center"},[e._v("\r\n      Seu IP esta sendo registrado!\r\n    ")])])],1)},n=[],s=a("a34a"),o=a.n(s),i=(a("96cf"),a("c973")),c=a.n(i),u=a("bc3a"),l=a.n(u),d={data:function(){return{username:"",solicitado:!1,sending:!1}},computed:{},mounted:function(){return c()(o.a.mark((function e(){return o.a.wrap((function(e){while(1)switch(e.prev=e.next){case 0:case"end":return e.stop()}}),e)})))()},methods:{redirectNow:function(){var e=this;return c()(o.a.mark((function t(){var a;return o.a.wrap((function(t){while(1)switch(t.prev=t.next){case 0:a=e,a.redirect?a.$router.push({path:a.redirect}):a.$router.push({name:"home"});case 2:case"end":return t.stop()}}),t)})))()},closeApp:function(){return c()(o.a.mark((function e(){return o.a.wrap((function(e){while(1)switch(e.prev=e.next){case 0:navigator.app.exitApp();case 1:case"end":return e.stop()}}),e)})))()},actRequest:function(e){var t=this;return c()(o.a.mark((function e(){var a,r;return o.a.wrap((function(e){while(1)switch(e.prev=e.next){case 0:if(a=t,e.prev=1,""!==a.username){e.next=5;break}return a.$refs.txtuser.$el.focus(),e.abrupt("return");case 5:e.next=14;break;case 7:if(e.prev=7,e.t0=e["catch"](1),!e.t0.message){e.next=13;break}a.actShowError("Validação",e.t0.message,4e3),e.next=14;break;case 13:return e.abrupt("return");case 14:return a.sending=!0,e.next=17,a.sendRequest();case 17:r=e.sent,r.ok?(""!==r.msg&&a.$q.notify({message:r.msg,color:"positive",actions:[{label:"OK",color:"white",handler:function(){}}]}),a.$router.push({name:"login.resetpwd.change",query:{username:a.username,tipouser:a.tipouser}})):a.actShowError("Acesso restrito",r.msg,4e3),a.sending=!1;case 20:case"end":return e.stop()}}),e,null,[[1,7]])})))()},actShowError:function(e,t,a){var r=this,n=r.$q.dialog({title:e,message:t}).onDismiss((function(){clearTimeout(s),r.email="",r.username="",r.$refs.txtuser.$el.focus()})),s=setTimeout((function(){n.hide()}),a)},sendRequest:function(){var e=this;return c()(o.a.mark((function t(){var a,r,n,s;return o.a.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(a=e,t.prev=1,""!==a.username){t.next=5;break}throw e.$refs.txtuser.$el.focus(),new Error("Usuário inválido");case 5:t.next=10;break;case 7:return t.prev=7,t.t0=t["catch"](1),t.abrupt("return",{ok:!1,msg:t.t0.message});case 10:return r="Basic "+btoa(a.username+":"+a.tipouser),n=l.a.create({baseURL:a.$configini.API_URL,withCredentials:!1,headers:{"x-auth-uuid":a.$store.state.usuario.uuid,Authorization:r,"Access-Control-Allow-Origin":"*","Access-Control-Allow-Headers":"Authorization","Access-Control-Allow-Methods":"GET, POST, OPTIONS, PUT, PATCH, DELETE","Content-Type":"application/json;charset=UTF-8"}}),t.next=14,n.post("v1/painelcliente/login/usuario/resetpwd/request").then((function(e){var t=e.data;return t})).catch((function(e){var t=e;return t=e.response?"Code: "+e.response.status+" - "+e.response.data.message:e.message,{ok:!1,msg:"Falha ao consultar servidor online: "+t}}));case 14:return s=t.sent,s.ok&&(a.solicitado=!0),t.abrupt("return",s);case 17:case"end":return t.stop()}}),t,null,[[1,7]])})))()}}},p=d,f=(a("816d"),a("2877")),m=a("f09f"),w=a("a370"),h=a("0016"),v=a("27f9"),b=a("4b7e"),g=a("9c40"),x=a("eebe"),q=a.n(x),k=Object(f["a"])(p,r,n,!1,null,"484dd61d",null);t["default"]=k.exports;q()(k,"components",{QCard:m["a"],QCardSection:w["a"],QIcon:h["a"],QInput:v["a"],QCardActions:b["a"],QBtn:g["a"]})},"6eb6":function(e,t,a){},"816d":function(e,t,a){"use strict";var r=a("6eb6"),n=a.n(r);n.a}}]);