<template >
<div>
    <v-card class="ma-3">
        <v-card-title>
           Administrador
        </v-card-title>

        <v-card>
            <v-card-title>
                <v-toolbar elevation="0" max-width="150" >REPORTES
                    
                </v-toolbar>
            <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Buscar Documento"
                single-line
                hide-details
            ></v-text-field>

            <v-btn class="ml-auto mr-2 text-capitalize" dark color="purple" small @click="exportar_tiempos()">Tiempos de registro </v-btn>
            <v-btn class="ml-auto mr-2 text-capitalize" dark color="primary" small @click="exportar_tiempos_seguis()">Tiempos de seguimiento</v-btn>
            <v-btn class="ml-auto mr-2 text-capitalize" color="green accent-3" small @click="exportar_docs()">Reporte de documentos</v-btn>
           
            </v-card-title>
            <v-card elevation="0">
                <v-card-text class="d-flex">
                    <v-select
                        v-model="formfecha.tipo"
                        label="Buscar por"
                        :items="['Fecha de creación','Fecha archivado']"
                    ></v-select>
                    <v-text-field
                    class="mx-5"
                        v-model="formfecha.fecha1"
                        type="date"
                        color="primary"
                        label="Fecha inicial"
                    ></v-text-field>
                    <v-text-field
                        class="mx-5"
                        color="green accent-3"
                        v-model="formfecha.fecha2"
                        
                        type="date"
                        label="Fecha final"
                    ></v-text-field>
                    <v-btn
                    color="warning"
                    small
                    @click="buscar_fechas()">
                        Buscar
                    </v-btn>
                </v-card-text>
            </v-card>
            <v-data-table
            :headers="headers"
            :items="documentos"
            :search="search"
            >
            <template v-slot:[`item.action`]="{ item }">
                <v-btn
                small
                rounded
                color="green accent-3"
                elevation="0"
                style="color:#fff;"
                class="text-capitalize"
                @click="ver(item)"  
                >Ver</v-btn>
            </template>
            <template v-slot:[`item.editar`]="{ item }">
                <v-btn
                small
                rounded
                color="primary"
                elevation="0"
                style="color:#fff;"
                class="text-capitalize"
                @click="ver_proc(item)"  
                >Editar</v-btn>
            </template>
            
            </v-data-table>
        </v-card>

        <v-dialog
        v-model="dialog"
        max-width="700"
        >
            <v-card>
                <v-toolbar elevation="0" color="primary" dark>
                    Información del documento
                </v-toolbar>
                <v-card-text>
                    <v-card>
                        <v-row class="ma-2">
                            <v-col cols="12" sm="4" class="py-0 my-0" >
                                <strong>Asunto:</strong> <br>{{doc.documento}}
                            </v-col>
                            <v-col cols="12" sm="4" class="py-0 my-0" >
                                <strong>TIpo documento:</strong> <br>{{doc.tipo_doc}}
                            </v-col>
                             <v-col v-if="doc.remitente" cols="12" sm="4" class="py-0 my-0">
                                <strong>Interesado:</strong> <br>{{doc.remitente}}
                            </v-col>
                             <v-col v-if="doc.dni" cols="12" sm="4" class="py-0 my-0">
                                <strong>DNI :</strong> <br>{{doc.dni}}
                            </v-col>
                             <!--v-col cols="12" sm="4" class="py-0 my-0">
                                <strong>Destino:</strong> <br>{{doc.destino}}
                            </v-col-->
                            <v-col cols="12"  sm="4" class="py-0 my-0">
                                <strong>Inicio:</strong> <br>{{doc.fecha}}
                            </v-col>
                            <v-col v-if="doc.fecha_fin" cols="12"  sm="4" class="py-0 my-0">
                                <strong>Fin:</strong> <br>{{doc.fecha_fin}}
                            </v-col>
                             <v-col cols="12" sm="4" class="py-0 my-0">
                                <strong>Prioridad:</strong> <br>{{doc.nombre_prioridad}}
                            </v-col>
                            <v-col cols="12" sm="4" class="py-0 my-0">
                                <strong>Tipo:</strong> <br>{{doc.tipo}}
                            </v-col>
                            <v-col cols="12" sm="4" class="py-0 my-0">
                                <strong>Oficina actual:</strong> <br>{{doc.oficina}}
                            </v-col>
                            <v-col cols="12" sm="4" class="py-0 my-0">
                                <strong >Estado:</strong> <br> 
                                <strong style="color:green" v-if="doc.estado==1">Archivado</strong>
                                <strong style="color:orange" v-else-if="doc.estado==2">Atendido</strong>
                                <strong style="color:red" v-else>Pendiente</strong>
                            </v-col>

                        </v-row>
                    </v-card>
                </v-card-text>
                <v-card-actions>
                <v-spacer></v-spacer>
                    <v-btn
                        color="blue darken-1"
                        text
                        @click="dialog = false "
                    >
                        Cerrar
                    </v-btn>
                    <v-btn
                        color="green darken-1"
                        text
                        @click="procesos()"
                    >
                       visualizar procesos
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog
        v-model="dialog_proc"
        max-width="500"
        >
        <v-card>
            <v-card-title class="text-h6">
                Seguimiento del documento
            </v-card-title>
            <v-card-text>
                <v-card class="my-2" v-for="(proc,i) in doc.proceso" :key="i" elevation="2">
                    <div class="mt-2">
                            <strong class="ma-1" >{{proc.nom_input}}</strong>
                            <div class="ma-2 pa-2" >
                            <span>Fecha de recepción:</span> <v-chip small color="warning">{{proc.recepcion}}</v-chip> <br> <br>
                            <span v-if="proc.derivar" >Fecha de derivación:</span> <v-chip v-if="proc.derivar" small color="green accent-3">{{proc.derivar}}</v-chip>
                            <div v-else-if="doc.fecha_fin">
                                <strong >Archivado el </strong><v-chip small color="primary">{{doc.fecha_fin}}</v-chip>
                            </div>
                            
                            </div>
                    </div>
                </v-card>

            </v-card-text>
            <v-card-actions>
                <v-btn
                small
                dark
                color="green accent-3"
                @click="tiempo_seguimiento(doc)"
                >
                    Tiempos de seguimiento
                </v-btn>
                <v-btn
                small
                dark
                color="warning"
                @click="seguimiento_oficinas(doc)"
                >
                    Seguimiento de oficinas
                </v-btn>
                
            </v-card-actions>
        </v-card>
        </v-dialog>
        
        <v-dialog
        v-model="dialog_proc"
        fullscreen
        hide-overlay
        transition="dialog-bottom-transition"
        > 
                <v-card>
                    <v-toolbar
                    dark
                    color="primary"
                    >
                    <v-toolbar-title>PROCESOS DEL DOCUMENTO</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn
                        dark
                        text
                        rounded
                        @click="dialog_proc = false"
                        >
                        Cerrar
                        </v-btn>
                    </v-toolbar-items>
                    </v-toolbar>
                  
                    <v-subheader>Información del documento </v-subheader>
                    <v-card>
                        <datosdoc :dato="doc"/>
                        <soporteproc :dato="doc" @refresh="refrescar"/>
                    </v-card>
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
        search:'',
        documentos:[],
        doc:[],
        headers: [
          { text: 'Codigo',align: 'start', value: 'id' },
          { text: 'Asunto',align: 'start', value: 'documento' },
          { text: 'Fecha', value: 'fecha' },
          { text: 'Interesado', value: 'remitente' },
          { text: 'DNI', value: 'dni' },
          { text: 'Numero doc', value: 'numero' },
          { text: 'Tipo', value: 'tipo' },
          { text: 'Fecha final', value: 'fecha_fin' },
          { text: 'Tipo documento', value: 'tipo_doc' },
          {text:'prioridad', value:'nombre_prioridad'},
          { text: '', value: 'action' },      
          { text: '', value: 'editar' },     
        ],
        dialog_proc:false,
        formfecha:new Form({
            fecha1:'',
            fecha2:'',
            tipo:'',
        }),
        dialog_proc:false,
        
    }
   },mounted(){
    this.fecth_docs();
   },methods:{
        refrescar(result){
            if(result){
                this.fecth_docs();
                this.dialog_proc=false;
            }
        },
        fecth_docs(){
            axios.get('/api/get-documentos-rep').then(response=>{
                this.documentos=response.data;
            })
        },
        ver(item){
            this.doc=item;
            this.dialog=true
            
        },
        procesos(){
            this.dialog_proc=true;
        },
        buscar_fechas(){
            this.formfecha.post('/api/buscar-fechas').then(response=>{
                this.documentos=response.data;
            })
        },exportar_docs(){
             this.formfecha.post('/api/exportar-excel',{ headers: {
               // 'Content-Type': 'multipart/form-data',
            },
                'responseType': 'blob' // responseType is a sibling of headers, not a child
            }).then(response=>{    
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    console.log(url);
                    link.href = url;
                    link.setAttribute('download', 'documentos.xlsx');
                    document.body.appendChild(link);
                    link.click();
            }).catch(error=>{
                console.log('error');
            })
        },
        tiempo_seguimiento(doc){

            let form=new Form({
                documento:doc.id,    
            })
            form.post('/api/exportar-excel-seguimientos',{ headers: {
               // 'Content-Type': 'multipart/form-data',
            },
                'responseType': 'blob' // responseType is a sibling of headers, not a child
            }).then(response=>{    
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    console.log(url);
                    link.href = url;
                    link.setAttribute('download', 'seguimiento.xlsx');
                    document.body.appendChild(link);
                    link.click();
            }).catch(error=>{
                console.log('error');
            })
        },
        seguimiento_oficinas(doc){
            let form=new Form({
                documento:doc.id,    
            })
            form.post('/api/exportar-excel-seguimientos-oficinas',{ headers: {
               // 'Content-Type': 'multipart/form-data',
            },
                'responseType': 'blob' // responseType is a sibling of headers, not a child
            }).then(response=>{    
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    console.log(url);
                    link.href = url;
                    link.setAttribute('download', 'seguimiento-oficina.xlsx');
                    document.body.appendChild(link);
                    link.click();
            }).catch(error=>{
                console.log('error');
            })
        },
        exportar_tiempos(){
            let form=new Form({
                documento:1,    
            })
            form.post('/api/exportar-excel-creaciones',{ headers: {
               // 'Content-Type': 'multipart/form-data',
            },
                'responseType': 'blob' // responseType is a sibling of headers, not a child
            }).then(response=>{    
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    console.log(url);
                    link.href = url;
                    link.setAttribute('download', 'tiempos-creacion.xlsx');
                    document.body.appendChild(link);
                    link.click();
            }).catch(error=>{
                console.log('error');
            })
        },
        exportar_tiempos_seguis(){
            let form=new Form({
                documento:1,    
            })
            form.post('/api/exportar-seguimientos',{ headers: {
               // 'Content-Type': 'multipart/form-data',
            },
                'responseType': 'blob' // responseType is a sibling of headers, not a child
            }).then(response=>{    
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    console.log(url);
                    link.href = url;
                    link.setAttribute('download', 'tiempos-seguis.xlsx');
                    document.body.appendChild(link);
                    link.click();
            }).catch(error=>{
                console.log('error');
            })
        }
        ,ver_proc(item){
            this.dialog_proc=true;
            this.doc=item;
        }

   }
}
</script>

