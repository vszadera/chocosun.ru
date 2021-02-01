
/* Плагин маски ввода данных */
Vue.use(VueMask.VueMaskPlugin);
/* Плагин загрузки однофайловых компонентов */
Vue.use(httpVueLoader);

/* Подключение библиотеки маршрутизации (получение параметров строки браузера)*/
var router = new VueRouter({
	mode: 'history',
	routes: []
});

/* Основной компонент */
var app = new Vue({
	el: '#app',
    router,
	data: {
		type: 0,
		id: 0,
		cartcount: 0,
		issidebar: false
	},
	components: {
		'topbar': 'url:/template/topbar.vue',
		'sidebar': 'url:/template/sidebar.vue',
		'group': 'url:/template/group.vue',
		'cart': 'url:/template/cart.vue',
		'about': 'url:/template/about.vue',
		'delivery': 'url:/template/delivery.vue',
		'feedback': 'url:/template/feedback.vue'
	},
	methods: {
		/* Применение темы для скроллбара SideBar */
        bindScrollbar(){

        },
		/* Получение статуса видимости SideBar из шаблона topbar.vue */
		GetSideBar(state) {
			this.issidebar = state;
		},
		/* Получение статуса видимости SideBar из шаблона topbar.vue */
		GetCartCount(cartcount) {
			this.cartcount = parseInt(cartcount);
		}


	},
	mounted() {
		/* Получение параметров строки браузера */
		this.type = this.$route.query.type;
		if(typeof this.type !== 'undefined' && this.type) { this.type = this.type.replace(/\s+/g,'').toLowerCase(); }
		if((typeof this.type === 'undefined') || (this.type == 'null') || (this.type == '')) { this.type = 'group'; }
		
		this.id = this.$route.query.id;
		if(typeof this.id !== 'undefined' && this.id) { this.id = this.id.replace(/\s+/g,'').toLowerCase(); }
		if((typeof this.id === 'undefined') || (this.id == 'null') || (this.id == '')) { this.id = 0; }
	},
	updated() {
	}
});
