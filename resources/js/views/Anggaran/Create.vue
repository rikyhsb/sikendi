<template>
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <v-alert :alert=alert></v-alert>
                        <loading :opacity="100" :active.sync="isLoading" :can-cancel="false" :is-full-page="false"></loading>
                        <form method="POST" v-on:submit.prevent="onSubmit">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Program *</label>
                                    <select v-model="anggaran.program_id" class="form-control" @change="onChangeProgram($event)" :class="{ 'is-invalid': validasi.program_id }">
                                        <option value="">Pilih Program</option>
                                        <option v-for="v in this.program" :value="v.id" :key="v.id">{{ v.nama_program }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Kegiatan *</label>
                                    <select v-model="anggaran.kegiatan_id" class="form-control" @change="onChangeKegiatan($event)" :class="{ 'is-invalid': validasi.kegiatan_id }">
                                        <option value="">Pilih Kegiatan</option>
                                        <option v-for="v in this.kegiatan" :value="v.id" :key="v.id">{{ v.nama_kegiatan }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Belanja *</label>
                                    <select v-model="anggaran.belanja_id" class="form-control" :class="{ 'is-invalid': validasi.belanja_id }">
                                        <option value="">Pilih Belanja</option>
                                        <option v-for="v in this.belanja" :value="v.id" :key="v.id">{{ v.nama_belanja }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Jumlah Anggaran *</label>
                                    <money type="text" v-model="anggaran.jumlah" class="form-control" :class="{ 'is-invalid': validasi.jumlah }" />
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Tahun *</label>
                                    <select v-model="anggaran.tahun" class="form-control" :class="{ 'is-invalid': validasi.tahun }">
                                        <option value="">Pilih Tahun</option>
                                        <option v-for="v in this.tahun_data" :value="v" :key="v">{{ v }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan Data</button>
                                    <a :href="route" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">
                                    <b>*) Wajib Diisi</b>
                                </div>
                            </div>
                        </form>
                    </div>
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
                anggaran: {
                    'program_id': '',
                    'kegiatan_id': '',
                    'belanja_id': '',
                    'jumlah': '',
                    'tahun': ''
                },
                validasi: {
                    'program_id': '',
                    'kegiatan_id': '',
                    'belanja_id': '',
                    'jumlah': '',
                    'tahun': ''
                },
                program:'',
                belanja:'',
                kegiatan:'',
                alert: {
                    error: false,
                    save: false,
                    duplicate: false,
                    validate: false
                },
                isLoading: false
            }
        },
        props: ['program_data', 'kegiatan_data', 'belanja_data', 'tahun_data', 'api', 'route'],
        methods: {
            clearAlert() {
                this.alert.error = false;
                this.alert.save = false;
                this.alert.duplicate = false;
                this.alert.validate = false;
            },
            onChangeProgram(evt) {
                const program = evt.target.value;
                service.fetchData('../api/ajax/kegiatan?program=' + program)
                .then(response => {
                    this.anggaran.kegiatan_id = '';
                    this.anggaran.belanja_id = '';
                    this.kegiatan = response;
                    this.belanja = [];
                })
                .catch(error => {
                    console.log(error);
                });
            },
            onChangeKegiatan(evt) {
                const kegiatan = evt.target.value;
                service.fetchData('../api/ajax/belanja?kegiatan=' + kegiatan)
                .then(response => {
                    this.anggaran.belanja_id = '';
                    this.belanja = response;
                })
                .catch(error => {
                    console.log(error);
                });
            },
            onSubmit(evt) {
                evt.preventDefault();
                this.clearAlert();
                let validasi = this.validate();
                if (validasi === true) {
                    service.postData(this.api, this.anggaran)
                        .then(result => {
                            this.response(result);
                        }).catch(error => {
                            this.isLoading = false
                            this.alert.error = true;
                            window.scroll({top: 0, left: 0, behavior: 'smooth'});
                            console.log(error);
                        });
                } else {
                    this.alert.validate = true;
                    setTimeout(() => this.alert.validate = false, 2000);
                }
            },
            response(result) {
                setTimeout(() => { this.isLoading = false }, 1000);
                if (result.status === 'ok') {
                    this.reset();
                    this.alert.save = true;
                    window.scroll({ top: 0, left: 0, behavior: 'smooth' });
                    setTimeout(() => this.alert.save = false, 5000);
                } else if (result.status === 'duplicate') {
                    this.alert.duplicate = true;
                    window.scroll({ top: 0, left: 0, behavior: 'smooth' });
                }
            },
            reset() {
                this.anggaran.program_id = '';
                this.anggaran.kegiatan_id = '';
                this.anggaran.belanja_id = '';
                this.anggaran.jumlah = '';
                this.anggaran.tahun = '';
            },
            validate() {
                let condition = 0;
                if (this.anggaran.program_id.length === 0) {
                    this.validasi.program_id = true;
                    condition++;
                } else {
                    this.validasi.program_id = false;
                }

                if (this.anggaran.kegiatan_id.length === 0) {
                    this.validasi.kegiatan_id = true;
                    condition++;
                } else {
                    this.validasi.kegiatan_id = false;
                }

                if (this.anggaran.belanja_id.length === 0) {
                    this.validasi.belanja_id = true;
                    condition++;
                } else {
                    this.validasi.belanja_id = false;
                }

                if (this.anggaran.jumlah.length === 0) {
                    this.validasi.jumlah = true;
                    condition++;
                } else {
                    this.validasi.jumlah = false;
                }

                if (this.anggaran.tahun.length === 0) {
                    this.validasi.tahun = true;
                    condition++;
                } else {
                    this.validasi.tahun = false;
                }

                if (condition > 0) {
                    return false;
                } else {
                    return true;
                }
            }
        },
        created() {
            this.isLoading = true;
            this.program = this.program_data;
            this.belanja = this.belanja_data;
            this.kegiatan = this.kegiatan_data;
        },
        mounted() {
            this.isLoading = false;
        }
    };
</script>
