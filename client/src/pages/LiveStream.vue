<template>
    <v-container fluid grid-list-md>
      <v-layout wrap>
        <v-flex d-flex xs12 sm6 md3 v-for="item of cameras" :key="item.id_cctv">
          <v-card class="mx-auto" width="300">
            <div v-if="item.streamStatus">
              <v-img :src="item.stream"/>
            </div>
            <div v-else>
              <v-img src="@/assets/offline.jpg"/>
            </div>
            <v-img slot="error" src="@/assets/offline.jpg" aspect-ratio="1.7"/>
            <!-- <vue-load-image>
              <img slot="image" :src="'http://localhost:'+item.port+'/stream.mjpg'" style="max-width:276px;"/>
              <img slot="preloader" src="@/assets/loading.gif"/>
            </vue-load-image> -->
              <v-card-actions>
                <v-spacer></v-spacer>
                    <v-btn :to="'/live/'+item.id">
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
    </v-container>

</template>

<script>
// import VueLoadImage from 'vue-load-image'
  export default {
    // components: {
    //   'vue-load-image': VueLoadImage
    // },
    props: {
      source: String,
    },
    data: () => ({
      cameras:[],
      imageDefault: '/storage/default/loading.gif',
      imageError: '/storage/default/image-404.png',
      video: ''
    }),

    created () {
      this.initialize()
    },

    methods: {
      async initialize () {
        let _self = this
        const { data } = await this.axios.get('http://127.0.0.1:8001/camera')
        this.cameras = data

        data.forEach((element, i) => {
          this.axios.get('http://localhost:' + element.port).then(function(){
            _self.cameras[i].status = 1
            let random = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)
            _self.cameras[i].stream = 'http://localhost:' + element.port + '/' + random + '/stream.mjpg'
            _self.cameras[i].streamStatus = true
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
          return "http://127.0.0.1:" + port + "/" + random + "/stream.mjpg"
      },
    },
  }
</script>
