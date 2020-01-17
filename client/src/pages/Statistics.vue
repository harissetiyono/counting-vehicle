<template>
    <v-card class="pa-2">
        <div v-if="error_show">
            <v-alert dense type="error" outlined v-if="!$v.cameras.required"> Select Camera!</v-alert>
            <v-alert dense type="error" outlined v-if="!$v.vehicle_type_selected.required">Select Vehicle type</v-alert>
        </div>
        <v-row class="mb-n6">
            <v-col cols=12 md=3> 
                <v-select
                    v-model="cameras"
                    :items="camera_list"
                    item-value="id"
                    item-text="name"
                    dense
                    outlined
                    multiple
                    label="Camera"
                ></v-select>
            </v-col>
            <v-col cols=12 md=4>
                <VueCtkDateTimePicker 
                    v-model="DateValue"
                    :custom-shortcuts="custom_shortcuts"
                    :only-date="onlyDate"
                    :range="range"
                    :no-shortcuts="no_shortcuts"
                    :shortcut="'thisMonth'"
                     >
                </VueCtkDateTimePicker>
            </v-col>
            <v-col cols=12 md=3>
                <v-select
                    v-model="vehicle_type_selected"
                    :items="vehicle_type"
                    dense
                    outlined
                    multiple
                    label="Vehicle Type"
                ></v-select>
            </v-col>
            <v-col cols=12 md=2>
                <v-btn class="mt-1" rounded color="primary" dark @click="filter">Filter</v-btn>
            </v-col>
        </v-row>
        <v-row v-if="chart_graph">
            <v-col
            cols="12"
            md="8"
            >
            <v-card
                class="pa-2"
                outlined
                tile
            >
                <apexchart type=bar :options="chartOptionsBar" :series="series" />
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
                height="430"
            >
                <v-list-item-content>
                    <div class="overline mb-4">Percentage</div>
                </v-list-item-content>
                <apexchart type=pie height="90%" :options="chartOptionsPie" :series="seriesPie" />
            </v-card>
            </v-col>
            <v-col
                cols="12"
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

    </v-card>
</template>

<script>
import VueApexCharts from 'vue-apexcharts'
import moment from 'moment'
import { required, minLength } from 'vuelidate/lib/validators'

  export default {
    components: { apexchart: VueApexCharts },
    props: {
      source: String,
    },
    data: () => ({
        id_camera : '',
        camera_list:[],
        cameras: [],
        DateValue: '',
        onlyDate: true,
        range: true,
        custom_element: true, 
        no_shortcuts: false,
        chart_graph: false,
        error_show: false,
        custom_shortcuts : [
            { key: 'thisWeek', label: 'This week', value: 'isoWeek' },
            { key: 'lastWeek', label: 'Last week', value: '-isoWeek' },
            { key: 'last7Days', label: 'Last 7 days', value: 7 },
            { key: 'last30Days', label: 'Last 30 days', value: 30 },
            { key: 'thisMonth', label: 'This month', value: 'month' },
            { key: 'lastMonth', label: 'Last month', value: '-month' },
            { key: 'thisYear', label: 'This year', value: 'year' },
            { key: 'lastYear', label: 'Last year', value: '-year' }
        ],
        vehicle_type_selected : ['mobil', 'motor', 'truck', 'bus'],
        vehicle_type : ['mobil', 'motor', 'truck', 'bus'],

        start_date: null,
        end_date: null,

        series: [],
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
        }
    }),

    created () {
      this.$Progress.start()
      this.initialize()
    },

    mounted(){
      this.$Progress.finish()
    },

    validations: {
        cameras:{ minLength: minLength(1), required },
        vehicle_type_selected:{ minLength: minLength(1), required }
    },

    methods: {
      async initialize(){
        try {
            const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/camera?system=counting')
            this.camera_list = data
          } catch (error) {
              window.console.log(Object.keys(error), error.message);
          }
      },

      async pie(){
        try {
          const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/pie_range?start_date='+this.start_date+'&end_date='+this.end_date+'&lane=all&vehicle_type=' + this.vehicle_type_selected.toString() + '&id_camera=' + this.cameras)
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
        } catch (error) {
          window.console.log(error)
        }
      },

      async heatmap(){
        try {
          const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/heatmap_range?start_date='+this.start_date+'&end_date='+this.end_date+'&vehicle_type=' + this.vehicle_type_selected.toString() + '&id_camera=' + this.cameras)
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

      async traffic(){
        try {
          const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/trend_range?start_date='+this.start_date+'&end_date='+this.end_date+'&lane=all&vehicle_type=' + this.vehicle_type_selected.toString() + '&id_camera=' + this.cameras)
          this.series = data.datasets
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

      filter(){
        this.$v.$touch()
        if (this.$v.$invalid) {
          this.error_show = true
          this.chart_graph = false
        } else {
          this.start_date = moment(this.DateValue.start).tz('Asia/Jakarta').startOf('day').format('YYYY-MM-DD')
          this.end_date = moment(this.DateValue.end).tz('Asia/Jakarta').startOf('day').format('YYYY-MM-DD')
          
          this.heatmap()
          this.pie()
          this.traffic()

          this.chart_graph = true
        }
      }
    }

  }
</script>