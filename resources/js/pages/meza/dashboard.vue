<template >
<div>
    <v-card class="mx-2 mt-3">
        <v-card-title>
            {{hola}}
        </v-card-title>

       <v-card>
            <v-card-title>
            <v-btn v-if="!segui" @click="iniciar_tiempo_busqueda(), segui=true" class="text-capitalize" color="warning">Buscar Documento</v-btn>
            <v-text-field
            v-if="segui"
                v-model="search"
                append-icon="mdi-magnify"
                label="Buscar Documento"
                single-line
                hide-details
            
            ></v-text-field>
            <v-spacer></v-spacer>
            <adddocumento @refresh="refresh" ruta="add-documento"/>
            </v-card-title>
            <v-data-table
            :headers="headers"
            :items="documentos"
            :search="search"
            >
            <template v-slot:[`item.action`]="{ item }">
                <v-btn
                small
                v-if="item.atendido"
                rounded
                color="green"
                elevation="0"
                outlined
                style="color:#fff;"
                class="text-capitalize"
                @click=" $router.push({ path: `/meza-de-partes/documento/${item.id}`, }) ,finalizar_tiempo_busqueda(item.id) "  
                >Seguimiento</v-btn>
                 <v-btn
                small
                v-else
                rounded
                color="deep-purple accent-2"
                elevation="0"
                style="color:#fff;"
                class="text-capitalize"
                @click=" $router.push({ path: `/meza-de-partes/documento/${item.id}`, }) ,finalizar_tiempo_busqueda(item.id) "  
                >Seguimiento</v-btn>
            </template>
            <template v-slot:[`item.prioridad`]="{ item }">
                <v-chip
                small
                :color="color_pri(item)"
                >
                    {{nombre_pri(item)}}
                </v-chip>
            </template>
            <template v-slot:[`item.estado`]="{ item }">
                <v-chip
                small
                v-if="item.estado==1"
                color="green accent-3"
                >
                Archivado
                </v-chip>
                <v-chip v-else-if="item.estado==2" small color="primary">
                    Atendido
                </v-chip>
                <v-chip v-else-if="item.estado==3" small>
                    Pendiente
                </v-chip>

            </template>
            
            </v-data-table>
            <div class="ma-2 pb-2">
                <imprimir />
            </div>
          
        </v-card>
    </v-card>

</div>
</template>
<script>
import axios from 'axios';
import Form from "vform";
import imprimir from '../../components/imprimir.vue';
export default {
  components: { imprimir },
   data(){
    return{
        hola:'Bienvenido a Mesa de partes',
        search: '',
        headers: [
          { text: 'Codigo',align: 'start', value: 'id' },
          { text: 'Fecha', value: 'fecha' },
          { text: 'Documento', value: 'tipo' },
          { text: 'Numero DOC', value: 'numero_doc' },
          { text: 'Asunto',align: 'start', value: 'documento' },
          { text: 'Tipo doc', value: 'tipo_doc' },
          { text: 'Interesado', value: 'remitente' },
         // { text: 'DNI', value: 'dni' },          
          { text: 'Prioridad', value: 'prioridad' },
          { text: 'Estado', value: 'estado' },
          //{ text: 'Actual', value: 'archivado' },

          { text: '', value: 'action' },
         
        ],
        
        documentos:[],
        dialog:false,
        formtiempo: new Form({
            inicio:'0',
            fin:'0'
        }),
        segui:false,
    }
   },
    mounted(){
        this.fetch_docs()
    },
    methods:{
        refresh(result){
            if(result){
                this.fetch_docs()
            }
        },
        color_pri(item){
            switch(item.prioridad){
                case 20:
                    return 'error';
                case 19:
                    return 'warning';
                case 18:
                    return 'primary';
                case 17:
                    return 'green accent-2';
                default:
                    return '';
            }
        },
        nombre_pri(item){
            switch(item.prioridad){
                case 20:
                    return 'Normal';
                case 19:
                    return 'Especial';
                case 18:
                    return 'Urgente';
                case 17:
                    return 'Muy urgente';
                default:
                    return '';
            }
        },
        fetch_docs(){
            axios.get('/api/meza-documentos').then(response=>{
                this.documentos=response.data
            }).catch(error=>{
                console.log(error.response.data.message)
            })
        },

        iniciar_tiempo_busqueda(){
            let tiempo=new Date();
            this.formtiempo.inicio=tiempo
            console.log( tiempo.toLocaleString())
        },
        finalizar_tiempo_busqueda(doc){
            
            let tiempo=new Date();
            this.formtiempo.fin=tiempo
            console.log( tiempo.toLocaleString())
            this.formtiempo.post('/api/agregar-tiempo-busqueda/'+doc).then(response=>{
                return
            })
            this.segui=false;
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