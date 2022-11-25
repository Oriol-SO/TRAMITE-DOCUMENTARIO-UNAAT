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
      { title: "Organos-Unidades", icon: "mdi-folder", to: "admin.oficinas" },
       /*{ title: "Soporte-archivos", icon: "mdi-cog", to: "admin.archivos" },*/
   
    ],
    }
  }

};
</script>

