<template>
    <v-row
        class="mt-n4"
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
            <v-img class="ma-1" :src="'http://counting.test/storage/violation/picture/' + dateConvert(item.created_at)+ '/'+ item.picName + '.jpg'" width="80"/>
          </template>
          <template v-slot:item.id="{ item }">
            <v-btn class="ma-2" tile color="success" @click="openDetail(item)">
              <v-icon left>mdi-magnify</v-icon> Check
            </v-btn>
          </template>
          <template v-slot:item.action="{ item }">
            <v-icon dark v-if="item.action == 0" color="red">mdi-brightness-1</v-icon>
            <v-icon dark v-if="item.action == 1" color="yellow">mdi-brightness-1</v-icon>
            <v-icon dark v-if="item.action == 2" color="green">mdi-brightness-1</v-icon>
          </template>
          <template v-slot:item.created_at="{ item }">
            {{ item.created_at | moment }}
          </template>
          </v-data-table>
          <v-pagination class="pa-2" v-model="page" :length="pageCount" :total-visible="7" circle v-if="pageCount > 1"></v-pagination>
        </v-card>
      </v-container>

      <v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
      <v-card>
        <v-toolbar dark color="primary">
          <v-btn icon dark @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
          <v-toolbar-title>Data Detail</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-toolbar-items>
            <v-btn dark text @click="update">Save</v-btn>
          </v-toolbar-items>
        </v-toolbar>
        <v-divider></v-divider>
        <v-card-text>
          <v-row>
            <v-col
              cols="8"
              class="px-8"
            >
              <v-tabs v-model="tab" fixed-tabs class="py-3 mt-n2">
                <v-tabs-slider></v-tabs-slider>
                <v-tab href="#tab-1" class="primary--text">
                  <v-icon>mdi-image-multiple</v-icon>
                   Picture Capture
                </v-tab>
                <v-tab href="#tab-2" class="primary--text">
                  <v-icon>mdi-file-video</v-icon>
                   Video Capture
                </v-tab>

              </v-tabs>
              <v-tabs-items v-model="tab">
                <v-tab-item value="tab-1"> 
                  <v-row>
                      <v-img class="mt-8" v-if="failed_load_image" src="@/assets/no_result.gif" max-height="400"/>
                      <v-col
                        v-for="item in showDetail.image"
                        :key="item"
                        class="d-flex child-flex ma-n2 mt-n4"
                        cols="6"
                      >
                        <v-card flat tile class="d-flex">
                          <zoom-on-hover :img-normal='"data:image/jpeg;base64," + item'></zoom-on-hover>
                        </v-card>
                      </v-col>
                  </v-row>
                </v-tab-item>
                <v-tab-item value="tab-2"> 
                  <v-row class="px-2 mr-2">
                    <div v-if="failed_load_video">
                      <v-img class="mt-8" src="@/assets/no_result.gif" max-height="400"/>
                    </div>
                    <div v-else>
                      <vue-plyr v-if="renderComponent">
                        <video playsinline controls >
                          <source  :src="showDetail.video" type="video/mp4" size="1080">
                        </video>
                      </vue-plyr>
                    </div>
                  </v-row>
                </v-tab-item>
              </v-tabs-items>
            </v-col>

            <v-col class="ml-n8 mt-2" cols="4">
              <v-text-field
                v-model="showDetail.plateNumber"
                label="Nomor Polisi"
              ></v-text-field>
              <v-text-field
                v-model="showDetail.typeOfViolation"
                label="Jenis Pelanggaran"
                readonly
              ></v-text-field>
              <v-text-field
                v-model="showDetail.created_at"
                label="Kejadian Pada"
                readonly
              ></v-text-field>
              <v-text-field
                v-model="showDetail.location"
                label="Lokasi Kejadian"
                readonly
              ></v-text-field>
              <v-select
                v-model="actions_select"
                :items="actions"
                item-text="item"
                item-value="value"
                label="Tindakan"
              ></v-select>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-overlay :value="overlay">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
    </v-row>
</template>

<script>
import moment from 'moment'

  export default {
    // components: {videoPlayer},
    data () {
      return {
        tab: null,
        page: 1,
        pageCount: 0,
        itemsPerPage: 10,
        data_record: [],
        loading: true,
        dialog: false,
        plateNumber : '',
        cameras : [],
        camera_select : '',
        actions : [
          {'item' : 'Belum ditindak', 'value' : 0 },
          {'item' : 'Proses', 'value' : 1 },
          {'item' : 'Selesai', 'value' : 2 },
        ],
        actions_select : 0,
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
          'image' : [],
          'video' : ''
        },
        headers: [
          {
            text: 'Image',
            align: 'left',
            sortable: false,
            value: 'picName',
            width: 150,
          },
          { text: 'License Number', value: 'plateNumber' },
          { text: 'Violation Type', value: 'a_n_p_r_violation.violationName' },
          { text: 'Time', value: 'created_at' },
          { text: 'Action', value: 'id', sortable: false },
        ],
        failed_load_image : false,
        failed_load_video : false,
        overlay: false,
        renderComponent: false,
        default_image : '@/assets/no_result.gif'
      }
    },

    watch: {
      page(){
        this.getDataFromApi()
      },
      plateNumber(){
        this.getDataFromApi()
      },
      tab(){
        if (this.tab == "tab-2") {
          this.forceRerender()
        }
      },
      dialog(){
        if (this.dialog == false) {
          this.failed_load_image = false
          this.failed_load_video = false
        }
      }
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
        this.axios.get(process.env.VUE_APP_IP_SERVER + '/violation?page=' + this.page + '&itemsPerPage=' + this.itemsPerPage + '&plateNumber=' + this.plateNumber + '&camera=' + this.camera_select.id + '&matchingResult=' + this.matching_select)
          .then(function (response) {
            self.data_record = response.data.data
            setTimeout(() => {
              self.loading = false
              self.pageCount = response.data.last_page
            }, 1000)
          })
          .catch(function () {
            self.loading = false
            self.data_record = []
          })
      },

      async getCamera() {
        const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/camera')
        this.cameras = data
      },
      
      async openDetail(item){
        this.forceRerender()
        this.overlay = true
        let time_folder = this.dateConvert(item.created_at)
        let images = []
        let video = ''
        this.tab = 'tab-1'
        
        const {data} = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/violation/' + item.id)
        if (data.image !== null) {
            images = data.image
        }else{
            this.failed_load_image = true
        }

        if (data.video !== null) {
            video = 'http://counting.test/storage/violation/record/'+time_folder+'/'+data.video.id + '.mp4?t=' + Math.random().toString(36).substr(2, 5)
        }else{
            this.failed_load_video = true
        }
        this.showDetail = {
          'id' : data.anpr_record.id,
          'picName' : data.anpr_record.picName,
          'plateNumber' : data.anpr_record.plateNumber,
          'longitude' : data.anpr_record.longitude,
          'latitude' : data.anpr_record.latitude,
          'typeOfViolation' : data.anpr_record.a_n_p_r_violation.violationName,
          'location' : data.anpr_record.a_n_p_r_camera.name,
          'created_at' : this.dateConvert2(item.created_at),
          'image' : images,
          'video' : video
        }
        this.overlay = false
        this.dialog = true
      },

      update(){
        let self = this
        var id = this.showDetail.id
        var plateNumber = this.showDetail.plateNumber
        this.axios.put(process.env.VUE_APP_IP_SERVER + '/violation/' + id, { plateNumber: plateNumber})
          .then(function () {
            self.getDataFromApi()
            self.dialog = false
          })
          .catch(function (error) {
            window.console.log(error);
        });
      },

      getCoordinate(item){
          window.open('http://maps.google.com/maps?&z=15&mrt=yp&t=m&q='+item.longitude+',' +item.latitude, '_blank');
      },

      dateConvert(created_at){
          return moment(created_at).format('YYYY-MM-DD');
      },

      dateConvert2(date){
        moment.locale('id')
        return moment(date).format('LLLL') + ' WIB';
      },

      forceRerender() {
        // Remove my-component from the DOM
        this.renderComponent = false;
        
        this.$nextTick(() => {
          // Add the component back in
          this.renderComponent = true;
        });
      }

    },

    sockets: {
      connect: function (){
          window.console.log('socket connected')
      },
      violation: function (data) {
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
           this.getDataFromApi()
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