<template>
  <v-stepper v-model="e1">
    <v-stepper-header>
      <v-stepper-step :complete="e1 > 1" step="1">Camera Data</v-stepper-step>
      <v-divider></v-divider>
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
                @click="save(camera_data.id)"
            >
            Save
            </v-btn>
            <v-btn
                class="ma-2"
                to="/face_recognition/camera"
            >
            Cancel
            </v-btn>
        </v-card>
      </v-stepper-content>

    </v-stepper-items>
  </v-stepper>
</template>

<script>
import { required, minLength, numeric, between, decimal } from 'vuelidate/lib/validators'

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
              const respone_camera = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/camera/' + id_camera)
              this.camera_data = respone_camera.data
            } catch (error) {
                window.console.log(Object.keys(error), error.message);
            }
        },

        async save(id){
            this.camera_data.system = 'face_recognition'
            try {
                if (id) {
                    await this.axios.put(process.env.VUE_APP_IP_SERVER + '/camera', { camera_data : this.camera_data })
                }else{
                    await this.axios.post(process.env.VUE_APP_IP_SERVER + '/camera', { camera_data : this.camera_data })
                }
                this.$router.push('/face_recognition/camera/')
            } catch (error) {
                alert(error)
            }
        },

        doMath: function (index) {
            return index+1
        },
    }
  }
</script>