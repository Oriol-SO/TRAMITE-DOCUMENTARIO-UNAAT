<template>
    <div>
        <v-card  class="ma-3">
            <v-card-title>
               Seguimiento de tramite   
               <v-btn color="primary" class="text-capitalize" @click="dialogdoc=true">Cambiar documento</v-btn>    
            </v-card-title>
            <v-card-text>
                <v-card v-for="(proc,i) in dato.proceso" :key="i" class="mt-4">
                    <div class="ma-2">
                        <v-row v-if="proc.recepcion">
                            <v-col cols="4"><strong>Fecha de recepción:</strong> </v-col>
                            <v-col cols="4">{{proc.recepcion}}</v-col>
                             <v-col cols="4"> <v-chip small color="warning">{{proc.nom_input}}</v-chip></v-col>
                        </v-row>
                        <v-row v-if="proc.derivar">
                            <v-col cols="4"><strong>Fecha de derivación:</strong></v-col>
                            <v-col cols="4">{{proc.derivar}}</v-col>
                             <v-col cols="4"><v-chip small color="green accent-3">{{proc.nom_ouput}}</v-chip></v-col>
                        </v-row>
                    </div>
                    <v-card-actions>
                        <v-btn v-if="proc.ac_rep" small color="green accent-3" @click="recepcionar_doc(proc)">
                            Recepcionar
                        </v-btn>
                        <v-btn v-if="proc.ac_derivar" small color="primary" @click="dialog=true ,add_proc(proc.id)">Derivar</v-btn>
                        <v-btn v-if="proc.archivar" small color="green accent-3" @click="archivar(proc)">Archivar</v-btn>
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
            this.form2.documento=doc.documento;
            this.form2.proceso=doc.id;
            if(!confirm('¿Estas seguro de realizar esta accion?')){
                return
            }
            this.form2.post('/api/recepcionar-doc').then(response=>{
                this.$emit('refresh',true)
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
                documento:doc.documento,
            })
            if(!confirm('¿Estas seguro de realizar esta accion?')){
                return
            }
            form.post('/api/archivar-doc').then(response=>{
                this.$emit('refresh',true)
            }).catch(error=>{
                console.log(error.response.data.message);
            })
        }

    }
}
</script>