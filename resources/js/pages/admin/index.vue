<template>
   <div>   
     <div>
      <sidevar :items="links"/>
       <router-view></router-view>
     </div>     
   </div>  
</template> 

<script>

export default {
 middleware:["auth", "role:1"],

  metaInfo() {
    return { title: this.$t("Administrador") };
  },
  data(){
    return{
      links: [
      { title: "Inicio", icon: "mdi-view-dashboard", to: "dash.admin" },
      { title: "Reportes", icon: "mdi-file-document",to: "admin.repo"},
      /*{ title: "Personas/Usuarios", icon: "mdi-account-multiple",to: "admin.roles"},*/
      { title: "Oficinas", icon: "mdi-folder", to: "admin.oficinas" },
   
    ],
    }
  }

};
</script>

