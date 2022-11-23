<template>
    <div>
     <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
          
        <v-navigation-drawer
        v-model="drawer"
        absolute
        left
        temporary
        >
        <v-list
            nav
            dense
        >
            <v-list-item-group
            v-model="group"
            active-class="deep-primary--text text--accent-4"
            >
            <router-link 
                v-for="(item,i) in items" :key="i" :to="{name: item.to}" link style="text-decoration:none">   
                <v-list-item link class="iconos" >
                <v-list-item-icon>
                    <v-icon color="grey">{{item.icon}}</v-icon>
                </v-list-item-icon>
                <v-list-item-title>{{item.title}}</v-list-item-title>
                </v-list-item>
            </router-link>
            </v-list-item-group>
        </v-list>
        </v-navigation-drawer>
    </div>
</template>

<script>
export default {
    name:'sidevar',
    props: {
      items: {default: [] },
    },
    data(){
        return{
            drawer: false,
            group: null,
        }
    },
    watch: {
        group () {
            this.drawer = false
        },
    },
}
</script>