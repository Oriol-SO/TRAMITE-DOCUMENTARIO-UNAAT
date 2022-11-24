import Vue from 'vue'
import Child from './Child.vue'
import { HasError, AlertError, AlertSuccess } from 'vform/components/bootstrap5'
import datosdoc from '~/components/datosdoc.vue';
import verdoc from '~/components/verdocumento.vue';
import accionesdoc from '~/components/accionesdoc.vue';
import sidevar from '~/components/sidevar.vue';
import adddocumento from '~/components/agregardocumento.vue';
import soporteproc from '~/components/soporteprocesos.vue';
 
// Components that are registered globaly.
[
  Child,
  HasError,
  AlertError,
  AlertSuccess,
  datosdoc,
  verdoc,
  accionesdoc,
  sidevar,
  adddocumento,
  soporteproc
].forEach(Component => {
  Vue.component(Component.name, Component)
})
