<template>
  <v-stepper v-model="e1">
      <v-overlay :value="overlay">
            <v-row
            align="center"
            justify="justify"
          >
                <v-progress-linear
                    color="white"
                    indeterminate
                    rounded
                    height="6"
                ></v-progress-linear>
            </v-row>
            <v-row
                align="center"
                justify="justify"
            >
                <v-chip
                class="ma-4"
                color="green"
                text-color="white"
                >
                Train Data in Process, Please wait...
                </v-chip>
            </v-row>
        </v-overlay>
    <v-stepper-header>
      <v-stepper-step :complete="e1 > 1" step="1">Person Data</v-stepper-step>
      <v-divider></v-divider>
      <v-stepper-step :complete="e1 > 2" step="2">Person Photo</v-stepper-step>
    </v-stepper-header>

    <v-stepper-items>
      <v-stepper-content step="1">
        <!-- <div v-if="error_show">
            <v-alert dense type="error" outlined v-if="$v.person_data.$anyError">
                <div v-if="!$v.person_data.name.required">Camera name is required</div>
            </v-alert>
            <v-alert dense type="error" outlined v-if="!statusConnection">
                Cannot connect stream server!
            </v-alert>
        </div> -->

        <v-card>
            <v-row class="pa-2 mt-n4 mb-n4">
                <v-col cols="6">
                    <p class="font-weight-regular mb-1">NIK</p>
                    <v-text-field ref="nik" v-model="person_data.nik" solo></v-text-field>
                </v-col>
                <v-col cols="6">
                    <p class="font-weight-regular mb-1">Nama</p>
                    <v-text-field v-model="person_data.name" solo label="Isi nama lengkap tanpa gelar"></v-text-field>
                </v-col>
                <v-col class="mt-n10" cols="6">
                    <p class="font-weight-regular mb-1">Tempat Lahir</p>
                    <v-text-field v-model="person_data.place_of_birth" solo label="Isi tempat lahir"></v-text-field>
                </v-col>
                <v-col class="mt-n10" cols="6">
                    <p class="font-weight-regular mb-1">Tanggal Lahir</p>
                    <v-text-field v-model="person_data.date_of_birth" solo label="Isi tanggal lahir"></v-text-field>
                </v-col>
                <v-col class="mt-n10" cols="6">
                    <p class="font-weight-regular mb-1">Alamat</p>
                    <v-textarea
                        v-model="person_data.address"
                        label="Alamat"
                        solo
                        rows="1"
                        auto-grow
                    ></v-textarea>
                </v-col>
                <v-col class="mt-n10" cols="3">
                    <p class="font-weight-regular mb-1">Jenis Kelamin</p>
                    <v-autocomplete
                        v-model="person_data.gender"
                        :items="gender_items"
                        item-value="value"
                        item-text="text"
                        solo
                    ></v-autocomplete>
                </v-col>
                <v-col class="mt-n10" cols="3">
                    <p class="font-weight-regular mb-1">Agama</p>
                    <v-autocomplete
                        v-model="person_data.religion"
                        item-value="id"
                        item-text="religion_name"
                        :items="religion_items"
                        solo
                    ></v-autocomplete>
                </v-col>
                <v-col class="mt-n10" cols="6">
                    <p class="font-weight-regular mb-1">Status Perkawinan</p>
                    <v-autocomplete
                    v-model="person_data.marital_status"
                    :items="marital_status_items"
                    item-value="value"
                    item-text="text"
                    label="Pilih jenis kelamin"
                    solo
                    ></v-autocomplete>
                </v-col>
                <v-col class="mt-n10" cols="6">
                    <p class="font-weight-regular mb-1">Pekerjaan</p>
                    <v-autocomplete
                    v-model="person_data.profession"
                    :items="profession_items"
                    item-value="id"
                    item-text="profession_name"
                    label="Pilih jenis profession"
                    solo
                    ></v-autocomplete>
                </v-col>
                <v-col class="mt-n10" cols="6">
                    <p class="font-weight-regular mb-1">Kewarganegaraan</p>
                    <v-text-field v-model="person_data.citizenship" solo label="Isi kewarganegaraan"></v-text-field>
                </v-col>
            </v-row>
            <v-btn
                class="ma-2"
                color="primary"
                @click="next()"
            >
            Continue
            </v-btn>
            <v-btn
                class="ma-2"
                to="/face_recognition/person"
            >
            Cancel
            </v-btn>
        </v-card>
      </v-stepper-content>

      <v-stepper-content step="2">
        <div v-if="error_show">
            <v-alert dense type="error" outlined>
                Please make line for counting vehicle!
            </v-alert>
        </div>
        <v-file-input
            v-model="photos"
            label="Photo input"
            accept="image/*"
            multiple
            prepend-icon="mdi-camera"
            @change="check"
        ></v-file-input>
            <v-row class="px-4">
                <v-col cols="12">
                    <v-row
                        align="start"
                        justify="start"
                    >
                        <v-card class="ma-2"
                            v-for="(item, index) in img_url"
                            :key="item"
                        >
                            <v-img :src="item" max-width="200" height="250"></v-img>
                            <v-card-actions>
                            <v-spacer></v-spacer>
                                <v-btn small color="error" fab @click="deletePhoto(index)">
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            <v-spacer></v-spacer>
                            </v-card-actions>
                        </v-card>
                    </v-row>
                </v-col>
            </v-row>

        <v-btn @click="save" color="primary">Save</v-btn>
        <v-btn text @click="e1 = 1">Back</v-btn>
      </v-stepper-content>

    </v-stepper-items>
  </v-stepper>
</template>

<script>
// import { required, minLength,ipAddress, url, or, numeric, between, decimal } from 'vuelidate/lib/validators'
  export default {
    data () {
        return {
            e1: 1,
            error_show: false,
            person_data: {
                nik : null,
                address : null,
                place_of_birth : null,
                date_of_birth : null,
                gender : 'L',
                religion : 1,
                marital_status : 'BK',
                profession : 1,
                citizenship : 'WNI',
            },
            photos: [],
            img_url: [],

            gender_items : [
                { text : 'LAKI-LAKI' , value: 'L'},
                { text : 'PEREMPUAN' , value: 'P'},
            ],
            marital_status_items : [
                { text : 'Belum Kawin' , value: 'BK'},
                { text : 'Kawin' , value: 'K'},
            ],
            religion_items : [],
            profession_items : [],
            overlay: false,
        };
    },

    validations: {

    },

    created() {
        this.$Progress.start()
        this.$nextTick(() => this.$refs.nik.focus())
        if (this.$route.params.id) {
            this.initialize(this.$route.params.id)
        }
        this.loadCombobox()
        this.$Progress.finish()
    },

    methods: {
        async initialize(nik){
            let self = this
            try {
                const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER +'/person/' + nik + '/edit')
                this.person_data = data.person_data
                data.photos.forEach(element => {
                    self.img_url.push('data:image/jpeg;base64,' + element)
                });
            } catch (error) {
                window.console.log(Object.keys(error), error.message);
            }
        },

        async loadCombobox(){
            const response_profession = await this.axios.get(process.env.VUE_APP_IP_SERVER +'/profession')
            this.profession_items = response_profession.data
            const response_religion = await this.axios.get(process.env.VUE_APP_IP_SERVER +'/religion')
            this.religion_items = response_religion.data
        },

        async save(){
            this.overlay = true
            try {
                if (this.$route.params.id) {
                    await this.axios.put(process.env.VUE_APP_IP_SERVER + '/person/' + this.$route.params.id, {person_data : this.person_data, photos : this.img_url})
                }else{
                    await this.axios.post(process.env.VUE_APP_IP_SERVER + '/person', { person_data : this.person_data, photos : this.img_url})
                }
                this.overlay = false,
                this.$router.push({path: '/face_recognition/person'})
            } catch (error) {
                this.overlay = false,
                alert(error)
            }
        },

        next(){
            this.e1 = 2
        },

        check(){
            let self = this
            this.photos.forEach(element => {
                let reader = new FileReader()
                reader.onload = (event) => {
                    self.img_url.push(event.target.result)
                }
                reader.readAsDataURL(element)
            });
        },

        deletePhoto(index){
            this.img_url.splice(index, 1)
        }
    }
  }
</script>