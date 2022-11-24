<template >
<div>
    <v-card class="ma-3">
        <v-card-title>
           Administrador
        </v-card-title>

        <v-card>
            <v-card-title>
                <v-toolbar elevation="0" max-width="300" >
                    SOPORTE DE ARCHIVOS  
                </v-toolbar>
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
            >
            <template v-slot:[`item.action`]="{ item }">
                <v-btn
                small
                rounded
                color="primary"
                elevation="0"
                style="color:#fff;"
                class="text-capitalize"
                @click="ver(item)"  
                >Ver proceso</v-btn>
            </template>
            
            </v-data-table>
        </v-card>

        <v-dialog
        v-model="dialog"
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
                        @click="dialog = false"
                        >
                        Cerrar
                        </v-btn>
                    </v-toolbar-items>
                    </v-toolbar>
                  
                    <v-subheader>Información del documento </v-subheader>
                    <v-card>
                        <datosdoc :dato="doc"/>
                        <soporteproc :dato="doc"/>
                    </v-card>
                </v-card>
        </v-dialog>
    </v-card>
</div>
</template>
<script>

import axios from 'axios';
import Form from "vform";
import datosdoc from '../../components/datosdoc.vue';
export default {
  components: { datosdoc },
   data(){
    return{
        dialog:false,
        search:'',
        documentos:[],
        doc:[],
        headers: [
          { text: 'Codigo',align: 'start', value: 'id' },
          { text: 'Documento',align: 'start', value: 'documento' },
          { text: 'Fecha', value: 'fecha' },
          { text: 'Responsable', value: 'remitente' },
          { text: 'DNI', value: 'dni' },
          { text: 'Destino', value: 'destino' },
          { text: 'Tipo', value: 'tipo' },
           { text: 'Fecha final', value: 'fecha_fin' },
          { text: '', value: 'action' },
         
        ],
        dialog_proc:false,
        formfecha:new Form({
            fecha1:'',
            fecha2:'',
            tipo:'',
        })
        
    }
   },mounted(){
    this.fecth_docs();
   },methods:{
        fecth_docs(){
            axios.get('/api/documentos-archivo').then(response=>{
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
        

   }
}
</script>

