<template>
    <div>
        <v-card  class="ma-3">
            <v-card-title>
               Procesos del documento   
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
                        <v-row v-if="proc.prohevido">
                            <v-col cols="12" class="py-0">
                                <strong style="color:blue;">Provehido:</strong> <br>
                                <span>{{proc.prohevido}}</span>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" class="py-0">
                            <small v-if="dato.estado || dato.estado_res"><v-chip class="ml-auto" small color="green" dark>Finalizado</v-chip></small>
                            <small v-else-if="proc.estado_rep"><v-chip class="ml-auto" small color="primary" dark>Recepcionado</v-chip></small>
                            <small v-else-if="proc.estado_der"><v-chip class="ml-auto" small color="purple" dark>derivado</v-chip></small>
                            <small v-else><v-chip class="ml-auto" small color="grey" dark>Creado</v-chip></small>
                            </v-col>
                        </v-row>
                        
                    </div>
                    <v-card-actions>     
                       <v-btn v-if="proc.eliminar" class="ml-auto" small color="error" @click="del_proc(proc)">Eliminar</v-btn>      
                    </v-card-actions>
                </v-card>
              {{oficinas_fetch}}
            </v-card-text>
        </v-card>
    </div>
</template>
<script>
import Form from 'vform'
import axios from 'axios'
export default {
    name:'soporteproc',
    props:{
        dato:{default:[]}
    },data(){
        return{
            dialog:false,
            dialogdoc:false,
            form: new Form({
                documento:'',
                proceso:''
            }),

        }
    },computed:{

    },
    methods:{
        del_proc(proc){
            this.form.proceso=proc.id;
            this.form.documento=proc.documento_id;
        },  
    }
}
</script>