<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <loading :opacity="100" :active.sync="isLoading" :can-cancel="false" :is-full-page="false"></loading>
                    <transition name="fade">
                        <table class="table table-hover table-striped table-bordered">
                            <tbody>
                                <tr>
                                    <td style="width:15%;"><b>Nomor Nota Dinas</b></td>
                                    <td style="width:85%;">{{ pelimpahan.nota_dinas }}</td>
                                </tr>
                                <tr>
                                    <td style="width:15%;"><b>Tanggal Nota Dinas</b></td>
                                    <td style="width:85%;">{{ pelimpahan.tgl_nota_dinas | moment }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </transition>
                    <div style="margin-top:25px;"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-left">
                                <a v-if="(pelimpahan.status == 0 && access.write === 1)" :href="route + '/nominal/create?pelimpahan=' + pelimpahan.id" class="btn btn-success mb-2 mr-2"><i class="fa fa-plus"></i> Tambah Data</a>
                                <!-- <span v-if="pelim.length !== 0 && access.approval === 1 && dinasbop.status === 0">
                                    <a v-if="(approval_type === 'kassubag' || approval_type === 'administrator') && (approval_tab.kassubag.approval === 0)" class="btn btn-warning mb-2 mr-2" href="#" @click="toggleRevisiModal('kassubag')">
                                        <i class="fa fa-edit"></i> Form Revisi Kassubag
                                    </a>
                                    <a v-if="(approval_type === 'kassubag' || approval_type === 'administrator') && (approval_tab.kassubag.approval === 0)" class="btn btn-success mb-2 mr-2" href="#" @click="toggleApprovalModal('kassubag')">
                                        <i class="fa fa-check"></i> Approval Kassubag
                                    </a>
                                    <a v-if="(approval_type === 'sekretaris' || approval_type === 'administrator') && (approval_tab.sekretaris.approval === 0)" class="btn btn-warning mb-2 mr-2" href="#" @click="toggleRevisiModal('sekretaris')">
                                        <i class="fa fa-edit"></i> Form Revisi Sekretaris
                                    </a>
                                    <a v-if="(approval_type === 'sekretaris' || approval_type === 'administrator') && (approval_tab.sekretaris.approval === 0)" class="btn btn-success mb-2 mr-2" href="#" @click="toggleApprovalModal('sekretaris')">
                                        <i class="fa fa-check"></i> Approval Sekretaris
                                    </a>
                                    <a v-if="(approval_type === 'inspektur' || approval_type === 'administrator') && (approval_tab.inspektur.approval === 0)" class="btn btn-warning mb-2 mr-2" href="#" @click="toggleRevisiModal('inspektur')">
                                        <i class="fa fa-edit"></i> Form Revisi Inspektur
                                    </a>
                                    <a v-if="(approval_type === 'inspektur' || approval_type === 'administrator') && (approval_tab.inspektur.approval === 0)" class="btn btn-success mb-2 mr-2" href="#" @click="toggleApprovalModal('inspektur')">
                                        <i class="fa fa-check"></i> Approval Inspektur
                                    </a>
                                </span> -->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <v-alert :alert="alert"></v-alert>
                            <transition name="fade">
                                <div class="table-responsive" v-if="showTable == true">
                                    <table class="table table-hover table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width:10%;text-align:center;" rowspan="2">BPP</th>
                                                <th style="width:10%;text-align:center;" colspan="5">Nilai Pelimpahan</th>
                                                <th style="width:12%;text-align:center;" rowspan="2">Action</th>
                                            </tr>
                                            <tr>
                                                <th style="width:10%;text-align:center;">UP</th>
                                                <th style="width:10%;text-align:center;">GU</th>
                                                <th style="width:10%;text-align:center;">TU</th>
                                                <th style="width:10%;text-align:center;">LS</th>
                                                <th style="width:10%;text-align:center;">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="v in pelimpahandetail" :key="v.id">
                                                <td style="vertical-align: middle;">{{ v.bendahara }}</td>
                                                <td style="text-align:right;vertical-align: middle;">Rp.{{ v.up | rupiah }}</td>
                                                <td style="text-align:right;vertical-align: middle;">Rp.{{ v.gu | rupiah }}</td>
                                                <td style="text-align:right;vertical-align: middle;">Rp.{{ v.tu | rupiah }}</td>
                                                <td style="text-align:right;vertical-align: middle;">Rp.{{ v.ls | rupiah }}</td>
                                                <td style="text-align:right;vertical-align: middle;">
                                                    Rp.{{ (v.up + v.gu + v.tu. v.ls ) | rupiah }}
                                                </td>
                                                <td style="text-align: center;vertical-align: middle;">
                                                    <div>
                                                        <a v-if="(v.status == 0 && access.update === 1)" :href="route + '/edit?id=' + v.id" class="btn btn-sm btn-warning mr-sm-1">
                                                            <i class="fa fa-wrench"></i> Ubah
                                                        </a>
                                                        <button v-else class="btn btn-sm btn-warning disabled mr-sm-1"><i class="fa fa-wrench"></i> Ubah</button>
                                                        <a v-if="(v.status == 0 && access.delete === 1)" href="#" @click="toggleModal(v.id)"
                                                            class="btn btn-sm btn-danger">
                                                            <i class="fa fa-trash-o"></i> Hapus
                                                        </a>
                                                        <button v-else class="btn btn-sm btn-danger disabled"><i class="fa fa-trash-o"></i> Hapus</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </transition>
                            <transition name="fade">
                                <v-delete :element="'pelimpahandetail_delete_modal'" :id="id" @delete="deleteData"></v-delete>
                            </transition>
                        </div>
                        <!-- <div class="col-md-12 col-xs-12" v-if="pelimpahandetail.length !== 0">
                            <hr>
                            <transition name="fade"><v-revision-log :element="element" :revision="approval_tab"></v-revision-log></transition>
                            <transition name="fade"><v-revision :role="approval_role" :element="'driver_revision_modal'" @revision="createRevision"></v-revision></transition>
                            <transition name="fade"><v-approval :role="approval_role" :element="'driver_approval_modal'" @approve="createApproval"></v-approval></transition>
                        </div> -->
                    </div>
                    <a :href="route" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import service from './../../services.js';
    export default {
        data() {
            return {
                id:'',
                isLoading: false,
                alert: {
                    error:false,
                    empty:false,
                    delete:false
                },
                showForm: false,
                showTable: false
            }
        },
        props: [
            'pelimpahan',
            'pelimpahandetail',
            'lock',
            'route',
            'print_action',
            'access',
            'api'
        ],
        methods: {
            deleteData(id) {
                service.deleteData(this.api + '?id=' + id)
                .then(response => {
                    if(response.status === 'ok') {
                        this.alert.delete = true;
                        $('#pelimpahandetail_delete_modal').modal('hide');
                        window.scroll({ top: 0, left: 0, behavior: 'smooth' });
                        setTimeout(() => this.alert.delete=false, 5000);
                    }
                }).catch(error => {
                    this.alert.delete = false;
                    this.alert.error = true;
                    $('#pelimpahandetail_delete_modal').modal('hide');
                    window.scroll({ top: 0, left: 0, behavior: 'smooth' });
                    console.log(error);
                });
            },
            toggleModal(id) {
                $("#pelimpahandetail_delete_modal").modal('show');
                this.id = id;
            },
        },
        created() {
            this.isLoading = true;
            if (this.pelimpahandetail.length === 0) {
                this.alert.empty = true;
            }
        },
        mounted() {
            this.isLoading = false;
        }
    };
</script>
