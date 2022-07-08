<template>
<q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.platform.is.mobile">
    <q-card class="q-dialog-plugin" :class="$q.platform.is.mobile ? '' : 'full-90'">

    <q-toolbar class="bg-primary text-white" >
      <q-toolbar-title>
        Notas Fiscais de Coletas
      </q-toolbar-title>
      <q-btn flat stretch icon="done_outline" label="Importar" v-if="selected_rows ? selected_rows.length > 0 : false"
          @click="actOk(selected_rows)" class="q-mr-md">
          <q-badge color="indigo" text-color="white" :label="selected_rows.length" class="q-ml-sm" />
      </q-btn>
      <q-btn flat stretch icon="close" @click="actClose"/>
    </q-toolbar>
    <q-card-section class="q-pa-0 q-ma-0">
      <div class="row">
        <div class="col-12">
          <q-input outlined dense debounce="500" v-model="filter" placeholder="Procurar">
            <template v-slot:append>
              <q-icon name="search" />
              <q-btn color="grey" icon="sync"  @click="refreshData(null)" dense round flat :loading="loading" />
            </template>
          </q-input>
        </div>
      </div>
    </q-card-section>
    <q-card-section class="q-pa-0 q-ma-0">
      <div class="full-width"  v-if="rows" id="sticky-table-scroll">
        <div class="col-sm-12" >
          <q-table :data="rows" :columns="columns" dense :loading="loading" id="sticky-table"
            :pagination.sync="dataset.pagination" row-key="id" :rows-per-page-options="$qtable.rowsperpageoptions"
            @request="refreshData" :filter="filter" flat class="full-width"
            selection="multiple"
            :selected.sync="selected_rows"
            >
            <template v-slot:loading>
              <q-inner-loading showing color="primary" >
                <q-spinner color="primary" size="3em" :thickness="3" />
                <div class="text-h6 text-primary">Consultando...</div>
              </q-inner-loading>
            </template>
            <!-- headers -->
            <template v-slot:header-cell-dhlocal_data="props">
                <q-th :props="props" >
                  <span  @click="actChangeSortBy(props)" class="cursor-pointer" >
                    {{ props.col.label }}
                    <q-icon class="q-ml-xs text-black" size="14px"
                      v-if="orderbylist.dhlocal_data"
                      :color="!orderbylist.dhlocal_data ? 'red' : (orderbylist.dhlocal_data  === 'asc' ? 'green' : 'red')"
                      :name="!orderbylist.dhlocal_data ? 'fas fa-sort-alpha-up-alt' : (orderbylist.dhlocal_data  === 'asc' ? 'fas fa-sort-alpha-down' : 'fas fa-sort-alpha-up-alt')"
                    />
                    <q-tooltip :delay="1000">{{!orderbylist.dhlocal_data ? 'Nenhum ordenação - clique para ordenar por ' + props.col.name : (orderbylist.dhlocal_data  === 'asc' ? 'Ordem crescente' : 'Ordem decrescente') }}</q-tooltip>
                  </span>
                  <div v-if="props.col.filterconfig ? props.col.filterconfig.value !=null : false" >
                    <q-chip label="Filtrado" dense color="accent" text-color="white" clickable  @click="actShowFilter(props)"
                      removable @remove="actClearFilter(props)" class="text-caption truncate-chip-labels "
                      style="max-width: 100px"
                      >
                      <div class="ellipsis">
                        <span v-if="props.col.filterconfig.value.dti" v-html="'>=' + $helpers.dateToBR(props.col.filterconfig.value.dti)" />
                        <span v-if="props.col.filterconfig.value.dti && props.col.filterconfig.value.dtf"> à </span>
                        <span v-if="props.col.filterconfig.value.dtf" v-html="'<=' + $helpers.dateToBR(props.col.filterconfig.value.dtf)" />
                      </div>
                      <q-tooltip :delay="1000" >
                        <span v-if="props.col.filterconfig.value.dti">De {{$helpers.dateToBR(props.col.filterconfig.value.dti)}}</span>
                        <span v-if="props.col.filterconfig.value.dti && props.col.filterconfig.value.dtf"> à </span>
                        <span v-if="props.col.filterconfig.value.dtf">{{$helpers.dateToBR(props.col.filterconfig.value.dtf)}}</span>
                      </q-tooltip>
                    </q-chip>
                  </div>
                  <div v-else >
                    <q-chip label="filtrar" dense color="grey-3" text-color="grey-7" clickable  @click="actShowFilter(props)" class="text-caption" icon="filter_alt"  />
                  </div>
                </q-th>
            </template>
            <template v-slot:header-cell-idcoleta="props">
                <q-th :props="props" >
                  <span  @click="actChangeSortBy(props)" class="cursor-pointer" >
                    {{ props.col.label }}
                    <q-icon class="q-ml-xs text-black" size="14px"
                      v-if="orderbylist.idcoleta"
                      :color="!orderbylist.idcoleta ? 'red' : (orderbylist.idcoleta  === 'asc' ? 'green' : 'red')"
                      :name="!orderbylist.idcoleta ? 'fas fa-sort-alpha-up-alt' : (orderbylist.idcoleta  === 'asc' ? 'fas fa-sort-alpha-down' : 'fas fa-sort-alpha-up-alt')"
                    />
                    <q-tooltip :delay="1000">{{!orderbylist.idcoleta ? 'Nenhum ordenação - clique para ordenar por ' + props.col.name : (orderbylist.idcoleta  === 'asc' ? 'Ordem crescente' : 'Ordem decrescente') }}</q-tooltip>
                  </span>
                  <div class="full-width">
                    <q-input type="text" input-class="text-accent" color="accent" dense v-model="props.col.filterconfig.value" clearable @input="actInputFilter()" :debounce="700" :loading="loading" />
                  </div>
                </q-th>
            </template>
            <template v-slot:header-cell-coletaavulsa="props">
                <q-th :props="props" >
                  <span  @click="actChangeSortBy(props)" class="cursor-pointer" >
                    {{ props.col.label }}
                    <q-icon class="q-ml-xs text-black" size="14px"
                      v-if="orderbylist.coletaavulsa"
                      :color="!orderbylist.coletaavulsa ? 'red' : (orderbylist.coletaavulsa  === 'asc' ? 'green' : 'red')"
                      :name="!orderbylist.coletaavulsa ? 'fas fa-sort-alpha-up-alt' : (orderbylist.coletaavulsa  === 'asc' ? 'fas fa-sort-alpha-down' : 'fas fa-sort-alpha-up-alt')"
                    />
                    <q-tooltip :delay="1000">{{!orderbylist.coletaavulsa ? 'Nenhum ordenação - clique para ordenar por ' + props.col.name : (orderbylist.coletaavulsa  === 'asc' ? 'Ordem crescente' : 'Ordem decrescente') }}</q-tooltip>
                  </span>
                  <div v-if="props.col.filterconfig ? props.col.filterconfig.value : false" >
                    <q-chip :label="'=' + props.col.filterconfig.value" dense color="accent" text-color="white" clickable  @click="actShowFilter(props)"
                      removable @remove="actClearFilter(props)" class="text-caption truncate-chip-labels "
                      style="max-width: 40px"
                      />
                    <q-tooltip :delay="1500">{{props.col.filterconfig.value}}</q-tooltip>
                  </div>
                  <div v-else >
                    <q-chip  dense color="grey-3" text-color="grey-7" clickable  @click="actShowFilter(props)" class="text-caption" icon="filter_alt"  />
                  </div>
                </q-th>
            </template>
            <template v-slot:header-cell-status="props">
                <q-th :props="props" >
                  <span  @click="actChangeSortBy(props)" class="cursor-pointer" >
                    {{ props.col.label }}
                    <q-icon class="q-ml-xs text-black" size="14px"
                      v-if="orderbylist.status"
                      :color="!orderbylist.status ? 'red' : (orderbylist.status  === 'asc' ? 'green' : 'red')"
                      :name="!orderbylist.status ? 'fas fa-sort-alpha-up-alt' : (orderbylist.status  === 'asc' ? 'fas fa-sort-alpha-down' : 'fas fa-sort-alpha-up-alt')"
                    />
                    <q-tooltip :delay="1000">{{!orderbylist.status ? 'Nenhum ordenação - clique para ordenar por ' + props.col.name : (orderbylist.status  === 'asc' ? 'Ordem crescente' : 'Ordem decrescente') }}</q-tooltip>
                  </span>
                  <div v-if="props.col.filterconfig ? (props.col.filterconfig.value ? props.col.filterconfig.value.length > 0 : false) : false" >
                    <q-chip :label="'=' + props.col.filterconfig.value.join(', ')" dense color="accent" text-color="white" clickable  @click="actShowFilter(props)"
                      removable @remove="actClearFilter(props)" class="text-caption truncate-chip-labels "
                      style="max-width: 40px"
                      />
                    <q-tooltip :delay="700">{{props.col.filterconfig.value.join(', ')}}</q-tooltip>
                  </div>
                  <div v-else >
                    <q-chip  dense color="grey-3" text-color="grey-7" clickable  @click="actShowFilter(props)" class="text-caption" icon="filter_alt"  />
                  </div>
                </q-th>
            </template>
            <template v-slot:header-cell-notanumero="props">
              <q-th :props="props" >
                <span  @click="actChangeSortBy(props)" class="cursor-pointer" >
                  {{ props.col.label }}
                  <q-icon class="q-ml-xs text-black" size="14px"
                    v-if="orderbylist.notanumero"
                    :color="!orderbylist.notanumero ? 'red' : (orderbylist.notanumero  === 'asc' ? 'green' : 'red')"
                    :name="!orderbylist.notanumero ? 'fas fa-sort-alpha-up-alt' : (orderbylist.notanumero  === 'asc' ? 'fas fa-sort-alpha-down' : 'fas fa-sort-alpha-up-alt')"
                  />
                  <q-tooltip :delay="1000">{{!orderbylist.notanumero ? 'Nenhum ordenação - clique para ordenar por ' + props.col.name : (orderbylist.notanumero  === 'asc' ? 'Ordem crescente' : 'Ordem decrescente') }}</q-tooltip>
                </span>
                <div style="width: 70px;">
                  <q-input type="text" input-class="text-accent" color="accent" dense v-model="props.col.filterconfig.value" clearable @input="actInputFilter()" :debounce="700" :loading="loading" />
                </div>
              </q-th>
            </template>
            <template v-slot:header-cell-remetentenome="props">
              <q-th :props="props" >
                <span @click="actChangeSortBy(props)" class="cursor-pointer" >
                  {{ props.col.label }}
                  <q-icon class="q-ml-xs text-black" size="14px"
                    v-if="orderbylist.remetentenome"
                    :color="!orderbylist.remetentenome ? 'red' : (orderbylist.remetentenome  === 'asc' ? 'green' : 'red')"
                    :name="!orderbylist.remetentenome ? 'fas fa-sort-alpha-up-alt' : (orderbylist.remetentenome  === 'asc' ? 'fas fa-sort-alpha-down' : 'fas fa-sort-alpha-up-alt')"
                  />
                  <q-tooltip :delay="1000">{{!orderbylist.remetentenome ? 'Nenhum ordenação - clique para ordenar por ' + props.col.name : (orderbylist.remetentenome  === 'asc' ? 'Ordem crescente' : 'Ordem decrescente') }}</q-tooltip>
                </span>
                <div class="full-width">
                  <q-input type="text" input-class="text-accent" color="accent" dense v-model="props.col.filterconfig.value" clearable @input="actInputFilter()" :debounce="700" :loading="loading" />
                </div>
              </q-th>
            </template>
            <template v-slot:header-cell-destinatarionome="props">
              <q-th :props="props" >
                <span @click="actChangeSortBy(props)" class="cursor-pointer" >
                  {{ props.col.label }}
                  <q-icon class="q-ml-xs text-black" size="14px"
                    v-if="orderbylist.destinatarionome"
                    :color="!orderbylist.destinatarionome ? 'red' : (orderbylist.destinatarionome  === 'asc' ? 'green' : 'red')"
                    :name="!orderbylist.destinatarionome ? 'fas fa-sort-alpha-up-alt' : (orderbylist.destinatarionome  === 'asc' ? 'fas fa-sort-alpha-down' : 'fas fa-sort-alpha-up-alt')"
                  />
                  <q-tooltip :delay="1000">{{!orderbylist.destinatarionome ? 'Nenhum ordenação - clique para ordenar por ' + props.col.name : (orderbylist.destinatarionome  === 'asc' ? 'Ordem crescente' : 'Ordem decrescente') }}</q-tooltip>
                </span>
                <div class="full-width">
                  <q-input type="text" input-class="text-accent" color="accent" dense v-model="props.col.filterconfig.value" clearable @input="actInputFilter()" :debounce="700" :loading="loading" />
                </div>
              </q-th>
            </template>
            <template v-slot:header-cell-remetentecnpj="props">
              <q-th :props="props" >
                <span @click="actChangeSortBy(props)" class="cursor-pointer" >
                  {{ props.col.label }}
                  <q-icon class="q-ml-xs text-black" size="14px"
                    v-if="orderbylist.remetentecnpj"
                    :color="!orderbylist.remetentecnpj ? 'red' : (orderbylist.remetentecnpj  === 'asc' ? 'green' : 'red')"
                    :name="!orderbylist.remetentecnpj ? 'fas fa-sort-alpha-up-alt' : (orderbylist.remetentecnpj  === 'asc' ? 'fas fa-sort-alpha-down' : 'fas fa-sort-alpha-up-alt')"
                  />
                  <q-tooltip :delay="1000">{{!orderbylist.remetentecnpj ? 'Nenhum ordenação - clique para ordenar por ' + props.col.name : (orderbylist.remetentecnpj  === 'asc' ? 'Ordem crescente' : 'Ordem decrescente') }}</q-tooltip>
                </span>
                <div class="full-width">
                  <q-input type="text" input-class="text-accent" color="accent" dense v-model="props.col.filterconfig.value" clearable @input="actInputFilter()" :debounce="700" :loading="loading" />
                </div>
              </q-th>
            </template>
            <template v-slot:header-cell-destinatariocnpj="props">
              <q-th :props="props" >
                <span @click="actChangeSortBy(props)" class="cursor-pointer" >
                  {{ props.col.label }}
                  <q-icon class="q-ml-xs text-black" size="14px"
                    v-if="orderbylist.destinatariocnpj"
                    :color="!orderbylist.destinatariocnpj ? 'red' : (orderbylist.destinatariocnpj  === 'asc' ? 'green' : 'red')"
                    :name="!orderbylist.destinatariocnpj ? 'fas fa-sort-alpha-up-alt' : (orderbylist.destinatariocnpj  === 'asc' ? 'fas fa-sort-alpha-down' : 'fas fa-sort-alpha-up-alt')"
                  />
                  <q-tooltip :delay="1000">{{!orderbylist.destinatariocnpj ? 'Nenhum ordenação - clique para ordenar por ' + props.col.name : (orderbylist.destinatariocnpj  === 'asc' ? 'Ordem crescente' : 'Ordem decrescente') }}</q-tooltip>
                </span>
                <div class="full-width">
                  <q-input type="text" input-class="text-accent" color="accent" dense v-model="props.col.filterconfig.value" clearable @input="actInputFilter()" :debounce="700" :loading="loading" />
                </div>
              </q-th>
            </template>
            <template v-slot:header-cell-motorista="props">
              <q-th :props="props" >
                <span @click="actChangeSortBy(props)" class="cursor-pointer" >
                  {{ props.col.label }}
                  <q-icon class="q-ml-xs text-black" size="14px"
                    v-if="orderbylist.motorista"
                    :color="!orderbylist.motorista ? 'red' : (orderbylist.motorista  === 'asc' ? 'green' : 'red')"
                    :name="!orderbylist.motorista ? 'fas fa-sort-alpha-up-alt' : (orderbylist.motorista  === 'asc' ? 'fas fa-sort-alpha-down' : 'fas fa-sort-alpha-up-alt')"
                  />
                  <q-tooltip :delay="1000">{{!orderbylist.motorista ? 'Nenhum ordenação - clique para ordenar por ' + props.col.name : (orderbylist.motorista  === 'asc' ? 'Ordem crescente' : 'Ordem decrescente') }}</q-tooltip>
                </span>
                <div class="full-width">
                  <q-input type="text" input-class="text-accent" color="accent" dense v-model="props.col.filterconfig.value" clearable @input="actInputFilter()" :debounce="700" :loading="loading" />
                </div>
              </q-th>
            </template>
            <template v-slot:header-cell-id="props">
                <q-th :props="props" >
                  <span @click="actChangeSortBy(props)" class="cursor-pointer" >
                    {{ props.col.label }}
                    <q-icon class="q-ml-xs text-black" size="14px"
                      v-if="orderbylist.id"
                      :color="!orderbylist.id ? 'red' : (orderbylist.id  === 'asc' ? 'green' : 'red')"
                      :name="!orderbylist.id ? 'fas fa-sort-alpha-up-alt' : (orderbylist.id  === 'asc' ? 'fas fa-sort-alpha-down' : 'fas fa-sort-alpha-up-alt')"
                    />
                    <q-tooltip :delay="1000">{{!orderbylist.id ? 'Nenhum ordenação - clique para ordenar por ' + props.col.name : (orderbylist.id  === 'asc' ? 'Ordem crescente' : 'Ordem decrescente') }}</q-tooltip>
                  </span>
                  <div class="full-width">
                    <q-input type="text" input-class="text-accent" color="accent" dense v-model="props.col.filterconfig.value" clearable @input="actInputFilter()" :debounce="700" :loading="loading" />
                  </div>
                </q-th>
            </template>
            <!-- headers -->
            <!-- colunas -->
            <template v-slot:body-cell-action="props">
              <q-td :props="props">
                <q-btn flat round :dense="!$q.platform.is.mobile" :size="$q.platform.is.mobile ? '12px' : '8px'" icon="done_outline" @click="actOk([props.row])" >
                  <q-tooltip>Importar</q-tooltip>
                </q-btn>
                <q-btn flat round :dense="!$q.platform.is.mobile" :size="$q.platform.is.mobile ? '12px' : '8px'" icon="menu" class="q-ml-sm" >
                  <q-menu>
                    <q-list dense>
                      <q-item tag="label" :dense="!$q.platform.is.mobile" @click="showNotaPDF(props.row.notachave)" clickable v-close-popup>
                        <q-item-section avatar>
                          <q-icon name="receipt_long" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Abrir nota (DANFE)</q-item-label>
                        </q-item-section>
                      </q-item>
                      <q-item tag="label" :dense="!$q.platform.is.mobile" :to="{ name: 'comercial.nfe.view', query: { chave: props.row.notachave }}" clickable v-close-popup>
                        <q-item-section avatar>
                          <q-icon name="open_in_new" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Abrir PDF da nota em outra aba</q-item-label>
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>
                </q-btn>
              </q-td>
            </template>
            <template v-slot:body-cell-remetentenome="props">
              <q-td :props="props" >
                <div style="max-width: 200px" class="ellipsis cursor-pointer" @click="showNotaPDF(props.row.notachave)">
                  {{props.row.remetentenome}}
                  <q-tooltip :delay="700">{{props.row.remetentenome}}</q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-destinatarionome="props">
              <q-td :props="props" >
                <div style="max-width: 200px" class="ellipsis cursor-pointer" @click="showNotaPDF(props.row.notachave)">
                  {{props.row.destinatarionome}}
                  <q-tooltip :delay="700">{{props.row.destinatarionome}}</q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-motorista="props">
              <q-td :props="props" >
                <div style="max-width: 150px" v-if="props.row.motorista ? props.row.motorista.id > 0 : false" class="ellipsis cursor-pointer" @click="showNotaPDF(props.row.notachave)">
                  {{props.row.motorista.nome}}
                  <q-tooltip :delay="700">{{props.row.motorista.apelido}}</q-tooltip>
                </div>
                <div v-else class="ellipsis cursor-pointer" @click="showNotaPDF(props.row.notachave)">
                  -
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-dhlocal_data="props">
              <q-td :props="props" >
                <div v-if="!props.row.dhlocal_data" class="text-caption ellipsis cursor-pointer" @click="showNotaPDF(props.row.notachave)">
                  Nenhum
                </div>
                <div v-if="props.row.dhlocal_data" class="ellipsis cursor-pointer" @click="showNotaPDF(props.row.notachave)">
                  {{ $helpers.datetimeToBR(props.row.dhlocal_data, false, true) }}
                  <q-tooltip :delay="700">
                    <div>Data e hora da coleta no aplicativo pelo motorista</div>
                    <div>{{$helpers.datetimeToBR(props.row.dhlocal_data, false, false)}}</div>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-coletaavulsa="props">
              <q-td :props="props" >
                <div class="ellipsis cursor-pointer" @click="showNotaPDF(props.row.notachave)">
                <q-icon name="grading" size="20px" :color="(props.row.idcoleta ? props.row.idcoleta > 0 : false) ? 'blue' : 'red'" v-if="props.row.coletaavulsa" >
                  <q-tooltip :delay="700">
                    <div>Originado de uma coleta avulsa</div>
                    <div v-if="props.row.idcoletaavulsa ? props.row.idcoletaavulsa > 0 : false">Coleta avulsa associada a coleta #{{props.row.idcoletaavulsa}}</div>
                    <div v-if="(props.row.idcoleta ? !(props.row.idcoleta > 0) : true)">Nenhum coleta gerada</div>
                  </q-tooltip>
                </q-icon>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-idcoleta="props">
              <q-td :props="props" >
                <div class="ellipsis cursor-pointer" @click="showNotaPDF(props.row.notachave)">
                  <span v-if="props.row.idcoleta ? !(props.row.idcoleta > 0) : true" class="text-caption text-red">Pendente
                    <q-tooltip :delay="700">
                      <div>Originado de uma coleta avulsa</div>
                      <div v-if="props.row.idcoletaavulsa ? props.row.idcoletaavulsa > 0 : false">Coleta avulsa associada a coleta #{{props.row.idcoletaavulsa}}</div>
                    </q-tooltip>
                  </span>
                  <span v-if="props.row.idcoleta">
                    {{props.row.idcoleta}}
                  </span>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-status="props">
              <q-td :props="props" >
                <div style="max-width: 150px" class="ellipsis cursor-pointer" @click="showNotaPDF(props.row.notachave)">
                  <q-icon :name="props.row.status.icon" :color="props.row.status.color" size="18px" />
                  {{props.row.status ? props.row.status.msgshort : '?'}}
                  <q-tooltip :delay="700">
                    <div v-if="props.row.status">
                      <div>{{props.row.status.msg}}</div>
                      <div v-if="props.row.status.code === 4" class="text-red">
                        {{ props.row.baixanfemsg === '' ? 'Erro ao processar XML' : props.row.baixanfemsg }}
                      </div>
                    </div>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-notanumero="props">
              <q-td :props="props" >
                <div class="cursor-pointer" style="width: 70px;" @click="showNotaPDF(props.row.notachave)">
                  {{props.row.notanumero}}
                  <q-tooltip :delay="700">
                    <div class="q-mb-md">Clique aqui para abrir o PDF da chave</div>

                    <div v-if="props.row.xmlprocessado">
                      <div v-if="props.row.notadh">Data emissão: {{props.row.notadh}}</div>
                      <div v-if="props.row.notavalor">Valor: {{props.row.notavalor}}</div>
                      <div v-if="props.row.notachave">Chave: {{props.row.notachave}}</div>
                    </div>
                    <div v-else>
                        Nota ainda não foi processada
                    </div>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-destinatariocnpj="props">
              <q-td :props="props" >
                <div class="cursor-pointer" style="max-width: 150px" @click="showNotaPDF(props.row.notachave)">
                  {{$helpers.mascaraDocCPFCNPJ(props.row.destinatariocnpj)}}
                  <q-tooltip :delay="700">
                    <div class="q-mb-md">Clique aqui para abrir o PDF da chave</div>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-remetentecnpj="props">
              <q-td :props="props" >
                <div class="cursor-pointer" style="max-width: 150px" @click="showNotaPDF(props.row.notachave)">
                  {{$helpers.mascaraDocCPFCNPJ(props.row.remetentecnpj)}}
                  <q-tooltip :delay="700">
                    <div class="q-mb-md">Clique aqui para abrir o PDF da chave</div>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <!-- colunas -->
          </q-table>
        </div>
      </div>
    </q-card-section>

    <!-- filtros -->
    <q-dialog v-model="showfilter" persistent >
      <q-card>
        <q-card-actions>
          <tablefiltersituacaocoletaavulsa v-if="showfilteridx >= 0 ? columns[showfilteridx].filterconfig.tipo == 'tablefiltersituacaocoletaavulsa' : false"
            :config="showfilteridx >= 0 ? columns[showfilteridx].filterconfig : null" @close="showfilter = false" @update="actFilterUpdated" />
        <tablefilterstatusprocessamento v-if="showfilteridx >= 0 ? columns[showfilteridx].filterconfig.tipo == 'tablefilterstatusprocessamento' : false"
            :config="showfilteridx >= 0 ? columns[showfilteridx].filterconfig : null" @close="showfilter = false" @update="actFilterUpdated" />
          <tablefilterperiodo v-if="showfilteridx >= 0 ? columns[showfilteridx].filterconfig.tipo == 'tablefilterperiodo' : false"
            :config="showfilteridx >= 0 ? columns[showfilteridx].filterconfig : null" @close="showfilter = false" @update="actFilterUpdated" />
        </q-card-actions>
      </q-card>
    </q-dialog>
    <!-- filtros -->

    </q-card>
  </q-dialog>
</template>

<script>
import NFe from 'src/mvc/models/nfe.js'
import ColetasNotas from 'src/mvc/collections/coletasnotas.js'
import tablefiltersituacaocoletaavulsa from './filters/cpn-input-tablefilter-situacaocoletaavulsa.vue'
import tablefilterstatusprocessamento from './filters/cpn-input-tablefilter-statusprocessamento.vue'
import tablefilterperiodo from './filters/cpn-input-tablefilter-periodo'
export default {
  name: 'coletasnotas.consulta',
  props: ['startFilter'],
  components: {
    tablefilterperiodo,
    tablefiltersituacaocoletaavulsa,
    tablefilterstatusprocessamento
  },
  data () {
    var dataset = new ColetasNotas()
    return {
      dataset,
      rows: [],
      selected_rows: [],
      filter: '',
      showfilter: false,
      showfilteridx: -1,
      orderbylist: {
        dhlocal_data: 'desc'
      },
      columns: [
        { name: 'action', width: '70px', style: 'width: 70px', align: 'left', label: '*', field: 'id' },
        { name: 'status', align: 'left', label: 'Status', field: 'status', filterconfig: { tipo: 'tablefilterstatusprocessamento', value: null, label: '', info: 'Status do processamento da nota', sorted: true } },
        { name: 'notanumero', align: 'center', label: 'Número Nota', field: 'notanumero', filterconfig: { value: null, sorted: true } },
        { name: 'dhlocal_data', align: 'center', label: 'Data da coletagem', field: 'dhlocal_data', filterconfig: { tipo: 'tablefilterperiodo', value: null, label: '', info: 'Período para data de coletagem', sorted: true } },
        { name: 'coletaavulsa', align: 'center', label: 'Avulsa', field: 'coletaavulsa', filterconfig: { tipo: 'tablefiltersituacaocoletaavulsa', value: null, label: 'Status coleta avulsa', info: '', sorted: true } },
        { name: 'idcoleta', align: 'center', label: 'Coleta', field: 'idcoleta', filterconfig: { value: null, sorted: true } },
        { name: 'qtde', align: 'center', label: 'Volumes', field: 'qtde', filterconfig: { value: null, sorted: true } },
        { name: 'peso', align: 'right', label: 'Peso KG', field: 'peso', filterconfig: { value: null, sorted: true } },
        { name: 'remetentenome', align: 'left', label: 'Remetente', field: 'remetentenome', filterconfig: { value: null, sorted: true } },
        { name: 'remetentecnpj', align: 'left', label: 'CNPJ', field: 'remetentecnpj', filterconfig: { value: null, sorted: true } },
        { name: 'destinatarionome', align: 'left', label: 'Destinatário', field: 'destinatarionome', filterconfig: { value: null, sorted: true } },
        { name: 'destinatariocnpj', align: 'left', label: 'CNPJ', field: 'destinatariocnpj', filterconfig: { value: null, sorted: true } },
        { name: 'motorista', align: 'left', label: 'Motorista', field: 'motorista', filterconfig: { value: null, sorted: true } },
        { name: 'id', align: 'center', label: 'id', field: 'id', filterconfig: { value: null, sorted: true } }
      ],
      loading: false,
      posting: false,
      msgError: ''
    }
  },
  async mounted () {
  },

  methods: {
    async showNotaPDF (pChave) {
      var app = this
      var nfe = new NFe()
      var ret = await nfe.dialogShow(app, pChave)
      if (!ret.ok) {
        if (ret.msg ? ret.msg !== '' : false) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {})
        }
      }
    },
    async queryRead () {
      var app = this
      if (app.startFilter) {
        for (let index = 0; index < app.columns.length; index++) {
          const element = app.columns[index]
          if (element.filterconfig) {
            if (element.name === 'motorista' && app.startFilter.motorista) element.filterconfig.value = app.startFilter.motorista.nome
            if (element.name === 'dhlocal_data') {
              if (app.startFilter.dhlocal_datai || app.startFilter.dhlocal_dataf) {
                element.filterconfig.value = { dti: app.startFilter.dhlocal_datai, dtf: app.startFilter.dhlocal_dataf }
              } else {
                element.filterconfig.value = null
              }
            }
          }
        }
      }
    },
    async actFilterUpdated (val) {
      var app = this
      app.showfilter = false
      app.columns[app.showfilteridx].filterconfig.value = val
      app.showfilteridx = -1
      app.refreshData(null)
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
      app.showfilter = false
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
    actShowFilter (props, pIndex = -1) {
      var app = this
      app.showfilteridx = pIndex
      if (props) {
        for (let index = 0; index < app.columns.length; index++) {
          const element = app.columns[index]
          if (element.name === props.col.name) {
            app.showfilteridx = index
            break
          }
        }
      }
      this.showfilter = true
    },
    actInputFilter () {
      var app = this
      if (app.loading) return
      app.refreshData(null)
    },
    async refreshData (props) {
      var app = this
      app.loading = true
      app.msgError = ''
      app.dataset.readPropsTable(props)
      if (app.$q.platform.is.mobile) app.dataset.pagination.rowsPerPage = 50
      app.dataset.notanumero = null
      app.dataset.status = null
      app.dataset.coletaavulsa = null
      app.dataset.idcoleta = null
      app.dataset.dhlocal_datai = null
      app.dataset.dhlocal_dataf = null
      app.dataset.remetentenome = null
      app.dataset.remetentecnpj = null
      app.dataset.destinatarionome = null
      app.dataset.destinatariocnpj = null
      app.dataset.motorista = null
      app.dataset.id = null

      for (let index = 0; index < app.columns.length; index++) {
        const element = app.columns[index]
        if (element.filterconfig) {
          if (element.name === 'status') app.dataset.status = element.filterconfig.value
          if (element.name === 'coletaavulsa') app.dataset.coletaavulsa = element.filterconfig.value
          if (element.name === 'notanumero') app.dataset.notanumero = element.filterconfig.value
          if (element.name === 'idcoleta') app.dataset.idcoleta = element.filterconfig.value
          if (element.name === 'remetentenome') app.dataset.remetentenome = element.filterconfig.value
          if (element.name === 'remetentecnpj') app.dataset.remetentecnpj = element.filterconfig.value
          if (element.name === 'destinatarionome') app.dataset.destinatarionome = element.filterconfig.value
          if (element.name === 'destinatariocnpj') app.dataset.destinatariocnpj = element.filterconfig.value
          if (element.name === 'motorista') app.dataset.motorista = element.filterconfig.value
          if (element.name === 'id') app.dataset.id = element.filterconfig.value

          if (element.name === 'dhlocal_data') {
            if (element.filterconfig.value) {
              if (element.filterconfig.value.dti) app.dataset.dhlocal_datai = element.filterconfig.value.dti
              if (element.filterconfig.value.dtf) app.dataset.dhlocal_dataf = element.filterconfig.value.dtf
            }
          }
        }
      }
      app.dataset.orderby = null
      if (app.orderbylist) {
        var c = Object.keys(app.orderbylist).length
        if (c > 0) app.dataset.orderby = app.orderbylist
      }
      app.dataset.omitircomcargaentrada = true
      var ret = await app.dataset.fetch()
      if (ret.ok) {
        app.rows = app.dataset.itens
        // // atualiza url
        var query = {}
        if (app.dataset.filter !== null && app.dataset.filter !== '') query.find = app.dataset.filter
        if (app.dataset.motorista !== null && app.dataset.motorista !== '') query.motorista = app.dataset.motorista
        if (app.dataset.remetentenome !== null && app.dataset.remetentenome !== '') query.remetentenome = app.dataset.remetentenome
        if (app.dataset.remetentecnpj !== null && app.dataset.remetentecnpj !== '') query.remetentecnpj = app.dataset.remetentecnpj
        if (app.dataset.destinatarionome !== null && app.dataset.destinatarionome !== '') query.destinatarionome = app.dataset.destinatarionome
        if (app.dataset.destinatariocnpj !== null && app.dataset.destinatariocnpj !== '') query.destinatariocnpj = app.dataset.destinatariocnpj
        if (app.dataset.notanumero !== null && (parseInt(app.dataset.notanumero) > 0)) query.notanumero = app.dataset.notanumero
        if (app.dataset.idcoleta !== null && (parseInt(app.dataset.idcoleta) > 0)) query.idcoleta = app.dataset.idcoleta
        if (app.dataset.id !== null && (parseInt(app.dataset.id) > 0)) query.id = app.dataset.id
        if (app.dataset.coletaavulsa !== null) query.coletaavulsa = app.dataset.coletaavulsa
        if (app.dataset.status !== null) query.status = JSON.stringify(app.dataset.status)

        if (app.dataset.dhlocal_datai !== null) query.dhlocal_datai = app.dataset.dhlocal_datai
        if (app.dataset.dhlocal_dataf !== null) query.dhlocal_dataf = app.dataset.dhlocal_dataf
        if (app.dataset.pagination.page !== null && app.dataset.pagination.page > 1) query.page = app.dataset.pagination.page
        if ((app.dataset.pagination.rowsPerPage !== null) && (app.dataset.pagination.rowsPerPage > 0) && (parseInt(app.dataset.pagination.rowsPerPage) !== 20)) query.pagesize = app.dataset.pagination.rowsPerPage
        if (app.dataset.orderby !== null) query.sortby = JSON.stringify(app.dataset.orderby)

        // query.t = new Date().getTime()
        // try {
        //   app.$router.replace({ name: app.$route.name, query: query, replace: true, append: false })
        // } catch (error) {
        //   console.error(error)
        // }
      } else {
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {
        })
      }
      app.loading = false
    },
    // following method is REQUIRED
    // (don't change its name --> "show")
    async show () {
      var app = this
      this.$refs.dialog.show()
      if (app.startFilter) await app.queryRead()
      await app.refreshData(null)
    },

    // following method is REQUIRED
    // (don't change its name --> "hide")
    hide () {
      this.$refs.dialog.hide()
    },

    onDialogHide () {
      // required to be emitted
      // when QDialog emits "hide" event
      this.$emit('hide')
    },
    actClose () {
      this.$emit('hide')
    },

    actOk (pRows) {
      try {
        var app = this
        if (!pRows) throw new Error('Nenhum registro')
        if (!(pRows.length > 0)) throw new Error('Nenhum registro')

        var ids = []
        var chaves = []
        for (let index = 0; index < pRows.length; index++) {
          const element = pRows[index]
          if (element.id > 0) ids.push(element.id)
          if ((element.docfiscal === 'nfe') && (element.notachave !== '')) chaves.push(element.notachave)
        }
      } catch (error) {
        app.error = error.message
        setTimeout(() => {
          app.error = ''
        }, 3000)
        return false
      }
      // on OK, it is REQUIRED to
      // emit "ok" event (with optional payload)
      // before hiding the QDialog
      this.$emit('ok', { rows: pRows, chaves: chaves, ids: ids })
      // or with payload: this.$emit('ok', { ... })

      // then hiding dialog
      this.hide()
    },

    onCancelClick () {
      // we just need to hide dialog
      this.hide()
    }
  }
}
</script>
