<template>
    <v-row>
         <v-col>
            <v-data-table
                :headers="headers"
                :items="persons"
                class="elevation-1"
            >
                <template v-slot:item.gender="{ item }">
                    <div v-if="item.gender == 'L'">Laki-laki</div>
                    <div v-else>Perempuan</div>
                </template>
                <template v-slot:item.religion="{ item }">
                    {{ item.religion.religion_name }}
                </template>
                <template v-slot:item.profession="{ item }">
                    {{ item.profession.profession_name }}
                </template>
                <template v-slot:top>
                    <v-toolbar>
                        <v-toolbar-title>Person data</v-toolbar-title>
                        <v-divider
                        class="mx-4"
                        inset
                        vertical
                        ></v-divider>
                        <v-spacer></v-spacer>
                        <v-btn color="orange darken-1" dark class="mx-2" @click="train" icons="mdi-face-recognition">
                            <v-icon left>mdi-face-recognition</v-icon> Train Data</v-btn>
                        <v-btn color="primary" dark class="mx-2" to="/face_recognition/person/form">
                        <v-icon left>mdi-account-plus</v-icon> Add Person</v-btn>
                        <v-btn color="green" dark class="mx-2" @click="reload">
                            <v-icon left>mdi-reload</v-icon> Refresh</v-btn>
                    </v-toolbar>
                </template>
                <template v-slot:item.action="{ item }">
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on }">
                            <v-icon
                                v-on="on"
                                small
                                class="mr-2"
                                @click="personDetail(item.nik)"
                            >
                                mdi-account-search
                            </v-icon>
                        </template>
                        <span>View Data</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on }">
                            <v-icon
                                v-on="on"
                                small
                                class="mr-2"
                                @click="route(item.nik)"
                            >
                                mdi-pencil
                            </v-icon>
                        </template>
                        <span>Edit Data</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on }">
                            <v-icon
                                v-on="on"
                                small
                                class="mr-2"
                                @click="deletePerson(item.nik)"
                            >
                                mdi-delete
                            </v-icon>
                        </template>
                        <span>Delete</span>
                    </v-tooltip>
                </template>
            </v-data-table>
         </v-col>

        <v-dialog v-model="dialog" v-if="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
            <v-card>
                <v-toolbar dark color="primary">
                <v-btn icon dark @click="dialog = false">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
                <v-toolbar-title>Personal Data</v-toolbar-title>
                <v-spacer></v-spacer>
                </v-toolbar>
                <v-row>
                    <v-col cols=4>
                        <div class="pa-2 pl-8">
                            <p class="title">Personal Data</p>
                            <p class="caption mb-n1">Nama</p>
                            <p class="font-weight-bold">{{ person_data.name }}</p>

                            <p class="caption mb-n1">Tempat / Tanggal Lahir</p>
                            <p class="font-weight-bold">{{ person_data.place_of_birth + ', ' + person_data.date_of_birth}}</p>

                            <p class="caption mb-n1">Jenis Kelamin</p>
                            <p v-if="person_data.gender == 'L'" class="font-weight-bold">LAKI-LAKI</p>
                            <p v-if="person_data.gender == 'P'" class="font-weight-bold">PEREMPUAN</p>

                            <p class="caption mb-n1">Alamat</p>
                            <p class="font-weight-bold">{{ person_data.address }}</p>
                        </div>
                    </v-col>
                    <v-col cols=4>
                        <div class="pa-2 pl-8">
                            <p class="title pt-8"></p>
                            <p class="caption mb-n1">Agama</p>
                            <p class="font-weight-bold">{{ person_data.religion.religion_name }}</p>

                            <p class="caption mb-n1">Status Perkawinan</p>
                            <p class="font-weight-bold" v-if="person_data.marital_status == 'BK'">BELUM KAWIN</p>
                            <p class="font-weight-bold" v-if="person_data.marital_status == 'K'">KAWIN</p>

                            <p class="caption mb-n1">Jenis Kelamin</p>
                            <p class="font-weight-bold">{{ person_data.profession.profession_name }}</p>

                            <p class="caption mb-n1">Kewarganegaraan</p>
                            <p class="font-weight-bold">{{ person_data.citizenship }}</p>
                        </div>
                    </v-col>
                    <v-col cols=2>
                        <v-card class="mt-8">
                            <vue-load-image>
                                <img slot="image" :src="person_data.photo" style="width: 100%"/>
                                <v-img slot="preloader" src="@/assets/preloader.gif"/>
                                <v-img slot="error" src="@/assets/default_photo.png" height="300"/>
                            </vue-load-image>
                        </v-card>
                    </v-col>
                </v-row>
                <v-divider></v-divider>
                <!-- <v-card>
                    <v-row class="pa-4 pb-0">
                        <v-col
                            v-for="n in 6"
                            :key="n"
                            class="d-flex child-flex"
                            cols="2"
                            >
                            <v-card flat tile>
                                <v-img
                                :src="`https://picsum.photos/500/300?image=${n * 5 + 10}`"
                                :lazy-src="`https://picsum.photos/10/6?image=${n * 5 + 10}`"
                                aspect-ratio="1.4"
                                class="grey lighten-2"
                            />
                                <v-card-subtitle class="mb-n4">ID Camera : {{ getRandomID() }}</v-card-subtitle>
                                <v-divider></v-divider>
                                <v-card-subtitle class="mt-n4">Time : {{ getRandomDate() }}</v-card-subtitle>
                            </v-card>
                        </v-col>
                    </v-row>
                    <div class="text-center pb-4">
                        <v-pagination
                        v-model="page"
                        :length="4"
                        circle
                        ></v-pagination>
                    </div>
                </v-card> -->
            </v-card>
        </v-dialog>

        <v-overlay :value="overlay">
            <v-row
            align="center"
            justify="center"
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
                justify="center"
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
    </v-row>
</template>

<script>
import moment from 'moment'
import VueLoadImage from 'vue-load-image'
    export default {
        components: {'vue-load-image': VueLoadImage },
        data: () => ({
            headers: [
                {
                    text: 'Name',
                    align: 'left',
                    sortable: false,
                    value: 'name',
                },
                { text: 'Gender', value: 'gender' },
                { text: 'Religion', value: 'religion' },
                { text: 'Profession', value: 'profession' },
                { text: 'Actions', value: 'action', width: 180, sortable: false },
            ],
            persons: [],
            person_data: '',
            page: 1,

            dialog: false,
            overlay: false,
            notifications: false,
            sound: true,
            widgets: false,
        }),

        created() {
            this.$Progress.start()
            this.initialize()
        },

        mounted() {
            this.$Progress.finish()            
        },

        methods : {
            async initialize(){
                const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/person')
                this.persons = data
            },

            async personDetail(id){
                const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/person/' + id)
                this.person_data = data.person_data
                this.person_data.photo = 'data:image/jpeg;base64,' + data.photos[0]
                this.dialog = true
            },

            deletePerson(id){
                this.axios.delete(process.env.VUE_APP_IP_SERVER + '/person/' + id).then(response => {
                    alert("sukses")
                    return response
                }).catch(error => {
                    window.console.log(error)
                });
            },

            route(nik){
                this.$router.push('/face_recognition/person/' + nik + '/edit') 
            },

            reload(){
                this.overlay = true
                this.initialize()
                this.overlay = false
            },

            train(){
                this.overlay = true
                this.axios.get(process.env.VUE_APP_IP_SERVER + '/train').then(response => {
                    this.overlay = false
                    return response
                }).catch(error => {
                    this.overlay = false
                    window.console.log(error)
                });
            },

            getRandomID(){
                return Math.floor(Math.random() * Math.floor(20));
            },

            getRandomDate() {
                return moment().tz('Asia/Jakarta').format('DD/MM/YY HH:mm') + ' WIB'
            }
        }
    }
</script>