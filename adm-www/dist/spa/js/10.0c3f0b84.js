(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([[10],{"18b5":function(e,t,a){"use strict";var r=a("b6e7"),o=a.n(r);o.a},b5d1:function(e,t,a){"use strict";a.r(t);var r=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",[a("q-page",{},[a("div",{staticClass:"full-width header-top-bg bg-primary shadow-up-21"}),a("div",{staticClass:"row doc-header "},[a("div",{staticClass:"col-sm-12",class:e.$q.platform.is.mobile?"":"q-pa-lg"},[a("q-card",{staticClass:"bg-white ",class:e.$q.platform.is.mobile?"q-ma-sm":"",attrs:{flat:"",bordered:""}},[e.error?a("q-card-section",{staticClass:"col-12"},[a("q-banner",{staticClass:"bg-negative text-white"},[e._v(e._s(e.error))])],1):e._e(),a("q-card-section",{directives:[{name:"show",rawName:"v-show",value:e.showfilters,expression:"showfilters"}]},[a("div",{staticClass:"row q-col-gutter-sm"},[a("div",{staticClass:"col-xs-12"},[e.cnpj&&""!==e.cnpj?a("selectbackupmesano",{attrs:{outlined:"",doc:e.cnpj},model:{value:e.filtermesano,callback:function(t){e.filtermesano=t},expression:"filtermesano"}}):e._e()],1)])]),a("q-card-section",{staticClass:"q-gutter-x-sm"},[a("q-btn",{attrs:{unelevated:"",label:"Consultar",color:"primary",icon:"search",loading:e.loading},on:{click:function(t){return e.refreshData(null)}}}),a("q-btn",{attrs:{unelevated:"",label:e.$q.platform.is.mobile?"":"Forçar sync",color:"secondary",icon:"sync",loading:e.loading},on:{click:e.actSync}},[a("q-tooltip",{attrs:{delay:500}},[e._v("\n                  Essa função faz o sincronismo entre os arquivos em disco com o banco de dados, atualizando as informações de arquivos.\n                ")])],1),a("q-btn",{attrs:{unelevated:"",label:e.$q.platform.is.mobile?"":"Remover",color:"red",icon:"delete",disable:e.loading||!e.selected_rows||e.selected_rows.length<=0},on:{click:e.actDeleteSelected}},[e.selected_rows&&e.selected_rows.length>0?a("q-badge",{attrs:{color:"yellow","text-color":"black",label:e.selected_rows.length,floating:""}}):e._e()],1)],1)],1),e.rows&&e.rows.length>0?a("q-card",{staticClass:"bg-white q-mt-md",attrs:{flat:"",bordered:!e.$q.platform.is.mobile,square:e.$q.platform.is.mobile}},[e.$q.platform.is.mobile?a("q-separator"):e._e(),a("q-card-section",{staticClass:"q-pa-none"},[a("div",{staticStyle:{"max-width":"100vw"}},[a("q-table",{attrs:{data:e.rows,columns:e.columns,dense:!e.$q.platform.is.mobile,flat:"",loading:e.loading,color:"primary",id:"sticky-table",pagination:e.dataset.pagination,"row-key":"md5",selection:"multiple",selected:e.selected_rows,"rows-per-page-options":e.$qtable.rowsperpageoptions},on:{"update:pagination":function(t){return e.$set(e.dataset,"pagination",t)},"update:selected":function(t){e.selected_rows=t},request:e.refreshData},scopedSlots:e._u([{key:"body-cell-action",fn:function(t){return[a("q-td",{attrs:{props:t}},[a("q-btn",{attrs:{flat:"",round:"",dense:!e.$q.platform.is.mobile,size:e.$q.platform.is.mobile?"12px":"8px",icon:"file_download"},on:{click:function(a){return e.actDownload(t.row)}}},[a("q-tooltip",[e._v("Download do backup")])],1)],1)]}},{key:"body-cell-cliente",fn:function(t){return[a("q-td",{attrs:{props:t}},[a("div",{staticClass:"cursor-pointer",class:t.row.cliente.ativo?"":"text-red"},[a("div",{staticClass:"ellipsis"},[t.row.cliente.ativo?e._e():a("span",{staticClass:"bg-red-1 text-red rounded-borders q-pa-xs text-caption"},[e._v("Inativo")]),a("span",[e._v("\n                              "+e._s(t.row.cliente.fantasia&&""!==t.row.cliente.fantasia?t.row.cliente.fantasia:t.row.cliente.nome)+"\n                            ")]),t.row.cliente.fantasia&&""!==t.row.cliente.fantasia?a("span",{staticClass:"q-ml-md text-caption text-grey-7"},[e._v(e._s(e.$helpers.strEllipsis(t.row.cliente.nome,30)))]):e._e()])]),a("q-tooltip",{attrs:{delay:700}},[a("div",[e._v(e._s(t.row.cliente.fantasia))]),a("div",[e._v(e._s(t.row.cliente.nome))]),a("div",[e._v("Documento: "+e._s(t.row.cliente.doc))]),a("div",[e._v("Id: "+e._s(t.row.cliente.id))]),a("div",[e._v(e._s(t.row.cliente.cidade+" - "+t.row.cliente.uf))])])],1)]}},{key:"body-cell-cidade",fn:function(t){return[a("q-td",{attrs:{props:t}},[a("div",{staticClass:"cursor-pointer"},[t.row.cliente.cidade?a("div",{staticClass:"ellipsis"},[e._v(e._s(t.row.cliente.cidade+" - "+t.row.cliente.uf))]):e._e()]),a("q-tooltip",{attrs:{delay:700}},[a("div",[e._v(e._s(t.row.cliente.cidade+" - "+t.row.cliente.uf))])])],1)]}},{key:"body-cell-size",fn:function(t){return[a("q-td",{attrs:{props:t}},[a("div",{staticClass:"cursor-pointer"},[a("div",{},[e._v(e._s(e.$helpers.bytesToHumanFileSizeString(t.row.size)))]),a("q-tooltip",{attrs:{delay:700}},[a("div",[e._v(e._s(e.$helpers.bytesToHumanFileSizeString(t.row.size)))])])],1)])]}},{key:"body-cell-controlabkp",fn:function(t){return[a("q-td",{attrs:{props:t}},[a("div",{staticClass:"cursor-pointer"},[t.row.cliente&&t.row.cliente.controlabkp?a("div",[a("q-avatar",{attrs:{size:"24px","font-size":"20px","text-color":t.row.cliente.controlabkp.color?t.row.cliente.controlabkp.color:"black",color:"grey-3",icon:t.row.cliente.controlabkp.icon}}),a("q-tooltip",{attrs:{delay:700}},[e._v("\n                            "+e._s(t.row.cliente.controlabkp.memo)+"\n                          ")])],1):e._e()])])]}},{key:"body-cell-downloadcount",fn:function(t){return[a("q-td",{attrs:{props:t}},[t.row.downloadcount&&t.row.downloadcount>0?a("div",[e._v("\n                        "+e._s(t.row.downloadcount)+" x\n                      ")]):e._e()])]}},{key:"body-cell-bucket",fn:function(t){return[a("q-td",{attrs:{props:t}},[a("div",{staticClass:"cursor-pointer"},[a("div",{staticClass:"text-caption text-grey-7"},[e._v(e._s(1===t.row.bucket?"EUA":"BR"))])])])]}},{key:"body-cell-lastmodified",fn:function(t){return[a("q-td",{attrs:{props:t}},[a("div",[t.row.lastmodified?a("div",[a("div",[e._v(e._s(e.$helpers.datetimeToBR(t.row.lastmodified,!1,!0)))]),a("q-tooltip",[t.row.lastmodified?a("div",[e._v(e._s(e.$helpers.datetimeRelativeToday(t.row.lastmodified)))]):e._e()])],1):e._e()])])]}},{key:"bottom-row",fn:function(){return[a("q-tr",[a("q-td"),a("q-td",{staticClass:"text-right",attrs:{colspan:"6"}},[a("div",[e.totalbytesselected>0&&e.selected_rows&&e.selected_rows.length>0?a("span",{staticClass:"rounded-borders bg-yellow text-body q-px-xs q-mr-md"},[e._v("\n                            Selecionado: "+e._s(e.$helpers.bytesToHumanFileSizeString(e.totalbytesselected))+"\n                          ")]):e._e(),a("span",{staticClass:"text-weight-bold"},[e._v("Total: "+e._s(e.$helpers.bytesToHumanFileSizeString(e.totalbytes)))])])])],1)]},proxy:!0}],null,!1,3324225404)})],1)])],1):e._e(),a("q-page-sticky",{attrs:{position:"top",expand:""}},[a("q-toolbar",{staticClass:"bg-primary text-white"},[a("q-btn",{attrs:{flat:"",dense:"",round:"","aria-label":"Menu",icon:"menu"},on:{click:function(t){return e.$store.dispatch("homedashboard/togglemenu")}}}),a("q-btn",{attrs:{flat:"",round:"",icon:"arrow_back"},on:{click:function(t){return e.$router.go(-1)}}}),e.$q.platform.is.mobile?e._e():a("q-separator",{attrs:{vertical:"",inset:"",spaced:""}}),a("q-toolbar-title",[e._v("Backup - Cliente: "+e._s(e.cnpj))]),a("q-btn",{attrs:{icon:e.showfilters?"filter_alt_off":"filter_alt",round:e.$q.platform.is.mobile,flat:""},on:{click:function(t){e.showfilters=!e.showfilters}}}),a("q-btn",{attrs:{icon:"search",round:e.$q.platform.is.mobile,flat:"",loading:e.loading},on:{click:function(t){return e.refreshData(null)}}})],1)],1)],1)])])],1)},o=[],n=(a("7f7f"),a("a481"),a("ac6a"),a("cadf"),a("06db"),a("456d"),a("a34a")),s=a.n(n),i=(a("96cf"),a("c973")),l=a.n(i),c=a("eb45"),d=a("5a5b"),u=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",[a("q-select",{ref:"txtselect",staticClass:"full-width",attrs:{dense:e.dense,outlined:e.outlined,"input-debounce":"300",label:e.label,"stack-label":"",clearable:"",counter:"",disable:e.disable,options:e.options,"map-options":"","emit-value":"","option-value":function(t){return Object(t)===t&&"primeirolastmodified"in t?e.$helpers.datetimeFormat(t.primeirolastmodified,"YYYY-MM"):null},"clear-icon":"clear",loading:e.loading||e.loadingdata},scopedSlots:e._u([{key:"append",fn:function(){return[a("q-btn",{attrs:{color:"grey-6",flat:"",round:"",dense:"",icon:"sync",loading:e.loading},on:{click:e.refreshData}})]},proxy:!0},{key:"selected-item",fn:function(t){return[t.opt?a("div",{staticClass:"ellipsis"},[t.opt.primeirolastmodified?a("span",[e._v(e._s(e.$helpers.datetimeFormat(t.opt.primeirolastmodified,"MMM/YYYY")))]):e._e(),t.opt.primeirolastmodified!==t.opt.ultimolastmodified?a("span",[e._v(e._s(" dias "+e.$helpers.datetimeFormat(t.opt.primeirolastmodified,"DD")+" a "+e.$helpers.datetimeFormat(t.opt.ultimolastmodified,"DD")))]):a("span",[e._v(e._s(" dia "+e.$helpers.datetimeFormat(t.opt.primeirolastmodified,"DD")))]),a("span",{staticClass:"q-ml-sm text-caption text-grey"},[e._v(e._s(e.$helpers.formatRS(t.opt.qtdearquivos,"",0)+" arquivo(s) em "+e.$helpers.bytesToHumanFileSizeString(t.opt.totalsize)))])]):e._e()]}},{key:"option",fn:function(t){return[a("q-item",e._g(e._b({staticClass:"border-bottom-separator",attrs:{dense:e.dense}},"q-item",t.itemProps,!1),t.itemEvents),[a("q-item-section",{attrs:{avatar:""}},[a("q-avatar",{attrs:{color:"primary","text-color":"white","font-size":"12px",size:"28px"}},[e._v(e._s(t.opt.qtdearquivos))])],1),a("q-item-section",[t.opt.primeirolastmodified?a("q-item-label",{staticStyle:{width:"70px"}},[e._v(e._s(e.$helpers.datetimeFormat(t.opt.primeirolastmodified,"MMM/YYYY")))]):e._e()],1),a("q-item-section",[t.opt.primeirolastmodified?a("q-item-label",{staticClass:"text-center"},[t.opt.primeirolastmodified!==t.opt.ultimolastmodified?a("span",[e._v(e._s(e.$helpers.datetimeFormat(t.opt.primeirolastmodified,"DD")+" a "+e.$helpers.datetimeFormat(t.opt.ultimolastmodified,"DD")))]):a("span",[e._v(e._s(e.$helpers.datetimeFormat(t.opt.primeirolastmodified,"DD")))])]):e._e()],1),a("q-item-section",{attrs:{side:""}},[t.opt.totalsize?a("q-item-label",[e._v(e._s(e.$helpers.bytesToHumanFileSizeString(t.opt.totalsize)))]):e._e()],1)],1)]}},{key:"counter",fn:function(){return[e._v("\n          "+e._s(e.$helpers.formatRS(e.totalarquivos,"",0)+" arquivo(s) em "+e.$helpers.bytesToHumanFileSizeString(e.totalbytes))+"\n        ")]},proxy:!0},{key:"no-option",fn:function(){return[a("q-item",[a("q-item-section",{staticClass:"text-grey"},[e._v("\n              Sem resultados\n            ")])],1)]},proxy:!0}]),model:{value:e.modelselect,callback:function(t){e.modelselect=t},expression:"modelselect"}})],1)},p=[],m={components:{},props:["doc","value","dense","outlined","label","clearable","nullabled","loading","disable"],data:function(){var e=new d["a"];return{dataset:e,modelselect:null,options:null,showing:!1,loadingdata:!1}},mounted:function(){var e=this;return l()(s.a.mark((function t(){return s.a.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,e.refreshData();case 2:e.value&&(e.modelselect=e.value);case 3:case"end":return t.stop()}}),t)})))()},computed:{totalbytes:function(){var e=this;if(!e.options)return 0;for(var t=0,a=0;a<e.options.length;a++){var r=e.options[a];t=parseInt(t+parseInt(r.totalsize))}return t},totalarquivos:function(){var e=this;if(!e.options)return 0;for(var t=0,a=0;a<e.options.length;a++){var r=e.options[a];t=parseInt(t+parseInt(r.qtdearquivos))}return t}},watch:{doc:function(){var e=l()(s.a.mark((function e(t){return s.a.wrap((function(e){while(1)switch(e.prev=e.next){case 0:this.refreshData(),this.modelselect=null;case 2:case"end":return e.stop()}}),e,this)})));function t(t){return e.apply(this,arguments)}return t}(),modelselect:function(){var e=l()(s.a.mark((function e(t){var a;return s.a.wrap((function(e){while(1)switch(e.prev=e.next){case 0:a=this,a.$emit("input",t),a.$emit("updated");case 3:case"end":return e.stop()}}),e,this)})));function t(t){return e.apply(this,arguments)}return t}()},methods:{actOnFocus:function(){var e=this;this.$nextTick((function(){e.$refs.txtselect.$el.focus()}))},refreshData:function(){var e=this;return l()(s.a.mark((function t(){var a,r;return s.a.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return a=e,a.loadingdata=!0,t.next=4,a.dataset.fetchGroupMesAno(a.doc);case 4:r=t.sent,r.ok&&(a.options=r.data),a.loadingdata=!1;case 7:case"end":return t.stop()}}),t)})))()}}},f=m,v=(a("18b5"),a("2877")),w=a("ddd8"),h=a("9c40"),b=a("66e5"),g=a("4074"),_=a("cb32"),q=a("0170"),k=a("eebe"),y=a.n(k),x=Object(v["a"])(f,u,p,!1,null,null,null),$=x.exports;y()(x,"components",{QSelect:w["a"],QBtn:h["a"],QItem:b["a"],QItemSection:g["a"],QAvatar:_["a"],QItemLabel:q["a"]});var D={name:"servidores.index",components:{selectbackupmesano:$},data:function(){var e=new c["a"],t=new d["a"];return{dataset:t,opcoesNivel:e,rows:[],filternivel:["8","1","2"],filtermesano:null,cnpj:"",showfilters:!1,selected_rows:[],columns:[{name:"action",align:"left",label:"*",field:"id"},{name:"name",align:"left",label:"Arquivo",field:"name",filterconfig:{value:null}},{name:"bucket",align:"left",label:"Bucket",field:"bucket",filterconfig:{value:null}},{name:"lastmodified",align:"center",label:"Data backup",field:"lastmodified"},{name:"downloadcount",align:"center",label:"Download",field:"downloadcount",filterconfig:{value:null}},{name:"size",align:"right",label:"Tamanho",field:"size",filterconfig:{value:null}}],loading:!1,posting:!1,error:null}},mounted:function(){return l()(s.a.mark((function e(){return s.a.wrap((function(e){while(1)switch(e.prev=e.next){case 0:case"end":return e.stop()}}),e)})))()},computed:{totalbytes:function(){var e=this;if(!e.rows)return 0;for(var t=0,a=0;a<e.rows.length;a++){var r=e.rows[a];t=parseInt(t+parseInt(r.size))}return t},totalbytesselected:function(){var e=this;if(!e.selected_rows)return 0;for(var t=0,a=0;a<e.selected_rows.length;a++){var r=e.selected_rows[a];t=parseInt(t+parseInt(r.size))}return t}},activated:function(){var e=this;return l()(s.a.mark((function t(){var a,r;return s.a.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(a=e,r=!a.cnpj||""===a.cnpj||a.cnpj!==a.$route.params.doc,a.cnpj=a.$route.params.doc,!a.$route.query){t.next=6;break}return t.next=6,a.queryRead(a.$route.query);case 6:if(!r){t.next=9;break}return t.next=9,a.refreshData(null);case 9:case"end":return t.stop()}}),t)})))()},deactivated:function(){},methods:{queryRead:function(e){var t=this;return l()(s.a.mark((function a(){var r;return s.a.wrap((function(a){while(1)switch(a.prev=a.next){case 0:r=t,e&&(e.page&&(r.dataset.pagination.page=parseInt(e.page)),e.mesano&&(r.filtermesano=e.mesano));case 2:case"end":return a.stop()}}),a)})))()},actSync:function(){var e=this;return l()(s.a.mark((function t(){var a,r,o;return s.a.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return a=e,r=a.$helpers.dialogProgress(a,"Aguarde o processo terminar!","Sincronizando..."),t.prev=2,t.next=5,a.dataset.syncSend(a.cnpj);case 5:o=t.sent,o.ok?(a.$q.notify({color:"positive",icon:"sync",timeout:2500,caption:o.msg,message:"Sincronismo de backup"}),a.refreshData()):a.$helpers.showDialog(o),t.next=12;break;case 9:t.prev=9,t.t0=t["catch"](2),a.$helpers.showDialog({ok:!1,msg:t.t0.message,warning:!0});case 12:return t.prev=12,r.hide(),t.finish(12);case 15:case"end":return t.stop()}}),t,null,[[2,9,12,15]])})))()},actDownload:function(e){var t=this;return l()(s.a.mark((function a(){var r;return s.a.wrap((function(a){while(1)switch(a.prev=a.next){case 0:r=t,r.$q.dialog({title:"Download de backup",message:"Justifique o motivo do download?",prompt:{model:"",isValid:function(e){return e.length>2},type:"text"},cancel:!0,persistent:!0}).onOk(function(){var t=l()(s.a.mark((function t(a){var o,n;return s.a.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(!(a&&a.length>2)){t.next=17;break}return o=r.$helpers.dialogProgress(r,"Em alguns segundos o download iniciará","Preparando download..."),t.prev=2,t.next=5,e.download(a);case 5:n=t.sent,n.ok?r.$helpers.forceFileDownload(n.url):r.$helpers.showDialog(n),t.next=12;break;case 9:t.prev=9,t.t0=t["catch"](2),r.$helpers.showDialog({ok:!1,msg:t.t0.message,warning:!0});case 12:return t.prev=12,o.hide(),t.finish(12);case 15:t.next=18;break;case 17:r.$helpers.showDialog({ok:!1,msg:"Informe no mínimo 3 e no máximo 100 caracteres"});case 18:case"end":return t.stop()}}),t,null,[[2,9,12,15]])})));return function(e){return t.apply(this,arguments)}}());case 2:case"end":return a.stop()}}),a)})))()},actDeleteSelected:function(){var e=this;return l()(s.a.mark((function t(){var a,r,o,n,i;return s.a.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(a=e,a.selected_rows&&!(a.selected_rows.length<=0)){t.next=3;break}return t.abrupt("return");case 3:if(r=a.$helpers.dialogProgress(a,"Excluido arquivo de backup","Aguarde..."),t.prev=4,o=[],a.selected_rows&&a.selected_rows.length>0)for(n=0;n<a.selected_rows.length;n++)o.push(a.selected_rows[n].md5);return t.next=9,a.dataset.deleteSend(o);case 9:i=t.sent,i.ok?a.$q.notify({color:"positive",icon:"delete",timeout:2500,caption:i.msg,message:"Arquivos de backup"}):a.$helpers.showDialog(i),t.next=16;break;case 13:t.prev=13,t.t0=t["catch"](4),a.$helpers.showDialog({ok:!1,msg:t.t0.message,warning:!0});case 16:return t.prev=16,r.hide(),t.finish(16);case 19:case"end":return t.stop()}}),t,null,[[4,13,16,19]])})))()},actEditDialog:function(e){var t=this;return l()(s.a.mark((function a(){var r,o,n,i;return s.a.wrap((function(a){while(1)switch(a.prev=a.next){case 0:return r=t,o=r.rows[e],a.next=4,o.dialogAddOrEdit(r);case 4:if(n=a.sent,!n.ok){a.next=9;break}r.refreshData(null),a.next=13;break;case 9:if(!n.msg||""===n.msg){a.next=13;break}return i=r.$helpers.showDialog(n),a.next=13,i.then((function(){}));case 13:case"end":return a.stop()}}),a)})))()},actLoadMore:function(){var e=this;return l()(s.a.mark((function t(){var a,r;return s.a.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return a=e,a.loading=!0,t.next=4,a.dataset.loadmore();case 4:r=t.sent,r.ok&&(a.rows=a.dataset.itens),a.loading=!1;case 7:case"end":return t.stop()}}),t)})))()},refreshData:function(e){var t=this;return l()(s.a.mark((function a(){var r,o,n,i,l;return s.a.wrap((function(a){while(1)switch(a.prev=a.next){case 0:return r=t,a.prev=1,r.loading=!0,r.error=null,r.error=null,r.dataset.readPropsTable(e),r.dataset.filter=r.filter,r.dataset.mesano=r.filtermesano,r.dataset.orderby=null,r.orderbylist&&(o=Object.keys(r.orderbylist).length,o>0&&(r.dataset.orderby=r.orderbylist)),a.next=12,r.dataset.fetchDetalhe(r.cnpj);case 12:if(n=a.sent,!n.ok){a.next=23;break}r.selected_rows=[],r.rows=r.dataset.itens,i={},null!==r.dataset.mesano&&(i.mesano=r.dataset.mesano),null!==r.dataset.pagination.page&&r.dataset.pagination.page>1&&(i.page=r.dataset.pagination.page),i.t=(new Date).getTime();try{r.$router.replace({name:r.$route.name,query:i,replace:!0,append:!1})}catch(s){console.error(s)}a.next=26;break;case 23:return l=r.$helpers.showDialog(n),a.next=26,l.then((function(){}));case 26:a.next=32;break;case 28:a.prev=28,a.t0=a["catch"](1),r.loading=!1,r.error=a.t0.message;case 32:r.loading=!1;case 33:case"end":return a.stop()}}),a,null,[[1,28]])})))()}}},C=D,S=a("9989"),z=a("f09f"),Q=a("a370"),T=a("54e1"),F=a("05c0"),j=a("58a8"),I=a("eb85"),Y=a("eaac"),M=a("db86"),E=a("bd08"),A=a("de5e"),B=a("65c6"),P=a("6ac5"),R=Object(v["a"])(C,r,o,!1,null,null,null);t["default"]=R.exports;y()(R,"components",{QPage:S["a"],QCard:z["a"],QCardSection:Q["a"],QBanner:T["a"],QBtn:h["a"],QTooltip:F["a"],QBadge:j["a"],QSeparator:I["a"],QTable:Y["a"],QTd:M["a"],QAvatar:_["a"],QTr:E["a"],QPageSticky:A["a"],QToolbar:B["a"],QToolbarTitle:P["a"]})},b6e7:function(e,t,a){}}]);