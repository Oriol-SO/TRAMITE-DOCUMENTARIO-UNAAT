<template>
    <div>
        <v-btn
        dark
        @click="imprimir()"
        color="green accent-3"
        >
            Imprimir
        </v-btn>
    </div>
</template>
<script>
import Form from 'vform'
export default {
    name:'imprimir',
    data(){
        return{

        }
    },
    methods:{
        imprimir(){
            let form =new Form({
                doc:''
            });
            form.post('/api/imprimir',{ headers: {
               // 'Content-Type': 'multipart/form-data',
            },
                'responseType': 'blob' // responseType is a sibling of headers, not a child
            }).then(response=>{    
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    console.log(url);
                    link.href = url;
                    link.setAttribute('download', 'documentos.xlsx');
                    document.body.appendChild(link);
                    link.click();
            }).catch(error=>{
                console.log('error');
            })
        }
    }
}
</script>
