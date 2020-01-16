<template>
    <v-row>
      <v-col cols="12" v-if="alert">
        <v-alert 
          class="mb-n4"
          v-model="alert" 
          type="error"
          dense
        >
          {{ error_text }}
        </v-alert>
      </v-col>
      <v-col
        :cols="12"
        md="8"
      >
        <v-card
          class="pa-2"
          height="450"
          tile
          outlined
        >
          <vue-load-image>
            <img slot="image" :src="videoDefault" style="width: 100%"/>
            <v-img slot="preloader" src="@/assets/preloader.gif"/>
            <v-img slot="error" src="@/assets/offline.jpg" aspect-ratio="1.7"/>
          </vue-load-image>
        </v-card>
      </v-col>

      <v-col
        :cols="12"
        md="4"
      >
         <v-card
          class="pa-2 overflow-y-auto"
          height="450"
          tile
          outlined
        >
          <div v-if="face_identity.length == 0">
            <v-img src="@/assets/no_result.gif"></v-img>
          </div>
          <div v-else>
            <div class="overline ">Valid Identity Face</div>
            <v-divider></v-divider>
            <v-row 
              class="mb-n4"
              v-for="item in face_identity"
              :key="item.time" 
              align="center"
              justify="center"
              >
              <v-col cols="4">
                <center>
                  <v-img class="mb-n2" :src="item.photo" ></v-img>
                </center>
              </v-col>
              <v-col cols="3">
                <v-progress-circular
                  :rotate="90"
                  :size="65"
                  :width="10"
                  :value="item.percentage"
                  color="blue"
                >
                  {{ item.percentage }}
                </v-progress-circular>
              </v-col>
              <v-col cols="4">
                <center>
                  <v-img class="mb-n2" :src="'http://localhost:8001/storage/face_history/' + item.unique_id + '.jpg'"></v-img>
                </center>
              </v-col>
              <p class="overline">{{item.name}} - </p>
              <p class="overline"> {{item.time}}</p>
            </v-row>
          </div>
        </v-card>
      </v-col>
      
      <v-col
        cols="12"
      >
        <v-card
          class="pa-2 justify-center"
          outlined
          tile
          
        >
          <div v-if="face_all.length == 0">
            <v-layout justify-center>
                <v-img src="@/assets/no_result.gif" max-width="500"></v-img>
            </v-layout>
          </div>
          <div v-else>
            <div class="overline">History Detection</div>
            <v-divider></v-divider>
            <v-row class="pl-2">
              <v-col cols="auto"
                v-for="item in face_all"
                :key="item.time" 
              >
                <v-card max-width="160">
                  <v-img :src="'http://localhost:8001/storage/face_history/' + item.unique_id + '.jpg'" height="200"></v-img>
                  <center>
                      <p>{{ item.name }}</p>
                      <p class="overline mt-n4">{{ item.time }}</p>
                  </center>
                </v-card>
              </v-col>
            </v-row>
            <v-card-actions>
              <v-spacer></v-spacer>
                <v-pagination
                  class="pt-2"
                  v-model="page"
                  :length="4"
                  prev-icon="mdi-menu-left"
                  next-icon="mdi-menu-right"
                ></v-pagination>
              <v-spacer></v-spacer>
            </v-card-actions>
          </div>
        </v-card>
      </v-col>
    </v-row>
</template>

<script>
import VueLoadImage from 'vue-load-image'

  export default {
    components: {'vue-load-image': VueLoadImage },
    props: {
      source: String,
    },
    data: () => ({
        alert : false,
        error_text: '',
        page: 1,
        chartHide: false,

        id_camera : '',
        port: '',
        camera_list:[],
        videoDefault: "@/assets/loading.gif",

        face_all :[],
        face_identity :[],
    }),

    created () {
      this.$Progress.start()
      this.id_camera = this.$route.params.id
      this.initialize()      
    },
    
    mounted(){
      this.$Progress.finish()
    },

    watch: {
        id_camera:function() {
          this.$socket.emit('join', this.$route.params.id);
          this.getData(this.$route.params.id)
        },
    },

    methods: {
        async initialize () {
          const { data } = await this.$http.get(process.env.VUE_APP_IP_SERVER + '/camera')
          this.camera_list = data
        },

        async getData(id_camera){
          try {
              const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/camera/' + id_camera)
              this.port = data['port']
              this.checkConnection(this.port)
            } catch (error) {
                window.console.log(Object.keys(error), error.message);
            }
        },

        changeRoute(){
          this.$socket.emit('leave', this.$route.params.id);
          this.$router.push({path: '/live/'})
          this.id_camera = this.$route.params.id
        },

        checkConnection(port){
          const _self = this
          this.axios.get(process.env.VUE_APP_IP_SERVER_WITHOUT_PORT + ':' + port).then(function(){
            _self.videoStream()
          }).catch(function(error){
              if (!error.response) {
                window.console.log(error)
                _self.streamStatus = false
                _self.imageLoadError()
                _self.text = "Can't connect video stream !"
                _self.color_snackbar = "red darken-1"
              } else {
                const code = error.response.status
                const response = error.response.data
                _self.text = "Can't connect video stream ! (" + code + ") response: " +  response
                _self.color_snackbar = "yellow darken-1"
              }
              _self.snackbar = true
          });
        },

        videoStream(){
            let random = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)
            this.videoDefault = process.env.VUE_APP_IP_SERVER_WITHOUT_PORT + ":" + this.port + "/" + random + "/stream.mjpg"
        },

        imageLoadError(){
            this.videoDefault = require('@/assets/offline.jpg')
        },

    },

    sockets: {
      connect: function (){
          window.console.log('socket connected')
      },
      face_detect: function (data) {
          if (this.face_all.length == 12) {
            this.$delete(this.face_all, 11)
          }
          this.face_all.unshift(data)

          if (data.name != "Unknown") {
            this.axios.get('http://localhost:8001/get_face/' + data.nik).then(
              response => {
                if (this.face_identity.length == 12) {
                  this.$delete(this.face_identity, 11)
                }
                data.photo = 'data:image/jpeg;base64,' + response.data
                this.face_identity.unshift(data)
              }
            ).catch(error => {
                window.console.log(error)
            });
          }
      }
    },

    beforeDestroy: function() {
        this.$socket.emit('leave', this.$route.params.id);
    },

  }
</script>
