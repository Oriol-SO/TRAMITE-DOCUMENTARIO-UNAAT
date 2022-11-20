<template >
    <v-card class="mx-2 mt-3">
        <v-card-title>
            {{hola}}
        </v-card-title>

       <v-card>
            <v-card-title>
            <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Buscar Documento"
                single-line
                hide-details
            ></v-text-field>
            </v-card-title>
            <v-data-table
            :headers="headers"
            :items="documentos"
            :search="search"
            ></v-data-table>
        </v-card>
    </v-card>
</template>
<script>
import axios from 'axios';
import Form from "vform";
export default {
   data(){
    return{
        hola:'Vienvenido a Meza de partes',
        search: '',
        headers: [
          { text: 'Codigo',align: 'start', value: 'id' },
          { text: 'Documento',align: 'start', value: 'documento' },
          { text: 'fecha', value: 'fecha' },
          { text: 'Responsable', value: 'user' },
         
        ],
        documentos:[],
    }
   }
}
</script>
<style>
 #evaluador .v-data-table-header th[role=columnheader] {
  font-size: 16px !important;
  color:#304156;
  background:#eaeff3  !important;
}

#evaluador, .card-shadow {
     box-shadow: 0 7px 14px rgba(50,50,93,.1),0 3px 6px rgba(0,0,0,.08)!important;
}
.btn-shadow{
     box-shadow: 0 2px 12px -1px rgb(85 85 85 / 8%), 0 4px 12px 0 rgb(85 85 85 / 6%), 0 1px 12px 0 rgb(85 85 85 / 3%) !important;
}

 .v-data-table table tbody tr:not(.v-data-table__selected):hover {
    box-shadow: 0 3px 15px -2px rgb(0 0 0 / 12%) !important;
    transform: translateY(-4px) !important;
     background: #fff !important;
}

.v-data-table table td {
    border-bottom: 0!important;
}
</style> 