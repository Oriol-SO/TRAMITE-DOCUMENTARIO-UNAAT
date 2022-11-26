<template>
    <div>
        <v-card  class="ma-3">
            <v-card-title>
               Seguimiento de tramite 
               {{datos_asunt}} 
               <!--v-btn color="primary" class="text-capitalize" @click="dialogdoc=true">Cambiar documento</v-btn-->    
               <v-btn v-if="dato.accion" class="ml-3 text-capitalize" dark small color="green accent-3" @click="archivar(dato.id)">Archivar</v-btn>
               <v-btn v-if="dato.accion && !dato.resuelto" class="ml-3 text-capitalize" dark small color="cyan lighten-1" @click="resolver(dato.id)">Atender</v-btn>
            </v-card-title>
            <v-card-text>
                <v-card v-for="(proc,i) in dato.proceso" :key="i" class="mt-4">
                    <div class="ma-2">
                        <v-row v-if="proc.recepcion">
                            <v-col cols="3"><strong>Fecha de recepción:</strong> </v-col> 
                            <v-col cols="3">{{proc.recepcion}}</v-col>
                            <v-col cols="3"> <v-chip small color="warning">{{proc.nom_input}}</v-chip></v-col>
                            <v-col cols="3" v-if="proc.num_corre"><strong>Correlativo:</strong>{{zeroFill(proc.num_corre,4)}}</v-col>
                          
                        </v-row>
                        <v-row v-if="proc.derivar">
                            <v-col cols="3"><strong>Fecha de derivación:</strong></v-col>
                            <v-col cols="3">{{proc.derivar}}</v-col>
                             <v-col cols="3"><v-chip small color="green accent-3">{{proc.nom_ouput}}</v-chip></v-col>
                        </v-row>
                        <v-row v-if="proc.prohevido || proc.asunto">
                            <v-col cols="4" class="py-0">
                                <strong style="color:blue;">Número:</strong> <br>
                                <span>{{proc.numero}}</span>
                            </v-col>
                            <v-col cols="4" class="py-0">
                                <strong style="color:blue;">Asunto:</strong> <br>
                                <span>{{proc.asunto}}</span>
                            </v-col>
                            <!--v-col cols="4" class="py-0">
                                <strong style="color:blue;">Provehido:</strong> <br>
                                <span>{{proc.prohevido}}</span>
                            </v-col-->
                            <v-col cols="4" class="py-0">
                                <strong style="color:blue;">Tipo de documento:</strong> <br>
                                <span>{{proc.tipo}}</span>
                            </v-col>
                        </v-row>
                    </div>
                    <v-card-actions>
                         <v-text-field
                         v-if="proc.ac_derivar"
                         v-model="form.numero"
                         label="Número"
                        >
                        </v-text-field>
                        
                          <v-text-field
                         class="ml-4"
                         v-if="proc.ac_derivar"
                         v-model="form.asunto"
                         color="green"
                         label="Asunto"
                        >
                        </v-text-field>
                        <v-select
                         class="ml-4"
                         v-if="proc.ac_derivar"
                         v-model="form.tipo"
                         color="green"
                         label="Tipo doc"
                         :items="['RESOLUCION',
                                'MEMORANDO MULTIPLE',
                                'MEMORANDO',
                                'PROVEIDO',
                                'INFORME',
                                'INFORME TECNICO',
                                'INFORME LEGAL',
                                'CARTA',
                                'OFICIO',
                                'SOLICITUD',
                                'OFICIO MULTIPLE',]"
                        >

                        </v-select>
                        <!--v-text-field
                         v-if="proc.ac_derivar"
                         v-model="form.prohevido"
                         label="Provehido"
                        >
                        </v-text-field-->
                    </v-card-actions>
                    
                    <v-card-actions>
                        <v-btn v-if="proc.ac_rep" small color="green accent-3" @click="recepcionar_doc(proc)">
                            Recepcionar
                        </v-btn>
                        <v-btn v-if="proc.ac_derivar" small color="primary" @click="dialog=true ,add_proc(proc.id)">Derivar</v-btn>
                        
                    </v-card-actions>
                </v-card>
              {{oficinas_fetch}}
            </v-card-text>
        </v-card>
        <v-dialog
        v-model="dialog"
        max-width="600"
        >
            <v-card >
                <v-toolbar color="primary" dark elevation="0">
                    Derivar documento
                </v-toolbar>
                <v-card-text>
                    <v-select 
                    v-model="form.oficina"
                    label="Elija el organo/unidad"
                    :items="oficinas"
                    return-object
                    item-text="nombre"
                    item-value="id"
                    >

                    </v-select>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="primary" outlined @click="derivar()">Guardar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog
        v-model="dialogdoc"
        max-width="600"
        >
            <v-card >
                <v-toolbar color="warning" dark elevation="0">
                  Cambiar documento
                </v-toolbar>
                <v-card-text>
                   <v-file-input
                    v-model="formdoc.archivo"
                    label="Seleccione un nuevo documento"
                    ></v-file-input>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="warning" dark @click="actualizar_doc()">Actualizar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>
<script>
import Form from 'vform'
import axios from 'axios'
export default {
    name:'accionesdoc',
    props:{
        dato:{default:[]}
    },data(){
        return{
            dialog:false,
            dialogdoc:false,
            form: new Form({
                oficina:'',
                documento:'',
                proceso:'',
                prohevido:'',
                asunto:'',
                tipo:'',
                numero:'',
            }),

            form2:new Form({
                documento:'',
                proceso:'',
            }),
            oficinas:[],  
            
            formdoc:new Form({
                documento:'',
                archivo:[],
            })
            //proc:'',
        }
    },computed:{
        oficinas_fetch(){
            if(this.dialog==true){
                this.fetch_oficinas();
                
            } 
        },
        datos_asunt(){
            if(this.dato!=[]){
                if(this.dato.proceso?.length==1 && this.dato.proceso[0].ac_derivar==1){
                    this.form.asunto=this.dato.documento;
                    this.form.prohevido=this.dato.provehido;
                    this.form.tipo=this.dato.tipo;
                    this.form.numero=this.dato.numero;
                }else{
                    this.form.reset();
                }
            }
        }
    },
    methods:{
        fetch_oficinas(){
            axios.get('/api/fetch-oficinas').then(response=>{
                this.oficinas=response.data
            })
        },
        derivar(){
            this.form.documento=this.dato.id;
            this.form.post('/api/derivar-doc').then(response=>{
                this.dialog=false;
                this.$emit('refresh',true) 
                this.form.reset()
            }).catch(error=>{
                console.log(error.response.data.message)
            })
        },
        add_proc(id){
            this.form.proceso=id;
        },
        recepcionar_doc(doc){
            this.form.reset();
            this.form2.documento=doc.documento;
            this.form2.proceso=doc.id;
            if(!confirm('¿Estas seguro de realizar esta accion?')){
                return
            }
            this.form.reset()
            this.form2.post('/api/recepcionar-doc').then(response=>{
                this.$emit('refresh',true)
                this.form.asunto=this.dato.documento;
                this.form.prohevido='';
                this.form.tipo='';
                this.form.numero='';
            }).catch(error=>{
                console.log(error.response.data.message);
            })

        },
        actualizar_doc(){
            this.formdoc.documento=this.dato.id;
            this.formdoc.post('/api/actualizar-doc/'+this.dato.id).then(response=>{
                this.dialogdoc=false;
                this.$emit('refresh',true) 
                this.formdoc.reset()
            })
        },archivar(doc){
            let form= new Form({
                documento:doc,
            })
            if(!confirm('¿Estas seguro de realizar esta accion?')){
                return
            }
            form.post('/api/archivar-doc').then(response=>{
                this.$emit('refresh',true)
            }).catch(error=>{
                console.log(error.response.data.message);
            })
        },
        resolver(doc){
            let form= new Form({
                documento:doc,
            })
            if(!confirm('¿Estas seguro de realizar esta accion?')){
                return
            }
            form.post('/api/resolver-doc').then(response=>{
                this.$emit('refresh',true)
            }).catch(error=>{
                console.log(error.response.data.message);
            })
        }

        ,zeroFill( number, width ){
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