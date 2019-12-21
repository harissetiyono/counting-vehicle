<template>
    <v-row>
      <v-col
        :cols="12"
        md="8"
      >
      <v-alert 
        v-model="alert" 
        type="error"
        dense
      >
        {{ error_text }}
      </v-alert>
        <v-card
          class="pa-2"
          tile
          outlined
        >
        <!-- <div class="pa-2">
          <div v-if="streamStatus">
            <v-img :src="videoDefault"/>
          </div>
          <div v-else>
            <v-img src="@/assets/offline.jpg"/>
          </div>
        </div>  -->
        <vue-load-image>
          <img slot="image" :src="videoDefault" style="width: 100%"/>
          <v-img slot="preloader" src="@/assets/preloader.gif"/>
          <v-img slot="error" src="@/assets/offline.jpg" aspect-ratio="1.7"/>
        </vue-load-image>
        <v-card-actions>
          <v-spacer></v-spacer>
            <v-chip
              class="ma-2"
              color="blue"
              outlined
            >
              <v-avatar left>
                <v-icon>mdi-car</v-icon>
              </v-avatar>
              Mobil : {{ mobil_in + ' | ' + mobil_out}}
            </v-chip>

            <v-chip
              class="ma-2"
              color="green"
              outlined
            >
              <v-avatar left>
                <v-icon>mdi-motorbike</v-icon>
              </v-avatar>
              Motor : {{ motor_up + ' | ' + motor_down}}
            </v-chip>

            <v-chip
              class="ma-2"
              color="orange"
              outlined
            >
              <v-avatar left>
                <v-icon>mdi-truck</v-icon>
              </v-avatar>
              Truck : {{ truck_up + ' | ' + truck_down}}
            </v-chip>

            <v-chip
              class="ma-2"
              color="red"
              outlined
            >
              <v-avatar left>
                <v-icon>mdi-bus</v-icon>
              </v-avatar>
              Bus : {{ bus_up + ' | ' + bus_down }}
            </v-chip>
            <v-spacer></v-spacer>
            <v-tooltip top>
                <template v-slot:activator="{ on }">
                    <v-btn color="green" v-on="on" fab small dark @click="checkConnection(port)">
                      <v-icon>mdi-refresh</v-icon>
                    </v-btn>
                </template>
                <span>Refresh Video</span>
            </v-tooltip>
        </v-card-actions>
        </v-card>
      </v-col>

      <v-col
        :cols="12"
        md="4"
      >
         <v-card
          class="pa-2"
          height="430"
          tile
          outlined
        >
        <v-select
            v-model="cameras"
            :items="camera_list"
            item-value="id"
            item-text="name"
            @change="changeRoute"
            filled
            dense
            label="Camera Active"
          ></v-select>
          <div v-if="chartHide">
            <v-img src="@/assets/no_result.gif"></v-img>
          </div>
          <div v-else>
            <v-list-item-content>
              <div class="overline">Percentage</div>
            </v-list-item-content>
            <v-layout justify-center>
              <apexchart type=pie width="350" :options="chartOptionsPie" :series="seriesPie" />
            </v-layout>
            <div class="text-center mt-4">
              <v-btn-toggle
                v-model="id_lane"
                rounded
                dense
              >
                <v-btn value="In" @click="id_lane = 'In'">
                  <v-icon>mdi-arrow-up-circle</v-icon>
                </v-btn>
                <v-btn value="all" @click="id_lane = 'all'">
                  <v-icon>mdi-swap-vertical-bold</v-icon>
                </v-btn>
                <v-btn value="Out" @click="id_lane = 'Out'">
                  <v-icon>mdi-arrow-down-circle</v-icon>
                </v-btn>
              </v-btn-toggle>
            </div>
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
          <div v-if="chartHide">
            <v-layout justify-center>
                <v-img src="@/assets/no_result.gif" max-width="500"></v-img>
            </v-layout>
          </div>
          <div v-else>
            <v-list-item-content>
              <div class="overline">Traffic Today</div>
            </v-list-item-content>
            <apexchart type=line height="400" :options="chartOptions" :series="series" />
          </div>
        </v-card>
      </v-col>
     
      <v-snackbar
        v-model="snackbar"
        :timeout="timeout"
        :color="color_snackbar"
      >
        {{ text }}
        <v-btn
          color="white"
          text
          @click="snackbar = false"
        >
          Close
        </v-btn>
      </v-snackbar>
    </v-row>
</template>

<script>
import VueApexCharts from 'vue-apexcharts'
import moment from 'moment'
import VueLoadImage from 'vue-load-image'

  export default {
    components: { apexchart: VueApexCharts, 'vue-load-image': VueLoadImage },
    props: {
      source: String,
    },
    data: () => ({
        snackbar: false,
        text: '',
        timeout: 3000,
        color_snackbar: '',

        alert : false,
        error_text: '',

        id_camera : '',
        id_lane : 'all',
        port: '',
        cameras:[],
        camera_list:[],
        videoDefault: "@/assets/loading.gif",
        start_date: moment().tz('Asia/Jakarta').startOf('day').format('YYYY-MM-DD'),
        end_date: moment().tz('Asia/Jakarta').endOf('day').format('YYYY-MM-DD'),
        chartHide : false,
        no_result: '@/assets/offline.jpg',
        interval_select: {
          'interval_id' : "1h",
        },
        interval: [
          {
            'interval_id' : "1h",
            'interval_name' : "1 Hours",
          },
          {
            'interval_id' : "3h",
            'interval_name' : "3 Hours",
          },
          {
            'interval_id' : "1d",
            'interval_name' : "1 Day",
          },
          {
            'interval_id' : "1w",
            'interval_name' : "1 Week",
          },
        ],

        realtime: true,
        seriesPie: [],
        chartOptionsPie: {
          labels: [],
          chart: {
            width: '100%'
          },
          legend: {
                position: 'bottom'
          },
          responsive: [{
            breakpoint: 480,
            options: {
              chart: {
                width: '100%'
              },
              legend: {
                position: 'bottom'
              }
            }
          }]
        },

        series: [
          {
            'name' : 'mobil',
            'data' : [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
          },
          {
            'name' : 'motor',
            'data' : [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
          },
          {
            'name' : 'truck',
            'data' : [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
          },
          {
            'name' : 'bus',
            'data' : [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
          }
        ],
        chartOptions: {
          chart: {
            shadow: {
              enabled: true,
              color: '#000',
              top: 18,
              left: 7,
              blur: 10,
              opacity: 1
            },
            toolbar: {
              show: true
            }
          },

          colors: ['rgb(0,134,251)', 'rgb(0,227,150)', 'rgb(254,176,25)', 'rgb(255,69,96)'],
          dataLabels: {
            enabled: true,
          },
          stroke: {
            curve: 'smooth'
          },
          title: {
            text: 'Live traffic',
            align: 'left'
          },
          grid: {
            borderColor: '#e7e7e7',
            row: {
              colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
              opacity: 0.5
            },
          },
          markers: {
            size: 6
          },
          xaxis: {
            categories: ['00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00'],
            title: {
              text: 'Time'
            },
            labels: {
              rotate: -45
            },
          },
          yaxis: {
            title: {
              text: 'Count'
            },
          },
          legend: {
            position: 'top',
            horizontalAlign: 'right',
            floating: true,
            offsetY: -25,
            offsetX: -5
          }
        },

        mobil_in : 0,
        mobil_out : 0,

        motor_up : 0,
        motor_down : 0,

        truck_up : 0,
        truck_down : 0,
        
        bus_up : 0,
        bus_down : 0,

        timer: '',
        timer_live: '',
        streamStatus: false,
        hours : ['00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00']
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
          this.traffic()
          this.pie()
          this.getLiveCount()
        },

        id_lane:function(){
          window.console.log(this.id_lane)
          this.pie()
          this.traffic()
        },
        
        realtime:function() {
          if (this.realtime) {
            this.startIntervalGraph()
          }else{
            clearInterval(this.timer)
          }
        }
    },

    methods: {
        async initialize () {
          const { data } = await this.$http.get(process.env.VUE_APP_IP_SERVER + '/camera')
          this.camera_list = data
        },

        async getData(id_camera){
          try {
              const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/camera/' + id_camera)
              this.cameras = data
              this.port = data['port']
              this.checkConnection(this.port)
            } catch (error) {
                window.console.log(Object.keys(error), error.message);
            }
        },

        async traffic(){
          var hours = ['00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00']
          try {
            const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/trend/'+ this.$route.params.id +'?start_date=&end_date=&interval=' + this.interval_select.interval_id + '&lane=' + this.id_lane)
            // const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + 'trend/'+ this.$route.params.id +'?start_date=2019-12-13&end_date=2019-12-13&interval=' + this.interval_select.interval_id + '&lane=' + this.id_lane)
            data.datasets.forEach((value, index) => {
              Object.keys(value.data).forEach((hour) => {
                var index_hour = hours.indexOf(hour);
                this.series[index]['data'][index_hour] = value.data[hour]
              });
            });
            this.chartOptions = {
              xaxis : {
                labels: {
                  rotate: -45
                },
              }
            }
            this.alert = false
          } catch (error) {
            this.error_text = "Can't connect database !"
            this.alert = true
          }
        },

        async pie(){
          try {
            const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/pie/'+ this.$route.params.id +'?start_date=&end_date=&lane=' + this.id_lane)
            // const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + 'pie/'+ this.$route.params.id +'?start_date=2019-01-10&end_date=2019-12-13&lane=' + this.id_lane)
            this.seriesPie = data.datasets
            this.chartOptionsPie = {
              labels : data.categories,
              legend: {
                position: 'bottom'
              },
              responsive: [{
                breakpoint: 950,
                options: {
                  chart: {
                    width: '350'
                  },
                  legend: {
                    position: 'bottom'
                  }
                }
              }]
            }
            if (data.datasets.length == 0) {
              this.chartHide = true
            }else{
              this.chartHide = false
            }
          } catch (error) {
            window.console.log(error)
          }
        },

        async getLiveCount(){
          try {
            const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + 'realtime/count/' + this.id_camera)
            this.mobil_in = data.mobil[0]
            this.mobil_out = data.mobil[1]
            this.motor_up = data.motor[0]
            this.motor_down = data.motor[1]
            this.truck_up = data.truck[0]
            this.truck_down = data.truck[1]
            this.bus_up = data.bus[0]
            this.bus_down = data.bus[1]
          } catch (error) {
            this.text = "Live count error ( "  + error + " )"
            this.color_snackbar = "red darken-1"
            this.alert = true
            this.chartHide = true
          }
        },

        changeRoute(e){
          this.$socket.emit('leave', this.$route.params.id);
          this.$router.push({path: '/live/' + e })
          this.id_camera = this.$route.params.id
        },

        dateFilter(){
          window.console.log(this.id_lane)
          this.traffic()
          this.pie()
        },

        startIntervalGraph: function () {
          this.timer = setInterval(() => {
            this.traffic()
            this.pie()
          }, 3000);
        },

        checkConnection(port){
          const _self = this
          this.axios.get('http://127.0.0.1:' + port).then(function(){
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
            this.videoDefault = "http://127.0.0.1:" + this.port + "/" + random + "/stream.mjpg"
        },

        imageLoadError(){
            this.videoDefault = require('@/assets/offline.jpg')
        },

        addCount(data){
          const now = moment().tz('Asia/Jakarta').format('HH:00')
          let index_hour = this.hours.indexOf(now);
          window.console.log(index_hour)
          switch (data.categories) {
            case 'mobil':
              if (data.lane == 'In') {
                this.mobil_in += 1
              }else{
                this.mobil_out += 1
              }
              this.series[0]['data'][index_hour] = this.series[0]['data'][index_hour]+1
              this.reloadChartTrend()
              break;
            case 'motor':
              if (data.lane == 'In') {
                this.motor_up += 1
              }else{
                this.motor_down += 1
              }
              this.series[1]['data'][index_hour] = this.series[1]['data'][index_hour]+1
              this.reloadChartTrend()
              break;
            case 'truck':
              if (data.lane == 'In') {
                this.truck_up += 1
              }else{
                this.truck_down += 1
              }
              this.series[2]['data'][index_hour] = this.series[2]['data'][index_hour]+1
              this.reloadChartTrend()
              break;
            case 'bus':
              if (data.lane == 'In') {
                this.bus_up += 1
              }else{
                this.bus_down += 1
              }
              this.series[2]['data'][index_hour] = this.series[2]['data'][index_hour]+1
              this.reloadChartTrend()
              break;
            default:
              break;
          }
        },

        reloadChartTrend (){
          this.chartOptions = {
              xaxis : {
                labels: {
                  rotate: -45
                },
              }
            }
        }
    },

    sockets: {
      connect: function (){
          window.console.log('socket connected')
      },
      counting_live: function (data) {
          this.addCount(data)
      }
    },

    beforeDestroy: function() {
        this.$socket.emit('leave', this.$route.params.id);
        this.videoDefault = require('@/assets/offline.jpg')
        clearInterval(this.timer)
    },

  }
</script>
