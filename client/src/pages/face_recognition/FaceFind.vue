<template>
    <v-row
        align="start"
        justify="center"
    >
        <v-col cols="12">
            <v-card class="pa-2" outlined>
                <v-row class="my-n4">
                    <v-col class="mt-n2" cols="6">
                        <v-subheader class="pl-8">Photo File</v-subheader>
                        <v-file-input
                            v-model="photo_file"
                            prepend-icon="mdi-camera"
                            dense
                            @change="check"
                        ></v-file-input>
                    </v-col>
                    <v-col class="mt-2" cols="4">
                        <v-subheader class="pl-0">Tolerance</v-subheader>
                        <v-slider
                        dense
                        v-model="tolerance"
                        thumb-label="always"
                        ></v-slider>
                    </v-col>
                    <v-col class="mt-2" cols="1">
                        <v-btn color="primary" small dark @click="findAction">
                            Filter
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card>
        </v-col>

        <v-col cols="12">
            <v-card class="pa-2" v-if="showBox == true">
                <v-row
                    class="pa-1"
                    align="center"
                    justify="space-around"
                >
                <v-col cols="auto">
                    <v-card> 
                        <v-img width="200" height="300" :src="photo" ></v-img>
                    </v-card>
                </v-col>
                <v-col cols="2">
                    <v-progress-circular
                        :rotate="-90"
                        :size="200"
                        :width="20"
                        :value="percentage"
                        color="primary"
                        >
                        <p class="display-2 pt-5"> {{ percentage}}</p>
                    </v-progress-circular>
                </v-col>
                 <v-col cols="auto">
                     <v-card>
                        <v-img width="200" height="300" :src="photo_compare"></v-img>
                     </v-card>
                </v-col>
                </v-row>
                
                <v-divider></v-divider>
                <v-row
                    class="pa-1"
                    align="start"
                    justify="center"
                >
                    <v-col
                        v-for="card in cards"
                        :key="card.title"
                        :cols="card.flex"
                    >
                    <v-card outlined>
                        <!-- <vue-load-image>
                            <img slot="image" :src="card.src" style="width: 100%"/>
                            <v-img slot="preloader" src="@/assets/preloader.gif"/>
                            <v-img slot="error" src="@/assets/offline.jpg" aspect-ratio="1.7"/>
                        </vue-load-image> -->
                        <v-img
                            :src="card.src"
                            class="white--text align-end"
                            height="200px"
                        >
                        </v-img>
                        <v-card-title v-text="card.title" class="subtitle-2 my-n2"></v-card-title>
                        <v-card-text class="overline mt-n4 mb-n2">
                            <div>Camera 1</div>
                            <div>2019-02-23 18:00 WIB</div>
                        </v-card-text>
                    </v-card>
                    </v-col>
                </v-row>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
// import VueLoadImage from 'vue-load-image'
export default {
    // components: {'vue-load-image': VueLoadImage },
    props: {
      source: String,
    },
    data: () => ({
        cards: [],
        status: null,
        photo : null,
        photo_compare : '@/assets/loading.gif',
        photo_file: null,
        tolerance: 50,
        showBox : false,
        progress: true,
        percentage: 0
    }),

    methods: {
        async findAction(){
            this.showBox = true
            this.cards = []
            let data = new FormData()

            data.append('name', 'image')
            data.append('file', this.photo_file)
            data.append('tolerance', this.tolerance)

            let config = {
                header : {
                'Content-Type' : 'multipart/form-data'
                }
            }
            await this.axios.post(process.env.VUE_APP_IP_SERVER + '/find_face', data, config)
        },

        check(){
            let reader = new FileReader()
            reader.onload = (event) => {
                this.photo = event.target.result
            }
            reader.readAsDataURL(this.photo_file)
            this.cards = []
        },
    },

    sockets: {
      connect: function (){
          window.console.log('socket connected')
      },
      find_face: function (data) {
          if (data.status == "correct") {
              this.status = "Correct"
              this.cards.unshift({src : 'data:image/jpeg;base64,' + data.photo, flex: 2}) 
          }else{
              this.status = "Incorrect"
          }
          this.photo_compare = 'data:image/jpeg;base64,' + data.photo
          this.percentage = data.percentage
      }
    },
  }
</script>