<template>
    <div> 
        <v-card elevation="0">
             <v-card  height="95%" width="95%" style="height:100ch;" class="my-2 mx-2 d-flex" >
                <embed :src="dato.path" type="application/pdf" class="mx-auto" width="100%" height="100%"/>
            </v-card>
        </v-card>
    </div>
</template>
<script>
export default {
    name:'verdoc',
    props:{
        dato:{default:[]},
    }
}
</script>