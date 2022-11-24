<template >
<div>


    <v-card class="ma-3">
        <v-card-title>
            {{msg}}
        </v-card-title>

        <v-card>
            <v-card-title>
                <v-toolbar elevation="0" >Órganos-Unidades </v-toolbar>
            <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Buscar órgano/unidad"
                single-line
                hide-details
            ></v-text-field>
            <v-spacer></v-spacer>
            <v-btn color="primary" dark @click="agregar()">
                Agregar
            </v-btn>
            </v-card-title>
            <v-data-table
            :headers="headers"
            :items="oficinas"
            :search="search"
            >
            <template v-slot:[`item.action`]="{ item }">
                <v-switch
                v-model="item.estado"   
                @change="cambiarestado(item)"
                ></v-switch>
            </template>
            </v-data-table>
        </v-card>

        <v-dialog
        v-model="dialog"
        max-width="600"
        >
            <v-card>
                <v-toolbar elevation="0" :color="color_user">
                    {{msg_user}}
                </v-toolbar>
                <v-card-text>
                    <v-container>
                        <v-row>
                        <v-col
                        cols="12"   
                        >
                            <v-text-field
                            v-model="form.nombre"
                            label="Nombre del organo-unidad"
                            required
                            ></v-text-field>
                        </v-col>
                        
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                <v-spacer></v-spacer>
                    <v-btn
                        color="blue darken-1"
                        text
                        @click="dialog = false , form.reset()"
                    >
                        Cerrar
                    </v-btn>
                    <v-btn
                        color="blue darken-1"
                        text
                        @click="enviar_datos()"
                    >
                        Guardar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

    </v-card>
</div>
</template>
<script>

import axios from 'axios';
import Form from "vform";
export default {
   data(){
    return{
        dialog:false,
        color_user:'green accent-3',
        msg_user:'Agregar nuevo organo/unidad',
        search:'',
        msg:'Panel de administrador',
        oficinas:[],
        headers:[
          { text: 'Codigo',align: 'start', value: 'id' },
          { text: 'Nombre',align: 'start', value: 'nombre' },
          { text: 'Acciones',align: 'start', value: 'action' },
        ],
        form:new Form({
            nombre:''
        }),

    }
   },mounted(){
    this.fetch_oficinas();
   },methods:{

        fetch_oficinas(){
            axios.get('/api/get-oficinas').then(response=>{
                this.oficinas=response.data;
            })
        },
        agregar(){
            this.dialog=true;
        },enviar_datos(){
            //agregar
            this.form.post('/api/add-oficina').then(response=>{
                this.form.reset()
                this.dialog=false;
                this.fetch_oficinas()
            })
        },
        cambiarestado(item){
            console.log(item)
            let form=new Form({
                estado:item.estado,
                oficina:item.id
            });

            form.p
        }

   }
}
</script>

