<template>
    <div>
    <v-btn color="primary"  class="text-capitalize" @click="dialog=true,iniciar_tiempo()" >Agregar registro</v-btn>

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
                        <!--v-col cols="6">
                        <v-text-field
                        v-model="form.numero_doc"
                        label="Numero de documento Ejem:0001"
                        >
                        </v-text-field>
                        </v-col-->
                        <v-col cols="4">
                            <v-select
                            v-model="form.tipo_doc"
                            :items="['INTERNO','EXTERNO']"
                            label="Tipo de documento"
                            >
                            </v-select>
                        </v-col>
                        <v-col cols="8">
                        <v-text-field
                        v-model="form.direccion"
                        label="Direcciones"
                        >
                        </v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="4">
                        <v-text-field
                        v-model="form.referencia"
                        label="Referencias"
                        >
                        </v-text-field>
                        </v-col>
                        <v-col cols="4">
                        <v-text-field
                        v-model="form.anexo"
                        label="Anexos"
                        >
                        </v-text-field>
                        </v-col>
                        <v-col cols="4">
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
                        <!--v-col cols="6">
                            <v-text-field
                            v-model="form.destino"
                            label="Destinatario"
                            >

                            </v-text-field>
                        </v-col-->
                        <v-col cols="6">
                            <v-select
                            :items="tipos"
                            v-model="form.tipo"
                            label="Tipo"
                            ></v-select>
                        </v-col>
                        <v-col cols="6">
                            <v-select
                            :items="prioridades"
                            v-model="form.prioridad"
                            label="Prioridad"
                            ></v-select>
                        </v-col>
                    </v-row>  
                    <!--v-col cols="12">
                        <v-file-input
                        v-model="form.archivo"
                        label="Seleccionar archivo"
                        ></v-file-input>
                    </v-col-->
                    
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
    import axios from 'axios'
    import Form from 'vform'
    export default{
        name:'adddocumento',
        props:{
            ruta:{default:'add-documento'},
        },
        data(){
            return{
                dialog:false,
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
                'SOLICITUD',
                'OFICIO MULTIPLE',
            ],
            prioridades:[
                'NORMAL',
                'ESPECIAL',
                'URGENTE',
                'MUY URGENTE'
            ],
            }
        },methods:{
            add_doc(){
            this.finalizar_tiempo();
            this.form.post('/api/'+this.ruta).then(response=>{
                this.form.reset()
                this.dialog=false;
                this.$emit('refresh',true);
                
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
        }
    }
</script>
