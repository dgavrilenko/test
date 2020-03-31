Vue.component('form-component', require('./components/FormComponent.vue').default);
Vue.component('catalog-component', require('./components/catalog/CatalogComponent.vue').default);
Vue.component('basket-component', require('./components/BasketComponent.vue').default);
Vue.component('stepper-component', require('./components/StepperComponent.vue').default);

/* нотификации */
import Notifications from 'vue-notification';
import velocity      from 'velocity-animate';

Vue.use(Notifications, { velocity });

/* окна, заменяем не красивый confirm */
import VuejsDialog from 'vuejs-dialog';
//import VuejsDialogMixin from 'vuejs-dialog/dist/vuejs-dialog-mixin.min.js'; // only needed in custom components

// include the default style
import 'vuejs-dialog/dist/vuejs-dialog.min.css';
Vue.use(VuejsDialog);
