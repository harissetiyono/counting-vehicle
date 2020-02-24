<template>
  <v-app id="inspire">
    <v-app-bar
      app
      clipped-left
    >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-toolbar-title><v-icon class="pb-1" medium>{{ categories_vision_icon }}</v-icon> {{ categories_vision_select }}</v-toolbar-title>
      <v-spacer />
      <v-row
        class="pt-4 pr-4"
        align="center"
        style="max-width: 450px"
      >
        <v-select
          v-model="categories_vision_select"
          :items="categories_vision"
          @change="changeCategories"
        ></v-select>
      </v-row>
    </v-app-bar>
    <v-navigation-drawer
      v-model="drawer"
      app
      clipped
    >
      <div v-if="categories_vision_select == 'Face Recognition'">
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
      <div v-else-if="categories_vision_select == 'Counting Vehicle'">
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
      <div v-else-if="categories_vision_select == 'ANPR'">
          <v-list dense>
            <v-list-item to="/anpr/data">
              <v-list-item-action>
                <v-icon>mdi-database</v-icon>
              </v-list-item-action>
              <v-list-item-content>
                <v-list-item-title>Data Record</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item to="/anpr/violation">
              <v-list-item-action>
                <v-icon>mdi-traffic-light</v-icon>
              </v-list-item-action>
              <v-list-item-content>
                <v-list-item-title>Violation</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          <v-list-item to="/anpr/stream">
            <v-list-item-action>
              <v-icon>mdi-wifi</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>Live Stream</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item to="/anpr/filtering">
            <v-list-item-action>
              <v-icon>mdi-database-search</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>Whitelist / Blacklist</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item to="/anpr/camera">
            <v-list-item-action>
              <v-icon>mdi-settings</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>Camera</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </div>
    </v-navigation-drawer>
    <v-content>
      <vue-progress-bar></vue-progress-bar>
      <v-container fluid>
        <dashboard-content> </dashboard-content>
      </v-container>
    </v-content>

    <v-footer app>
      <span>Made with ❤️️ &copy; 2020</span>
      <!-- <span>2019 &copy; Synergics Digital</span> -->
    </v-footer>
  </v-app>
</template>

<script>
import DashboardContent from "./Content.vue";
// import Menu from "./Menu.vue";
  export default {
    components: {
      DashboardContent
    },
    props: {
      source: String,
    },
    data: () => ({
      drawer: true,
      categories_vision: ['Face Recognition', 'Counting Vehicle', 'ANPR'],
      categories_vision_select : 'ANPR',
      categories_vision_icon : null,
    }),
    created () {
      this.$vuetify.theme.dark = false
    },

    methods : {
      changeCategories(){
        var categories = this.categories_vision_select
        if (categories == "Face Recognition") {
          this.categories_vision_icon = 'mdi-face-recognition'
          this.$router.push({path: '/face_recognition/stream'})
        }else if (categories == "Counting Vehicle") {
          this.categories_vision_icon = 'mdi-car-multiple'
          this.$router.push({path: '/dashboard'})
        }else if (categories == "ANPR") {
          this.categories_vision_icon = 'mdi-focus-field-horizontal'
          this.$router.push({path: '/anpr/stream'})
        }
      }
    }
  }
</script>