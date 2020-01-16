<template>
    <v-container fluid grid-list-md>
      <v-layout wrap>
        <v-flex class="pb-4" d-flex xs12 sm4 md3 v-for="item of cameras" :key="item.id_cctv">
          <v-card width="300">
            <vue-load-image>
              <img slot="image" :src="item.ip_local +':'+ item.port+ '/' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15) +'/stream.mjpg'" style="width: 100%"/>
              <v-img slot="preloader" src="@/assets/preloader.gif"/>
              <v-img slot="error" src="@/assets/offline.jpg" aspect-ratio="1.7"/>
            </vue-load-image>
              <v-card-actions>
                <v-spacer></v-spacer>
                    <v-btn :to="'/anpr/'+item.id+'/stream'">
                      {{ item.name + ' ' }}
                      <div v-if="item.status == 1">
                        <v-icon class="pl-2" color="green" left >mdi-brightness-1</v-icon>
                      </div>
                      <div v-else>
                        <v-icon class="pl-2" color="red" left >mdi-brightness-1</v-icon>
                      </div>
                    </v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
          </v-card>
        </v-flex>
      </v-layout>
        <div class="text-center" v-if="length > 1">
          <v-pagination
            v-model="page"
            :length="length"
            circle
          ></v-pagination>
        </div>
    </v-container>

</template>

<script>
import VueLoadImage from 'vue-load-image'
  export default {
    components: {
      'vue-load-image': VueLoadImage
    },
    props: {
      source: String,
    },
    data: () => ({
      cameras:[],
      page: 1,
      length : null,
    }),

    created () {
      this.$Progress.start()
      this.initialize()
      this.$Progress.finish()
    },

    watch :{
      page(){
        this.$Progress.start()
        this.initialize()
        this.$Progress.finish()
      }
    },

    methods: {
      async initialize () {
        let _self = this
        const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/camera?system=anpr&page=' + this.page)
        this.cameras = data.data
        this.length = data.last_page

        data.data.forEach((element, i) => {
          this.axios.get(element.ip_local + ':' + element.port).then(function(){
            _self.cameras[i].status = 1
          }).catch(function(error){
              if (!error.response) {
                // network error
                _self.cameras[i].status = 0
                _self.cameras[i].streamStatus = false
              } else {
                // http status code
                const code = error.response.status
                window.console.log(code)
                // response data
                const response = error.response.data
                window.console.log(response)
              }
          });
        });
      },

      videoStream(port){
          let random = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)
          return process.env.VUE_APP_IP_SERVER_WITHOUT_PORT+":" + port + "/" + random + "/stream.mjpg"
      },
    },
  }
</script>
