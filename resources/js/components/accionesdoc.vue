<template>
    <div>
        <v-card  class="ma-3">
            <v-card-title>
               Seguimiento de tramite
            </v-card-title>
            <v-card-text>
                <v-card v-for="(proc,i) in dato.proceso" :key="i">
                    <v-row>
                        <v-col cols="4"><strong>Fecha de recepción:</strong> </v-col>
                        <v-col cols="4">{{proc.recepcion}}</v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="4"><strong>Fecha de derivación:</strong></v-col>
                        <v-col cols="4">{{proc.derivar}}</v-col>
                    </v-row>
                </v-card>
              
            </v-card-text>
        </v-card>
    </div>
</template>
<script>
export default {
    name:'accionesdoc',
    props:{
        dato:{default:[]}
    }
}
</script>