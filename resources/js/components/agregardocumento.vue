<template>
    <div>
    <v-btn color="primary"  class="text-capitalize" @click="dialog=true,iniciar_tiempo(),obtener_numero()" >Agregar registro</v-btn>
    
    <v-dialog
        v-model="dialog"
        max-width="700"
    >   
        <v-card elevation="0">
            <v-toolbar :color="color" dark elevation="0">
                Registrar nuevo documento  N°: {{numero_corre}}
            </v-toolbar>
            <v-card-text>
                <v-card elevation="0">
                 {{tipo_documents}}
                    
                    <v-row>
                        <v-col cols="6">
                            <v-select
                            v-model="form.tipo_doc"
                            :items="tipos_docs"
                            label="Tipo de documento"
                            >
                            </v-select>
                        </v-col>
                        <v-col cols="6">
                            <v-select
                            :items="tipos"
                            v-model="form.tipo"
                            label="Documento"
                            ></v-select>
                        </v-col>
                        <!--v-col cols="4">
                            <v-text-field
                            v-model="form.provehido"
                            label="Provehido"
                            >
                            </v-text-field>
                        </v-col-->

                        <v-col cols="6">
                            <v-text-field
                            v-model="form.numero_doc"
                            label="Numero de documento"
                            >
                            </v-text-field>
                        </v-col>
                        <v-col cols="6">
                        <v-text-field
                        v-model="form.folio"
                        label="Folios"
                        >
                        </v-text-field>
                        </v-col>
                    </v-row>
                    <!--v-row>
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

                       
                    </v-row-->
                    
                    <v-row>
                         <v-col cols="12">
                            <v-text-field
                            v-model="form.nombre"
                            label="Asunto"
                            >
                            </v-text-field>
                        </v-col> 
                        <v-col cols="8" v-if="form.tipo_doc=='EXTERNO'">
                        <v-text-field
                         v-model="form.remitente"
                        label="Interesado"
                        >
                        </v-text-field>
                        </v-col>
                        <v-col cols="4" v-if="form.tipo_doc=='EXTERNO'">
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
    import { mapGetters } from "vuex";
    import axios from 'axios'
    import Form from 'vform'
    export default{
        name:'adddocumento',
        props:{
            ruta:{default:'add-documento'},
            color:{default:'#FB8C00'},
        },
        data(){
            return{
                dialog:false,
                form: new Form({
                remitente:'',
                dni:'',
                archivo:[],
                nombre:'',
                tipo:'',
                destino:'',
                prioridad:'',
                tipo_doc:'',
                numero_doc:'',
                direccion:'',
                referencia:'',
                anexo:'',
                folio:'',
                provehido:'',

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
            tipos_docs:[],
            numero_corre:'',
            }
        },computed:{
            ...mapGetters({
            user: "auth/user",
            }),
            tipo_documents(){
                if(this.user.oficina_id!=1  ){
                    this.tipos_docs=['INTERNO']
                }else{
                    this.tipos_docs=['INTERNO','EXTERNO']
                }   
            }
        },        
        methods:{
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
            obtener_numero(){
                axios.get('/api/obtener-numero').then(response=>{
                    this.numero_corre=this.zeroFill(response.data,4);
                })
            },
            zeroFill( number, width ){
                width -= number.toString().length;
                if ( width > 0 )
                {
                    return new Array( width + (/\./.test( number ) ? 2 : 1) ).join( '0' ) + number;
                }
                return number + ""; // siempre devuelve tipo cadena
            }
        }
    }
</script>
