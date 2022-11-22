<template>
<div>
    
    <v-row>
        <v-col cols="12" md="6">
            <datosdoc :dato="doc"/>
            <accionesdoc :dato="doc" @refresh="refrescar"/>
        </v-col>
        <v-col cols="12" md="6">
            <verdoc :dato="doc" />
        </v-col>
    </v-row>
    
</div>

</template>
<script>
import axios from 'axios'
export default {

    data(){
        return{
            doc:[],
        }
    },mounted(){
        this.fetch_doc()
    },methods:{
        fetch_doc(){
            axios.get('/api/datos_doc_meza/'+this.$route.params.id).then(response=>{
                this.doc=response.data
            }).catch(error=>{   
                console.log(error.response.data.message);
            })
        },
        refrescar(result){
            if(result){
                this.fetch_doc()
            }
        }
    }
    
}
</script>