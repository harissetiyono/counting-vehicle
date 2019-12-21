<template>
    <v-row justify="center">
    <v-col cols="12" md="10" >
        <v-stepper v-model="e1">
            <v-stepper-header>
                <v-stepper-step :complete="e1 > 1" step="1">Peristiwa yang Terjadi</v-stepper-step>
                <v-divider></v-divider>
                <v-stepper-step :complete="e1 > 2" step="2">Tindakan pidana dan saksi</v-stepper-step>
                <v-divider></v-divider>
                <v-stepper-step :complete="e1 > 3" step="3">Barang bukti dan uraian kejadian</v-stepper-step>
                <v-divider></v-divider>
                <v-stepper-step :complete="e1 > 4" step="4">Tindakan</v-stepper-step>
            </v-stepper-header>

            <v-stepper-items>
                <v-stepper-content step="1">
                    <v-card class="mt-n2">
                        <v-card-text>
                            <v-row class="mb-n8">
                                <v-col >
                                    <v-datetime-picker 
                                        label="Waktu kejadian" 
                                        :text-field-props="textField_kejadian" 
                                        :datePickerProps="datePicker_kejadian" 
                                        v-model="waktu_kejadian" 
                                        date-format="dd/MM/yyyy"
                                    >
                                    </v-datetime-picker>
                                </v-col>
                                <v-col><v-text-field label="Waktu Dilaporkan" v-model="waktu_laporan" filled readonly></v-text-field></v-col>
                            </v-row>
                            <div v-if="!$v.waktu_kejadian.required">
                                <v-alert
                                dense
                                outlined
                                type="error"
                            >
                               Waktu kejadian harus diisi
                            </v-alert>
                            </div>

                            <!-- Form tempat kejadian -->
                            <v-row>
                                <v-col cols=10>
                                    <v-textarea
                                        ref="tempat_kejadian"
                                        v-model="tempat_kejadian"
                                        filled
                                        label="Tempat Kejadian"
                                        autofocus
                                        rows=1
                                        auto-grow
                                        clearable
                                        :error-messages="tempat_kejadian_Errors"
                                        @input="$v.tempat_kejadian.$touch()"
                                        @blur="$v.tempat_kejadian.$touch()"
                                    ></v-textarea>
                                </v-col>
                                <v-col cols=2>
                                    <v-btn @click="insertSymbol(1)">symbol ( ° )</v-btn>
                                </v-col>
                            </v-row>                                                     
                            <!-- Form tempat kejadian -->

                            <!-- Form ket tejadian -->
                            <v-textarea
                                ref="ket_terjadi"
                                v-model="ket_terjadi"
                                filled
                                rows="1"
                                auto-grow
                                clearable
                                label="Apa yang terjadi"
                                :error-messages="ket_terjadi_Errors"
                                @input="$v.ket_terjadi.$touch()"
                                @blur="$v.ket_terjadi.$touch()"
                            ></v-textarea>
                            <!-- Form ket tejadian -->

                            <v-row>
                                <v-col>
                                    <v-text-field
                                        v-model="pelaku"
                                        label="Nama Pelaku"
                                        placeholder="Masukkan nama pelaku"
                                        clearable
                                        filled
                                        :error-messages="pelaku_Errors"
                                        @input="$v.pelaku.$touch()"
                                        @blur="$v.pelaku.$touch()"
                                    ></v-text-field>
                                </v-col>
                                <v-col>
                                    <v-text-field
                                        ref="korban"
                                        v-model="korban"
                                        label="Nama Korban"
                                        placeholder="Masukkan nama korban"
                                        clearable
                                        filled
                                    ></v-text-field>
                                </v-col>
                            </v-row>

                            <!-- Form ket kejadian -->
                            <v-textarea
                                ref="ket_kejadian"
                                v-model="ket_kejadian"
                                filled
                                label="Bagaimana terjadi"
                                autofocus
                                rows=1
                                auto-grow
                                clearable
                                :error-messages="ket_kejadian_Errors"
                                @input="$v.ket_kejadian.$touch()"
                                @blur="$v.ket_kejadian.$touch()"
                            ></v-textarea>
                            <!-- Form ket kejadian -->
                        </v-card-text>
                        
                        <v-divider></v-divider>

                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="primary" text @click="submit(1)">Selanjutnya</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-stepper-content>

                <v-stepper-content step="2">
                    <v-card ref="form" class="mt-n4">
                        <v-card-text>
                            <v-row>
                                <v-col>
                                    <!-- Form ket pidana -->
                                   <v-autocomplete
                                        v-model="pidana"
                                        :items="pidana_list"
                                        item-text="pasal"
                                        item-value="id"
                                        dense
                                        chips
                                        small-chips
                                        label="Pidana"
                                        multiple
                                        required
                                        :error-messages="pidana_Errors"
                                        @blur="$v.pidana.$touch()"
                                    ></v-autocomplete>
                                    <p v-for="(value, index) in pidana" :key="index">
                                        {{ showPasal(value) }}
                                    </p>
                                    <!-- Form ket pidana -->

                                    <!-- Form ket kejadian -->
                                </v-col>
                                <v-col>
                                    <v-row dense class="pb-2">
                                        <v-col cols="12" v-for="(value, index) in saksi" :key="index">
                                            <v-card filled class="pa-2">
                                                <div class="overline mb-4"> Saksi {{ index+1}} </div>
                                                <v-text-field
                                                    v-model="value['nama']"
                                                    label="Nama"
                                                    placeholder="Isikan nama saksi"
                                                    @input="$v.saksi.$each[index].nama.$touch()"
                                                    @blur="$v.saksi.$each[index].nama.$touch()"
                                                ></v-text-field>
                                                <div v-if="$v.saksi.$each[index].nama.$dirty">
                                                    <div v-if="!$v.saksi.$each[index].nama.required">
                                                        <v-alert
                                                            dense
                                                            outlined
                                                            type="error"
                                                        >
                                                            {{ "Nama harus diisi" }}
                                                        </v-alert>
                                                    </div>
                                                </div>
                                                <v-text-field
                                                    v-model="value['umur']"
                                                    label="Umur"
                                                    placeholder="Isikan umur saksi"
                                                    @input="$v.saksi.$each[index].umur.$touch()"
                                                    @blur="$v.saksi.$each[index].umur.$touch()"
                                                ></v-text-field>
                                                <div v-if="$v.saksi.$each[index].umur.$dirty">
                                                    <div v-if="!$v.saksi.$each[index].umur.required">
                                                        <v-alert
                                                            dense
                                                            outlined
                                                            type="error"
                                                        >
                                                            {{ "Umur harus diisi" }}
                                                        </v-alert>
                                                    </div>
                                                    <div v-if="!$v.saksi.$each[index].umur.numeric">
                                                        <v-alert
                                                            dense
                                                            outlined
                                                            type="error"
                                                        >
                                                            {{ "harus berupa angka" }}
                                                        </v-alert>
                                                    </div>
                                                </div>
                                                <v-text-field
                                                    v-model="value['alamat']"
                                                    label="Alamat"
                                                    placeholder="Isikan Alamat saksi"
                                                    @input="$v.saksi.$each[index].alamat.$touch()"
                                                    @blur="$v.saksi.$each[index].alamat.$touch()"
                                                ></v-text-field>
                                                <div v-if="$v.saksi.$each[index].alamat.$dirty">
                                                    <div v-if="!$v.saksi.$each[index].alamat.required">
                                                        <v-alert
                                                            dense
                                                            outlined
                                                            type="error"
                                                        >
                                                            {{ "alamat harus diisi" }}
                                                        </v-alert>
                                                    </div>
                                                </div>
                                                
                                                <v-select
                                                    v-model="value['jenis_kelamin']"
                                                    :items="items_jk"
                                                    item-value="id"
                                                    item-text="jenis_kelamin_text"
                                                    label="Jenis Kelamin"
                                                    @input="$v.saksi.$each[index].jenis_kelamin.$touch()"
                                                    @blur="$v.saksi.$each[index].jenis_kelamin.$touch()"
                                                ></v-select>
                                                <div v-if="$v.saksi.$each[index].jenis_kelamin.$dirty">
                                                    <div v-if="!$v.saksi.$each[index].jenis_kelamin.required">
                                                        <v-alert
                                                            dense
                                                            outlined
                                                            type="error"
                                                        >
                                                            {{ "jenis kelamin harus diisi" }}
                                                        </v-alert>
                                                    </div>
                                                </div>
                                                
                                                <v-text-field
                                                    v-model="value['pekerjaan']"
                                                    label="Pekerjaan"
                                                    placeholder="Isikan pekerjaan saksi"
                                                    @input="$v.saksi.$each[index].pekerjaan.$touch()"
                                                    @blur="$v.saksi.$each[index].pekerjaan.$touch()"
                                                ></v-text-field>
                                                 <div v-if="$v.saksi.$each[index].pekerjaan.$dirty">
                                                    <div v-if="!$v.saksi.$each[index].pekerjaan.required">
                                                        <v-alert
                                                            dense
                                                            outlined
                                                            type="error"
                                                        >
                                                            {{ "Pekerjaan harus diisi" }}
                                                        </v-alert>
                                                    </div>
                                                </div>
                                                
                                                <v-card-actions v-if="saksi.length > 1">
                                                    <v-spacer></v-spacer>
                                                    <v-btn color="error" small dark @click="delSaksi(index)">
                                                        hapus
                                                        <v-icon right>cancel</v-icon>
                                                    </v-btn>
                                                </v-card-actions>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                    <v-btn v-if="saksi.length < 3" @click="addSaksi">{{ 'Tambah Saksi' }}</v-btn>
                                </v-col>
                            </v-row>
                        </v-card-text>
                        <v-divider></v-divider>
                        <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="primary" text @click="submit(2)">Selanjutnya</v-btn>
                        <v-btn color="primary" text @click="e1 = 1">kembali</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-stepper-content>

                <v-stepper-content step="3">
                    <v-card ref="form" class="mt-n4">
                        <v-card-text>
                            <v-textarea
                                v-model="barang_bukti"
                                filled
                                class="pt-2"
                                label="Barang Bukti"
                                :error-messages="barang_bukti_Errors"
                                @input="$v.barang_bukti.$touch()"
                                @blur="$v.barang_bukti.$touch()">
                            </v-textarea>
                            <v-row>
                                <v-col cols=10>
                                    <v-textarea
                                        ref="uraian"
                                        v-model="uraian"
                                        filled
                                        label="Uraian singkat kejadian"
                                        :error-messages="uraian_Errors"
                                        auto-grow
                                        rows="5"
                                        @input="$v.uraian.$touch()"
                                        @blur="$v.uraian.$touch()"
                                    ></v-textarea> 
                                </v-col>
                                <v-col cols=2>
                                    <v-btn @click="insertSymbol(2)">symbol ( ° )</v-btn>
                                </v-col>
                            </v-row>           
                        </v-card-text>
                        <v-divider></v-divider>
                        <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="primary" text @click="submit(3)">Selanjutnya</v-btn>
                        <v-btn color="primary" text @click="e1 = 2">kembali</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-stepper-content>

                <v-stepper-content step="4">
                    <v-card ref="form" class="pl-4">
                        <v-textarea
                            v-model="tindakan"
                            class="pt-2"
                            label="Tindakan"
                            filled
                            :error-messages="tindakan_Errors"
                            @input="$v.tindakan.$touch()"
                            @blur="$v.tindakan.$touch()"
                        ></v-textarea>
                        <v-divider></v-divider>
                        <v-row>
                            <v-col>
                                 <v-text-field
                                    ref="nama_pelapor"
                                    v-model="nama_pelapor"
                                    label="Pelapor"
                                    placeholder="Masukkan nama pelapor"
                                    clearable
                                    filled
                                    :error-messages="nama_pelapor_Errors"
                                    @input="$v.nama_pelapor.$touch()"
                                    @blur="$v.nama_pelapor.$touch()"
                                ></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field
                                    ref="pangkat_pelapor"
                                    v-model="pangkat_pelapor"
                                    label="Pangkat Pelapor"
                                    placeholder="Masukkan pangkat pelapor"
                                    clearable
                                    filled
                                    :error-messages="pangkat_pelapor_Errors"
                                    @input="$v.pangkat_pelapor.$touch()"
                                    @blur="$v.pangkat_pelapor.$touch()"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn v-if="action != 'edit' " color="primary" text @click="submit(4)">Simpan</v-btn>
                            <v-btn v-if="action == 'edit' " color="primary" text @click="postData">Simpan</v-btn>
                            <v-btn color="primary" text @click="e1 = 3">kembali</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-stepper-content>
            </v-stepper-items>
        </v-stepper>
    </v-col>
  </v-row>
</template>

<script>
import moment from 'moment'
import { required, minLength, maxValue, between, helpers } from 'vuelidate/lib/validators'

const alpha = helpers.regex('alpha', /^[a-zA-Z]*$/)
const numeric = helpers.regex('numeric', /^[0-9]*$/)
  export default {
    data: vm => ({
        id_abk: '1',
        drawer: false,
        small : true,
        e1: 1,
        waktu_kejadian: moment().tz('Asia/Jakarta').format('DD/MM/YYYY HH:mm'),
        waktu_laporan: moment().tz('Asia/Jakarta').format('DD/MM/YYYY HH:mm'),
        pelaku: null,
        korban: null,
        tempat_kejadian:null,
        ket_terjadi: null,
        ket_kejadian: null,
        pidana:[],
        pidana_list: [],
        tindakan:'',
        barang_bukti:'',
        uraian:'',
        nama_pelapor:'',
        pangkat_pelapor:'',
        textField_kejadian:
        {
            appendIcon: 'event',
            required: true,
            filled: true,
        },
        datePicker_kejadian:
        {
            max: moment().tz('Asia/Jakarta').format()
        }, 
        items_jk : [
            { id : 'L', jenis_kelamin_text: 'Laki-laki'},
            { id : 'P', jenis_kelamin_text: 'Perempuan'}
        ],
        saksi: [
            {
                nama: '',
                umur: '',
                pekerjaan: '',
                jenis_kelamin: '',
                alamat: '',
            },
        ],
        action: '',
    }),

    watch: {
    },

    validations: {
        waktu_kejadian: { required },
        tempat_kejadian: { required, minLength: minLength(4) },
        ket_terjadi: { required },
        ket_kejadian: { required },
        pelaku: { required },
        waktu_laporan: { required },
        pidana: { required },
        barang_bukti: { required },
        uraian: { required },
        tindakan: { required },
        nama_pelapor: { required },
        pangkat_pelapor: { required },
        saksi: { 
            required,
            $each: {
                nama : { required },
                umur : { required, numeric, minLength: minLength(1) },
                alamat : { required },
                jenis_kelamin : { required },
                pekerjaan : { required },
            }
         },

        validationGroup1: ['waktu_kejadian', 'tempat_kejadian', 'ket_terjadi', 'ket_kejadian', 'pelaku', 'waktu_laporan'],
        validationGroup2: ['pidana', 'saksi'],
        validationGroup3: ['uraian', 'barang_bukti'],
        validationGroup4: ['tindakan', 'nama_pelapor', 'pangkat_pelapor'],
    },

    computed: {
        waktu_kejadian_Errors () {
            const errors = []
            if (!this.$v.waktu_kejadian.$dirty) return errors
            !this.$v.waktu_kejadian.required && errors.push('Waktu kejadian harus diisi')
            return errors
        },
        
        tempat_kejadian_Errors () {
            const errors = []
            if (!this.$v.tempat_kejadian.$dirty) return errors
            !this.$v.tempat_kejadian.required && errors.push('Tempat kejadian harus diisi')
            !this.$v.tempat_kejadian.minLength && errors.push('Karakter terlalu sedikit')
            return errors
        },

        ket_terjadi_Errors () {
            const errors = []
            if (!this.$v.ket_terjadi.$dirty) return errors
            !this.$v.ket_terjadi.required && errors.push('Apa yang terjadi harus diisi')
            return errors
        },

        pelaku_Errors () {
            const errors = []
            if (!this.$v.pelaku.$dirty) return errors
            !this.$v.pelaku.required && errors.push('Nama pelaku harus diisi')
            // !this.$v.pelaku.alpha && errors.push('Karakter harus berupa huruf')
            return errors
        },

        ket_kejadian_Errors () {
            const errors = []
            if (!this.$v.ket_kejadian.$dirty) return errors
            !this.$v.ket_kejadian.required && errors.push('Ketarangan kejadian harus diisi')
            return errors
        },

        pidana_Errors () {
            const errors = []
            if (!this.$v.pidana.$dirty) return errors
            !this.$v.pidana.required && errors.push('pidana harus diisi!')
            return errors
        },

        saksi_Errors () {
            const errors = []
            if (!this.$v.saksi.$dirty) return errors
            !this.$v.saksi.required && errors.push('saksi harus diisi!')
            return errors
        },

        barang_bukti_Errors () {
            const errors = []
            if (!this.$v.barang_bukti.$dirty) return errors
            !this.$v.barang_bukti.required && errors.push('Barang bukti harus diisi')
            return errors
        },

        uraian_Errors () {
            const errors = []
            if (!this.$v.uraian.$dirty) return errors
            !this.$v.uraian.required && errors.push('uraian harus diisi')
            return errors
        },

        tindakan_Errors () {
            const errors = []
            if (!this.$v.tindakan.$dirty) return errors
            !this.$v.tindakan.required && errors.push('tindakan harus diisi')
            return errors
        },

        nama_pelapor_Errors () {
            const errors = []
            if (!this.$v.nama_pelapor.$dirty) return errors
            !this.$v.nama_pelapor.required && errors.push('nama pelapor harus diisi')
            return errors
        },

        pangkat_pelapor_Errors () {
            const errors = []
            if (!this.$v.pangkat_pelapor.$dirty) return errors
            !this.$v.pangkat_pelapor.required && errors.push('pangkat pelapor harus diisi')
            return errors
        },
    },

    created(){
        const id = this.$route.params.id
        if(id){
            this.action = "edit"
            this.getData(id)
        }
        this.getPasal()
    },

    methods: {
        async postData(){
            const id = this.$route.params.id
            const postData = {
                id_abk: this.id_abk,
                waktu_kejadian: this.waktu_kejadian,
                tempat_kejadian: this.tempat_kejadian,
                ket_terjadi: this.ket_terjadi,
                pelaku: this.pelaku,
                korban: this.korban,
                ket_kejadian: this.ket_kejadian,
                pidana: this.pidana,
                saksi: this.saksi,
                waktu_laporan: this.waktu_laporan,
                barang_bukti: this.barang_bukti,
                uraian_kejadian: this.uraian,
                tindakan_kejadian: this.tindakan,
                nama_pelapor: this.nama_pelapor,
                pangkat_pelapor: this.pangkat_pelapor,
            }

            if(this.action == 'edit'){
                var $this = this
                await axios.put('laporan/' + id, postData)
                .then(function (response) {
                    $this.$swal({
                        title: 'Data berhasil diperbarui',
                        text: "You won't be able to revert this!",
                        type: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        cancelButtonText: 'tutup',
                        confirmButtonText: 'print'
                        }).then((result) => {
                        if (result.value) {
                            window.open("print/"+id, '_blank');
                        }
                        $this.$router.push('/')
                    })
                })
                .catch(function (error) {
                    $this.$swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal mengubah data, pastikan input data benar!',
                    })
                })
            }else{
                var $this = this
                await axios.post('laporan', postData, {headers: {'Accept': 'application/json'}})
                .then(function (response) {
                    $this.$swal({
                        title: 'Data berhasil disimpan',
                        type: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        cancelButtonText: 'Tutup',
                        confirmButtonText: 'Print'
                        }).then((result) => {
                        if (result.value) {
                            window.open("print/"+response.data.id, '_blank');
                        }
                        $this.$router.push('/')
                    })
                })
                .catch(function (error) {
                    $this.$swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal menyimpan data, pastikan input data benar!',
                    })
                })
            }
        },
         
        async getData(id){
            const { data } = await axios.get('laporan/' + id)
            const pidana_data = data.pidana
            const saksi_data = data.saksi

            this.$delete(this.saksi, 0)

            this.waktu_kejadian = moment(data.waktu_kejadian).tz('Asia/Jakarta').format('DD/MM/YYYY HH:mm'),
            this.tempat_kejadian = data.tempat_kejadian,
            this.ket_terjadi = data.ket_terjadi,
            this.ket_kejadian = data.ket_kejadian,
            this.pelaku = data.pelaku,
            this.korban = data.korban,
            this.waktu_laporan = moment(data.waktu_laporan).tz('Asia/Jakarta').format('DD/MM/YYYY HH:mm'),

            pidana_data.forEach(item => this.pidana.push(item.id_pasal))

            Object.keys(saksi_data).forEach(key => {
                const id = saksi_data[key]['id']
                const nama_saksi = saksi_data[key]['nama_saksi']
                const umur_saksi = saksi_data[key]['umur_saksi']
                const pekerjaan_saksi = saksi_data[key]['pekerjaan_saksi']
                const jenis_kelamin_saksi = saksi_data[key]['jenis_kelamin_saksi']
                const alamat_saksi = saksi_data[key]['alamat_saksi']
        
                const val =
                {
                    id: id,
                    nama: nama_saksi,
                    umur: umur_saksi,
                    pekerjaan: pekerjaan_saksi,
                    jenis_kelamin: jenis_kelamin_saksi,
                    alamat: alamat_saksi
                }
                this.saksi.push(val)
            })

            this.barang_bukti = data.barang_bukti,
            this.uraian = data.uraian_kejadian,
            this.tindakan = data.tindakan_kejadian        
            this.nama_pelapor = data.nama_pelapor,
            this.pangkat_pelapor = data.pangkat_pelapor    
        }, 

        async getPasal(){
            const { data } = await axios.get('pasal')
            this.pidana_list = data
        },

        formatDate (date) {
            if (!date) return null
            const [year, month, day] = date.split('-')
            return `${day}/${month}/${year}`
        },

        insertSymbol(model){
            var v = ''
            var cursorPosStart = ''
            var cursorPosEnd =  '' 
            var textBefore = ''
            var textAfter = ''
            switch (model) {
                case 1:
                    v = this.tempat_kejadian
                    cursorPosStart = this.$refs.tempat_kejadian.$refs.input.selectionStart
                    cursorPosEnd = this.$refs.tempat_kejadian.$refs.input.selectionEnd
                    textBefore = v.substring(0,  cursorPosStart);
                    textAfter  = v.substring(cursorPosEnd, v.length);
                    this.tempat_kejadian = textBefore + '°' + textAfter
                    this.$refs.tempat_kejadian.focus()
                    break;
                case 2:
                    v = this.uraian
                    cursorPosStart = this.$refs.uraian.$refs.input.selectionStart
                    cursorPosEnd = this.$refs.uraian.$refs.input.selectionEnd
                    textBefore = v.substring(0,  cursorPosStart);
                    textAfter  = v.substring(cursorPosEnd, v.length);
                    this.uraian = textBefore + '°' + textAfter
                    this.$refs.uraian.focus()
                default:
                    break;
            }
        },

        addSaksi(){
            const val =
            {
                nama: '',
                umur: '',
                jenis_kelamin: '',
                pekerjaan: '',
                alamat: ''
            }
            this.saksi.push(val)
        },

        delSaksi(index){
            this.$delete(this.saksi, index)
        },

        submit(index) {
            switch (index) {
                case 1:
                    if (this.$v.validationGroup1.$invalid) {
                        this.$v.validationGroup1.$touch()
                    }else {
                        this.e1 = index + 1
                    }
                    break;

                case 2:
                    if (this.$v.validationGroup2.$invalid) {
                        this.$v.validationGroup2.$touch()
                    }else {
                        this.e1 = index + 1
                    }
                    break;

                case 3:
                    if (this.$v.validationGroup3.$invalid) {
                        this.$v.validationGroup3.$touch()
                    }else {
                        this.e1 = index + 1
                    }
                    break;
                case 4:
                    if (this.$v.validationGroup4.$invalid) {
                        this.$v.validationGroup4.$touch()
                    }else {
                        this.postData()
                    }
                    break;
                default:
                    break;
            }
        },

        showPasal(id){
            const result = this.pidana_list.filter(obj => obj.id === id);
            return result[0].pasal + ' - ' + result[0].ket_pasal
        },
    },
  }
</script>