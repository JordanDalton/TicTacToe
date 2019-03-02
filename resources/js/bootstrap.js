import Vue from 'vue'
import axios from 'axios'
import _ from 'lodash'
import SuperAxios from './components/Mixins/SuperAxios'

Vue.mixin(SuperAxios);

window.Vue = Vue
window.axios = axios

window.axios.defaults.headers.common = {
    'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    'X-Requested-With': 'XMLHttpRequest'
}
