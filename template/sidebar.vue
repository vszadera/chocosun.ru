<template>
	<span>
		<!-- Боковая навигационная панель -->
		<div id="ch-sidebar" class="container" 
			v-bind:class="{active: issidebar}" 
			v-on:click="HideSideBar()">
			<div class="row">
				<div class="col-12 ch-logotip"
					v-on:click="window.open('/', '_top');">
					<img src="/images/logo.svg">
				</div>
				<div class="col-12 ch-content">
					<div class="vuebar" v-bar>
						<div>
							<div class="container ch-content-body">
								<div v-for="item in data.menu">
									<div>
										<span v-on:click="window.open(item.link, '_top');">
											<i v-bind:class="[item.picture]"></i>
											{{item.name}}
										</span>	
									</div>
									<div v-if="item.products">
										<div v-for="itemproducts in item.products">
											<span v-on:click="window.open('/?type=group&id=' + itemproducts.id, '_top');">
												{{itemproducts.name}}
											</span>
										</div>
									</div>	
								</div>	
							</div>	
						</div>		
					</div>
				</div>	
			</div>	
		</div>
	
		<!-- Страница перекрытия Оверлей -->
		<div id="ch-overlay" v-bind:class="{active: issidebar}" v-on:click="HideSideBar()"></div>
	</span>
</template>

<style>
	/* Параметры панели */
	#ch-sidebar {
		position: fixed;
		top: 0;
		left: -110vw;
		height: 100vh;
		width: 90vw;
		max-width: 300px;
		padding-top: 10px;
		padding-right: 15px;
		padding-left: 15px;
		margin-right: auto;
		margin-left: auto;
		background: rgb(var(--ch-color-01-rgb));
		transition: all 0.5s;
		overflow: hidden;
		box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.2);
		z-index: 999;
	}
		#ch-sidebar.active {
			left: 0;
		}

	/* Логотип */
	#ch-sidebar .ch-logotip > img {
		width: auto;
		height: 100px;
		margin-left: auto!important;
		margin-right: auto!important;
		display: block!important;	
	}
		#ch-sidebar .ch-logotip > img {
			cursor: pointer;
		}


	/* Контент */
	#ch-sidebar .ch-content {
		padding: 5px 15px 5px 15px;
		height: calc(100vh - 120px);
		overflow: hidden;
	}
		#ch-sidebar .ch-content-body {
		}
			#ch-sidebar .ch-content-body > div {
			}
				/* Наименование группы */
				#ch-sidebar .ch-content-body > div > div:nth-child(1) {
					margin-right: 10px;
					padding: 10px 10px 10px 15px;
					font-size: 1.1em;
					letter-spacing: 1px;
					color: rgb(var(--ch-color-02-rgb));
					border-bottom: 1px solid #FDF4E3;
					border-radius: 0px 0px 0px 20px;	
				}

				/* Содержимое группы */
				#ch-sidebar .ch-content-body > div > div:nth-child(2) {
					padding: 10px 10px 10px 45px;
					font-size: 0.8em;
					line-height: 28px;
					letter-spacing: 1px;
					color: rgb(var(--ch-color-02-rgb));
				}
					#ch-sidebar .ch-content-body > div > div span:hover,
					#ch-sidebar .ch-content-body > div > div span:hover {
						cursor: pointer;
						text-shadow: -1px -1px rgba(var(--ch-color-04-rgb), 0.25);
						text-decoration: underline;
					}

	/* Оверлей */
	#ch-overlay {
		display: none;
		position: fixed;
		width: 100vw;
		height: 100vh;
		background: rgba(0, 0, 0, 0.3);
		z-index: 998;
		opacity: 100;
		transition: all 0.5s ease-in-out;
	}
		#ch-overlay.active {
			display: block;
			opacity: 1;
		}

	/* кастомизация font-chocosun */
	#ch-sidebar [class^="icon-"]:before, 
	#ch-sidebar [class*=" icon-"]:before 
	{
		margin-right: 10px;
	}
		
</style>

<script>
	module.exports = {
		props:['issidebar'],
		data: function() {
			return {
				data: {
					menu: [
						{
							name: 'Продукция',
							picture: 'icon-coffee',
							link: '/?type=group&id=0',
							products: []
						},
						{
							name: 'О нас',
							picture: 'icon-address',
							link: '/?type=about'
						},
						{
							name: 'Доставка и оплата',
							picture: 'icon-truck',
							link: '/?type=delivery'
						},
						{
							name: 'Обратная связь',
							picture: 'icon-fidback',
							link: '/?type=feedback'
						}
					]	
				}
			}
		},
		methods: {
			/* Отдаем в родителя событие о закрытии SideBar */
			HideSideBar() {
				this.issidebar = false;
				this.$emit('issidebar', this.issidebar);
			}
		},
		mounted: function(){
			fetch('/core/get_data.php?type=menu')
				.then(response => response.json())
				.then(json => this.data.menu[0].products = json.data.menu)
		}		
	}
</script>

