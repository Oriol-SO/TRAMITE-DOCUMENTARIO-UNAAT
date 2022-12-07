<template>
    <div>
        <v-btn
        small
        rounded
        color="primary"
        elevation="0"
        style="color:#fff;"
        class="text-capitalize"
        @click="dialog=true"  
        >Editar</v-btn>
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
                        <datosdoc :dato="dato"/>
                        <soporteproc :dato="dato" @refresh="refrescar"/>
                    </v-card>
                </v-card>
        <v-snackbar
            :timeout="2000"
            :value="msg?true:false"
            absolute
            centered
            top
            tile
            color="red accent-2"
            >
            {{msg}}
        </v-snackbar>
        </v-dialog>
       
    </div>
</template>
<script>
export default {
    name:'editarprocs',
    props:{
        dato:{default:[]}
    },
    data(){
        return{
            dialog:false,
            msg:'',
        }
    },methods:{
        refrescar(result){
            this.msg=''
            if(result){ 
                this.msg='Derivación eliminada'
                this.$emit('refresh',true);
            }
        },
    }
}
</script>