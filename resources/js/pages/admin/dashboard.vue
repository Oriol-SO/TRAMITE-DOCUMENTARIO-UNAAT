<template >
<div>


    <v-card class="ma-3">
        <v-card-title>
            {{msg}}
        </v-card-title>

        <v-card>
            <v-card-title>
                <v-toolbar elevation="0" >Usuarios </v-toolbar>
            <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Buscar Usuario"
                single-line
                hide-details
            ></v-text-field>
            <v-spacer></v-spacer>
            <v-btn color="green accent-3" dark @click="agregar()">
                Agregar
            </v-btn>
            </v-card-title>
            <v-data-table
            :headers="headers"
            :items="usuarios"
            :search="search"
            >
            <template v-slot:[`item.action`]="{ item }">
                <v-btn
                small
                rounded
                color="blue accent-2"
                elevation="0"
                style="color:#fff;"
                class="text-capitalize"
                @click="editar(item)"  
                >Editar</v-btn>
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
                            label="Nombre"
                            required
                            ></v-text-field>
                        </v-col>
                        
                        <v-col 
                        cols="12"
                        sm="6">
                            <v-text-field
                            v-model="form.correo"
                            label="Correo Electronico"
                            required
                            ></v-text-field>
                        </v-col>
                        <v-col 
                        cols="12"
                        sm="6">
                            <v-text-field
                            v-model="form.dni"
                            label="DNI"
                            required
                            ></v-text-field>
                        </v-col>
                        
                        <v-col
                            cols="12"
                            sm="6"
                            v-if="tipo_envio==1"
                        >
                            <v-select
                            v-model="form.rol"
                            :items="roles"
                            label="Rol"
                            return-object
                            item-text="nombre"
                            item-value="id"
                            required
                            ></v-select>
                        </v-col>
                        <v-col
                            cols="12"
                            sm="6"
                            v-if="tipo_envio==1"
                        >
                            <v-autocomplete
                            v-model="form.oficina"
                            :items="oficinas"
                            label="Organo/Unidad"
                            required
                            return-object
                            item-text="nombre"
                            item-value="id"
                            ></v-autocomplete>
                        </v-col>
                        <v-col
                            cols="12"
                            sm="6"
                            v-if="tipo_envio==2"
                        >
                            <strong>Rol:</strong><v-chip color="purple" dark> {{rol_user}}</v-chip>
                        </v-col>
                         <v-col
                            cols="12"
                            sm="6"
                            v-if="tipo_envio==2"
                        >
                            <strong>Oficina:</strong><v-chip color="primary" dark> {{rol_oficina}}</v-chip>
                        </v-col>
                        </v-row>
                    </v-container>
                    <span>*La contraseña será el DNI del usuario</span>
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
        msg_user:'Agregar nuevo usuario',
        search:'',
        msg:'Panel de administrador',
        usuarios:[],
        headers:[
          { text: 'Codigo',align: 'start', value: 'id' },
          { text: 'Nombre',align: 'start', value: 'nombre' },
           {text:'DNI',value:'dni'},
          { text: 'Email', value: 'email' },
          { text:'Rol' ,value:'rol'},
          { text:'Oficina' ,value:'oficina'},
          { text: '', value: 'action' },
        ],
        form:new Form({
            dni:'',
            nombre:'',
            correo:'',
            rol:[],
            oficina:[],
        }),
        tipo_envio:1,
        usuario:'',
        rol_user:'',
        rol_oficina:'',
        oficinas:[],
        roles:[],
    }
   },mounted(){
    this.fetch_users();
    this.fetch_roles();
    this.fetch_oficinas();
   },methods:{
        fetch_roles(){
            axios.get('/api/get-roles').then(response=>{
                this.roles=response.data;
            })
        },
        fetch_oficinas(){
            axios.get('/api/get-oficinas').then(response=>{
                this.oficinas=response.data;
            })
        },
        fetch_users(){
            axios.get('/api/get-users').then(response=>{
                this.usuarios=response.data;
            })
        },editar(user){
            this.tipo_envio=2;
            this.usuario=user.id;
            this.form.nombre=user.nombre;
            this.form.correo=user.email;
            this.form.dni=user.dni;
            this.rol_oficina=user.oficina;
            this.rol_user=user.rol;
            this.msg_user='Editar este usuario';
            this.color_user='primary';
            this.dialog=true;
        },agregar(){
            this.tipo_envio=1;
            this.msg_user='Agregar nuevo usuario';
            this.color_user='green accent-3';
            this.dialog=true;
        },enviar_datos(){
            if(this.tipo_envio==1){
                //agregar
                this.form.post('/api/add-user').then(response=>{
                    this.form.reset()
                    this.dialog=false;
                    this.fetch_users()
                })

            }else if(this.tipo_envio==2){
                //editar
                this.form.post('/api/edit-user/'+this.usuario).then(response=>{
                    this.form.reset()
                    this.dialog=false;
                    this.fetch_users()
                })
            }
        }

   }
}
</script>

