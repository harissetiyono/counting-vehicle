<template>
<v-row>
    <v-col
      cols="12"
      md="8"
    >
      <v-card
        class="pa-2"
        outlined
        tile
      >
        <apexchart type=line height="350" :options="chartOptions" :series="series" />
      </v-card>
    </v-col>
    <v-col
      cols="12"
      md="4"
    >
      <v-card
        class="pa-2"
        outlined
        tile
        height="385"
      >
        <v-list-item-content>
          <div class="overline mb-4">Percentage Today</div>
        </v-list-item-content>
        <apexchart type=pie height="90%" :options="chartOptionsPie" :series="seriesPie" />
      </v-card>
    </v-col>

    <v-col
      cols="12"
      md="6"
    >
      <v-card
        class="pa-2"
        outlined
        tile
      >
        <apexchart type=bar :options="chartOptionsBar" :series="seriesWeekly" />
      </v-card>
    </v-col>
    <v-col
      cols="12"
      md="6"
    >
      <v-card
        class="pa-2"
        outlined
        tile
      >
        <apexchart type=heatmap :options="chartOptionsHeatmap" :series="seriesHeatmap" />
      </v-card>
    </v-col>
</v-row>
</template>

<script>
import VueApexCharts from 'vue-apexcharts'
  export default {
    components: { apexchart: VueApexCharts },
    props: {
      source: String,
    },
    data: () => ({
        id_camera : '',
        camera_list:[],
        cameras: [],

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
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
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
            text: 'Live traffic today',
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

        seriesWeekly: [],
        chartOptionsBar: {
          chart: {
            height: 500,
            stacked: true,
            toolbar: {
              show: true
            },
            zoom: {
              enabled: true
            }
          },
          title: {
            text: 'Last 7 day traffic',
            align: 'left'
          },
          responsive: [{
            breakpoint: 480,
            options: {
              legend: {
                position: 'bottom',
                offsetX: -10,
                offsetY: 0
              }
            }
          }],
          plotOptions: {
            bar: {
              horizontal: false,
            },
          },

          xaxis: {
            categories: [],
            labels: {
              rotate: -45
            },
          },
          legend: {
            position: 'right',
            offsetY: 40
          },
          fill: {
            opacity: 1
          }
        },

        seriesPie: [],
        chartOptionsPie: {
          labels: ['Mobil', 'Motor', 'Truck', 'Bus'],
          chart: {
                width: 300
          },
          legend: {
            position: 'bottom'
          },
          responsive: [{
            breakpoint: 480,
            options: {
              chart: {
                width: 300
              },
              legend: {
                position: 'bottom'
              }
            }
          }]
        },

        seriesHeatmap: [],
        chartOptionsHeatmap: {
          chart: {
                height: 500,
                type: 'heatmap'
            },
            dataLabels: {
                enabled: false,
            },
            plotOptions: {
                heatmap: {
                    colorScale: {
                        inverse: true
                    }
                }
            },
            theme: {
              pallete: 'palette7'
            },
            xaxis: {
                type: 'category',
                categories: []
            },

            title: {
                text: 'Heatmap traffic per hours'
            },
        },
        hours : ['00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00']
    }),

    created () {
      this.$Progress.start()
      this.traffic()
      this.pie()
      this.traffic_weekly()
      this.heatmap()
    },

    mounted(){
      this.$Progress.finish()
    },

    computed: {
    },

    methods: {
      async initialize(){
        try {
            const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/camera/')
            this.cameras = data
          } catch (error) {
              window.console.log(Object.keys(error), error.message);
          }
      },

      async traffic(){
        const self = this
        try {
          const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + 'trend?start_date=&end_date=&lane=all')
          data.datasets.forEach((value, index) => {
            Object.keys(value.data).forEach((hour) => {
              var index_hour = self.hours.indexOf(hour);
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
        } catch (error) {
          window.console.log(error)
        }
      },

      async pie(){
        try {
          const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/pie?start_date=&end_date=&lane=all')
          this.seriesPie = data.datasets
        } catch (error) {
          window.console.log(error)
        }
      },

      async heatmap(){
        try {
          const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/heatmap')
          this.seriesHeatmap = data.datasets
          this.chartOptionsHeatmap = {
            xaxis : {
              type: 'category',
              categories: data.date
            },
          }
        } catch (error) {
          window.console.log(error)
        }
      },

      async traffic_weekly(){
        try {
          const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + 
          '/weekly/trend')
          this.seriesWeekly = data.datasets
          this.chartOptionsBar = {
            xaxis : {
              categories : data.categories,
              labels: {
                rotate: -45
              },
            }
          }
        } catch (error) {
          window.console.log(error)
        }
      },

      changeRoute(){
        window.console.log("sad")
      },
    }

  }
</script>
