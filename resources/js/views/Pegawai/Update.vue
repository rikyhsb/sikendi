<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <transition name="fade">
                        <v-alert :alert=alert></v-alert>
                    </transition>
                    <loading :opacity="100" :active.sync="isLoading" :can-cancel="false" :is-full-page="false"></loading>
                    <form method="POST" v-on:submit.prevent="onSubmit">
                        <div class="row">
                            <div class="form-group col-md-6 col-xs-12">
                                <label>NIP *</label>
                                <input type="text" class="form-control" v-model="pegawai.nip" :class="{ 'is-invalid': validasi.nip }">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 col-xs-12">
                                <label>Nama *</label>
                                <input type="text" class="form-control" v-model="pegawai.nama" :class="{ 'is-invalid': validasi.nama }">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 col-xs-12">
                                <label>Pangkat *</label>
                                <select v-model="pegawai.pangkat" class="form-control" @change="onChangePangkat($event)" :class="{ 'is-invalid': validasi.pangkat }">
                                    <option value="">Pilih Pangkat</option>
                                    <option v-for="v in this.pangkat_data" :value="v.nama_pangkat" :key="v.id">
                                        {{ v.nama_pangkat }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 col-xs-12">
                                <label>Jabatan *</label>
                                <select v-model="pegawai.jabatan" class="form-control" :class="{ 'is-invalid': validasi.jabatan }">
                                    <option value="">Pilih Jabatan</option>
                                    <option v-for="v in this.jabatan_data" :value="v.nama_jabatan" :key="v.id">
                                        {{ v.nama_jabatan }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 col-xs-12">
                                <label>Nomor Rekening</label>
                                <input type="text" class="form-control" v-model="pegawai.norek">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 col-xs-12">
                                <label>Keterangan</label>
                                <input type="text" class="form-control" v-model="pegawai.keterangan">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12 col-xs-12">
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan Data</button>
                                <a :href="route" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12 col-xs-12">
                                <b>*) Wajib Diisi</b>
                            </div>
                        </div>
                    </form>
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
                alert: {
                    error: false,
                    update: false,
                    validate: false
                },
                validasi: {
                    'nip': '',
                    'nama': '',
                    'pangkat': '',
                    'golongan': '',
                    'jabatan': ''
                },
                isLoading: false,
            }
        },
        props: ['golongan_data', 'pangkat_data', 'jabatan_data', 'api', 'route', 'pegawai'],
        methods: {
            clearAlert() {
                this.alert.error = false;
                this.alert.update = false;
                this.alert.validate = false;
            },
            onSubmit(evt) {
                evt.preventDefault();
                this.clearAlert();
                let validasi = this.validate();
                if (validasi === true) {
                    this.isLoading = true;
                    service.putData(this.api, this.pegawai)
                        .then(result => {
                            this.response(result);
                        }).catch(error => {
                            this.isLoading = false;
                            this.alert.error = true;
                            window.scroll({top: 0, left: 0, behavior: 'smooth'});
                            console.log(error);
                        });
                } else {
                    this.alert.validate = true;
                    setTimeout(() => this.alert.validate = false, 3000);
                }
            },
            onChangePangkat(evt) {
                const pangkat = evt.target.value;
                service.postData('../api/ajax/golongan', {'pangkat': pangkat})
                .then(response => {
                    this.pegawai.golongan = response.golongan;
                })
                .catch(error => {
                    console.log(error);
                });
            },
            response(result) {
                setTimeout(() => { this.isLoading = false }, 1000);
                if (result.status === 'ok') {
                    this.alert.update = true;
                    window.scroll({ top: 0, left: 0, behavior: 'smooth' });
                    setTimeout(() => this.alert.update = false, 5000);
                }
                this.isLoading = false;
            },
            validate() {
                let condition = 0;

                if (this.pegawai.nip.length === 0) {
                    this.validasi.nip = true;
                    condition++;
                } else {
                    this.validasi.nip = false;
                }

                if (this.pegawai.nama.length === 0) {
                    this.validasi.nama = true;
                    condition++;
                } else {
                    this.validasi.nama = false;
                }

                if (this.pegawai.pangkat.length === 0) {
                    this.validasi.pangkat = true;
                    condition++;
                } else {
                    this.validasi.pangkat = false;
                }

                if (this.pegawai.jabatan.length === 0) {
                    this.validasi.jabatan = true;
                    condition++;
                } else {
                    this.validasi.jabatan = false;
                }

                if (condition > 0) {
                    return false;
                } else {
                    return true;
                }
            },
        },
        created() {
            this.isLoading = true;
        },
        mounted() {
            this.isLoading = false;
            if (this.pegawai.eselon === null) {
                this.pegawai.eselon = '';
            }
        }
    };
</script>
