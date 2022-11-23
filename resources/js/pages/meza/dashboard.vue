<template >
<div>
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
                @click="iniciar_tiempo_busqueda()"
            ></v-text-field>
            <v-spacer></v-spacer>
            <v-btn color="primary"  class="text-capitalize" @click="dialog=true,iniciar_tiempo()" >Agregar registro</v-btn>
            </v-card-title>
            <v-data-table
            :headers="headers"
            :items="documentos"
            :search="search"
            >
            <template v-slot:[`item.action`]="{ item }">
                <v-btn
                small
                rounded
                color="deep-purple accent-2"
                elevation="0"
                style="color:#fff;"
                class="text-capitalize"
                @click=" $router.push({ path: `/meza-de-partes/documento/${item.id}`, }) ,finalizar_tiempo_busqueda(item.id) "  
                >Seguimiento</v-btn>
            </template>
            
            </v-data-table>
        </v-card>
    </v-card>

    <v-dialog
        v-model="dialog"
        max-width="700"
    >   
        <v-card elevation="0">
            <v-toolbar color="#FB8C00" dark elevation="0">
                Registrar nuevo documento
            </v-toolbar>
            <v-card-text>
                <v-card elevation="0">
                 
                    <v-col cols="12">
                        <v-text-field
                        v-model="form.nombre"
                        label="Asunto"
                        >
                        </v-text-field>
                    </v-col>  
                    <v-row>
                        <v-col cols="6">
                        <v-text-field
                        v-model="form.numero_doc"
                        label="Numero de documento Ejem:0001"
                        >
                        </v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-select
                            v-model="form.tipo_doc"
                            :items="['INTERNO','EXTERNO']"
                            label="Tipo de documento"
                            >
                            </v-select>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="3">
                        <v-text-field
                        v-model="form.direccion"
                        label="Direcciones"
                        >
                        </v-text-field>
                        </v-col>
                        <v-col cols="3">
                        <v-text-field
                        v-model="form.referencia"
                        label="Referencias"
                        >
                        </v-text-field>
                        </v-col>
                        <v-col cols="3">
                        <v-text-field
                        v-model="form.anexo"
                        label="Anexos"
                        >
                        </v-text-field>
                        </v-col>
                        <v-col cols="3">
                        <v-text-field
                        v-model="form.folio"
                        label="Folios"
                        >
                        </v-text-field>
                        </v-col>
                       
                    </v-row>
                    
                    <v-row>
                        <v-col cols="8">
                        <v-text-field
                         v-model="form.remitente"
                        label="Interesado"
                        >
                        </v-text-field>
                        </v-col>
                        <v-col cols="4">
                            <v-text-field
                            v-model="form.dni"
                            label="DNI interesado"
                            >
                            </v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="6">
                            <v-text-field
                            v-model="form.destino"
                            label="Destinatario"
                            >

                            </v-text-field>
                        </v-col>
                        <v-col cols="3">
                            <v-select
                            :items="tipos"
                            v-model="form.tipo"
                            label="Tipo"
                            ></v-select>
                        </v-col>
                        <v-col cols="3">
                            <v-select
                            :items="prioridades"
                            v-model="form.prioridad"
                            label="Prioridad"
                            ></v-select>
                        </v-col>
                    </v-row>  
                    <v-col cols="12">
                        <v-file-input
                        v-model="form.archivo"
                        label="Seleccionar archivo"
                        ></v-file-input>
                    </v-col>
                    
                </v-card>
            </v-card-text>
            <v-card-actions>
                <v-btn color="green accent-3" dark
                @click="add_doc()"
                >
                    Guardar
                </v-btn>
                <v-btn color="error" dark text
                @click="form.reset()"
                >
                    Limpiar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</div>
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
          { text: 'Fecha', value: 'fecha' },
          { text: 'Responsable', value: 'remitente' },
          { text: 'DNI', value: 'dni' },
          { text: 'Destino', value: 'destino' },
          { text: 'Tipo', value: 'tipo' },
          { text: '', value: 'action' },
         
        ],
        form: new Form({
            remitente:'Oriol Simon',
            dni:'72852803',
            archivo:[],
            nombre:'Tramite documento primero',
            tipo:'',
            destino:'DECANO',
            prioridad:'',
            tipo_doc:'',
            numero_doc:'0001',
            direccion:'',
            referencia:'',
            anexo:'',
            folio:'',

            tiempo_inicio:'',
            tiempo_fin:'',
        }),
        tipos:[
            'RESOLUCION',
            'MEMORANDO MULTIPLE',
            'INFORME',
            'INFORME TECNICO',
            'INFORME LEGAL',
            'CARTA',
            'OFICIO',
            'OFICIO MULTIPLE',
        ],
        prioridades:[
            'NORMAL',
            'ESPECIAL',
            'URGENTE',
            'MUY URGENTE'
        ],
        documentos:[],
        dialog:false,
        formtiempo: new Form({
            inicio:'0',
            fin:'0'
        })
    }
   },
    mounted(){
        this.fetch_docs()
    },
    methods:{
        fetch_docs(){
            axios.get('/api/meza-documentos').then(response=>{
                this.documentos=response.data
            }).catch(error=>{
                console.log(error.response.data.message)
            })
        },
        add_doc(){
            this.finalizar_tiempo();
            this.form.post('/api/add-documento').then(response=>{
                this.form.reset()
                this.dialog=false;
                this.fetch_docs();
                
            }).catch(error=>{
                console.log(error.response.data.message)
            })
        },
        iniciar_tiempo(){
            let tiempo=new Date();
            this.form.tiempo_inicio=tiempo
            console.log( tiempo.toLocaleString())
        },
        finalizar_tiempo(){
            let tiempo=new Date();
            this.form.tiempo_fin=tiempo
            console.log( tiempo.toLocaleString())
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