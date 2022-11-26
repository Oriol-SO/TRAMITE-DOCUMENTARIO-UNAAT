<template>
    <div>
        <v-card class="ma-3">
            <v-card-text>
                <v-list-item-title class="text-h6">
                    Datos del Documento {{dato.doc_tipo}} <v-btn v-if="dato.editar"  text outlined color="green" dark @click="abrirdialog()" > <v-icon>mdi-pencil</v-icon>Editar</v-btn>
                </v-list-item-title>
                <v-card elevation="0" class="mt-2">
                    <div id="dato">
                        <v-row>
                        <v-col cols="12" class="py-0" sm="6" md="3">
                            <v-list-item-subtitle>
                            <div class="box-data"> <strong>Numero de Doc: </strong>  <div class="box-data-valor">{{dato.numero}}</div></div> 
                        </v-list-item-subtitle> 
                        </v-col>
                        <v-col cols="12" class="py-0" sm="6" md="3">
                            <v-list-item-subtitle>
                            <div class="box-data"> <strong>Folios: </strong>  <div class="box-data-valor">{{dato.folio}}</div></div> 
                        </v-list-item-subtitle> 
                        </v-col>
                        <v-col cols="12" class="py-0" sm="6" md="3">
                            <v-list-item-subtitle>
                            <div class="box-data"> <strong>Asunto: </strong>  <div class="box-data-valor">{{dato.documento}}</div></div> 
                        </v-list-item-subtitle> 
                        </v-col>
                        <v-col cols="12" class="py-0" sm="6" md="3">
                             <v-list-item-subtitle>
                                <div class="box-data"> <strong>Documento: </strong>  <div class="box-data-valor">{{dato.tipo}}</div></div> 
                            </v-list-item-subtitle> 
                        </v-col>
                        <!--v-col cols="12" class="py-0" sm="6" md="3">
                             <v-list-item-subtitle>
                                <div class="box-data"> <strong>provehido: </strong>  <div class="box-data-valor">{{dato.provehido}}</div></div> 
                            </v-list-item-subtitle> 
                        </v-col-->
                        <v-col cols="12" class="py-0" sm="6" md="3">
                             <v-list-item-subtitle>
                                <div class="box-data"> <strong>Prioridad: </strong>  <div class="box-data-valor">{{dato.prioridad}}</div></div> 
                            </v-list-item-subtitle> 
                        </v-col>

                        <v-col cols="12" class="py-0" sm="6" md="3" v-if="dato.remitente">
                            <v-list-item-subtitle>
                                <div class="box-data"><strong>Interesado: </strong>  <div class="box-data-valor">{{dato.remitente}}</div></div>
                            </v-list-item-subtitle>
                        </v-col>
                        <v-col cols="12" class="py-0" sm="6" md="3" v-if="dato.dni">
                            <v-list-item-subtitle>
                                <div class="box-data"><strong>DNI del interesado: </strong>  <div class="box-data-valor">{{dato.dni}}</div></div>
                            </v-list-item-subtitle>
                        </v-col>
                        <!--v-col cols="12" class="py-0" sm="6" md="4">
                            <v-list-item-subtitle>
                                <div class="box-data"><strong>Destino: </strong>  <div class="box-data-valor">{{dato.destino}}</div></div>
                            </v-list-item-subtitle>
                        </v-col-->
                        <v-col cols="12" class="py-0" sm="6" md="3">
                            <v-list-item-subtitle>
                                <div class="box-data"><strong>Fecha registro: </strong>   <div class="box-data-valor"><v-chip small color="warning"> {{dato.fecha}}</v-chip></div></div>
                            </v-list-item-subtitle>
                        </v-col>
                        <!--v-col cols="12" class="py-0" sm="6" md="4">
                            <v-list-item-subtitle>
                                <div class="box-data"><strong>Tiempo de registro: </strong>   <div class="box-data-valor"><v-chip small color="purple lighten-4"> {{dato.tiempo_creacion}}</v-chip></div></div>
                            </v-list-item-subtitle>
                        </v-col-->
                        <v-col cols="12" class="py-0" sm="6" md="3" v-if="dato.tiempo_final">
                            <v-list-item-subtitle>
                                <div class="box-data"><strong>Fecha final: </strong>   <div class="box-data-valor"><v-chip small color="green accent-3"> {{dato.tiempo_final}}</v-chip></div></div>
                            </v-list-item-subtitle>
                        </v-col>
                        </v-row>

                    </div>
                </v-card>
            </v-card-text>
        </v-card>
        <v-dialog
        v-model="dialog"
        max-width="700"
        >
        <v-card>
            <v-card-title>
                Editar documento
            </v-card-title>
            <v-card-text>
                <v-container>
                    <v-row>
                        <v-col
                        cols="12"
                        sm="6"
                        md="4"
                    >
                        <v-text-field
                        label="Numero de doc"
                        v-model="form.numero"
                        ></v-text-field>
                    </v-col>
                    <v-col
                        cols="12"
                        sm="6"
                        md="4"
                    >
                        <v-text-field
                        label="Asunto"
                        v-model="form.documento"
     
                        ></v-text-field>
                    </v-col>
                    <v-col
                    v-if="form.doc_tipo=='EXTERNO'"
                        cols="12"
                        sm="6"
                        md="4"
                    >
                        <v-text-field
                        label="Interesado"
                        v-model="form.remitente"
                        ></v-text-field>
                    </v-col>
                    <v-col
                        v-if="form.doc_tipo=='EXTERNO'"
                        cols="12"
                        sm="6"
                        md="4"
                    >
                        <v-text-field
                        label="DNI del remitente"
                        v-model="form.dni"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6"
                        md="6">
                        <v-select
                        label="Documento"
                        v-model="form.tipo"
                        :items="['RESOLUCION',
                                'MEMORANDO MULTIPLE',
                                'MEMORANDO',
                                'PROVEHIDO',
                                'INFORME',
                                'INFORME TECNICO',
                                'INFORME LEGAL',
                                'CARTA',
                                'OFICIO',
                                'SOLICITUD',
                                'OFICIO MULTIPLE',]"
                        ></v-select>
                    </v-col>
                    <!--v-col cols="12" sm="6"
                        md="6">
                        <v-text-field
                        label="provehido"
                        v-model="form.provehido"
                        ></v-text-field>
                    </v-col-->
                    <v-col
                        cols="12"
                        sm="4"
                    >
                        <v-select
                        :items="['INTERNO','EXTERNO']"
                        label="Documento tipo"
                        v-model="form.doc_tipo"
                        ></v-select>
                    </v-col>
                    <v-col
                        cols="12"
                        sm="4"
                    >
                        <v-select
                        :items="['NORMAL',
                                'ESPECIAL',
                                'URGENTE',
                                'MUY URGENTE']"
                        label="Prioridad"
                        v-model="form.prioridad"
                        ></v-select>
                    </v-col>
                     <v-col
                        cols="12"
                        sm="4"
                    >
                       <v-text-field
                       label="Folio"
                       v-model="form.folio"
                       >

                       </v-text-field>
                    </v-col>
                    </v-row>

                </v-container>
            </v-card-text>
            <v-card-actions >
                <v-btn @click="guardar_cambios()" text color="primary">Guardar</v-btn>
            </v-card-actions>
        </v-card>
        </v-dialog>
    </div>
</template>
<script>
import Form from 'vform'
export default {
    name:'datosdoc',
    props:{
        dato:{default:[]}
    },
    data(){
        return{
            dialog:false,
            form: new Form({
                'id':'',
                'documento':'',
                'remitente':'',
                'dni':'',
                'tipo':'',
                'prioridad':'',
                'doc_tipo':'',
                'numero':'',
                'folio':'',
                'provehido':'',
            }),
        }
    },computed:{
        
    },    
    methods:{
        abrirdialog(){
            this.form.keys().forEach((key) => {
                this.form[key] = this.dato[key];
                });
           // this.form=this.dato
            this.dialog=true
            console.log(this.form)
        },
        guardar_cambios(){
            this.form.post('/api/cambiar-datos-doc').then(response=>{
                this.$emit('refresh',true);
                this.dialog=false;
            })
        }
    }
}
</script>
<style>
/*
    #dato .box-data{
        display: flex;
        justify-content: space-between;
        max-width: 310px;
    }
    #dato .box-data-valor{
        min-width: 150px;
    }*/
</style>