<template>
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="pull-right">
                            <button type="button" v-on:click.prevent="toggle" class="btn btn-outline-info mb-2">
                                <i class="fa fa-search"></i> Form Pencarian
                            </button>
                            <a v-if="access.write === 1" :href="route + '/create'" class="btn btn-success mb-2">
                                <i class="fa fa-plus"></i> Tambah Data
                            </a>
                        </div>
                        <transition name="fade">
                            <div class="card" style="margin-top:50px;" v-show="showForm">
                                <div class="card-body">
                                    <form v-on:submit.prevent="fetchData()">
                                       <div class="row">
                                           <div class="form-group col-md-3">
                                                <input type="text" class="form-control" v-model="search.q" placeholder="Nomor Nota Dinas">
                                            </div>
                                            <div class="form-group col-md-4">
                                               <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                    <date-picker
                                                        v-model="search.start"
                                                        :config="options"
                                                        class="form-control"
                                                        placeholder="Awal Tanggal Nota Dinas"
                                                        autocomplete="off">
                                                    </date-picker>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                               <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                    <date-picker
                                                        v-model="search.end"
                                                        :config="options"
                                                        class="form-control"
                                                        placeholder="Akhir Tanggal Nota Dinas"
                                                        autocomplete="off">
                                                    </date-picker>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-group col-md-4">
                                                <button type="submit" class="btn btn-success mr-sm-2">
                                                    <i class="fa fa-search"></i> Cari Data
                                                </button>
                                                <button type="button" v-on:click.prevent="clear" class="btn btn-info">
                                                    <i class="fa fa-refresh"></i> Reset
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </transition>
                    </div>
                    <div class="card-body">
                        <v-alert :alert="alert"></v-alert>
                        <loading :opacity="100" :active.sync="isLoading" :can-cancel="false" :is-full-page="false"></loading>
                        <transition name="fade">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-bordered" v-if="showTable == true">
                                    <thead>
                                        <tr>
                                            <th style="width:10%;text-align:center;vertical-align:middle;" rowspan="2">Nomor Nota Dinas</th>
                                            <th style="width:10%;text-align:center;vertical-align:middle;" rowspan="2">Tanggal Nota Dinas</th>
                                            <th style="width:10%;text-align:center;vertical-align:middle;" colspan="4">Nilai Pelimpahan Uang</th>
                                            <th style="width:10%;text-align:center;vertical-align:middle;" rowspan="2">Jumlah Pelimpahan Uang</th>
                                            <th style="width:10%;text-align:center;vertical-align:middle;" rowspan="2">Sisa Anggaran di Bank BP</th>
                                            <th style="width:13%;text-align:center;vertical-align:middle;" rowspan="2">Action</th>
                                        </tr>
                                        <tr>
                                            <th style="width:10%;text-align:center;vertical-align:middle;">UP</th>
                                            <th style="width:10%;text-align:center;vertical-align:middle;">GU</th>
                                            <th style="width:10%;text-align:center;vertical-align:middle;">TU</th>
                                            <th style="width:10%;text-align:center;vertical-align:middle;">LS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="v in pelimpahan" :key="v.id">
                                            <td style="vertical-align: middle;">
                                                <a :href="route + '/detail?id=' + v.id">{{ v.nota_dinas }}</a>
                                            </td>
                                            <td style="text-align:center;vertical-align: middle;">
                                                {{ v.tgl_nota_dinas | moment }}
                                            </td>
                                            <td style="text-align:right;vertical-align: middle;">
                                                Rp.{{ v.up | rupiah }}
                                            </td>
                                            <td style="text-align:right;vertical-align: middle;">
                                                Rp.{{ v.gu | rupiah }}
                                            </td>
                                            <td style="text-align:right;vertical-align: middle;">
                                                Rp.{{ v.tu | rupiah }}
                                            </td>
                                            <td style="text-align:right;vertical-align: middle;">
                                                Rp.{{ v.ls | rupiah }}
                                            </td>
                                            <td style="text-align:right;vertical-align: middle;">
                                                <span>Rp.{{ v.jumlah_pelimpahan | rupiah }}</span>
                                            </td>
                                            <td style="text-align:right;vertical-align: middle;">
                                                Rp.{{ v.sisa_sp2d | rupiah }}
                                            <td style="text-align: center;vertical-align: middle;">
                                                <div>
                                                    <a v-if="(v.status == 0 && access.update === 1)" :href="route + '/edit?id=' + v.id" class="btn btn-xs btn-warning mr-sm-1">
                                                        <i class="fa fa-wrench"></i> Ubah
                                                    </a>
                                                    <button v-else class="btn btn-xs btn-warning disabled mr-sm-1">
                                                        <i class="fa fa-wrench"></i> Ubah
                                                    </button>
                                                    <a v-if="(v.status == 0 && access.delete === 1)" href="#" @click="toggleModal(v.id)"
                                                        class="btn btn-xs btn-danger">
                                                        <i class="fa fa-trash-o"></i> Hapus
                                                    </a>
                                                    <button v-else class="btn btn-xs btn-danger disabled">
                                                        <i class="fa fa-trash-o"></i> Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </transition>
                        <transition name="fade">
                            <v-delete :element="'pelimpahan_delete_modal'" :id="id" @delete="deleteData"></v-delete>
                        </transition>
                        <transition name="fade">
                            <div class="card-footer clearfix">
                                <v-pagination
                                    :pagination="pagination"
                                    v-on:next="nextPage"
                                    v-on:previous="prevPage"
                                    v-if="showTable === true">
                                </v-pagination>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import service from './../../services.js';

export default {
    data: function() {
        return {
            pelimpahan: {},
            total_penyerapan:0,
            search: {
                q:'',
                start:'',
                end:''
            },
            options: {
                format: 'YYYY-MM-DD',
                useCurrent: false,
                locale: 'id'
            },
            alert: {
                error:false,
                empty:false,
                delete:false
            },
            pagination: {
                page: 1,
                last:0,
                total:0,
                to:0,
                from:0
            },
            isLoading: false,
            showForm: false,
            showTable: false,
            id:'',
            usernip:''
        }
    },
    props: ['api', 'route', 'access'],
    methods: {
        toggle() {
            this.showForm = !this.showForm
        },
        clear() {
            this.search.q = '';
            this.search.start = '';
            this.search.end = '';
            this.fetchData();
        },
        nextPage() {
            this.pagination.page++;
            this.fetchData();
        },
        prevPage() {
            this.pagination.page--;
            this.fetchData();
        },
        toggleModal(id) {
            $("#pelimpahan_delete_modal").modal('show');
            this.id = id;
        },
        anggaran_tersedia: function(belanja, tahun) {
            service.postData('./api/ajax/total_anggaran', {'tahun':tahun,'belanja':belanja})
            .then(response => {
                return "<span class='label'>"+ response.total_anggaran +"</span>";
            })
            .catch(error => {
                console.log(error);
            });
        },
        fetchData() {
            this.isLoading = true;
            let query  = this.generateParams();
            service.fetchData(this.api + '?'+ query + '&page='+ this.pagination.page)
            .then(response => {
                this.renderData(response);
            })
            .catch(error => {
                this.isLoading = false;
                this.alert.error = true;
                console.log(error);
            });
        },
        renderData(response) {
            if (response.total === 0) {
                this.showTable = false;
                this.alert.empty = true;
                this.alert.error = false;
            } else {
                this.showTable = true;
                this.alert.empty = false;
                this.alert.error = false;
                this.pelimpahan = response.data;
                this.pagination.last = response.last_page;
                this.pagination.from = response.from;
                this.pagination.to = response.to;
                this.pagination.total = response.total;
            }
            this.isLoading = false;
        },
        generateParams() {
            return Object.keys(this.search).map(key => key + '=' + this.search[key]).join('&');
        },
        deleteData(id) {
            service.deleteData(this.api + '?nip='+this.usernip+'&id=' + id)
            .then(response => {
                if(response.status === 'ok') {
                    this.alert.delete = true;
                    $('#pelimpahan_delete_modal').modal('hide');
                    this.fetchData();
                    window.scroll({ top: 0, left: 0, behavior: 'smooth' });
                    setTimeout(() => this.alert.delete=false, 5000);
                }
            }).catch(error => {
                this.alert.delete = false;
                this.alert.error = true;
                $('#pelimpahan_delete_modal').modal('hide');
                window.scroll({ top: 0, left: 0, behavior: 'smooth' });
                this.fetchData();
                console.log(error);
            });
        }
    },
    created() {
        this.isLoading = true;
        this.fetchData();
        this.usernip = this.$cookies.get('nip');
    }
};
</script>
