<template>
  <v-stepper v-model="e1">
    <v-stepper-header>
      <v-stepper-step :complete="e1 > 1" step="1">Name of step 1</v-stepper-step>
      <v-divider></v-divider>
      <v-stepper-step :complete="e1 > 2" step="2">Name of step 2</v-stepper-step>
      <v-divider></v-divider>
      <v-stepper-step step="3">Name of step 3</v-stepper-step>
    </v-stepper-header>

    <v-stepper-items>
      <v-stepper-content step="1">
        <v-card>
            <v-row class="pa-2 mt-n4 mb-n4">
                <v-col cols="6">
                    <p class="font-weight-regular mb-1">Camera Name</p>
                    <v-text-field v-model="camera_data.name" solo label="Camera Name"></v-text-field>
                </v-col>
                <v-col cols="6">
                    <p class="font-weight-regular mb-1">IP Stream</p>
                    <v-text-field v-model="camera_data.ip_stream" solo label="IP Address"></v-text-field>
                </v-col>
                <v-col class="mt-n10" cols="6">
                    <p class="font-weight-regular mb-1">Location</p>
                    <v-textarea
                    v-model="camera_data.location"
                    label="Location"
                    solo
                    rows="1"
                    auto-grow
                    ></v-textarea>
                </v-col>
                <v-col class="mt-n10" cols="3">
                    <p class="font-weight-regular mb-1">Longitude</p>
                    <v-text-field v-model="camera_data.longitude" solo label="Longitude"></v-text-field>
                </v-col>
                <v-col class="mt-n10" cols="3">
                    <p class="font-weight-regular mb-1">Latitude</p>
                    <v-text-field v-model="camera_data.latitude" solo label="Latitude"></v-text-field>
                </v-col>
                <v-col class="mt-n10" cols="6">
                    <p class="font-weight-regular mb-1">Port</p>
                    <v-text-field v-model="camera_data.port" solo label="ex: 9001"></v-text-field>
                </v-col>
                <v-col class="mt-n10" cols="6">
                    <p class="font-weight-regular mb-1">Ip Server</p>
                    <v-text-field v-model="camera_data.ip_local" solo label="ex: htttp://127.0.0.1"></v-text-field>
                </v-col>
            </v-row>
            <v-btn
                color="primary"
                @click="continue1"
            >
            Continue
            </v-btn>
        </v-card>
      </v-stepper-content>

      <v-stepper-content step="2">
        <v-card
          class="pa-2 mb-4"
          color="grey lighten-1"
        >
            <v-row
                align="center"
                justify="center"
            >
                <v-col
                    cols="12"
                    md="8"
                >
                    <v-card
                        class="pa-2"
                        max-width="800"
                        max-height="450"
                    >
                        <v-stage ref="stage" :config="stageSize">
                            <v-layer ref="layer">
                                    <v-image ref="image" @click="testClick" :config="{
                                        image: image,
                                        scale: {
                                            x: 0.5,
                                            y: 0.5
                                        }
                                        }"
                                    />

                                <v-line 
                                v-for="item in list"
                                :key="item.id"
                                :config="{
                                    x: 0,
                                    y: 0,
                                    points: item.point,
                                    closed: true,
                                    stroke: item.stroke,
                                }"
                                />
                                <v-text ref="text" :config="{
                                x: 10,
                                y: 10,
                                fontFamily: 'Calibri',
                                fontSize: 24,
                                text: text,
                                fill: 'white'
                                }" />
                            </v-layer>
                        </v-stage>
                    </v-card>
                </v-col>
                <v-col
                    cols="12"
                    md="4"
                >
                    <v-card
                        class="pa-2"
                        height="450"
                    >
                         <v-row class="mb-n8" v-for="(item, index) in list" :key="item.id">
                            <v-divider></v-divider>
                            <v-col class="mb-n4" cols="12" >
                            <span>{{ "Line " + doMath(index) }}</span>
                            <v-divider></v-divider>
                            </v-col>
                            <v-col cols="12" sm="3">
                            <v-text-field
                                ref='line'
                                :value="item.point[0] == null ? '0,0' : item.point[0] * 2 + ',' + item.point[1] * 2"
                                label="start (x,y)"
                                readonly
                            ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="3">
                            <v-text-field
                                :value="item.point[2] == null ? '0,0' : item.point[2] * 2 + ',' + item.point[3] * 2"
                                label="end (x,y)"
                                readonly
                            ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="3">
                            <v-select
                                v-model="item.path"
                                ref="path"
                                class="mt-4"
                                :items="items"
                                label="Line Status"
                                dense
                            ></v-select>
                            </v-col>

                            <v-col cols="12" sm="2">
                                <v-btn v-if="index != 0" class="mt-4" color="error" @click="delLine(index)" fab x-small dark>
                                    <v-icon>mdi-close-circle</v-icon>
                                </v-btn>
                            </v-col>
                        </v-row>
                        <v-btn class="mt-4" color="warning" @click="reset">reset</v-btn>
                    </v-card>
                </v-col>
            </v-row>
        </v-card>

        <v-btn
          color="primary"
          @click="save"
        >
          Continue
        </v-btn>

        <v-btn text @click="e1 = 1">Back</v-btn>
      </v-stepper-content>

      <v-stepper-content step="3">
        <v-card
          class="mb-12"
          color="grey lighten-1"
        >
        </v-card>

        <v-btn
          color="primary"
        >
          Save
        </v-btn>
      </v-stepper-content>
    </v-stepper-items>
  </v-stepper>
</template>

<script>
const width = window.innerWidth;
const height = window.innerHeight;

  export default {
    data () {
      return {
        e1: 1,
        camera_data : {
            name: null,
            location: null,
            ip_stream: null,
            ip_local: null,
            longitude: null, 
            latitude: null,
            port: null
        },
        stageSize: {
            width: width,
            height: height
        },
        list: [{
            x: 0,
            y: 0,
            point: [],
            closed: true,
            stroke: '#00a1ff',
            path: 'IN'
        }],
        text: '',
        image: null,
        scale: null,
        color: ['#00a1ff', '#ff5757','#32B332', '#ffc60a'],
        items: ['IN', 'OUT'],
        };
    },

    created() {
        
    },

    methods: {
        testClick(evt){
        const stage = evt.target.getStage();
        const pos = stage.getPointerPosition();
        const x = Math.round(pos.x);
        const y = Math.round(pos.y);
        const id = this.list.length - 1
        if(this.list.length > 4){
            alert("maksimum 4 line")
            this.$delete(this.list, 4)
        }else{
            if(this.list[id].point.length == 4){
            let data = { x: 0, y: 0, point: [x,y], closed: true, stroke: this.color[id], path: 'IN'}
            this.list.push(data)
            }else{
            this.list[id].point.push(x,y)
            this.list[id].stroke = this.color[id]
            }
        }
        // window.console.log(this.list[id])
        this.text = "x: " + x * 2 + " and " + "y: " + y * 2
        window.console.log(this.text)
        },

        reset(){
            this.list = [{
                x: 0,
                y: 0,
                point: [],
                closed: true,
                stroke: 'black',
                path: 'IN'
            }]
        },

        testText(){
            window.console.log(this.list)
        }, 

        delLine(index){
            this.$delete(this.list, index)
        },

        save(){
            const response = this.axios.post('http://localhost:8001/camera', { camera_data : this.camera_data, line_path : this.list })
            window.console.log(response)
        },

        continue1(){
            const response = this.axios.post('http://localhost:8001/generate', { url : this.camera_data.ip_stream, port : this.camera_data.port })
            window.console.log(this.camera_data.ip_stream)
            window.console.log(this.camera_data.port)
            window.console.log(response)
            setTimeout(() => {
                this.e1 = 2
                const image = new window.Image();
                const today = new Date();
                const time = today.getHours() + today.getMinutes() + today.getSeconds();
                image.src = 'http://localhost:8001/storage/'+ this.camera_data.port +'.jpg?t=' + time;
                image.onload = () => {
                    this.image = image; 
                };
            },10000);
        },

        doMath: function (index) {
        return index+1
        }
    }
  }
</script>