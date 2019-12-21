<template>
  <v-app id="inspire">
    
    <v-navigation-drawer
      v-model="drawer"
      app
      clipped
    >
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
    </v-navigation-drawer>

    <v-app-bar
      app
      clipped-left
    >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-toolbar-title>{{ app_name }}</v-toolbar-title>
    </v-app-bar>

    <v-content>
      <vue-progress-bar></vue-progress-bar>
      <v-container fluid>
        <dashboard-content> </dashboard-content>
      </v-container>
    </v-content>

    <v-footer app>
      <span>Made with ❤️️ &copy; 2019</span>
    </v-footer>
  </v-app>
</template>

<script>
import DashboardContent from "./Content.vue";
  export default {
    components: {
      DashboardContent,
    },
    props: {
      source: String,
    },
    data: () => ({
      drawer: null,
      app_name: ''
    }),
    created () {
      this.app_name = process.env.VUE_APP_NAME
      this.$vuetify.theme.dark = false
    },
  }
</script>