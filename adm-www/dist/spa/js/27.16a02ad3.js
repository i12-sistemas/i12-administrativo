(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([[27],{ea9d:function(t,a,e){"use strict";e.r(a);var r=function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",[e("q-page",{},[e("div",{staticClass:"row doc-page",class:t.$q.platform.is.mobile?"q-pa-0":"q-pa-md"},[t.error?e("div",{staticClass:"col-12"},[e("q-card",{staticClass:"bg-red text-white q-ma-md",attrs:{bordered:!t.$q.platform.is.mobile,flat:""}},[e("q-card-section",{staticClass:"full-width text-center justify-center items-center q-ma-lg"},[e("div",[e("q-icon",{attrs:{name:"error",color:"white",size:"4rem"}})],1)]),e("q-card-section",{staticClass:"text-body text-center"},[e("div",[t._v("\r\n              "+t._s(t.error)+"\r\n            ")])]),e("q-separator",{attrs:{dark:""}}),e("q-card-actions",[e("q-btn",{staticClass:"full-width",attrs:{flat:"",icon:"arrow_back",label:"Voltar"},on:{click:function(a){return t.$router.go(-1)}}})],1)],1)],1):t._e(),t.error?t._e():e("div",{staticClass:"col-sm-12 full-width"},[t.data.nota?t._e():e("q-card",{staticClass:"bg-white",attrs:{bordered:!t.$q.platform.is.mobile,square:t.$q.platform.is.mobile,flat:""}},[t.consultando?e("q-card-section",{staticClass:"full-width text-center justify-center items-center q-ma-lg"},[e("div",{staticStyle:{"max-width":"15rem",margin:"0 auto"}},[e("lottie-animation",{attrs:{animationData:t.lottiessearchdoc,speed:1,loop:!0,autoPlay:""}})],1)]):t._e()],1),t.data.nota&&!t.loading?e("q-card",{staticClass:"bg-white",attrs:{flat:"",bordered:!t.$q.platform.is.mobile,square:t.$q.platform.is.mobile}},[e("q-card-section",[e("div",{staticClass:"row text-h6 q-col-gutter-sm"},[e("div",{staticClass:"col-7"},[e("q-field",{attrs:{label:"Número","stack-label":"",outlined:""},scopedSlots:t._u([{key:"control",fn:function(){return[e("div",{staticClass:"self-center full-width no-outline text-h6",attrs:{tabindex:"0"}},[t._v(t._s(t.$helpers.formatRS(t.data.nota.numero,"",0)))])]},proxy:!0}],null,!1,3820029096)})],1),e("div",{staticClass:"col-5"},[e("q-field",{attrs:{label:"Série","stack-label":"",outlined:""},scopedSlots:t._u([{key:"control",fn:function(){return[e("div",{staticClass:"self-center full-width no-outline text-h6",attrs:{tabindex:"0"}},[t._v(t._s(t.$helpers.padLeftZero(t.data.nota.serie,3)))])]},proxy:!0}],null,!1,1882682991)})],1),e("div",{staticClass:"col-12"},[e("q-field",{attrs:{label:"Chave de acesso","stack-label":"",outlined:""},scopedSlots:t._u([{key:"control",fn:function(){return[e("div",{staticClass:"self-center full-width no-outline text-caption",attrs:{tabindex:"0"}},[t._v(t._s(t.data.nota.chave))])]},proxy:!0}],null,!1,13131523)})],1),e("div",{staticClass:"col-12"},[e("q-field",{attrs:{label:"Remetente","stack-label":"",outlined:""},scopedSlots:t._u([{key:"control",fn:function(){return[e("div",{staticClass:"self-center full-width no-outline ",attrs:{tabindex:"0"}},[e("div",{staticClass:"text-weight-bold"},[t._v(t._s(t.data.nota.remetentenome))]),e("div",{staticClass:"text-caption"},[t._v(t._s(t.$helpers.mascaraCpfCnpj(t.data.nota.remetentecnpj)))]),e("div",[t._v(t._s(t.data.nota.remetentecidadeeuf))])])]},proxy:!0}],null,!1,3053445771)})],1),e("div",{staticClass:"col-12"},[e("q-field",{attrs:{label:"Destinatário","stack-label":"",outlined:""},scopedSlots:t._u([{key:"control",fn:function(){return[e("div",{staticClass:"self-center full-width no-outline ",attrs:{tabindex:"0"}},[e("div",{staticClass:"text-weight-bold"},[t._v(t._s(t.data.nota.destinatarionome))]),e("div",{staticClass:"text-caption"},[t._v(t._s(t.$helpers.mascaraCpfCnpj(t.data.nota.destinatariocnpj)))]),e("div",[t._v(t._s(t.data.nota.destinatariocidadeeuf))])])]},proxy:!0}],null,!1,2097813531)})],1)])]),t.data?e("q-card-section",{staticClass:"q-pl-lg"},[e("q-timeline",{attrs:{color:"white",layout:"dense",cl:""}},t._l(t.data,(function(a,r){return e("q-timeline-entry",{directives:[{name:"show",rawName:"v-show",value:"nota"!==r,expression:"k !== 'nota'"}],key:r,attrs:{title:a.title,color:t.statusToConfig(a.status).color,icon:t.statusToConfig(a.status).icon},scopedSlots:t._u([{key:"subtitle",fn:function(){return[a.dh?e("div",{staticClass:"text-black"},[e("div",[t._v(t._s(t.$helpers.dateToBR(a.dh)))]),e("div",{staticClass:"text-caption"},[t._v(t._s(t.$helpers.datetimeRelativeToday(a.dh)))]),e("q-tooltip",{attrs:{delay:500}},[t._v("\r\n                      "+t._s(t.$helpers.datetimeToBR(a.dh))+"\r\n                    ")])],1):t._e()]},proxy:!0}],null,!0)},[e("div",[a.eventos&&a.eventos.length>0?e("div",t._l(a.eventos,(function(r,s){return e("div",{key:"proc"+s},[e("div",{staticClass:"row"},[e("div",{staticClass:"col-xs-12 col-md-8 text-subtitle2"},[e("q-icon",{attrs:{name:"fiber_manual_record",size:"10px",color:a.color}}),t._v(t._s(r.title))],1),r.dh?e("div",{staticClass:"q-pl-md col-xs-12 col-md-4 text-caption",class:t.$q.platform.is.mobile?"":"text-right"},[e("div",[t._v(t._s(t.$helpers.datetimeToBR(r.dh)))]),e("q-tooltip",{attrs:{delay:500}},[t._v("\r\n                            "+t._s(t.$helpers.datetimeRelativeToday(r.dh))+"\r\n                          ")])],1):t._e(),r.text&&""!==r.text?e("div",{staticClass:"q-pl-md",domProps:{innerHTML:t._s(r.text)}},[t._v(t._s(r.text))]):t._e()]),s<a.eventos.length-1?e("q-separator",{attrs:{spaced:""}}):t._e()],1)})),0):t._e()])])})),1)],1):t._e(),e("q-card-section",[e("div",{staticClass:"full-width text-right text-caption"},[t._v("\r\n                Consulta realizada em "+t._s(t.$helpers.datetimeToBR(t.datadaconsulta))+"\r\n              ")])])],1):t._e()],1),e("q-page-sticky",{attrs:{position:"top",expand:""}},[e("q-toolbar",{staticClass:"bg-white text-primary shadow-up-21"},[e("q-btn",{attrs:{flat:"",dense:"",round:"","aria-label":"Menu",icon:"menu"},on:{click:function(a){return t.$store.dispatch("homedashboard/togglemenu")}}}),e("q-btn",{attrs:{flat:"",round:"",icon:"arrow_back"},on:{click:function(a){return t.$router.go(-1)}}}),t.$q.platform.is.mobile?t._e():e("q-separator",{attrs:{vertical:"",inset:"",spaced:""}}),e("q-toolbar-title",[t._v("Rastreamento de carga")]),e("q-btn",{attrs:{flat:"",rounded:"",icon:"clear",label:t.$q.platform.is.mobile?"":"Limpar"},on:{click:t.actClear}})],1)],1)],1)])],1)},s=[],o=e("a34a"),n=e.n(o),i=(e("96cf"),e("c973")),l=e.n(i),c=e("523b"),d=e("1421"),u=e("38fe"),p=e("c1df"),m=e.n(p),f={name:"usuario.meuperfil",components:{},data:function(){var t=e("492b"),a=new d["a"],r=new c["a"],s=new u["a"];return{lottiessearchdoc:t,usuario:a,datadaconsulta:null,data:{nota:null,coletagem:{eventos:null,title:"Coletagem",tooltip:"Carga  em trânsito para o centro de distribuição",status:"0"},preparo:{eventos:null,title:"Preparo / Logística",tooltip:"Carga  em trânsito para o centro de distribuição",status:"0"},ementrega:{title:"Em entrega",tooltip:"Carga em rota de entrega",eventos:null,status:"0"},finalizado:{id:"entregaencerrada",title:"Encerrado",eventos:null,tooltip:"Carga  em trânsito para o centro de distribuição",status:"0"}},consultachave:"",consultanfe:s,error:null,dataset:r,loading:!1,consultando:!1}},activated:function(){var t=this;return l()(n.a.mark((function a(){return n.a.wrap((function(a){while(1)switch(a.prev=a.next){case 0:return a.next=2,t.init();case 2:t.actConsultar();case 3:case"end":return a.stop()}}),a)})))()},deactivated:function(){},mounted:function(){return l()(n.a.mark((function t(){return n.a.wrap((function(t){while(1)switch(t.prev=t.next){case 0:case"end":return t.stop()}}),t)})))()},computed:{usuariologado:function(){var t=this,a=null;return t.$store.state.usuario.logado&&t.$store.state.usuario.user&&(a=t.$store.state.usuario.user),a}},methods:{statusToConfig:function(t){var a={icon:"radio_button_unchecked",color:"grey-5",textcolor:"white"};switch(t){case"1":a.icon="task_alt",a.color="positive";break;case"2":a.icon="swipe_right_alt",a.color="orange";break;case"3":a.icon="error",a.color="red";break}return a},init:function(){var t=this;return l()(n.a.mark((function a(){var e;return n.a.wrap((function(a){while(1)switch(a.prev=a.next){case 0:if(e=t,a.prev=1,e.loading=!0,e.error=null,e.usuariologado){a.next=6;break}throw new Error("Nenhum usuário logado");case 6:if(e.usuariologado.permitecoletasver){a.next=8;break}throw new Error("Sem permissão de acesso");case 8:return e.consultachave=null,e.$route.params.chave&&""!==e.$route.params.chave&&(e.consultachave=e.$route.params.chave),e.usuario.limpardados(),e.dataset.limpardados(),a.next=14,e.refreshData();case 14:a.next=20;break;case 16:a.prev=16,a.t0=a["catch"](1),e.loading=!1,e.error=a.t0.message;case 20:e.loading=!1;case 21:case"end":return a.stop()}}),a,null,[[1,16]])})))()},refreshData:function(){var t=this;return l()(n.a.mark((function a(){var e,r,s,o,i;return n.a.wrap((function(a){while(1)switch(a.prev=a.next){case 0:return e=t,a.next=3,e.usuario.meuUsuario();case 3:if(r=a.sent,r.ok){a.next=11;break}return e.loading=!1,s=e.$helpers.showDialog(r),a.next=9,s.then((function(){}));case 9:a.next=23;break;case 11:if(!(!e.empresaselecionada&&e.usuario.clientes&&e.usuario.clientes.length>0)){a.next=22;break}if(!e.cnpjstart||""===e.cnpjstart){a.next=22;break}o=0;case 14:if(!(o<e.usuario.clientes.length)){a.next=22;break}if(i=e.usuario.clientes[o],i.cnpj!==e.cnpjstart){a.next=19;break}return e.empresaselecionada=i,a.abrupt("break",22);case 19:o++,a.next=14;break;case 22:!e.empresaselecionada&&e.usuario.clientes&&1===e.usuario.clientes.length&&(e.empresaselecionada=e.usuario.clientes[0]);case 23:case"end":return a.stop()}}),a)})))()},actClear:function(){var t=this;return l()(n.a.mark((function a(){var e;return n.a.wrap((function(a){while(1)switch(a.prev=a.next){case 0:e=t,e.data.nota=null,e.consultanumero=null,e.consultachave=null;case 4:case"end":return a.stop()}}),a)})))()},actConsultar:function(){var t=this;return l()(n.a.mark((function a(){var e,r;return n.a.wrap((function(a){while(1)switch(a.prev=a.next){case 0:if(e=t,e.data.nota=null,e.consultando=!0,a.prev=3,e.consultachave&&""!==e.consultachave){a.next=6;break}throw new Error("Chave vazia");case 6:return e.consultanfe.setChave(e.consultachave),a.next=9,e.consultanfe.isValid();case 9:if(r=a.sent,r.ok){a.next=12;break}throw new Error(r.msg);case 12:return a.next=14,e.dataset.findrastreio(e.consultachave);case 14:r=a.sent,r.ok?(r.data.coletagem&&(e.data.coletagem.dh=r.data.coletagem.dh,e.data.coletagem.status=r.data.coletagem.status,e.data.coletagem.eventos=r.data.coletagem.eventos),r.data.preparo&&(e.data.preparo.dh=r.data.preparo.dh,e.data.preparo.status=r.data.preparo.status,e.data.preparo.eventos=r.data.preparo.eventos),r.data.ementrega&&(e.data.ementrega.dh=r.data.ementrega.dh,e.data.ementrega.status=r.data.ementrega.status,e.data.ementrega.eventos=r.data.ementrega.eventos),r.data.finalizado&&(e.data.finalizado.dh=r.data.finalizado.dh,e.data.finalizado.status=r.data.finalizado.status,e.data.finalizado.eventos=r.data.finalizado.eventos),e.datadaconsulta=m()(),r.data.nota&&(e.data.nota=r.data.nota)):e.error=r.msg,a.next=21;break;case 18:a.prev=18,a.t0=a["catch"](3),e.error=a.t0.message;case 21:return a.prev=21,e.consultando=!1,a.finish(21);case 24:case"end":return a.stop()}}),a,null,[[3,18,21,24]])})))()}}},v=f,h=e("2877"),b=e("9989"),g=e("f09f"),x=e("a370"),_=e("0016"),w=e("eb85"),C=e("4b7e"),k=e("9c40"),q=e("8572"),y=e("05eb"),$=e("74af"),T=e("05c0"),Q=e("de5e"),S=e("65c6"),z=e("6ac5"),j=e("eebe"),R=e.n(j),E=Object(h["a"])(v,r,s,!1,null,null,null);a["default"]=E.exports;R()(E,"components",{QPage:b["a"],QCard:g["a"],QCardSection:x["a"],QIcon:_["a"],QSeparator:w["a"],QCardActions:C["a"],QBtn:k["a"],QField:q["a"],QTimeline:y["a"],QTimelineEntry:$["a"],QTooltip:T["a"],QPageSticky:Q["a"],QToolbar:S["a"],QToolbarTitle:z["a"]})}}]);