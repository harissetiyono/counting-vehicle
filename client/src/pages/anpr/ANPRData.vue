<template>
    <v-row
        align="center"
        justify="center"
     >
      <v-container fluid>
        <v-card outlined>
          <v-row class="mb-n6">
            <v-col cols="12" md="4">
              <v-text-field 
                class="pl-2"
                v-model="plateNumber"
                label="License Number" 
                outlined 
                dense
                clearable
                >
              </v-text-field>
            </v-col>
            <v-col cols="12" md="5">
              <v-combobox
                  v-model="camera_select"
                  :items="cameras"
                  item-value="id"
                  item-text="name"
                  label="Camera Select"
                  outlined 
                  dense
                  @change="getDataFromApi"
              ></v-combobox>
            </v-col>
            <v-col cols="12" md="3">
                <v-combobox
                    class="pr-2"
                    v-model="matching_select"
                    :items="matching"
                    label="Matching Type"
                    outlined 
                    dense
                    @change="getDataFromApi"
                ></v-combobox>
            </v-col>
          </v-row>
          <v-divider class="pa-1"></v-divider>
          <v-data-table
            :headers="headers"
            :items="data_record"
            :page="page"
            :items-per-page="itemsPerPage"
            :loading="loading"
            class="elevation-1"
            @page-count="pageCount = $event"
            hide-default-footer
          >
          <template v-slot:item.matchingResult="{ item }">
            <div class="text-center">
              <v-chip
                class="ma-2"
                v-if="item.matchingResult == null || item.matchingResult == 'otherlist'"
              >
                None
              </v-chip>

              <v-chip
                class="ma-2"
                color="light-blue"
                text-color="white"
                v-if="item.matchingResult == 'whitelist'"
              >
                Whitelist
              </v-chip>

              <v-chip
                class="ma-2"
                color="red"
                text-color="white"
                v-if="item.matchingResult == 'blacklist'"
              >
                Blacklist
              </v-chip>
            </div>
          </template>
          <template v-slot:item.picName="{ item }">
            <v-img class="ma-1" :src="'http://127.0.0.1:8001/storage/' + dateConvert(item.created_at)+ '/'+ item.picName + '.jpg'" width="80"/>
          </template>
          <template v-slot:item.id_camera="{ item }">
            <v-btn class="ma-2" tile color="success" @click="openDetail(item)">
              <v-icon left>mdi-magnify</v-icon> Check
            </v-btn>
          </template>
          <template v-slot:item.created_at="{ item }">
            {{ item.created_at | moment }}
          </template>
          </v-data-table>
          <v-pagination class="pa-2" v-model="page" :length="pageCount" :total-visible="7" circle></v-pagination>
        </v-card>
      </v-container>

      <v-dialog v-model="dialog" width="700px">
      <v-card>
        <v-card-title>
          <span class="headline">Data Detail</span>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text>
          <v-simple-table fixed-header height="300px">
            <template v-slot:default>
              <tbody>
                <tr>
                  <td width="150">Image</td>
                  <td width="5">{{ ':' }}</td>
                  <td class="text-left">
                    <vue-load-image>
                      <img slot="image" class="pa-1" :src="'http://127.0.0.1:8001/storage/' + dateConvert(showDetail.created_at)+ '/'+ showDetail.picName + '.jpg'" width="120"/>
                      <v-img slot="preloader" src="@/assets/preloader.gif" width="100"/>
                      <v-img slot="error" src="@/assets/offline.jpg" width="100"/>
                    </vue-load-image>
                  </td>
                </tr>
                <tr>
                  <td width="150">License Number</td>
                  <td width="5">{{ ':' }}</td>
                  <td class="text-left">{{ showDetail.plateNumber }}</td>
                </tr>
                <tr>
                  <td>Status</td>
                  <td>{{ ':' }}</td>
                  <td>
                      <v-chip
                        class="ma-2"
                        v-if="showDetail.matchingResult == null || showDetail.matchingResult == 'otherlist'"
                        small
                      >
                        None
                      </v-chip>

                      <v-chip
                        class="ma-2"
                        color="light-blue"
                        text-color="white"
                        v-if="showDetail.matchingResult == 'whitelist'"
                        small
                      >
                        Whitelist
                      </v-chip>

                      <v-chip
                        class="ma-2"
                        color="red"
                        text-color="white"
                        small
                        v-if="showDetail.matchingResult == 'blacklist'"
                      >
                        Blacklist
                      </v-chip>
                  </td>
                </tr>
                <tr>
                  <td>{{ 'Camera' }}</td>
                  <td>{{ ':' }}</td>
                  <td>{{ showDetail.camera }}</td>
                </tr>
                <tr>
                  <td>{{ 'Coordinate' }}</td>
                  <td>{{ ':' }}</td>
                  <td>
                    <div v-if="showDetail.longitude !== null || showDetail.latitude !== null">
                      <v-btn small color="primary" dark @click="getCoordinate(showDetail)"><v-icon class="pr-2" small>mdi-open-in-new</v-icon>Show location</v-btn>
                    </div>
                    <div v-else>-</div>
                    </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="error darken-1" text @click="dialog = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    </v-row>
</template>

<script>
import VueLoadImage from 'vue-load-image'
import moment from 'moment'

  export default {
    components: {'vue-load-image': VueLoadImage },
    data () {
      return {
        page: 1,
        pageCount: 0,
        itemsPerPage: 10,
        data_record: [],
        loading: true,
        dialog: false,
        plateNumber : '',
        cameras : [],
        camera_select : '',
        directions : ['forward', 'backward'],
        direction_select: ['forward', 'backward'],
        matching : ['all','none', 'blacklist', 'whitelist'],
        matching_select : ['all'],
        showDetail : {
          'picName' : null,
          'plateNumber' : null,
          'matchingResult' : null,
          'camera' : null,
          'longitude' : null,
          'latitude' : null,
          'created_at' : null,
        },
        headers: [
          // {
          //   text: 'Image',
          //   align: 'left',
          //   sortable: false,
          //   value: 'picName',
          //   width: 150,
          // },
          { text: 'License Number', value: 'plateNumber' },
          { text: 'Lane', value: 'laneNo' },
          { text: 'Direction', value: 'direction' },
          { text: 'Status', value: 'matchingResult', align: 'center' },
          { text: 'Camera', value: 'camera.name' },
          { text: 'Time', value: 'created_at' },
          { text: 'Action', value: 'id_camera', sortable: false },
        ],
      }
    },

    watch: {
      page(){
        this.getDataFromApi()
      },
      plateNumber(){
        this.getDataFromApi()
      },
    },

    created(){
      this.getDataFromApi()
      this.getCamera()
    },

    methods: {
      async getDataFromApi () {
        this.loading = true
        let self = this
        // Make a request for a user with a given ID
        this.axios.get(process.env.VUE_APP_IP_SERVER + '/anpr?page=' + this.page + '&itemsPerPage=' + this.itemsPerPage + '&plateNumber=' + this.plateNumber + '&matchingResult=' + this.matching_select + '&camera=' + this.camera_select.id)
          .then(function (response) {
            self.data_record = response.data.data
            setTimeout(() => {
              self.loading = false
              self.pageCount = response.data.last_page
            }, 1000)
          })
          .catch(function (error) {
            self.loading = false
            self.data_record = []
            window.console.log(error)
          })
      },

      async getCamera() {
        const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/camera?system=anpr')
        this.cameras = data
      },
      
      openDetail(item){
        this.dialog = true
        this.showDetail = {
          'picName' : item.picName,
          'plateNumber' : item.plateNumber,
          'matchingResult' : item.matchingResult,
          'camera' : item.camera.name,
          'longitude' : item.longitude,
          'latitude' : item.latitude,
          'created_at' : item.created_at,
        }
      },

      getCoordinate(item){
          window.open('http://maps.google.com/maps?&z=15&mrt=yp&t=m&q='+item.longitude+',' +item.latitude, '_blank');
      },

      dateConvert(created_at){
          return moment(created_at).format('YYYY-MM-DD');
      }

    },

    sockets: {
      connect: function (){
          window.console.log('socket connected')
      },
      plate_recognition: function (data) {
         this.axios.get(process.env.VUE_APP_IP_SERVER + '/filtering/'+data.plateNumber+'/check').then( response => {
           if (response.data.categories == 'blacklist') {
             this.$swal.fire({
                type: 'error',
                title: 'BLACKLIST PLATE DETECTED',
                text: 'Keterangan : ' + response.data.description,
                allowOutsideClick: false,
                showCancelButton: true,
                cancelButtonColor: '#d33',
                confirmButtonText: 'Check It',
                cancelButtonText: 'No, cancel!',
                reverseButtons: false
              })
           }
           this.data_record.unshift(data)
         }).catch(function (error){
           window.console.log(error)
         })
      }
    },

    filters: {
      moment: function (date) {
        moment.locale('id')
        return moment(date).format('LLL');
      },
    }
}
</script>