<template >
<div>
    <v-card class="mx-2 mt-3">
        <v-card-title>
            {{hola}} {{user.oficina}}
        </v-card-title>

       <v-card>
            <v-card-title>
                <v-toolbar elevation="0" >Lista de documentos </v-toolbar>
            <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Buscar Documento"
                single-line
                hide-details
            ></v-text-field>
            <v-spacer></v-spacer>

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
                outlined
                color="deep-purple accent-2"
                elevation="0"
                style="color:#fff;"
                class="text-capitalize"
                @click=" $router.push({ path: `/unidad-organica/documento/${item.id}`, }) "  
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
                <v-chip
                small
                v-else
                color="warning"
                >
                Pendiente
                </v-chip>

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
import { mapGetters } from "vuex";
export default {
   data(){
    return{
        hola:'',
        search: '',
        headers: [
          { text: 'Codigo',align: 'start', value: 'id' },
          { text: 'Documento',align: 'start', value: 'documento' },
          { text: 'Fecha', value: 'fecha' },
          { text: 'Responsable', value: 'remitente' },
          { text: 'DNI', value: 'dni' },
          { text: 'Destino', value: 'destino' },
          { text: 'Tipo', value: 'tipo' },
          { text: 'Prioridad', value: 'prioridad' },
          { text: 'Estado', value: 'estado' },
          { text: '', value: 'action' },
         
        ],
        form: new Form({
            remitente:'',
            dni:'',
            archivo:[],
            nombre:'',
            tipo:'',
            destino:'',
            prioridad:'',
        }),
        tipos:[
            'INFORME',
            'SOLICITUD',
            'OTRO',
        ],
        prioridades:[
            'NORMAL',
            'ESPECIAL',
            'URGENTE',
            'MUY URGENTE'
        ],
        documentos:[],
        dialog:false,
    }
   },
    mounted(){
        this.fetch_docs()
    },
    computed:{
    ...mapGetters({
        user: "auth/user",
        }),

        
    },
    methods:{
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
            axios.get('/api/unidad-documentos/'+this.user.oficina_id).then(response=>{
                this.documentos=response.data
            }).catch(error=>{
                console.log(error.response.data.message)
            })
        },
        add_doc(){
            this.form.post('/api/add-documento').then(response=>{
                this.form.reset()
                this.dialog=false;
                this.fetch_docs();
                
            }).catch(error=>{
                console.log(error.response.data.message)
            })
        }
    }
   
}
</script>
 