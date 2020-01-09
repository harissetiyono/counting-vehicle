<template>
    <div v-if="categories_vision_select == 'Face Recognition'" @change="emitEventChanged">
        <v-list dense>
        <!-- <v-list-item to="/face_recognition/dashboard">
          <v-list-item-action>
            <v-icon>mdi-view-dashboard</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Dashboard</v-list-item-title>
          </v-list-item-content>
        </v-list-item> -->
        <v-list-item to="/face_recognition/stream">
          <v-list-item-action>
            <v-icon>mdi-wifi</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Live Stream</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <!-- <v-list-item>
          <v-list-item-action>
            <v-icon>mdi-chart-areaspline-variant</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Statistics</v-list-item-title>
          </v-list-item-content>
        </v-list-item> -->
        <v-list-item to="/face_recognition/person">
          <v-list-item-action>
            <v-icon>mdi-database</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Person data</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item to="/face_recognition/find">
          <v-list-item-action>
            <v-icon>mdi-selection-search</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Find Data</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item to="/face_recognition/camera">
          <v-list-item-action>
            <v-icon>mdi-settings</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Camera</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </div>
    <div v-else-if="cv_system == 'counting'">
        <v-list dense>
        <v-list-item to="/dashboard">
          <v-list-item-action>
            <v-icon>mdi-view-dashboard</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Dashboard</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item to="/livestream">
          <v-list-item-action>
            <v-icon>mdi-wifi</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Live Stream</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item to="/statistics">
          <v-list-item-action>
            <v-icon>mdi-chart-areaspline-variant</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Statistics</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item to="/camera">
          <v-list-item-action>
            <v-icon>mdi-settings</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Camera</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </div>
    
</template>

<script>
export default {
    props: {
      source: String,
    },
    data: () => ({
        drawer: null,
        categories_vision_select : 'Face Recognition'
    }),

    methods :{
      emitEventChanged () {
        this.$emit('CustomEventInputChanged', this.categories_vision_select);
        window.console.log(this.categories_vision_select)
      }
    },
  }
</script>