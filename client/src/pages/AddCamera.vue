<template>
  <v-stepper v-model="e1">
    <v-stepper-header>
      <v-stepper-step :complete="e1 > 1" step="1">Camera Data</v-stepper-step>
      <v-divider></v-divider>
      <v-stepper-step :complete="e1 > 2" step="2">Counting Lines</v-stepper-step>
    </v-stepper-header>

    <v-stepper-items>
      <v-stepper-content step="1">
        <div v-if="error_show">
            <v-alert dense type="error" outlined v-if="$v.camera_data.$anyError">
                <div v-if="!$v.camera_data.name.required">Camera name is required</div>
                <div v-if="!$v.camera_data.name.minLength">Camera name minumum 4 character</div>
                <div v-if="!$v.camera_data.location.required">Location is required</div>
                <div v-if="!$v.camera_data.ip_stream.required">Ip Stream is required</div>
                <div v-if="!$v.camera_data.ip_local.required">Ip Local is required</div>
                <div v-if="!$v.camera_data.ip_local.url_or_ip">Ip Local is invalid</div>
                <div v-if="!$v.camera_data.longitude.decimal">coordinate not longitude valid</div>
                <div v-if="!$v.camera_data.latitude.decimal">coordinate latitude not valid</div>
                <div v-if="!$v.camera_data.port.required">Port is required</div>
                <div v-if="!$v.camera_data.port.between">Port is not valid (min : 1025 and max: 65535)</div>
            </v-alert>
            <v-alert dense type="error" outlined v-if="!statusConnection">
                Cannot connect stream server!
            </v-alert>
        </div>

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
                class="ma-2"
                color="primary"
                @click="continue1"
            >
            Continue
            </v-btn>
            <v-btn
                class="ma-2"
                to="/camera"
            >
            Cancel
            </v-btn>
        </v-card>
      </v-stepper-content>

      <v-stepper-content step="2">
        <div v-if="error_show">
            <v-alert dense type="error" outlined>
                Please make line for counting vehicle!
            </v-alert>
        </div>
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
                >
                    <v-card
                        class="pa-2"
                        max-height="580"
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
                    md="6"
                >
                    <v-card
                        class="pa-2"
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
          @click="saveConfirmation"
        >
          Save
        </v-btn>

        <v-btn text @click="e1 = 1">Back</v-btn>
      </v-stepper-content>

    </v-stepper-items>
  </v-stepper>
</template>

<script>
import { required, minLength,ipAddress, url, or, numeric, between, decimal } from 'vuelidate/lib/validators'
const width = window.innerWidth;
const height = window.innerHeight;

  export default {
    data () {
      return {
        e1: 1,
        error_show: false,
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
        statusConnection: true,
        };
    },

    validations: {
        camera_data:{
            name: {
                required,
                minLength: minLength(4)
            },
            location: {
                required,
            },
            ip_stream:{
                required
            },
            longitude:{
                decimal
            },
            latitude:{
                decimal
            },
            port:{
                required,
                numeric,
                between: between(1025, 65535)
            },
            ip_local:{
                required,
                url_or_ip : or(ipAddress, url)
            }
        },        
    },

    created() {
        this.$Progress.start()
        if (this.$route.params.id) {
            this.initialize(this.$route.params.id)
        }
        this.$Progress.finish()
    },

    methods: {
        async initialize(id_camera){
          try {
              const respone_camera = await this.axios.get('http://127.0.0.1:8001/camera/' + id_camera)
              this.camera_data = respone_camera.data
              const respone_lines = await this.axios.get('http://127.0.0.1:8001/lines/' + id_camera)
              this.list = respone_lines.data
            } catch (error) {
                window.console.log(Object.keys(error), error.message);
            }
        },

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
            this.text = "x: " + x * 2 + " and " + "y: " + y * 2
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

        delLine(index){
            this.$delete(this.list, index)
        },

        saveConfirmation(){
            let self = this
            if (this.list[0].point.length == 0 || this.list[0].point.length == 2) {
                this.error_show = true
            }else{
                this.error_show = false
                this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!'
                }).then((result) => {
                if (result.value) {
                    self.save(self.$route.params.id)
                    this.$swal.fire(
                        'Saved!',
                        'Your Camera success save.',
                        'success'
                        )
                    }
                })
            }
        },

        async save(id){
            try {
                if (id) {
                    await this.axios.put('http://localhost:8001/camera', { camera_data : this.camera_data, line_path : this.list })
                }else{
                    await this.axios.post('http://localhost:8001/camera', { camera_data : this.camera_data, line_path : this.list })
                }
                this.$router.push({ name: 'Camera' })
            } catch (error) {
                alert(error)
            }
        },

        continue1(){
            const self = this
            this.$v.$touch()
            this.$Progress.start()
            this.axios.get(this.camera_data.ip_stream).then(function(){
                if (self.$v.$invalid) {
                    self.error_show = true
                } else {
                    self.error_show = false
                    self.statusConnection = true
                    self.axios.post('http://localhost:8001/generate', { url : self.camera_data.ip_stream, port : self.camera_data.port })
                    setTimeout(() => {
                        self.e1 = 2
                        const image = new window.Image();
                        const today = new Date();
                        const time = today.getHours() + today.getMinutes() + today.getSeconds();
                        image.src = 'http://localhost:8001/storage/'+ self.camera_data.port +'.jpg?t=' + time;
                        image.onload = () => {
                            self.image = image; 
                        };
                        self.$Progress.finish()
                    },5000);
                }
            }).catch(function(){
                self.statusConnection = false
                self.error_show = true
                self.$Progress.finish()
            })
            
        },

        doMath: function (index) {
            return index+1
        },
    }
  }
</script>