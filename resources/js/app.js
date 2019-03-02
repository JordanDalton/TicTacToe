import './bootstrap'

Vue.component('match', require('./components/Match.vue'))

const app = new Vue({
    el: '#app',

    data: {
        displayNavigation: false
    }
})
