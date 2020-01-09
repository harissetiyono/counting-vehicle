<template>
  <v-row class="pa-2">
    <notifications width="50%" position="bottom center" group="foo" />
    <v-col>
      <v-data-table
        :headers="headers"
        :items="cameras"
        sort-by="calories"
        class="elevation-1"
      >

        <template v-slot:top>
          <v-toolbar>
            <v-toolbar-title>Camera Data</v-toolbar-title>
            <v-divider
              class="mx-4"
              inset
              vertical
            ></v-divider>
            <v-spacer></v-spacer>
            <v-btn color="primary" dark class="mx-2" to="/face_recognition/camera/form">Add Camera</v-btn>
            <v-btn color="info" dark class="mx-2" @click="reload">Refresh</v-btn>
          </v-toolbar>
        </template>
        <template v-slot:item.status="{ item }">
            <v-chip :color="getColor(item.status)" dark>
            <div v-if="item.status == 1">Online <v-icon class="pl-2" left >mdi-access-point-network</v-icon></div>
            <div v-else>Offline <v-icon class="pl-2" left >mdi-access-point-network-off</v-icon></div>
            </v-chip>
        </template>

        <template v-slot:item.action="{ item }">
          <v-tooltip bottom v-if="item.status == 0">
                <template v-slot:activator="{ on }">
                    <v-icon
                        v-on="on"
                        small
                        class="mr-2"
                        @click="runCamera(item.id, item.port)"
                    >
                        mdi-play
                    </v-icon>
                </template>
                <span>Run Camera</span>
            </v-tooltip>
            <v-tooltip bottom v-if="item.status == 1">
                <template v-slot:activator="{ on }">
                    <v-icon
                        v-on="on"
                        small
                        class="mr-2"
                        @click="stopCamera(item.port)"
                    >
                        mdi-stop
                    </v-icon>
                </template>
                <span>Stop Camera</span>
            </v-tooltip>
            <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                    <v-icon
                        v-on="on"
                        small
                        class="mr-2"
                        @click="$router.push('/face_recognition/camera/' + item.id + '/edit')"
                    >
                        mdi-pencil
                    </v-icon>
                </template>
                <span>Edit</span>
            </v-tooltip>
            <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                    <v-icon
                        v-on="on"
                        small
                        class="mr-2"
                        @click="deleteItem(item)"
                    >
                        mdi-delete
                    </v-icon>
                </template>
                <span>Delete</span>
            </v-tooltip>
        </template>

        <template v-slot:no-data>
          <v-btn color="primary" @click="initialize">Reload</v-btn>
        </template>
      </v-data-table>
    </v-col>
    
  </v-row>
</template>

<script>
  export default {
    data: () => ({
      dialog: false,
      headers: [
        {
          text: 'Camera',
          align: 'left',
          sortable: false,
          value: 'name',
          width: '25%'
        },
        { text: 'Lokasi', value: 'location', width: '25%'} ,
        { text: 'Port', value: 'port' },
        { text: 'Ip Stream', value: 'ip_stream', width: '20%'},
        { text: 'Status', value: 'status' },
        { text: 'Actions', value: 'action', sortable: false, width: '20%' },
      ],
      cameras: [],
      status : [
        {'id' : 1, 'name' : 'Active'},
        {'id' : 0, 'name' : 'Deactive'},
      ],
    }),

    created () {
      this.$Progress.start()
      this.initialize()
    },

    mounted (){
      this.$Progress.finish()
    },

    methods: {
      async initialize () {
          let _self = this
          const { data } = await this.axios.get(process.env.VUE_APP_IP_SERVER + '/camera?system=face_recognition')
          this.cameras = data

          data.forEach((element, i) => {
            this.axios.get( element.ip_local +':' + element.port).then(function(){
              _self.cameras[i].status = 1
            }).catch(function(error){
                if (!error.response) {
                  // network error
                  _self.cameras[i].status = 0
                } else {
                  // http status code
                  const code = error.response.status
                  // response data
                  // const response = error.response.data
                  alert("Error: " + code)
                }
            });
          });
      },

      runCamera(id, port){
        this.$Progress.start()
        let self = this
    
          this.$notify({
            group: 'foo',
            title: 'Running Script',
            text: 'Running script Success, please wait for camera stream',
            duration: 20000,
          });

          setTimeout(() => { 
            this.axios.get(process.env.VUE_APP_IP_SERVER_WITHOUT_PORT + ':' + port).then(function(){
              self.$notify({
                group: 'foo',
                type: 'success',
                title: 'Video ready',
                text: 'Camera video stream ready to use !',
                duration: 10000,
              });
              self.initialize()
              self.$Progress.finish()
            }).catch(function(error){
                if (!error.response) {
                  self.$notify({
                    group: 'foo',
                    type: 'error',
                    title: 'Video Stream Failed',
                    text: 'Cannot Connect to camera, check internet connection or camera server !',
                    duration: 10000,
                  });
                  self.initialize()
                  self.$Progress.fail()
                } else {
                  const code = error.response.status
                  window.console.log(code)
                  const response = error.response.data
                  window.console.log(response)
                }
            });
          }, 20000)
          this.axios.post(process.env.VUE_APP_IP_SERVER + '/run?action=face_recognition', {id : id})
      },

      stopCamera(port){
        let self = this
        this.axios.post(process.env.VUE_APP_IP_SERVER_WITHOUT_PORT + ':' + port + '/stop', { action : 'stop' }).then(function(){
          self.$notify({
            group: 'foo',
            type: 'success',
            title: 'Stream success ',
            text: 'Camera video stream success to stop !',
            duration: 10000,
          });
          self.initialize()
        }).catch(function(error){
            if (!error.response) {
              self.$notify({
                group: 'foo',
                type: 'error',
                title: 'Video Stream Failed Stop',
                text: 'Cannot Connect Stop Camera stream, please check connection !',
                duration: 10000,
              });
              self.initialize()
            } else {
              self.$notify({
                group: 'foo',
                type: 'error',
                title: 'Video Stream Failed Stop',
                text: 'Sorry you do not have permission !',
                duration: 5000,
              });
            }
        });
      },

      editItem (item) {
        this.editedIndex = this.cameras.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        const index = this.cameras.indexOf(item)
        confirm('Are you sure you want to delete this item?') && this.cameras.splice(index, 1)
      },

      close () {
        this.dialog = false
        setTimeout(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        }, 300)
      },

      save () {
        if (this.editedIndex > -1) {
          Object.assign(this.cameras[this.editedIndex], this.editedItem)
        } else {
          this.cameras.push(this.editedItem)
        }
        this.close()
      },

      getColor (status) {
        if (status == 1) return 'green' 
        else return 'red'
      },

      reload() {
        this.initialize()
      }
    },
  }
</script>