<template>
    <div>
        <v-card class="ma-3">
            <v-card-text>
                <v-list-item-title class="text-h6">
                    Datos del Documento
                </v-list-item-title>
                <v-card elevation="0" class="mt-2">
                    <div id="dato">
                        <v-row>
                        <v-col cols="12" class="py-0" sm="6" md="4">
                            <v-list-item-subtitle>
                            <div class="box-data"> <strong>Nombre del documento: </strong>  <div class="box-data-valor">{{dato.documento}}</div></div> 
                        </v-list-item-subtitle>
                        </v-col>
                        <v-col cols="12" class="py-0" sm="6" md="4">
                            <v-list-item-subtitle>
                                <div class="box-data"><strong>Nombre del remitente: </strong>  <div class="box-data-valor">{{dato.remitente}}</div></div>
                            </v-list-item-subtitle>
                        </v-col>
                        <v-col cols="12" class="py-0" sm="6" md="4">
                            <v-list-item-subtitle>
                                <div class="box-data"><strong>DNI del remitente: </strong>  <div class="box-data-valor">{{dato.dni}}</div></div>
                            </v-list-item-subtitle>
                        </v-col>
                        <v-col cols="12" class="py-0" sm="6" md="4">
                            <v-list-item-subtitle>
                                <div class="box-data"><strong>Destino: </strong>  <div class="box-data-valor">{{dato.destino}}</div></div>
                            </v-list-item-subtitle>
                        </v-col>
                        <v-col cols="12" class="py-0" sm="6" md="4">
                            <v-list-item-subtitle>
                                <div class="box-data"><strong>Fecha creación: </strong>   <div class="box-data-valor"><v-chip small color="warning"> {{dato.fecha}}</v-chip></div></div>
                            </v-list-item-subtitle>
                        </v-col>
                        <v-col cols="12" class="py-0" sm="6" md="4">
                            <v-list-item-subtitle>
                                <div class="box-data"><strong>Tiempo de registro: </strong>   <div class="box-data-valor"><v-chip small color="purple lighten-4"> {{dato.tiempo_creacion}}</v-chip></div></div>
                            </v-list-item-subtitle>
                        </v-col>
                        <v-col cols="12" class="py-0" sm="6" md="4" v-if="dato.tiempo_final">
                            <v-list-item-subtitle>
                                <div class="box-data"><strong>Tiempo de final: </strong>   <div class="box-data-valor"><v-chip small color="green accent-3"> {{dato.tiempo_final}}</v-chip></div></div>
                            </v-list-item-subtitle>
                        </v-col>
                        </v-row>

                    </div>
                </v-card>
            </v-card-text>
        </v-card>
    </div>
</template>
<script>
export default {
    name:'datosdoc',
    props:{
        dato:{default:[]}
    },
    data(){
        return{

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