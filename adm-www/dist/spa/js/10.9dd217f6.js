(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([[10],{"72f5":function(t,e,s){"use strict";var a=s("b971"),i=s.n(a);i.a},"88b8":function(t,e,s){"use strict";s.r(e);var a=function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",[s("q-page",{},[s("div",{staticClass:"full-width header-top-bg bg-primary shadow-up-21"}),s("div",{staticClass:"row doc-header"},[s("div",{staticClass:"col-sm-12",class:t.$q.platform.is.mobile?"":"q-pa-lg"},[s("div",{staticClass:"col-12"},[t.error?s("div",{staticClass:"col-12"},[s("q-banner",{staticClass:"bg-negative text-white"},[t._v(t._s(t.error))])],1):t._e(),s("div",{staticClass:"row full-width q-px-md q-gutter-y-md",class:t.$q.platform.is.mobile?"":"q-col-gutter-x-md"},[s("div",{staticClass:"col-12"},[s("q-card",{staticClass:"bg-grey-2 text-grey-10",attrs:{bordered:"",flat:""}},[s("q-card-section",[s("div",{staticClass:"row q-gutter-y-lg"},[s("div",{staticClass:"col-xs-6 col-md-3 text-center"},[s("div",[s("q-icon",{attrs:{name:"calculate",size:"3em"}})],1),s("div",{staticClass:"text-h4 text-weight-bold"},[t._v(t._s(t.$helpers.bytesToHumanFileSizeString(t.rows.total.size)))]),s("div",{staticClass:"text-h6"},[t._v("armazenados")])]),s("div",{staticClass:"col-xs-6 col-md-3 text-center"},[s("div",[s("q-icon",{attrs:{name:"text_snippet",size:"3em"}})],1),s("div",{staticClass:"text-h4 text-weight-bold"},[t._v(t._s(t.$helpers.formatRS(t.rows.total.qtde,"",0)))]),s("div",{staticClass:"text-h6"},[t._v("arquivos")])]),s("div",{staticClass:"col-xs-6 col-md-3 text-center"},[s("div",[s("q-icon",{attrs:{name:"people_alt",size:"3em"}})],1),s("div",{staticClass:"text-h4 text-weight-bold"},[t._v(t._s(t.$helpers.formatRS(t.rows.total.qtdeclientes,"",0)))]),s("div",{staticClass:"text-h6"},[t._v("clientes")])]),s("div",{staticClass:"col-xs-6 col-md-3 text-center"},[s("div",[s("q-icon",{attrs:{name:"functions",size:"3em"}})],1),s("div",{staticClass:"text-h4 text-weight-bold"},[t._v(t._s(t.$helpers.bytesToHumanFileSizeString(t.rows.total.mediacliente)))]),s("div",{staticClass:"text-h6"},[t._v("por cliente")])])])])],1)],1),s("div",{staticClass:"col-xs-12 col-md-4"},[s("q-card",{staticClass:"bg-blue text-white full-height",attrs:{flat:""}},[s("q-card-section",[s("div",{staticClass:"text-h6"},[t._v("Nível pago")]),s("div",{staticClass:"text-subtitle2"},[s("q-linear-progress",{staticClass:"q-mt-sm",attrs:{value:t.rows.nivelpago.mediageral/100,rounded:"",color:"white","track-color":"blue-5",size:"20px"}},[s("div",{staticClass:"absolute-full flex flex-center"},[s("q-badge",{attrs:{color:"blue-5","text-color":"white",label:t.rows.nivelpago.mediageral}})],1)])],1),s("div",{staticClass:"posicaoprogresso"},[t._v("\n                    "+t._s(t.$helpers.bytesToHumanFileSizeString(t.rows.nivelpago.total))+"\n                  ")])]),s("q-card-section",{staticClass:"q-pt-none"},[s("div",{staticClass:"row"},[s("div",{staticClass:"col-xs-6 text-center"},[s("div",[s("q-icon",{attrs:{name:"text_snippet",size:"2em"}})],1),s("div",{staticClass:"text-h6"},[t._v(t._s(t.$helpers.formatRS(t.rows.nivelpago.qtdearquivos,"",0)))]),s("div",{staticClass:"text-caption"},[t._v("arquivos")])]),s("div",{staticClass:"col-xs-6 text-center"},[s("div",[s("q-icon",{attrs:{name:"people_alt",size:"2em"}})],1),s("div",{staticClass:"text-h6"},[t._v(t._s(t.$helpers.formatRS(t.rows.nivelpago.qtdeclientes,"",0)))]),s("div",{staticClass:"text-caption"},[t._v("clientes")])])]),s("q-separator",{attrs:{spaced:"",dark:""}}),s("div",{staticClass:"row"},[s("div",{staticClass:"col-12 text-center"},[t._v(t._s(t.$helpers.bytesToHumanFileSizeString(t.rows.nivelpago.mediaarquivoscliente))+" por cliente")])])],1)],1)],1),s("div",{staticClass:"col-xs-12 col-md-4"},[s("q-card",{staticClass:"bg-light-blue-10 text-white full-height",attrs:{flat:""}},[s("q-card-section",[s("div",{staticClass:"text-h6"},[t._v("Gratuito")]),s("div",{staticClass:"text-subtitle2"},[s("q-linear-progress",{staticClass:"q-mt-sm",attrs:{value:t.rows.nivelgratuito.mediageral/100,rounded:"",color:"white","track-color":"light-blue-9",size:"20px"}},[s("div",{staticClass:"absolute-full flex flex-center"},[s("q-badge",{attrs:{color:"light-blue-9","text-color":"white",label:t.rows.nivelgratuito.mediageral}})],1)])],1),s("div",{staticClass:"posicaoprogresso"},[t._v("\n                    "+t._s(t.$helpers.bytesToHumanFileSizeString(t.rows.nivelgratuito.total))+"\n                  ")])]),s("q-card-section",{staticClass:"q-pt-none"},[s("div",{staticClass:"row"},[s("div",{staticClass:"col-xs-6 text-center"},[s("div",[s("q-icon",{attrs:{name:"text_snippet",size:"2em"}})],1),s("div",{staticClass:"text-h6"},[t._v(t._s(t.$helpers.formatRS(t.rows.nivelgratuito.qtdearquivos,"",0)))]),s("div",{staticClass:"text-caption"},[t._v("arquivos")])]),s("div",{staticClass:"col-xs-6 text-center"},[s("div",[s("q-icon",{attrs:{name:"people_alt",size:"2em"}})],1),s("div",{staticClass:"text-h6"},[t._v(t._s(t.$helpers.formatRS(t.rows.nivelgratuito.qtdeclientes,"",0)))]),s("div",{staticClass:"text-caption"},[t._v("clientes")])])]),s("q-separator",{attrs:{spaced:"",dark:""}}),s("div",{staticClass:"row"},[s("div",{staticClass:"col-12 text-center"},[t._v(t._s(t.$helpers.bytesToHumanFileSizeString(t.rows.nivelgratuito.mediaarquivoscliente))+" por cliente")])])],1)],1)],1),s("div",{staticClass:"col-xs-12 col-md-4 "},[s("q-card",{staticClass:"bg-red-8 text-white full-height",attrs:{flat:""}},[s("q-card-section",[s("div",{staticClass:"text-h6"},[t._v("Inativos")]),s("div",{staticClass:"text-subtitle2"},[s("q-linear-progress",{staticClass:"q-mt-sm",attrs:{value:t.rows.inativos.mediageral/100,rounded:"",color:"white","track-color":"red-5",size:"20px"}},[s("div",{staticClass:"absolute-full flex flex-center"},[s("q-badge",{attrs:{color:"red-5","text-color":"white",label:t.rows.inativos.mediageral}})],1)])],1),s("div",{staticClass:"posicaoprogresso"},[t._v("\n                    "+t._s(t.$helpers.bytesToHumanFileSizeString(t.rows.inativos.total))+"\n                  ")])]),s("q-card-section",{staticClass:"q-pt-none"},[s("div",{staticClass:"row"},[s("div",{staticClass:"col-xs-6 text-center"},[s("div",[s("q-icon",{attrs:{name:"text_snippet",size:"2em"}})],1),s("div",{staticClass:"text-h6"},[t._v(t._s(t.$helpers.formatRS(t.rows.inativos.qtdearquivos,"",0)))]),s("div",{staticClass:"text-caption"},[t._v("arquivos")])]),s("div",{staticClass:"col-xs-6 text-center"},[s("div",[s("q-icon",{attrs:{name:"people_alt",size:"2em"}})],1),s("div",{staticClass:"text-h6"},[t._v(t._s(t.$helpers.formatRS(t.rows.inativos.qtdeclientes,"",0)))]),s("div",{staticClass:"text-caption"},[t._v("clientes")])])]),s("q-separator",{attrs:{spaced:"",dark:""}}),s("div",{staticClass:"row"},[s("div",{staticClass:"col-12 text-center"},[t._v(t._s(t.$helpers.datetimeRelativeToday(t.rows.inativos.minlastmodified))+" "),s("q-icon",{staticClass:"q-mx-xs",attrs:{name:"sync_alt",size:"sm"}}),t._v(" "+t._s(t.$helpers.datetimeRelativeToday(t.rows.inativos.maxlastmodified)))],1),s("q-tooltip",{attrs:{delay:700}},[s("div",[t._v("Backup mais antigo: "+t._s(t.$helpers.datetimeToBR(t.rows.inativos.minlastmodified)))]),s("div",[t._v("Backup mais atual: "+t._s(t.$helpers.datetimeToBR(t.rows.inativos.maxlastmodified)))])])],1)],1)],1)],1)]),t.rows?s("q-card",{staticClass:"bg-white q-mt-md",attrs:{flat:"",bordered:!t.$q.platform.is.mobile,square:t.$q.platform.is.mobile}},[s("q-card-section",{staticClass:"q-pa-none"},[s("div",{staticStyle:{"max-width":"100vw"}})])],1):t._e(),s("q-page-sticky",{attrs:{position:"top",expand:""}},[s("q-toolbar",{staticClass:"bg-primary text-white"},[s("q-btn",{attrs:{flat:"",dense:"",round:"","aria-label":"Menu",icon:"menu"},on:{click:function(e){return t.$store.dispatch("homedashboard/togglemenu")}}}),s("q-btn",{attrs:{flat:"",round:"",icon:"arrow_back"},on:{click:function(e){return t.$router.go(-1)}}}),t.$q.platform.is.mobile?t._e():s("q-separator",{attrs:{vertical:"",inset:"",spaced:""}}),s("q-toolbar-title",[t._v("Backups")]),s("q-btn",{attrs:{icon:"sync",label:t.$q.platform.is.mobile?"":"Atualizar",round:t.$q.platform.is.mobile,flat:"",loading:t.loading},on:{click:function(e){return t.refreshData(null)}}}),s("q-btn",{attrs:{icon:"search",label:t.$q.platform.is.mobile?"":"Consultar",round:t.$q.platform.is.mobile,flat:"",to:{name:"backup.listagem"}}})],1)],1)],1)])])])],1)},i=[],o=s("a34a"),r=s.n(o),l=(s("96cf"),s("c973")),n=s.n(l),c=s("5a5b"),d={name:"tabelaibpt.index",components:{},data:function(){var t=new c["a"];return{dataset:t,rows:[],loading:!1,posting:!1,error:null,customcnpj:null,customnumero:null,customchave:null}},created:function(){this.$eventbus.$on("coletasconsultaupdated",this.coletasconsultaupdated)},beforeDestroy:function(){this.$eventbus.$off("coletasconsultaupdated",this.coletasconsultaupdated)},computed:{usuariologado:function(){var t=this,e=null;return t.$store.state.usuario.logado&&t.$store.state.usuario.usuario&&(e=t.$store.state.usuario.usuario),e}},mounted:function(){var t=this;return n()(r.a.mark((function e(){var s;return r.a.wrap((function(e){while(1)switch(e.prev=e.next){case 0:return s=t,e.next=3,s.refreshData(null);case 3:case"end":return e.stop()}}),e)})))()},methods:{actAdd:function(){var t=this;return n()(r.a.mark((function e(){var s,a;return r.a.wrap((function(e){while(1)switch(e.prev=e.next){case 0:s=t,a=s.dataset.showAdd(s),a.ok&&s.refreshData(null);case 3:case"end":return e.stop()}}),e)})))()},actEditDialog:function(t){var e=this;return n()(r.a.mark((function s(){var a,i;return r.a.wrap((function(s){while(1)switch(s.prev=s.next){case 0:a=e,i=a.rows[t],a.$q.bottomSheet({message:"Tabela: "+i.uf,grid:!1,actions:[{label:"Excluir",icon:"delete_forever",color:"red",id:"delete"},{label:"Download",icon:"download_for_offline",id:"download"}]}).onOk(function(){var t=n()(r.a.mark((function t(e){var s;return r.a.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if("delete"!==e.id){t.next=5;break}return t.next=3,i.deleteWithQuestion(a);case 3:s=t.sent,s.ok?a.refreshData(null):a.$helpers.showDialog(s);case 5:"download"===e.id&&i.download(a);case 6:case"end":return t.stop()}}),t)})));return function(e){return t.apply(this,arguments)}}());case 3:case"end":return s.stop()}}),s)})))()},refreshData:function(){var t=this;return n()(r.a.mark((function e(){var s,a,i;return r.a.wrap((function(e){while(1)switch(e.prev=e.next){case 0:return s=t,e.prev=1,s.loading=!0,s.error=null,e.next=6,s.dataset.fetchDashbord();case 6:if(a=e.sent,!a.ok){e.next=11;break}s.rows=a.data,e.next=14;break;case 11:return i=s.$helpers.showDialog(a),e.next=14,i.then((function(){}));case 14:e.next=20;break;case 16:e.prev=16,e.t0=e["catch"](1),s.loading=!1,s.error=e.t0.message;case 20:s.loading=!1;case 21:case"end":return e.stop()}}),e,null,[[1,16]])})))()}}},v=d,u=(s("72f5"),s("2877")),p=s("9989"),m=s("54e1"),h=s("f09f"),x=s("a370"),f=s("0016"),w=s("6b1d"),b=s("58a8"),g=s("eb85"),q=s("05c0"),C=s("de5e"),_=s("65c6"),$=s("9c40"),k=s("6ac5"),S=s("eebe"),z=s.n(S),y=Object(u["a"])(v,a,i,!1,null,"c6d72b8e",null);e["default"]=y.exports;z()(y,"components",{QPage:p["a"],QBanner:m["a"],QCard:h["a"],QCardSection:x["a"],QIcon:f["a"],QLinearProgress:w["a"],QBadge:b["a"],QSeparator:g["a"],QTooltip:q["a"],QPageSticky:C["a"],QToolbar:_["a"],QBtn:$["a"],QToolbarTitle:k["a"]})},b971:function(t,e,s){}}]);