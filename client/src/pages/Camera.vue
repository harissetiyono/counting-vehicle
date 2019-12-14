<template>
  <v-row class="pa-2">
    <notifications width="50%" position="bottom center" group="foo" />
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
          <v-dialog v-model="dialog" max-width="600px">
              <template v-slot:activator="{ on }">
              <v-btn color="primary" dark class="mx-2" to="camera/add">Add CCTV</v-btn>
              <v-btn color="info" dark class="mx-2" @click="reload">Refresh</v-btn>
              </template>
              <v-card>
              <v-card-title>
                  <span class="headline">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                  <v-form
                  ref="form"
                  lazy-validation
                  >
                  <v-container>
                      <v-row>
                      <v-col cols="12">
                          <v-text-field v-model="editedItem.id" label="ID" style="display:none"></v-text-field>
                          <v-text-field v-if="dialog" autofocus v-model="editedItem.name" label="Nama CCTV"></v-text-field>
                      </v-col>
                      <v-col cols="12">
                          <v-text-field v-model="editedItem.location" label="Lokasi"></v-text-field>
                      </v-col>
                      <v-col cols="12">
                          <v-text-field v-model="editedItem.ip_stream" label="IP Address"></v-text-field>
                      </v-col>
                      <v-col cols="12">
                          <v-text-field v-model="editedItem.port" label="Port"></v-text-field>
                      </v-col>
                      <v-col cols="12">
                          <v-select
                          v-model="editedItem.direction"
                          :items="direction"
                          label="Direction"
                          ></v-select>
                      </v-col>
                      <v-col cols="12">
                          <v-text-field type="number" v-model="editedItem.scale" min="20" max="100" label="Scale"></v-text-field>
                      </v-col>
                      <v-col cols="12">
                          <v-select
                          v-model="editedItem.status"
                          :items="status"
                          item-value="id"
                          item-text="name"
                          label="Status"
                          ></v-select>
                      </v-col>
                      </v-row>
                  </v-container>
                  </v-form>
              </v-card-text>

              <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                  <v-btn color="blue darken-1" text @click="save">Save</v-btn>
              </v-card-actions>
              </v-card>
          </v-dialog>
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
          <!-- <v-tooltip bottom>
              <template v-slot:activator="{ on }">
                  <v-icon
                      v-on="on"
                      small
                      class="mr-2"
                  >
                      mdi-map-marker
                  </v-icon>
              </template>
              <span>Maps Location</span>
          </v-tooltip> -->
          <v-tooltip bottom>
              <template v-slot:activator="{ on }">
                  <v-icon
                      v-on="on"
                      small
                      class="mr-2"
                      @click="editItem(item)"
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
        },
        { text: 'Lokasi', value: 'location' },
        { text: 'Port', value: 'port' },
        { text: 'Ip Stream', value: 'ip_stream' },
        { text: 'Status', value: 'status' },
        { text: 'Actions', value: 'action', width: 180, sortable: false },
      ],
      cameras: [],
      editedIndex: -1,
      editedItem: {
        name: '',
        location: '',
        port: '',
        ip_stream: '',
        scale: 100,
        direction: 'horizontal',
        status: 1,
      },
      defaultItem: {
        name: '',
        location: '',
        port: '',
        ip_stream: '',
        scale: 100,
        direction: 'horizontal',
        status: 1,
      },
      direction : ['vertical', 'horizontal'],
      status : [
        {'id' : 1, 'name' : 'Active'},
        {'id' : 0, 'name' : 'Deactive'},
      ],
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
      },
    },

    watch: {
      dialog (val) {
        val || this.close()
      },
    },

    created () {
      this.initialize()
    },

    methods: {
      async initialize () {
          let _self = this
          const { data } = await this.axios.get('http://127.0.0.1:8001/camera')
          this.cameras = data

          data.forEach((element, i) => {
            this.axios.get('http://localhost:' + element.port).then(function(){
              _self.cameras[i].status = 1
            }).catch(function(error){
                if (!error.response) {
                  // network error
                  _self.cameras[i].status = 0
                } else {
                  // http status code
                  const code = error.response.status
                  window.console.log(code)
                  // response data
                  const response = error.response.data
                  window.console.log(response)
                }
            });
          });
      },

      runCamera(id, port){
        let self = this
    
          this.$notify({
            group: 'foo',
            title: 'Running Script',
            text: 'Running script Success, please wait for camera stream',
            duration: 20000,
          });

          setTimeout(() => { 
            this.axios.get('http://localhost:' + port).then(function(){
              self.$notify({
                group: 'foo',
                type: 'success',
                title: 'Video ready',
                text: 'Camera video stream ready to use !',
                duration: 10000,
              });
              self.initialize()
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
                } else {
                  const code = error.response.status
                  window.console.log(code)
                  const response = error.response.data
                  window.console.log(response)
                }
            });
          }, 20000)

          this.axios.post('http://127.0.0.1:8001/run', {id : id})
      },

      stopCamera(port){
        let self = this
        this.axios.post('http://127.0.0.1:' + port + '/stop', { action : 'stop' }).then(function(){
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