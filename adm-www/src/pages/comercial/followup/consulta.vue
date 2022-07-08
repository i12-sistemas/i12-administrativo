<template>
<div>
  <q-page class="">
    <!-- desktop mode -->
    <div class="row doc-header q-pa-lg"  >
      <div class="col-12" v-if="error">
        <q-banner class="bg-negative text-white">{{error}}</q-banner>
      </div>
      <div class="col-sm-12" v-if="!error" id="sticky-table-scroll">
        <q-table :data="rows" :columns="columns" dense bordered flat
          :loading="loading"
          id="sticky-table"
          :visible-columns="visiblecolumns"
          :pagination.sync="dataset.pagination"
          row-key="id"
          :rows-per-page-options="$qtable.rowsperpageoptions"
          @request="refreshData"
          :selected.sync="selected_rows"
          >
          <template v-slot:loading>
            <q-linear-progress indeterminate />
            <!-- <q-inner-loading showing color="primary" >
              <q-spinner color="primary" size="3em" :thickness="3" />
              <div class="text-h6 text-primary">Consultando...</div>
            </q-inner-loading> -->
          </template>
            <template v-slot:top-right>
              <q-btn color="black" icon="view_column" flat  @click="actConfigColuna" round class="q-ml-sm" size="sm" />
            </template>
            <!-- headers -->
            <template v-slot:header-cell="props">
                <q-th :props="props" class="" >
                  <div v-if="props.col.name === 'id11'" >
                    <span  @click="actChangeSortBy(props)">
                      {{ props.col.label }}
                      <q-icon class="q-ml-xs text-black" size="14px"
                        v-if="orderbylist.id"
                        :color="!orderbylist.id ? 'red' : (orderbylist.id  === 'asc' ? 'green' : 'red')"
                        :name="!orderbylist.id ? 'fas fa-sort-alpha-up-alt' : (orderbylist.id  === 'asc' ? 'fas fa-sort-alpha-down' : 'fas fa-sort-alpha-up-alt')"
                      />
                      <q-tooltip :delay="1000">{{!orderbylist.id ? 'Nenhum ordenação - clique para ordenar por ' + props.col.name : (orderbylist.id  === 'asc' ? 'Ordem crescente' : 'Ordem decrescente') }}</q-tooltip>
                    </span>
                    <div style="width: 70px;">
                      <q-input type="text" input-class="text-accent text-uppercase" color="accent" dense v-model="props.col.filterconfig.value" clearable @input="actInputFilter()" :debounce="700" :loading="loading" />
                    </div>
                  </div>
                  <div v-else>
                    <div v-if="props.col.filterconfig" @click="props.col.filterconfig.sorted ? actChangeSortBy(props) : null">
                      {{ props.col.label }}
                      <q-icon class="q-ml-xs text-black" size="14px"
                        v-if="orderbylist[props.col.name]"
                        :color="!orderbylist[props.col.name] ? 'red' : (orderbylist[props.col.name]  === 'asc' ? 'green' : 'red')"
                        :name="!orderbylist[props.col.name] ? 'fas fa-sort-alpha-up-alt' : (orderbylist[props.col.name]  === 'asc' ? 'fas fa-sort-alpha-down' : 'fas fa-sort-alpha-up-alt')"
                      />
                      <q-tooltip :delay="1000">{{!orderbylist[props.col.name] ? 'Nenhum ordenação - clique para ordenar por ' + props.col.name : (orderbylist[props.col.name]  === 'asc' ? 'Ordem crescente' : 'Ordem decrescente') }}</q-tooltip>
                    </div>
                    <div v-else>
                      {{ props.col.label }}
                    </div>
                    <div v-if="props.col.filterconfig">
                      <div v-if="props.col.filterconfig.filtertype ? props.col.filterconfig.filtertype === 'text' : false">
                        <q-input type="text" input-class="text-accent text-uppercase" color="accent" dense v-model="props.col.filterconfig.value" clearable @input="actInputFilter()"
                          :debounce="700" :loading="loading"
                          >
                          <template v-slot:prepend v-if="props.col.filterconfig.filtertype2 ? props.col.filterconfig.filtertype2 !== '' : false" >
                            <q-btn icon="filter_alt" round dense size="xs" unelevated @click="actShowFilter2(props)"
                              :color="(props.col.filterconfig.value2 ? 'accent' : 'grey-3')"
                              :text-color="(props.col.filterconfig.value2 ? 'white' : 'grey-7')"
                            >
                              <q-tooltip :delay="700" v-if="props.col.filterconfig.value2" >
                                <div v-html="props.col.filterconfig.value2"></div>
                              </q-tooltip>
                            </q-btn>
                          </template>
                          <q-tooltip :delay="700" v-if="props.col.filterconfig.tooltip ? props.col.filterconfig.tooltip !== '' : false" >
                            <div v-html="props.col.filterconfig.tooltip"></div>
                          </q-tooltip>
                        </q-input>
                      </div>
                      <div v-if="props.col.filterconfig.filtertype ? props.col.filterconfig.filtertype === 'floatinterval' : false">
                        <div v-if="props.col.filterconfig.value !=null" >
                          <q-chip label="Filtrado" dense color="accent" text-color="white" clickable  @click="actShowFilter(props)"
                            removable @remove="actClearFilter(props)" class="text-caption truncate-chip-labels "
                            style="max-width: 100px"
                            >
                            <div class="ellipsis">
                              <span v-if="props.col.filterconfig.value.valuei" v-html="'>=' + $helpers.formatRS(props.col.filterconfig.value.valuei, (props.col.filterconfig.prefix ? props.col.filterconfig.prefix == 'R$' : false), (props.col.filterconfig.decimal ? props.col.filterconfig.decimal : 0))" />
                              <span v-if="props.col.filterconfig.value.valuei && props.col.filterconfig.value.valuef"> à </span>
                              <span v-if="props.col.filterconfig.value.valuef" v-html="'<=' + $helpers.formatRS(props.col.filterconfig.value.valuef, (props.col.filterconfig.prefix ? props.col.filterconfig.prefix == 'R$' : false), (props.col.filterconfig.decimal ? props.col.filterconfig.decimal : 0))" />
                            </div>
                            <q-tooltip :delay="1000" >
                              <span v-if="props.col.filterconfig.value.valuei">De {{$helpers.formatRS(props.col.filterconfig.value.valuei, (props.col.filterconfig.prefix ? props.col.filterconfig.prefix == 'R$' : false), (props.col.filterconfig.decimal ? props.col.filterconfig.decimal : 0))}}</span>
                              <span v-if="props.col.filterconfig.value.valuei && props.col.filterconfig.value.valuef"> à </span>
                              <span v-if="props.col.filterconfig.value.valuef">{{$helpers.formatRS(props.col.filterconfig.value.valuef,  (props.col.filterconfig.prefix ? props.col.filterconfig.prefix == 'R$' : false), (props.col.filterconfig.decimal ? props.col.filterconfig.decimal : 0))}}</span>
                            </q-tooltip>
                          </q-chip>
                        </div>
                        <div v-else >
                          <q-chip label="filtrar" dense color="grey-3" text-color="grey-7" clickable  @click="actShowFilter(props)" class="text-caption" icon="filter_alt"  />
                        </div>
                      </div>
                      <div v-if="props.col.filterconfig.filtertype ? props.col.filterconfig.filtertype === 'datetimeinterval' : false">
                        <div v-if="props.col.filterconfig.value !=null" >
                          <q-chip label="Filtrado" dense color="accent" text-color="white" clickable  @click="actShowFilter(props)"
                            removable @remove="actClearFilter(props)" class="text-caption truncate-chip-labels "
                            style="max-width: 100px"
                            >
                            <div class="ellipsis">
                              <span v-if="props.col.filterconfig.value.valuei" v-html="'>=' + $helpers.dateToBR(props.col.filterconfig.value.valuei)" />
                              <span v-if="props.col.filterconfig.value.valuei && props.col.filterconfig.value.valuef"> à </span>
                              <span v-if="props.col.filterconfig.value.valuef" v-html="'<=' + $helpers.dateToBR(props.col.filterconfig.value.valuef)" />
                            </div>
                            <q-tooltip :delay="1000" >
                              <span v-if="props.col.filterconfig.value.valuei">De {{$helpers.dateToBR(props.col.filterconfig.value.valuei)}}</span>
                              <span v-if="props.col.filterconfig.value.valuei && props.col.filterconfig.value.valuef"> à </span>
                              <span v-if="props.col.filterconfig.value.valuef">{{$helpers.dateToBR(props.col.filterconfig.value.valuef)}}</span>
                            </q-tooltip>
                          </q-chip>
                        </div>
                        <div v-else >
                          <q-chip label="filtrar" dense color="grey-3" text-color="grey-7" clickable  @click="actShowFilter(props)" class="text-caption" icon="filter_alt"  />
                        </div>
                      </div>
                      <div v-if="(selected_rows ? selected_rows.length > 0 :false) && (props.col.filterconfig.updatemass)">
                        <q-chip label="editar" dense color="grey-3" text-color="grey-7" clickable class="text-caption" icon="edit" >
                        <!-- <q-btn round unelevated size="xs" color="grey-3" text-color="grey-7" clickable class="text-caption" icon="edit" label="editar"> -->
                            <q-popup-edit v-model="props.col.filterconfig.updatemass.value" :title="selected_rows.length + ' registros'" buttons label-set="Salvar"
                              @save="(val, initialValue) => onSetInputMass(props.col.field, val)"
                                :validate="()=>!$refs['txtpopinputtitle_' + props.col.name].hasError"
                                >
                              <inputdata outlined :ref="'txtpopinputtitle_' + props.col.name" v-if="props.col.filterconfig.updatemass.type === 'date'"
                                v-model="props.col.filterconfig.updatemass.value" :label="props.col.label" stacklabel :dense="!$q.platform.is.mobile" />

                              <q-input type="number" :ref="'txtpopinputtitle_' + props.col.name"  v-if="props.col.filterconfig.updatemass.type === 'integer'"
                                v-model.number="props.col.filterconfig.updatemass.value" autofocus :dense="!$q.platform.is.mobile" :debounce="400"
                                :rules="[val => (val ? (parseInt(val) > 0) && (parseInt(val) <= 99999999999) : true) || 'Se informado deve ser um número inteiro']"
                              />

                              <inputstatusagendamento :ref="'txtpopinputtitle_' + props.col.name" v-if="props.col.filterconfig.updatemass.type === 'inputStatusAgendaPopupEdit'"
                                :value="{ status: null, erro: null }"
                                :tipo="(props.col.name === 'erroagendastatus') ? 'agenda' : ((props.col.name === 'errocoletastatus') ? 'coleta' : 'dtpromessa')"
                                  @input="(val) => props.col.filterconfig.updatemass.value = val" />

                              <inputstatusiniciofup :ref="'txtpopinputtitle_' + props.col.name"  v-if="props.col.filterconfig.updatemass.type === 'inputstatusiniciofup'"
                                @input="(val) => props.col.filterconfig.updatemass.value = val"  />

                              <inputstatusconfirmacaocoleta :ref="'txtpopinputtitle_' + props.col.name"  v-if="props.col.filterconfig.updatemass.type === 'inputstatusconfirmacaocoleta'"
                                @input="(val) => props.col.filterconfig.updatemass.value = val"  />

                              <q-input type="textarea" :ref="'txtpopinputtitle_' + props.col.name" v-model="props.col.filterconfig.updatemass.value" autofocus counter
                                  v-if="props.col.filterconfig.updatemass.type === 'textarea'"
                                  input-class="text-uppercase" @keyup.enter.stop :dense="!$q.platform.is.mobile" :debounce="400" />
                            </q-popup-edit>
                            <q-tooltip :delay="700">Edição em massa do campo {{props.col.label}}</q-tooltip>
                        </q-chip>
                      </div>
                    </div>
                  </div>
                </q-th>
            </template>
            <!-- headers -->
            <template v-slot:body-cell-action="props">
              <q-td :props="props">
                <q-btn flat round :dense="!$q.platform.is.mobile" :size="$q.platform.is.mobile ? '12px' : '8px'" icon="edit" @click="actEditRow(props.row)" >
                  <q-tooltip>Editar</q-tooltip>
                </q-btn>
                <q-btn flat round :dense="!$q.platform.is.mobile" :size="$q.platform.is.mobile ? '12px' : '8px'" icon="storage" @click="actLogView(props.row)" >
                  <q-tooltip>Log</q-tooltip>
                </q-btn>
              </q-td>
            </template>
            <!-- rows -->
            <template v-slot:body-cell-dhimportacao="props">
              <q-td :props="props" class="text-caption">
                <div>
                  {{ props.row.dhimportacao ? $helpers.dateToBR(props.row.dhimportacao) : '' }}
                  <q-tooltip :delay="700">
                    {{ props.row.dhimportacao ? $helpers.datetimeToBR(props.row.dhimportacao) : '' }}
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-datahorafollowup="props">
              <q-td :props="props">
                <div class="text-caption" @click="actLogView(props.row)">
                  {{ props.row.datahora_followup ? $helpers.datetimeToBR(props.row.datahora_followup) : '-' }}
                  <q-tooltip :delay="700">
                    <div v-if="props.row.datahora_followup">Última alteração em {{ props.row.datahora_followup ? $helpers.datetimeToBR(props.row.datahora_followup) : '' }}</div>
                    <div>por {{ (props.row.updated_usuario ? props.row.updated_usuario.id >= 0 : false) ? props.row.updated_usuario.nome : 'Não identificado' }}</div>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-updatedusuario="props">
              <q-td :props="props">
                <div class="text-caption" @click="actLogView(props.row)">
                  {{ (props.row.updated_usuario ? props.row.updated_usuario.id >= 0 : false) ? props.row.updated_usuario.nome : 'Não identificado' }}
                  <q-tooltip :delay="700">
                    <div v-if="props.row.datahora_followup">Última alteração em {{ props.row.datahora_followup ? $helpers.datetimeToBR(props.row.datahora_followup) : '' }}</div>
                    <div>por {{ (props.row.updated_usuario ? props.row.updated_usuario.id >= 0 : false) ? props.row.updated_usuario.nome : 'Não identificado' }}</div>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-erroagendastatus="props">
              <q-td :props="props" v-if="props.row.erroagendastatus" :class="'bg-' + props.row.erroagendastatus.color + '-2 text-' + props.row.erroagendastatus.color + '-10'" class="text-caption">
                <div style="width: 70px" class="ellipsis" >
                  <span v-if="props.row.erroagendastatus.value === '2'">{{ props.row.erroagenda ? props.row.erroagenda.descricao : '?' }}</span>
                  <span v-else>{{ props.row.erroagendastatus.description }}</span>
                  <q-tooltip :delay="700">
                    <div v-if="props.row.erroagendastatus.value === '2'">Erro: {{ props.row.erroagenda ? props.row.erroagenda.descricao : '** Não identificado **' }}</div>
                    <span v-else>Cód.: {{ props.row.erroagendastatus }} - {{ props.row.erroagendastatus.description }}</span>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-dataconfirmacao="props">
              <q-td :props="props">
                <div class="ellipsis" >
                  {{ props.row.dataconfirmacao ? $helpers.dateToBR(props.row.dataconfirmacao) : '-' }}
                  <q-tooltip :delay="700" >
                    <div v-if="props.row.dataconfirmacao" >{{ $helpers.dateToBR(props.row.dataconfirmacao) }}</div>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-datacoleta="props">
              <q-td :props="props">
                <div class="ellipsis" >
                  {{ props.row.datacoleta ? $helpers.dateToBR(props.row.datacoleta) : '-' }}
                  <q-tooltip :delay="700" >
                    <div v-if="props.row.datacoleta" >{{ $helpers.dateToBR(props.row.datacoleta) }}</div>
                    <div>Clique aqui para editar</div>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-coletaid="props">
              <q-td :props="props">
                <div class="ellipsis" >
                  {{ props.row.coletaid ? props.row.coletaid : '-' }}
                  <q-tooltip :delay="700" >
                    <div v-if="props.row.coletaid" >Coleta #{{ props.row.coletaid }}</div>
                  </q-tooltip>
                  <q-menu auto-close anchor="center middle" self="center middle" dark context-menu>
                    <q-list style="min-width: 100px" dense dark>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, props.row.coletaid)">
                        <q-item-section>Filtrar :: {{props.row.coletaid}}</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="search"  />
                        </q-item-section>
                      </q-item>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, null)">
                        <q-item-section>Limpar filtro</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="clear"  />
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-notafiscal="props">
              <q-td :props="props">
                <div class="ellipsis" >
                  {{ props.row.notafiscal ? props.row.notafiscal : '-' }}
                  <q-tooltip :delay="700" >
                    <div v-if="props.row.coletaid" >Nota Fiscal #{{ props.row.notafiscal }}</div>
                  </q-tooltip>
                  <q-menu auto-close anchor="center middle" self="center middle" dark context-menu>
                    <q-list style="min-width: 100px" dense dark>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, props.row.notafiscal)">
                        <q-item-section>Filtrar :: {{props.row.notafiscal}}</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="search"  />
                        </q-item-section>
                      </q-item>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, null)">
                        <q-item-section>Limpar filtro</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="clear"  />
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-errocoletastatus="props">
              <q-td :props="props" :class="'bg-' + props.row.errocoletastatus.color + '-2 text-' + props.row.errocoletastatus.color + '-10'" class="text-caption">
                <div style="width: 70px" class="ellipsis" >
                  <span v-if="props.row.errocoletastatus.value === '2'">{{ props.row.errocoleta ? props.row.errocoleta.descricao : '?' }}</span>
                  <span v-else>{{ props.row.errocoletastatus.description }}</span>
                  <q-tooltip :delay="700">
                    <div v-if="props.row.errocoletastatus.value === '2'">Erro: {{ props.row.errocoleta ? props.row.errocoleta.descricao : '** Não identificado **' }}</div>
                    <span v-else>Cód.: {{ props.row.errocoletastatus }} - {{ props.row.errocoletastatus.description }}</span>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-statusconfirmacaocoleta="props">
              <q-td :props="props" :class="'bg-' + props.row.statusconfirmacaocoleta.color + '-2 text-' + props.row.statusconfirmacaocoleta.color + '-10'" class="text-caption">
                <div style="width: 70px" class="ellipsis" >
                  <span >{{ props.row.statusconfirmacaocoleta.description }}</span>
                  <q-tooltip :delay="700">
                    <span>Cód.: {{ props.row.statusconfirmacaocoleta }} - {{ props.row.statusconfirmacaocoleta.description }}</span>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-iniciofollowup="props">
              <q-td :props="props" :class="'bg-' + props.row.iniciofollowup.color + '-2 text-' + props.row.iniciofollowup.color + '-10'" class="text-caption">
                <div style="width: 70px" class="ellipsis" >
                  <span >{{ props.row.iniciofollowup.description }}</span>
                  <q-tooltip :delay="700">
                    <span>Cód.: {{ props.row.iniciofollowup }} - {{ props.row.iniciofollowup.description }}</span>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-errodtpromessastatus="props">
              <q-td :props="props" :class="'bg-' + props.row.errodtpromessastatus.color + '-2 text-' + props.row.errodtpromessastatus.color + '-10'" class="text-caption">
                <div style="width: 70px" class="ellipsis" >
                  <span v-if="props.row.errodtpromessastatus.value === '2'">{{ props.row.errodtpromessa ? props.row.errodtpromessa.descricao : '?' }}</span>
                  <span v-else>{{ props.row.errodtpromessastatus.description }}</span>
                  <q-tooltip :delay="700">
                    <div v-if="props.row.errodtpromessastatus.value === '2'">Erro: {{ props.row.errodtpromessa ? props.row.errodtpromessa.descricao : '** Não identificado **' }}</div>
                    <span v-else>Cód.: {{ props.row.errodtpromessastatus }} - {{ props.row.errodtpromessastatus.description }}</span>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-cliente="props">
              <q-td :props="props">
                <div style="max-width: 100px;" class="ellipsis" >
                  {{ (props.row.cliente ? (props.row.cliente.fantasia_followup ? props.row.cliente.fantasia_followup !== '' : false) : false) ? props.row.cliente.fantasia_followup : '-' }}
                  <q-tooltip :delay="700" v-if="props.row.cliente">
                    <div class="text-weight-bold">{{ (props.row.cliente.fantasia ? props.row.cliente.fantasia !== '' : false) ? props.row.cliente.fantasia : '-' }}</div>
                    <div class="text-caption">{{ (props.row.cliente.fantasia_followup ? props.row.cliente.fantasia_followup !== '' : false) ? props.row.cliente.fantasia_followup : '-' }}</div>
                  </q-tooltip>
                  <q-menu auto-close anchor="center middle" self="center middle" dark context-menu>
                    <q-list style="min-width: 100px" dense dark>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, props.row.cliente.fantasia_followup)">
                        <q-item-section>Filtrar :: {{props.row.cliente.fantasia_followup}}</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="search"  />
                        </q-item-section>
                      </q-item>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, null)">
                        <q-item-section>Limpar filtro</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="clear"  />
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-clientefollowupid="props">
              <q-td :props="props">
                <div style="max-width: 300px;" class="ellipsis" >
                  {{ (props.row.cliente ? (props.row.cliente.followupid ? props.row.cliente.followupid !== '' : false) : false) ? props.row.cliente.followupid : '-' }}
                  <q-tooltip :delay="700" v-if="props.row.cliente">
                    <div class="text-weight-bold">{{ (props.row.cliente.followupid ? props.row.cliente.followupid !== '' : false) ? props.row.cliente.followupid : '-' }}</div>
                    <div class="text-caption">{{ (props.row.cliente.fantasia_followup ? props.row.cliente.fantasia_followup !== '' : false) ? props.row.cliente.fantasia_followup : '-' }}</div>
                    <div class="text-caption">{{ (props.row.cliente.fantasia ? props.row.cliente.fantasia !== '' : false) ? props.row.cliente.fantasia : '-' }}</div>
                  </q-tooltip>
                  <q-menu auto-close anchor="center middle" self="center middle" dark context-menu>
                    <q-list style="min-width: 100px" dense dark>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, props.row.cliente.followupid)">
                        <q-item-section>Filtrar :: {{props.row.cliente.followupid}}</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="search"  />
                        </q-item-section>
                      </q-item>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, null)">
                        <q-item-section>Limpar filtro</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="clear"  />
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-fornecrazao="props">
              <q-td :props="props">
                <div style="max-width: 130px;" class="ellipsis" >
                  {{ props.row.fornecrazao }}
                  <q-menu auto-close anchor="center middle" self="center middle" dark context-menu>
                    <q-list style="min-width: 100px" dense dark>
                      <q-item clickable v-close-popup v-if="props.row.fornecedor ? props.row.fornecedor.id > 0 : false" @click="actEditCliente(props.row.fornecedor.id)">
                        <q-item-section>Abrir cadastro</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="edit"  />
                        </q-item-section>
                      </q-item>
                      <q-separator spaced dark />
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, props.row.fornecrazao)">
                        <q-item-section>Filtrar :: {{props.row.fornecrazao}}</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="search"  />
                        </q-item-section>
                      </q-item>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, null)">
                        <q-item-section>Limpar filtro</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="clear"  />
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>
                  <q-tooltip :delay="700">
                    <div v-html="getToolTipForneceor(props.row)" />
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-forneccnpj="props">
              <q-td :props="props">
                <div>
                  {{ $helpers.mascaraDocCPFCNPJ(props.row.forneccnpj) }}
                  <q-menu auto-close anchor="center middle" self="center middle" dark context-menu>
                    <q-list style="min-width: 100px" dense dark>
                      <q-item clickable v-close-popup v-if="props.row.fornecedor ? props.row.fornecedor.id > 0 : false" @click="actEditCliente(props.row.fornecedor.id)">
                        <q-item-section>Abrir cadastro</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="edit"  />
                        </q-item-section>
                      </q-item>
                      <q-separator spaced dark />
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, props.row.forneccnpj)">
                        <q-item-section>Filtrar :: {{props.row.forneccnpj}}</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="search"  />
                        </q-item-section>
                      </q-item>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, null)">
                        <q-item-section>Limpar filtro</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="clear"  />
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>
                  <q-tooltip :delay="700">
                    <div v-html="getToolTipForneceor(props.row)" />
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-fornectelefone="props">
              <q-td :props="props">
                <div>
                  {{ props.row.fornectelefone }}
                  <q-tooltip :delay="700">
                    <div v-html="getToolTipForneceor(props.row)" />
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-forneccidade="props">
              <q-td :props="props">
                <div>
                  {{ props.row.forneccidade + '/' + props.row.fornecuf }}
                  <q-tooltip :delay="700">
                    <div v-html="getToolTipForneceor(props.row)" />
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-itemdescricao="props">
              <q-td :props="props">
                <div style="min-width: 100px; max-width: 350px;" class="ellipsis">
                  {{ props.row.itemdescricao }}
                  <q-menu auto-close anchor="center middle" self="center middle" dark context-menu>
                    <q-list style="min-width: 100px" dense dark>
                      <q-item clickable v-close-popup @click="$helpers.copytoclipboard(props.row.itemdescricao)">
                        <q-item-section>Copiar texto :: {{props.row.itemdescricao}}</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="content_copy" />
                        </q-item-section>
                      </q-item>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, props.row.itemdescricao)">
                        <q-item-section>Filtrar :: {{props.row.itemdescricao}}</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="search"  />
                        </q-item-section>
                      </q-item>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, null)">
                        <q-item-section>Limpar filtro</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="clear"  />
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>
                  <q-tooltip :delay="700">
                    <div v-html="getToolTipItem(props.row)" />
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-itemid="props">
              <q-td :props="props">
                <div style="max-width: 350px;" class="ellipsis">
                  {{ props.row.itemid }}
                  <q-menu auto-close anchor="center middle" self="center middle" dark context-menu>
                    <q-list style="min-width: 100px" dense dark>
                      <q-item clickable v-close-popup @click="$helpers.copytoclipboard(props.row.itemid)">
                        <q-item-section>Copiar texto :: {{props.row.itemid}}</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="content_copy" />
                        </q-item-section>
                      </q-item>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, props.row.itemid)">
                        <q-item-section>Filtrar :: {{props.row.itemid}}</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="search"  />
                        </q-item-section>
                      </q-item>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, null)">
                        <q-item-section>Limpar filtro</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="clear"  />
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>
                  <q-tooltip :delay="700">
                    <div v-html="getToolTipItem(props.row)" />
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-itemnumerolinhapedido="props">
              <q-td :props="props">
                <div style="max-width: 350px;" class="ellipsis">
                  {{ props.row.itemnumerolinhapedido }}
                  <q-tooltip :delay="700">
                    <div v-html="getToolTipItem(props.row)" />
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-ordemcompra="props">
              <q-td :props="props">
                <div style="min-width: 80px">
                  {{ props.row.ordemcompra }}
                  <q-menu auto-close anchor="center middle" self="center middle" dark context-menu>
                    <q-list style="min-width: 100px" dense dark>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, props.row.ordemcompra)">
                        <q-item-section>Filtrar :: OC {{props.row.ordemcompra}}</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="search"  />
                        </q-item-section>
                      </q-item>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, null)">
                        <q-item-section>Limpar filtro</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="clear"  />
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>
                  <q-tooltip :delay="700">
                    <div>Ordem de compra: {{ props.row.ordemcompra }}</div>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-comprador="props">
              <q-td :props="props">
                <div style="min-width: 80px">
                  {{ props.row.comprador }}
                  <q-menu auto-close anchor="center middle" self="center middle" dark context-menu>
                    <q-list style="min-width: 100px" dense dark>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, props.row.comprador)">
                        <q-item-section>Filtrar :: {{props.row.comprador}}</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="search"  />
                        </q-item-section>
                      </q-item>
                      <q-item clickable v-close-popup @click="actApplyFastFilter(props, null)">
                        <q-item-section>Limpar filtro</q-item-section>
                        <q-item-section top avatar>
                          <q-avatar icon="clear"  />
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>
                  <q-tooltip :delay="700">
                    <div>Comprador: {{ props.row.comprador }}</div>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <!-- rows -->
            <!-- bottom -->
            <template v-slot:bottom-row>
              <q-tr>
                <q-td colspan="3"></q-td>
                <q-td>
                  <span class="text-caption rounded-borders bg-grey-3 q-pa-xs" v-if="dataset.pagination ? dataset.pagination.rowsNumber : false">
                    Linhas: <span class="text-subtitle2 q-pa-xs text-weight-bold">{{dataset.pagination.rowsNumber}}</span>
                  </span>
                </q-td>
                <q-td colspan="3"></q-td>
                <q-td>
                  <div class="justify-end text-subtitle2 text-weight-bold full-width text-subtitle2 rounded-borders bg-grey-3 q-pa-xs" v-if="dataset.counters ? dataset.counters.peso : false">
                    {{$helpers.formatRS(dataset.counters.peso, false, 0)}}
                    <q-tooltip :delay="700">
                      <div>Peso total: {{$helpers.formatRS(dataset.counters.peso, false, 2)}}</div>
                    </q-tooltip>
                  </div>
                </q-td>
                <q-td colspan="3"></q-td>
              </q-tr>
            </template>
        </q-table>
      </div>
    </div>
    <!-- desktop mode -->
    <q-page-sticky position="top" expand>
      <q-toolbar class="bg-white text-primary shadow-up-21">
        <q-btn flat dense round @click="$store.dispatch('homedashboard/togglemenu')" aria-label="Menu" icon="menu" />
        <q-btn flat round icon="arrow_back" @click="$router.go(-1)"/>
        <q-separator vertical inset v-if="!$q.platform.is.mobile" spaced/>
        <q-toolbar-title>Follow Up</q-toolbar-title>
        <q-btn  icon="sync" :label="$q.platform.is.mobile ? '' : 'Atualizar'" :round="$q.platform.is.mobile" flat @click="refreshData(null)" :loading="loading" />
      </q-toolbar>
    </q-page-sticky>
  </q-page>
</div>
</template>

<script>
import { FollowUpStatusErrosAgenda, FollowUpStatusErrosColeta, FollowUpStatusErrosDtPromessa, FollowUpStatusInicioFup, FollowUpStatusConfirmacaoColeta } from 'src/mvc/models/enums/followuptypes'
import Vue from 'vue'
import FollowUps from 'src/mvc/collections/followups.js'
import FollowUp from 'src/mvc/models/followup.js'
import Cliente from 'src/mvc/models/cliente.js'
import inputdata from 'src/components/cpn-input-data'
import inputstatusagendamento from './editores/cpn-change-status-agendamento'
import inputstatusiniciofup from './editores/cpn-change-status-iniciofup'
import inputstatusconfirmacaocoleta from './editores/cpn-change-status-confirmacaocoleta'
export default {
  name: 'followup.consulta',
  components: {
    inputstatusagendamento,
    inputstatusiniciofup,
    inputstatusconfirmacaocoleta,
    inputdata
  },
  data () {
    var dataset = new FollowUps()
    return {
      dataset,
      rows: [],
      inputStatusAgendaPopupEdit: { status: null, erro: null },
      permiteoperacionalcoletasbaixar: null,
      permiteoperacionalcoletasbaixardesfazer: null,
      permiteoperacionalcoletascancelar: null,
      permiteoperacionalcoletasconsulta: null,
      permiteoperacionalcoletassave: null,
      error: null,
      selected_rows: [],
      showtable: true,
      orderbylist: { dhimportacao: 'desc' },
      visiblecolumns: [
        'id', 'dhimportacao', 'contato', 'cliente', 'clientefollowupid', 'datasolicitacao', 'fornecrazao', 'forneccidade', 'fornecuf', 'erroagendastatus', 'errocoletastatus',
        'errodtpromessastatus', 'aprovacaooc', 'dataagendamentocoleta', 'datapromessa', 'iniciofollowup', 'dataconfirmacao', 'coletaid', 'datacoleta', 'statusconfirmacaocoleta', 'ordemcompra',
        'ordemcompradig', 'notafiscal', 'observacao',
        'comprador', 'itemid', 'itemdescricao', 'qtdesolicitada', 'qtderecebida', 'vlrunitario', 'qtdedevida'
      ],
      columns: [
        { name: 'id', align: 'center', label: 'id', field: 'id', style: 'min-width: 70px;', filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'updatedusuario', align: 'left', label: 'Usuário', field: 'updated_usuario', filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'dhimportacao', align: 'center', label: 'Data importação', field: 'dhimportacao', filterconfig: { filtertype: 'datetimeinterval', value: null, sorted: true } },
        { name: 'contato', align: 'left', label: 'Contato', field: 'contato', filterconfig: { filtertype: 'text', filtertype2: 'checkexistefilter', value: null, value2: null, allowempty2: true, sorted: false } },
        { name: 'fornecrazao', align: 'left', label: 'Fornecedor', field: 'fornecrazao', filterconfig: { filtertype: 'text', filtertype2: 'clientefilter', value2: null, datatype2: Number, value: null, sorted: true } },
        { name: 'forneccnpj', align: 'left', label: 'CNPJ', field: 'forneccnpj', filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'fornectelefone', align: 'left', label: 'Telefone', field: 'fornectelefone', filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'forneccidade', align: 'left', label: 'Cidade/UF', field: 'forneccidade', format: (val, row) => val + '/' + row.fornecuf, filterconfig: { filtertype: 'text', filtertype2: 'cidadefilter', value2: null, datatype2: Number, value: null, sorted: true } },
        { name: 'fornecedorid', align: 'center', label: 'Fornecedor ID', field: 'fornecedorid', filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'datasolicitacao', align: 'center', label: 'Data Solicitação', format: (val, row) => val ? Vue.prototype.$helpers.dateToBR(val) : '', field: 'datasolicitacao', filterconfig: { filtertype: 'datetimeinterval', value: null, sorted: true, updatemass: { value: null, type: 'date' } } },
        { name: 'cliente', align: 'left', label: 'Cliente', field: 'cliente', filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'clienterazaosocial', align: 'left', label: 'Cliente Razão Social', field: row => row.cliente ? row.cliente.razaosocial : null, filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'clientefollowupid', align: 'left', label: 'Cliente FUP ID', field: row => row.cliente ? row.cliente.followupid : null, filterconfig: { filtertype: 'text', value: null, filtertype2: 'clientefollowupidfilter', value2: null, datatype2: JSON, sorted: true } },
        { name: 'clienteid', align: 'center', label: 'Cliente ID', field: row => row.cliente ? row.cliente.id : null, filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'erroagendastatus', align: 'left', label: 'St. Agenda', field: 'erroagendastatus', filterconfig: { filtertype: 'text', value: null, filtertype2: 'statusagendafilter', value2: null, datatype2: JSON, sorted: true, updatemass: { value: null, type: 'inputStatusAgendaPopupEdit' } } },
        { name: 'errocoletastatus', align: 'left', label: 'St. Coleta', field: 'errocoletastatus', filterconfig: { filtertype: 'text', value: null, filtertype2: 'statuscoletafilter', value2: null, datatype2: JSON, sorted: true, updatemass: { value: null, type: 'inputStatusAgendaPopupEdit' } } },
        { name: 'errodtpromessastatus', align: 'left', label: 'St. Promessa', field: 'errodtpromessastatus', filterconfig: { filtertype: 'text', value: null, filtertype2: 'statusdtpromessafilter', value2: null, datatype2: JSON, sorted: true, updatemass: { value: null, type: 'inputStatusAgendaPopupEdit' } } },
        { name: 'iniciofollowup', align: 'left', label: 'Início FUP', field: 'iniciofollowup', filterconfig: { filtertype: 'text', value: null, filtertype2: 'statusiniciofupfilter', value2: null, datatype2: JSON, sorted: true, updatemass: { value: null, type: 'inputstatusiniciofup' }, tooltip: '<div>Informe:</div><div><b>0</b> ou <b>SEM</b> para filtrar sem status</div><div><b>1</b> ou <b>CONECTA</b> para filtrar com status CONECTA</div><div><b>2</b> ou <b>FORNECEDOR</b> para filtrar com status FORNECEDOR</div>' } },
        { name: 'statusconfirmacaocoleta', align: 'left', label: 'St. Confirmação', field: 'statusconfirmacaocoleta', filterconfig: { filtertype: 'text', value: null, filtertype2: 'statusconfirmacaocoletafilter', value2: null, datatype2: JSON, sorted: true, tooltip: '<div>Informe:</div><div><b>0</b> ou <b>SEM</b> para filtrar sem status</div><div><b>1</b> ou <b>OK</b> para filtrar com status OK</div><div><b>2</b> ou <b>ERRO</b> para filtrar com status ERRO</div>', updatemass: { value: null, type: 'inputstatusconfirmacaocoleta' } } },
        { name: 'aprovacaooc', align: 'center', label: 'Aprovação OC', field: 'aprovacaooc', format: (val, row) => val ? Vue.prototype.$helpers.dateToBR(val) : '', filterconfig: { filtertype: 'datetimeinterval', value: null, sorted: true } },
        { name: 'dataagendamentocoleta', align: 'center', label: 'Agenda Coleta', field: 'dataagendamentocoleta', format: (val, row) => val ? Vue.prototype.$helpers.dateToBR(val) : '', filterconfig: { filtertype: 'datetimeinterval', value: null, sorted: true, updatemass: { value: null, type: 'date' } } },
        { name: 'dataconfirmacao', align: 'center', label: 'Data Confirmação', field: 'dataconfirmacao', format: (val, row) => val ? Vue.prototype.$helpers.dateToBR(val) : '', filterconfig: { filtertype: 'datetimeinterval', value: null, sorted: true, updatemass: { value: null, type: 'date' } } },
        { name: 'dataliberacao', align: 'center', label: 'Data Liberação', field: 'dataliberacao', format: (val, row) => val ? Vue.prototype.$helpers.dateToBR(val) : '', filterconfig: { filtertype: 'datetimeinterval', value: null, sorted: true, updatemass: { value: null, type: 'date' } } },
        { name: 'datahorafollowup', align: 'center', label: 'Data FUP', field: 'datahora_followup', format: (val, row) => val ? Vue.prototype.$helpers.datetimeToBR(val, false, true) : '', filterconfig: { filtertype: 'datetimeinterval', value: null, sorted: true, updatemass: { value: null, type: 'date' } } },
        { name: 'coletaid', align: 'center', label: 'Nº da Coleta', field: 'coletaid', filterconfig: { filtertype: 'text', value: null, filtertype2: 'multivalorfilter', value2: null, datatype2: JSON, sorted: true, updatemass: { value: null, type: 'integer' } } },
        { name: 'datacoleta', align: 'center', label: 'Data Coleta', field: 'datacoleta', format: (val, row) => val ? Vue.prototype.$helpers.dateToBR(val) : '', filterconfig: { filtertype: 'datetimeinterval', value: null, sorted: true } },
        { name: 'datapromessa', align: 'center', label: 'Data Promessa', field: 'datapromessa', format: (val, row) => val ? Vue.prototype.$helpers.dateToBR(val) : '', filterconfig: { filtertype: 'datetimeinterval', value: null, sorted: true } },
        { name: 'ordemcompra', align: 'center', label: 'O.C.', field: 'ordemcompra', filterconfig: { filtertype: 'text', value: null, filtertype2: 'multivalorfilter', value2: null, datatype2: JSON, sorted: true } },
        { name: 'ordemcompradig', align: 'center', label: 'Dígito', field: 'ordemcompradig', filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'observacao', align: 'left', label: 'Observação', field: 'observacao', filterconfig: { filtertype: 'text', filtertype2: 'checkexistefilter', value: null, value2: null, allowempty2: true, sorted: false } },
        { name: 'notafiscal', align: 'center', label: 'Nota Fiscal', field: 'notafiscal', filterconfig: { filtertype: 'text', value: null, filtertype2: 'multivalorfilter', value2: null, allowempty2: true, datatype2: JSON, sorted: true, updatemass: { value: null, type: 'integer' } } },
        { name: 'requisicao', align: 'left', label: 'Requisição', field: 'requisicao', style: 'max-width: 100px;', classes: 'ellipsis', filterconfig: { filtertype: 'text', value: null, filtertype2: 'multivalorfilter', value2: null, datatype2: JSON, sorted: true } },
        { name: 'itemnumerolinhapedido', align: 'center', label: 'Nº linha do item', field: 'itemnumerolinhapedido', style: 'max-width: 100px;', classes: 'ellipsis', filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'comprador', align: 'left', label: 'Comprador', field: 'comprador', style: 'max-width: 150px;', classes: 'ellipsis', filterconfig: { filtertype: 'text', value: null, filtertype2: 'compradorfilter', value2: null, datatype2: JSON, sorted: true } },
        { name: 'email', align: 'left', label: 'E-mail', field: 'email', style: 'max-width: 250px;', classes: 'ellipsis', filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'itemid', align: 'left', label: 'Cód. Item', field: 'itemid', filterconfig: { filtertype: 'text', value: null, filtertype2: 'itemidfilter', value2: null, datatype2: JSON, sorted: true } },
        { name: 'itemdescricao', align: 'left', label: 'Item', field: 'itemdescricao', filterconfig: { filtertype: 'text', value: null, filtertype2: 'itensdescricaofilter', value2: null, datatype2: JSON, sorted: true } },
        { name: 'qtdesolicitada', align: 'right', label: 'Qtde Sol.', field: 'qtdesolicitada', filterconfig: { filtertype: 'floatinterval', prefix: '', mask: '#,##', decimal: 2, value: null, sorted: true } },
        { name: 'qtderecebida', align: 'right', label: 'Qtde Receb', field: 'qtderecebida', filterconfig: { filtertype: 'floatinterval', prefix: '', mask: '#,##', decimal: 2, value: null, sorted: true } },
        { name: 'vlrunitario', align: 'right', label: 'Vlr Unit', field: 'vlrunitario', format: (val, row) => Vue.prototype.$helpers.formatRS(val, false, 2), filterconfig: { filtertype: 'floatinterval', prefix: 'R$', mask: '#,##', decimal: 2, value: null, sorted: true } },
        { name: 'qtdedevida', align: 'right', label: 'Qtde Dev', field: 'qtdedevida', filterconfig: { filtertype: 'floatinterval', prefix: '', mask: '#,##', decimal: 2, value: null, sorted: true } }
      ],
      loading: false
    }
  },
  async mounted () {
    var app = this
    await this.init()
    if (!app.usuariologado.pemitefup) throw new Error('Sem permissão de acesso')
    app.permiteoperacionalcoletasbaixar = app.$helpers.permite('followup.consulta')
    app.permiteoperacionalcoletasbaixardesfazer = app.$helpers.permite('followup.consulta')
    app.permiteoperacionalcoletascancelar = app.$helpers.permite('followup.consulta')
    app.permiteoperacionalcoletasconsulta = app.$helpers.permite('followup.consulta')
    app.permiteoperacionalcoletassave = app.$helpers.permite('followup.consulta')
    await app.getConfigLocal()
    if (app.$route.query) app.queryRead(app.$route.query)
    await app.refreshData(null)
  },
  computed: {
    usuariologado: function () {
      var app = this
      let u = null
      if (app.$store.state.usuario.logado) {
        if (app.$store.state.usuario.user) {
          u = app.$store.state.usuario.user
        }
      }
      return u
    }
  },
  methods: {
    async actImprimirListagem (format = 'pdf') {
      var app = this
      await app.dataset.showPrintListagem(app, format, true, app.visiblecolumns)
    },
    getToolTipForneceor (pRow) {
      var app = this
      if (!pRow) return
      var s = ''
      if (pRow.fornecedor ? pRow.fornecedor.id > 0 : false) {
        s = '<div>Razão Social: ' + pRow.fornecedor.razaosocial + '</div>'
        if (pRow.fornecedor.fantasia !== '') s = s + '<div>Fantasia: ' + pRow.fornecedor.fantasia + '</div>'
        if (pRow.fornecedor.cnpj !== '') s = s + '<div>CNPJ: ' + app.$helpers.mascaraDocCPFCNPJ(pRow.fornecedor.cnpj) + '</div>'
        s = s + '<div>ID: ' + pRow.fornecedor.id + '</div>'
        s = s + '<div class="q-mt-md text-caption text-weight-bold">Telefones</div>'
        if (pRow.fornecedor.fone1 ? pRow.fornecedor.fone1 !== '' : false) s = s + '<div>' + app.$helpers.mascaratel(pRow.fornecedor.fone1) + '</div>'
        if (pRow.fornecedor.fone2 ? pRow.fornecedor.fone2 !== '' : false) s = s + '<div>' + app.$helpers.mascaratel(pRow.fornecedor.fone2) + '</div>'
        if (pRow.fornecedor.endereco ? pRow.fornecedor.endereco.cidade.id > 0 : false) {
          s = s + '<div class="q-mt-md text-caption text-weight-bold">Endereço</div>'
          s = s + '<div>' + pRow.fornecedor.endereco.enderecocompletotext + '</div>'
        }
      } else {
        s = '<div>Razão Social: <b>' + pRow.fornecrazao + '</b></div>'
        s = s + '<div>CNPJ: <b>' + app.$helpers.mascaraCpfCnpj(pRow.forneccnpj) + '</b></div>'
        s = s + '<div>Cidade: ' + pRow.forneccidade + '/' + pRow.fornecuf + '</b></div>'
        if ((pRow.fornectelefone !== '') && (pRow.fornectelefone !== '-')) s = s + '<div>Fone: <b>' + pRow.fornectelefone + '</b></div>'
        s = s + '<div>id: #' + pRow.fornecedorid + '</div>'
      }
      return s
    },
    getToolTipItem (pRow) {
      if (!pRow) return
      var s = ''
      s = '<div>Item: <b>' + pRow.itemdescricao + '</b></div>'
      s = s + '<div>Cód. Item: <b>' + pRow.itemid + '</b></div>'
      s = s + '<div>Nº da linha do item: ' + pRow.itemnumerolinhapedido + '</b></div>'
      return s
    },
    async actConfigColuna () {
      var app = this
      var ret = await app.$helpers.dialogConfigTableColunas(app, app.visiblecolumns, app.columns)
      if (ret.ok) {
        app.showtable = false
        await app.ReorderPositionColumn(ret.data.visible, ret.data.cols)
        await app.saveConfigLocal()
        app.showtable = true
      }
    },
    async ReorderPositionColumn (pVisible, pCols) {
      var app = this
      app.columns = []
      for (let index = 0; index < pCols.length; index++) {
        app.columns.push(pCols[index])
      }
      app.visiblecolumns = []
      for (let index = 0; index < pVisible.length; index++) {
        app.visiblecolumns.push(pVisible[index])
      }
    },
    async getConfigLocal () {
      var app = this
      var cols = await app.$helpers.recoveryTableColunasLocalStorage(app, 'fupconsulta_v1')
      if (!cols) return
      var columnsnew = []
      app.visiblecolumns = []

      for (let index = 0; index < cols.length; index++) {
        var col = cols[index]
        var idxcolatual = -1
        for (let n = 0; n < app.columns.length; n++) {
          const colatual = app.columns[n]
          if (colatual.name === col.name) {
            columnsnew.push(colatual)
            idxcolatual = n
            if (col.visible) app.visiblecolumns.push(col.name)
            break
          }
        }
        if (idxcolatual >= 0) app.columns.splice(idxcolatual, 1)
      }
      for (let index = 0; index < app.columns.length; index++) {
        const col = app.columns[index]
        columnsnew.push(col)
        app.visiblecolumns.push(col.name)
      }
      app.columns = columnsnew
    },
    async saveConfigLocal () {
      var app = this
      var columnssave = []
      for (let index = 0; index < app.columns.length; index++) {
        var col = app.columns[index]
        var itemcol = {
          name: col.name,
          visible: app.visiblecolumns.indexOf(col.name) >= 0
        }
        columnssave.push(itemcol)
      }
      await app.$helpers.saveTableColunasLocalStorage(app, 'fupconsulta_v1', columnssave)
    },
    async onSetInputMass (field, value) {
      var app = this
      var dialog = app.$q.dialog({
        message: 'Salvando, aguarde...',
        progress: true, // we enable default settings
        color: 'blue',
        persistent: true, // we want the user to not be able to close it
        ok: false // we want the user to not be able to close it
      })

      var lista = []
      for (let index = 0; index < app.selected_rows.length; index++) {
        const row = app.selected_rows[index]
        var item = { id: row.id }
        if (field === 'erroagendastatus') {
          item['erroagendastatus'] = new FollowUpStatusErrosAgenda(value.status)
          item['erroagenda'] = value.erro
        } else if (field === 'errocoletastatus') {
          item['errocoletastatus'] = new FollowUpStatusErrosColeta(value.status)
          item['errocoleta'] = value.erro
        } else if (field === 'errodtpromessastatus') {
          item['errodtpromessastatus'] = new FollowUpStatusErrosDtPromessa(value.status)
          item['errodtpromessa'] = value.erro
        } else if (field === 'iniciofollowup') {
          item['iniciofollowup'] = new FollowUpStatusInicioFup(value)
        } else if (field === 'statusconfirmacaocoleta') {
          item['statusconfirmacaocoleta'] = new FollowUpStatusConfirmacaoColeta(value)
        } else {
          item[field] = value
        }
        lista.push(item)
      }
      var ret = await app.dataset.savemass(lista)
      if (!ret.ok) {
        dialog.hide()
        if (ret.msg ? ret.msg !== '' : false) app.$helpers.showDialog(ret)
      } else {
        app.$q.notify({
          message: 'Informações atualizadas com sucesso!',
          color: 'positive',
          caption: ret.msg,
          actions: [
            { label: 'OK', color: 'white', handler: () => { /* ... */ } }
          ]
        })
        app.refreshData(null)
        dialog.hide()
      }
    },
    async init () {
      var app = this
      try {
        app.loading = true
        app.error = null
        app.consultaret = { ok: false }
        if (!app.usuariologado) throw new Error('Nenhum usuário logado')
        if (!app.usuariologado.pemitefup) throw new Error('Sem permissão de acesso')
        // consultatipo: 'chave',
        // consultanumero: '',
        // consultachave: '',
        if (app.$route.query.cnpj ? app.$route.query.cnpj !== '' : false) app.cnpjstart = app.$route.query.cnpj
        if (app.$route.query.chave ? app.$route.query.chave !== '' : false) {
          app.consultatipo = 'chave'
          app.consultachave = app.$route.query.chave
          app.onInputChave(app.consultachave)
        }
        // if (app.$route.query.modo) app.dadosconsulta.modo = app.$route.query.modo
        // if ((app.dadosconsulta.modo !== 'chave') && (app.dadosconsulta.modo !== 'dados')) app.dadosconsulta.modo = 'chave'
        // if (app.$route.query.cnpjemitente) app.dadosconsulta.cnpjemitente = app.$route.query.cnpjemitente
        // if (app.$route.query.cnpjdestinatario) app.dadosconsulta.cnpjdestinatario = app.$route.query.cnpjdestinatario
        await app.refreshData()
      } catch (error) {
        app.loading = false
        app.error = error.message
      }
      app.loading = false
    },
    async onSetInput (pRow, field, initialValue) {
      var app = this
      var dialog = app.$q.dialog({
        message: 'Salvando, aguarde...',
        progress: true, // we enable default settings
        color: 'blue',
        persistent: true, // we want the user to not be able to close it
        ok: false // we want the user to not be able to close it
      })
      var lista = []
      var item = { id: pRow.id }
      if (field === 'erroagendastatus') {
        item['erroagendastatus'] = new FollowUpStatusErrosAgenda(app.inputStatusAgendaPopupEdit.status)
        item['erroagenda'] = app.inputStatusAgendaPopupEdit.erro
      } else if (field === 'errocoletastatus') {
        item['errocoletastatus'] = new FollowUpStatusErrosColeta(app.inputStatusAgendaPopupEdit.status)
        item['errocoleta'] = app.inputStatusAgendaPopupEdit.erro
      } else if (field === 'errodtpromessastatus') {
        item['errodtpromessastatus'] = new FollowUpStatusErrosDtPromessa(app.inputStatusAgendaPopupEdit.status)
        item['errodtpromessa'] = app.inputStatusAgendaPopupEdit.erro
      } else {
        item[field] = pRow[field]
      }
      lista.push(item)
      var ret = await app.dataset.savemass(lista)
      if (!ret.ok) {
        dialog.hide()
        pRow[field] = initialValue
        if (ret.msg ? ret.msg !== '' : false) app.$helpers.showDialog(ret)
      } else {
        if (field === 'erroagendastatus') {
          pRow['erroagendastatus'] = new FollowUpStatusErrosAgenda(app.inputStatusAgendaPopupEdit.status)
          pRow['erroagenda'] = app.inputStatusAgendaPopupEdit.erro
        }
        if (field === 'errocoletastatus') {
          pRow['errocoletastatus'] = new FollowUpStatusErrosColeta(app.inputStatusAgendaPopupEdit.status)
          pRow['errocoleta'] = app.inputStatusAgendaPopupEdit.erro
        }
        if (field === 'errodtpromessastatus') {
          pRow['errodtpromessastatus'] = new FollowUpStatusErrosDtPromessa(app.inputStatusAgendaPopupEdit.status)
          pRow['errodtpromessa'] = app.inputStatusAgendaPopupEdit.erro
        }
        app.inputStatusAgendaPopupEdit.status = null
        app.inputStatusAgendaPopupEdit.erro = null
        app.$q.notify({
          message: 'Informação atualizada com sucesso!',
          color: 'positive',
          caption: ret.msg,
          actions: [
            { label: 'OK', color: 'white', handler: () => { /* ... */ } }
          ]
        })
        dialog.hide()
      }
    },
    async actEditRow (pRow) {
      var app = this
      if (typeof pRow === 'undefined') return
      var item = new FollowUp(pRow)
      var lista = []
      lista.push(item)
      var ret = await app.dataset.showEditorRapido(app, lista)
      if (!ret.ok) {
        if (ret.msg ? ret.msg !== '' : false) app.$helpers.showDialog(ret)
      } else {
        app.refreshData()
      }
    },
    async actLogView (pRow) {
      var app = this
      if (typeof pRow === 'undefined') return
      var item = new FollowUp(pRow)
      var ret = await item.showLogView(app)
      if (!ret.ok) {
        if (ret.msg ? ret.msg !== '' : false) app.$helpers.showDialog(ret)
      } else {
        app.refreshData()
      }
    },
    async actEditRowLista () {
      var app = this
      var lista = []
      for (let index = 0; index < app.selected_rows.length; index++) {
        const element = app.selected_rows[index]
        lista.push(element)
      }
      var ret = await app.dataset.showEditorRapido(app, lista)
      if (!ret.ok) {
        if (ret.msg ? ret.msg !== '' : false) app.$helpers.showDialog(ret)
      } else {
        app.refreshData()
      }
    },
    async queryRead () {
      var app = this
      var pQuery = app.$route.query
      if (pQuery) {
        if (pQuery.pagesize) {
          var pagesize = parseInt(pQuery.pagesize)
          if (app.$qtable.rowsperpageoptions.indexOf(pagesize) >= 0) app.dataset.pagination.rowsPerPage = pagesize
        }
        if (pQuery.page) app.dataset.pagination.page = parseInt(pQuery.page)

        var sortby = null
        if (pQuery.sortby) {
          sortby = JSON.parse(pQuery.sortby)
          if (Object.keys(sortby).length > 0) app.orderbylist = sortby
        }

        for (let index = 0; index < app.columns.length; index++) {
          const element = app.columns[index]
          if (element.filterconfig) {
            if (element.filterconfig.filtertype2 !== '') {
              if ((pQuery[element.name + '2']) && (pQuery[element.name + '2'] !== '')) {
                if (element.filterconfig.datatype2 === JSON) {
                  element.filterconfig.value2 = JSON.parse(pQuery[element.name + '2'].split(','))
                } else {
                  element.filterconfig.value2 = pQuery[element.name + '2'].split(',').map(element.filterconfig.datatype2 ? element.filterconfig.datatype2 : Number)
                }
              }
            }
            if (element.filterconfig.filtertype === 'text') {
              if ((pQuery[element.name]) && (pQuery[element.name] !== '')) element.filterconfig.value = pQuery[element.name]
            }
            if (element.filterconfig.filtertype === 'datetimeinterval') {
              element.filterconfig.value = {}
              if (pQuery[element.name + 'i']) element.filterconfig.value.valuei = pQuery[element.name + 'i']
              if (pQuery[element.name + 'f']) element.filterconfig.value.valuef = pQuery[element.name + 'f']
              if (Object.keys(element.filterconfig.value).length === 0) element.filterconfig.value = null
            }
            if (element.filterconfig.filtertype === 'floatinterval') {
              element.filterconfig.value = {}
              if (pQuery[element.name + 'i']) element.filterconfig.value.valuei = pQuery[element.name + 'i']
              if (pQuery[element.name + 'f']) element.filterconfig.value.valuef = pQuery[element.name + 'f']
              if (Object.keys(element.filterconfig.value).length === 0) element.filterconfig.value = null
            }
          }
        }
      }
    },
    async actClearFilter (props, pIndex = -1) {
      var app = this
      var idx = pIndex
      if (props) {
        for (let index = 0; index < app.columns.length; index++) {
          const element = app.columns[index]
          if (element.name === props.col.name) {
            idx = index
            break
          }
        }
      }
      app.columns[idx].filterconfig.value = null
      app.refreshData(null)
    },
    actChangeSortBy (props) {
      var app = this
      if (props) {
        var idx = -1
        for (let index = 0; index < app.columns.length; index++) {
          const element = app.columns[index]
          if (element.name === props.col.name) {
            idx = index
            break
          }
        }
        if (idx >= 0) {
          if (app.columns[idx].filterconfig.sorted) {
            var name = app.columns[idx].name
            var ord = app.orderbylist[name]
            if (ord) {
              ord = (ord === 'asc') ? 'desc' : 'asc'
            } else {
              ord = 'asc'
            }
            app.orderbylist = {}
            app.orderbylist[name] = ord
          }
        }
      }
      app.refreshData(null)
    },
    actInputFilter () {
      var app = this
      if (app.loading) return
      app.refreshData(null)
    },
    async actShowFilter (props) {
      var app = this
      if (props) {
        var config = null
        var ret = null
        if (props.col.filterconfig) {
          if (props.col.filterconfig.filtertype ? props.col.filterconfig.filtertype === 'datetimeinterval' : false) {
            config = props.col.filterconfig.value
            if (!config) config = {}
            config['title'] = 'Período :: ' + props.col.label
            ret = await this.$helpers.dialogFilterDateTimeInterval(this, config)
            if (ret ? ret.ok : null) {
              if (ret.dados) {
                props.col.filterconfig.value = ret.dados
              } else {
                props.col.filterconfig.value = null
              }
              app.refreshData(null)
            }
            return false
          }
          if (props.col.filterconfig.filtertype ? props.col.filterconfig.filtertype === 'floatinterval' : false) {
            config = props.col.filterconfig.value
            if (!config) config = {}
            config['title'] = 'Intervalo de valores :: ' + props.col.label
            config['prefix'] = props.col.filterconfig.prefix ? props.col.filterconfig.prefix : ''
            config['mask'] = props.col.filterconfig.mask ? props.col.filterconfig.mask : '#'
            config['decimal'] = props.col.filterconfig.decimal ? props.col.filterconfig.decimal : 0
            ret = await this.$helpers.dialogFilterFloatInterval(this, config)
            if (ret ? ret.ok : null) {
              if (ret.dados) {
                props.col.filterconfig.value = ret.dados
              } else {
                props.col.filterconfig.value = null
              }
              app.refreshData(null)
            }
            return false
          }
        }
      }
    },
    async actShowFilter2 (props) {
      var app = this
      if (props) {
        var config = null
        var ret = null
        if (props.col.filterconfig) {
          if (props.col.filterconfig.filtertype2 ? props.col.filterconfig.filtertype2 === 'cidadefilter' : false) {
            config = {}
            config['title'] = 'Filtro :: ' + props.col.label
            config['multiple'] = true
            config['fastadd'] = true
            config['allowempty'] = true
            config['value'] = props.col.filterconfig.value2
            ret = await this.$helpers.dialogFilterCidadesMultiplo(this, config)
            if (ret ? ret.ok : null) {
              if (ret.dados) {
                props.col.filterconfig.value2 = ret.dados
              } else {
                props.col.filterconfig.value2 = null
              }
              app.refreshData(null)
            }
            return false
          }
          if (props.col.filterconfig.filtertype2 ? props.col.filterconfig.filtertype2 === 'clientefilter' : false) {
            config = {}
            config['title'] = 'Filtro :: ' + props.col.label
            config['multiple'] = true
            config['fastadd'] = true
            config['allowempty'] = true
            config['value'] = props.col.filterconfig.value2
            ret = await this.$helpers.dialogFilterClientes(this, config)
            if (ret ? ret.ok : null) {
              if (ret.dados) {
                props.col.filterconfig.value2 = ret.dados
              } else {
                props.col.filterconfig.value2 = null
              }
              app.refreshData(null)
            }
            return false
          }
          if (props.col.filterconfig.filtertype2 ? props.col.filterconfig.filtertype2 === 'itensdescricaofilter' : false) {
            config = {}
            config['title'] = 'Fitro por descrição do item'
            config['value'] = props.col.filterconfig.value2
            ret = await app.dataset.dialogFilterItensDescricao(app, config)
            if (ret ? ret.ok : null) {
              if (ret.dados) {
                props.col.filterconfig.value2 = ret.dados
              } else {
                props.col.filterconfig.value2 = null
              }
              app.refreshData(null)
            }
            return false
          }
          if (props.col.filterconfig.filtertype2 ? props.col.filterconfig.filtertype2 === 'itemidfilter' : false) {
            config = {}
            config['title'] = 'Fitro por número do item'
            config['value'] = props.col.filterconfig.value2
            ret = await app.dataset.ShowDialogFilterItemid(app, config)
            if (ret ? ret.ok : null) {
              if (ret.dados) {
                props.col.filterconfig.value2 = ret.dados
              } else {
                props.col.filterconfig.value2 = null
              }
              app.refreshData(null)
            }
            return false
          }
          if (props.col.filterconfig.filtertype2 ? props.col.filterconfig.filtertype2 === 'clientefollowupidfilter' : false) {
            config = {}
            config['title'] = 'Fitro por id de FUP dos clientes'
            config['value'] = props.col.filterconfig.value2
            ret = await app.dataset.dialogFilterClientesFUPID(app, config)
            if (ret ? ret.ok : null) {
              if (ret.dados) {
                props.col.filterconfig.value2 = ret.dados
              } else {
                props.col.filterconfig.value2 = null
              }
              app.refreshData(null)
            }
            return false
          }
          if (props.col.filterconfig.filtertype2 ? props.col.filterconfig.filtertype2 === 'compradorfilter' : false) {
            config = {}
            config['title'] = 'Fitro por compradores'
            config['value'] = props.col.filterconfig.value2
            ret = await app.dataset.dialogFilterComprador(app, config)
            if (ret ? ret.ok : null) {
              if (ret.dados) {
                props.col.filterconfig.value2 = ret.dados
              } else {
                props.col.filterconfig.value2 = null
              }
              app.refreshData(null)
            }
            return false
          }
          if (props.col.filterconfig.filtertype2 ? props.col.filterconfig.filtertype2 !== '' : false) {
            if (['statusagendafilter', 'statuscoletafilter', 'statusdtpromessafilter'].indexOf(props.col.filterconfig.filtertype2) >= 0) {
              config = {}
              switch (props.col.filterconfig.filtertype2) {
                case 'statusagendafilter':
                  config['tipoerro'] = 'agenda'
                  config['title'] = 'Fitro por status do agendamento'
                  break
                case 'statuscoletafilter':
                  config['tipoerro'] = 'coleta'
                  config['title'] = 'Fitro por status da coleta'
                  break
                case 'statusdtpromessafilter':
                  config['tipoerro'] = 'dtpromessa'
                  config['title'] = 'Fitro por status da data de promessa'
                  break
              }
              config['value'] = props.col.filterconfig.value2
              ret = await app.dataset.ShowDialogFilterStatusAgenda(app, config)
              if (ret ? ret.ok : null) {
                if (ret.dados) {
                  props.col.filterconfig.value2 = ret.dados
                } else {
                  props.col.filterconfig.value2 = null
                }
                app.refreshData(null)
              }
              return false
            }

            if (['statusiniciofupfilter', 'statusconfirmacaocoletafilter'].indexOf(props.col.filterconfig.filtertype2) >= 0) {
              config = {}
              switch (props.col.filterconfig.filtertype2) {
                case 'statusiniciofupfilter':
                  config['options'] = [
                    {
                      label: 'Sem status',
                      value: '0',
                      color: 'grey'
                    },
                    {
                      label: 'Conecta',
                      value: '1',
                      color: 'orange'
                    },
                    {
                      label: 'Fornecedor',
                      value: '2',
                      color: 'blue'
                    }
                  ]
                  config['title'] = 'Fitro por status do Início do Follow Up'
                  break
                case 'statusconfirmacaocoletafilter':
                  config['options'] = [
                    {
                      label: 'Sem status',
                      value: '0',
                      color: 'grey'
                    },
                    {
                      label: 'Ok',
                      value: '1',
                      color: 'green'
                    },
                    {
                      label: 'Erro',
                      value: '2',
                      color: 'erro'
                    }
                  ]
                  config['title'] = 'Fitro por status da Confirmação da Coleta'
                  break
              }
              config['value'] = props.col.filterconfig.value2
              ret = await app.dataset.ShowDialogFilterStatusGeral(app, config)
              if (ret ? ret.ok : null) {
                if (ret.dados) {
                  props.col.filterconfig.value2 = ret.dados
                } else {
                  props.col.filterconfig.value2 = null
                }
                app.refreshData(null)
              }
              return false
            }
            if (['checkexistefilter'].indexOf(props.col.filterconfig.filtertype2) >= 0) {
              config = {}
              config['splitchar'] = ','
              config['type'] = 'integer'
              config['title'] = 'Fitro por ' + props.col.label
              config['value'] = props.col.filterconfig.value2
              if (props.col.filterconfig.allowempty2) config['allowempty'] = true
              ret = await app.dataset.ShowDialogFilterCheckExist(app, config)
              if (ret ? ret.ok : null) {
                if (ret.dados) {
                  props.col.filterconfig.value2 = ret.dados
                } else {
                  props.col.filterconfig.value2 = null
                }
                app.refreshData(null)
              }
              return false
            }
            if (['multivalorfilter'].indexOf(props.col.filterconfig.filtertype2) >= 0) {
              config = {}
              config['splitchar'] = ','
              config['type'] = 'integer'
              config['title'] = 'Fitro por ' + props.col.label
              config['value'] = props.col.filterconfig.value2
              if (props.col.filterconfig.allowempty2) config['allowempty'] = true
              ret = await app.dataset.ShowDialogFilterMultiGeral(app, config)
              if (ret ? ret.ok : null) {
                if (ret.dados) {
                  props.col.filterconfig.value2 = ret.dados
                } else {
                  props.col.filterconfig.value2 = null
                }
                app.refreshData(null)
              }
              return false
            }
          }
        }
      }
    },
    actApplyFastFilter (props, pValue) {
      var app = this
      if (props) {
        for (let index = 0; index < app.columns.length; index++) {
          const element = app.columns[index]
          if ((element.name === props.col.name) && (element.filterconfig)) {
            element.filterconfig.value = pValue
            break
          }
        }
      }
      app.refreshData(null)
    },
    async actEditCliente (pIDCliente) {
      var app = this
      var row = new Cliente()
      row.id = pIDCliente
      var ret = await row.dialogAddOrEdit(app)
      if (ret.ok) {
        // app.refreshData(null)
      } else {
        if (ret.msg ? ret.msg !== '' : false) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {
          })
        }
      }
    },
    async refreshData (props) {
      var app = this
      app.loading = true
      app.msgError = ''
      app.dataset.readPropsTable(props)
      if (app.$q.platform.is.mobile) app.dataset.pagination.rowsPerPage = 50
      app.dataset.query = {
        perpage: app.dataset.pagination.rowsPerPage,
        page: app.dataset.pagination.page
      }
      for (let index = 0; index < app.columns.length; index++) {
        const element = app.columns[index]
        if (element.filterconfig) {
          if (element.filterconfig.filtertype2 !== '') {
            if (element.filterconfig.datatype2 === JSON) {
              if (element.filterconfig.value2) app.dataset.query[element.name + '2'] = JSON.stringify(element.filterconfig.value2)
            } else {
              if (element.filterconfig.value2) app.dataset.query[element.name + '2'] = element.filterconfig.value2.join(',')
            }
          }
          if (element.filterconfig.filtertype === 'text') {
            if ((element.filterconfig.value) && (element.filterconfig.value !== '')) {
              app.dataset.query[element.name] = element.filterconfig.value
            }
          }
          if (element.filterconfig.filtertype === 'datetimeinterval') {
            if (element.filterconfig.value) {
              if (element.filterconfig.value.valuei) app.dataset.query[element.name + 'i'] = Vue.prototype.$helpers.strDateToFormated(element.filterconfig.value.valuei, 'YYYY/MM/DD', 'YYYY-MM-DD')
              if (element.filterconfig.value.valuef) app.dataset.query[element.name + 'f'] = Vue.prototype.$helpers.strDateToFormated(element.filterconfig.value.valuef, 'YYYY/MM/DD', 'YYYY-MM-DD')
            }
          }
          if (element.filterconfig.filtertype === 'floatinterval') {
            if (element.filterconfig.value) {
              if (element.filterconfig.value.valuei) app.dataset.query[element.name + 'i'] = element.filterconfig.value.valuei
              if (element.filterconfig.value.valuef) app.dataset.query[element.name + 'f'] = element.filterconfig.value.valuef
            }
          }
        }
      }

      app.dataset.orderby = null
      if (app.orderbylist) {
        var c = Object.keys(app.orderbylist).length
        if (c > 0) app.dataset.orderby = app.orderbylist
      }

      var ret = await app.dataset.fetch()
      if (ret.ok) {
        app.rows = app.dataset.itens
        app.selected_rows = []
        // atualiza url
        if (app.dataset.pagination.page !== null && app.dataset.pagination.page > 1) app.dataset.query.page = app.dataset.pagination.page
        if ((app.dataset.pagination.rowsPerPage !== null) && (app.dataset.pagination.rowsPerPage > 0) && (parseInt(app.dataset.pagination.rowsPerPage) !== 20)) app.dataset.query.pagesize = app.dataset.pagination.rowsPerPage
        if (app.dataset.orderby !== null) app.dataset.query.sortby = JSON.stringify(app.dataset.orderby)
        app.dataset.query.t = new Date().getTime()
        try {
          app.$router.replace({ name: app.$route.name, query: app.dataset.query, replace: true, append: false })
        } catch (error) {
          console.error(error)
        }
      } else {
        if (ret.msg ? ret.msg !== '' : false) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {
          })
        }
      }
      app.loading = false
    }
  }
}
</script>
