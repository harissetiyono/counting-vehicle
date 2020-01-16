<template>
    <v-row>
    <v-col cols="12" md=6>
        <v-card>
        <v-card-title>
            Live Stream
        <v-spacer></v-spacer>
        </v-card-title>
        <v-card-text>
          <vue-load-image>
            <img slot="image" :src="'http://192.168.2.9/Streaming/channels/102/httppreview'" style="width: 80px"/>
            <v-img slot="preloader" src="@/assets/preloader.gif"/>
            <v-img slot="error" src="@/assets/offline.jpg"/>
          </vue-load-image>
            <!-- <v-img
            src="http://192.168.2.9/Streaming/channels/102/httppreview"
            class="grey lighten-2"/> -->
        </v-card-text>
        </v-card>
    </v-col>

    <v-col cols="12" md=6>
        <v-card>
            <v-card-title>
            ALPR Data
            <v-spacer></v-spacer>
            <v-text-field
                v-model="search"
                append-icon="mdi-search"
                label="Search"
                single-line
                hide-details
            ></v-text-field>
            </v-card-title>
            <v-data-table
            :headers="headers"
            :items="dataPlate"
            :search="search"
            :sort-by="'created_at'"
            :sort-desc="true"
            >
            <template v-slot:item.picName="{ item }">
                <div class="pa-1">
                  <vue-load-image>
                    <img slot="image" :src=" ip_server + '/storage/plate_record/' + convertDate(item.created_at) + '/' + item.picName + '.jpg'" style="width: 80px"/>
                    <v-img slot="preloader" src="@/assets/preloader.gif"/>
                    <v-img slot="error" src="@/assets/offline.jpg" width="80"/>
                  </vue-load-image>
                <!-- <v-img
                    :src="'http://localhost:8000/storage/plate_record/' + convertDate(item.created_at) + '/' + item.picName + '.jpg'"
                    position="center"
                    width="70px"
                ></v-img> -->
                </div>
            </template>
            <template v-slot:item.created_at="{ item }">
                <div class="pa-1"> 
                    <p class="font-weight-medium">
                    {{ item.created_at | moment }}
                    </p>
                </div>
            </template>
            </v-data-table>
        </v-card>
    </v-col>
    </v-row>
</template>

<script>
import moment from 'moment'
import VueLoadImage from 'vue-load-image'

export default {
  components: {'vue-load-image': VueLoadImage },
  data () {
    return {
      search: '',
      id_camera: '',
      headers: [
        {
          text: 'Id',
          align: 'left',
          sortable: false,
          value: 'id',
        },
        { text: 'Plate Image', value: 'picName'},
        { text: 'Plate No', value: 'plateNumber' },
        { text: 'Time', value: 'created_at' },
      ],
      dataPlate : [],
      audio: '',
      dialog: false,
      ip_server : process.env.VUE_APP_IP_SERVER,
    }
  },

  created(){
      this.id_camera = this.$route.params.id
      this.getData()
  },

  methods:{
      async getData(){
          const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/anpr/'+this.id_camera)
          this.dataPlate = data
      },
      
      convertDate(date){
        return moment(date).format('YYYY-MM-DD');
      },
  }, 

  sockets: {
    connect() {
        window.console.log('socket connect')
    },
    plate_recognition: function (data) {
      this.dataPlate.unshift(data)
    }
  },

  filters: {
    moment: function (date) {
      return moment(date).format('DD-MM-YYYY HH:mm');
    },
  }
}
</script>