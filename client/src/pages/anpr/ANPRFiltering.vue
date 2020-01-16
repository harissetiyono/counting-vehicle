<template>
    <v-row>
        <v-container fluid>
            <v-data-table
                :headers="headers"
                :items="filter_data"
                sort-by="categories"
                class="elevation-1"
                >
                    <template v-slot:top>
                    <v-toolbar flat color="white">
                        <v-toolbar-title>Blacklist & Whitelist</v-toolbar-title>
                        <v-divider
                        class="mx-4"
                        inset
                        vertical
                        ></v-divider>
                        <v-text-field
                            v-model="search"
                            append-icon="mdi-search"
                            label="Search"
                            single-line
                            hide-details
                        ></v-text-field>
                        <v-spacer></v-spacer>
                        <v-dialog v-model="dialog" max-width="500px">
                        <template v-slot:activator="{ on }">
                            <v-btn color="primary" dark class="mb-2" v-on="on">New Data</v-btn>
                        </template>
                        <v-card>
                            <v-card-title>
                            <span class="headline">{{ formTitle }}</span>
                            </v-card-title>

                            <v-card-text>
                            <v-container>
                                <v-row>
                                <v-col cols="12">
                                    <v-text-field v-model="editedItem.plateNumber" label="Plate Number" outlined dense></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                    v-model="editedItem.categories"
                                    :items="categories"
                                    label="Categories data"
                                    outlined
                                    dense
                                    ></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea v-model="editedItem.description" label="Description" outlined dense></v-textarea>
                                </v-col>
                                </v-row>
                            </v-container>
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
                    <template v-slot:item.action="{ item }">
                        <v-icon
                            small
                            class="mr-2"
                            @click="showHistory(item)"
                        >
                            mdi-file-find
                        </v-icon>
                    <v-icon
                        small
                        class="mr-2"
                        @click="editItem(item)"
                    >
                        mdi-pencil
                    </v-icon>
                    <v-icon
                        small
                        @click="deleteItem(item)"
                    >
                        mdi-delete
                    </v-icon>
                    </template>
                    <template v-slot:no-data>
                    <v-btn color="primary" @click="initialize">Reset</v-btn>
                    </template>
            </v-data-table>
        </v-container>

        <v-dialog
        v-model="dialog_history"
        width="600"
        >
            <v-card class="pa-2">
                <v-card-title class="headline">History Plate Detected</v-card-title>
                <v-timeline dense>
                    <v-timeline-item
                        v-for="item in history"
                        :key="item.id"
                        small
                        dense
                    >
                    <v-card class="pa-2">
                        <v-simple-table dense>
                            <template v-slot:default>
                            <tbody>
                                <td width="120">
                                    <tr>Date</tr>
                                    <tr>Plate Number</tr>
                                    <tr>Camera</tr>
                                    <tr>Categories</tr>
                                    <tr>Coordinate</tr>
                                </td>
                                <td width="10">
                                    <tr>:</tr>
                                    <tr>:</tr>
                                    <tr>:</tr>
                                    <tr>:</tr>
                                    <tr>:</tr>
                                </td>
                                <td>
                                    <tr>{{ item.created_at }}</tr>
                                    <tr class="font-weight-bold">{{ item.plateNumber }}</tr>
                                    <tr>{{ item.camera.name }}</tr>
                                    <tr>{{ plate_categories }}</tr>
                                    <tr>
                                        <div v-if="item.longtitude !== null && item.latitude !== null">
                                            <v-btn small color="primary" dark @click="getCoordinate(item)"><v-icon class="pr-2" small>mdi-open-in-new</v-icon>Coordinate</v-btn>
                                        </div>
                                        <div v-else>-</div>
                                        
                                    </tr>
                                </td>
                            </tbody>
                            </template>
                        </v-simple-table>
                        <!-- <v-card-title class="headline">{{ }}</v-card-title> -->
                        <!-- <div class="overline mb-4">{{ item.created_at }}</div>
                        <div class="title">{{ item.plateNumber }}</div>
                        <div class="subtitle-1">{{ item.camera.name }}</div> -->
                    </v-card>
                    </v-timeline-item>
                </v-timeline>
            </v-card>
        </v-dialog>
    </v-row>

    
    
</template>
<script>
  export default {
    data: () => ({
      dialog: false,
      dialog_history : false,
      history: [],
      plate_categories : '', 
      search: '',
      categories: ['blacklist', 'whitelist'],
      headers: [
        {
          text: 'Plate Number',
          align: 'left',
          sortable: false,
          value: 'plateNumber',
        },
        { text: 'Categories', value: 'categories' },
        { text: 'Description', value: 'description' },
        { text: 'Actions', value: 'action', sortable: false },
      ],
      filter_data: [],
      editedIndex: -1,
      editedItem: {
        plateNumber: '',
        categories: 'blacklist',
        description: '',
      },
      defaultItem: {
        plateNumber: '',
        categories: 'blacklist',
        description: '',
      },
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
      initialize () {
        this.axios.get('http://127.0.0.1:8001/filtering/anpr').then(response => {
            this.filter_data = response.data
        })
      },

      editItem (item) {
        this.editedIndex = this.filter_data.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        const index = this.filter_data.indexOf(item)
        confirm('Are you sure you want to delete this item?') && this.filter_data.splice(index, 1)
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
          Object.assign(this.filter_data[this.editedIndex], this.editedItem)
        } else {
          this.filter_data.push(this.editedItem)
        }
        this.close()
      },

      showHistory(item){
          this.axios.get('http://127.0.0.1:8001/filtering/' + item.plateNumber + '/anpr').then(response => {
              this.history = response.data
              this.plate_categories = item.categories
              this.dialog_history = true
          }).catch(function(error){
              alert(error)
          })
      },

      getCoordinate(item){
          window.open('http://maps.google.com/maps?&z=15&mrt=yp&t=m&q='+item.longitude+',' +item.latitude, '_blank');
      }
    },

  }
</script>