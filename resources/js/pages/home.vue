<template>
  <v-container>
    <h3>Bienvenido</h3>
  </v-container>
</template>

<script>
// import axios from 'axios'
export default {
  middleware: "auth",


  metaInfo() {
    return { title: this.$t("home") };
  },
};
</script>
