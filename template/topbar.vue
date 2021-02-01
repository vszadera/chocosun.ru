<template>
	<div id="ch-topbar">
		<!-- Фоновое изображение -->
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
			<img src="/images/bg.png" class="ch-background">
		</div>
		<!-- Логотип -->
		<div class="ch-logotip"
			v-on:click="window.open('/', '_top');">
			<img src="/images/logo.svg" class="ch-logo-image">
		</div>	
		<div class="container">
			<div class="row"> 
				<div class="ch-content col">
					<!-- Левая часть навигационных кнопок -->
					<div class="ch-content-block">
						<!-- Меню -->
						<div v-on:click="ShowSideBar()">
							<i class="icon-th-list"></i>
						</div>
					</div>
					<!-- Правая часть навигационных кнопок -->
					<div class="ch-content-block">
						<div v-on:click="window.open('https://vk.com/chocosun', '_blank');">
							<i class="icon-vkontakte"></i>
						</div>	
						<div v-on:click="window.open('https://taplink.cc/chocosun_tmn', '_blank');">
							<i class="icon-instagram"></i>
						</div>	
						<div v-on:click="window.open('/?type=cart', '_top');">
							<i class="icon-basket"></i>
							<span v-if="cartcount > 0" class="ch-badge">
								{{cartcount > 99 ? '99+' : cartcount}}
							</span>
						</div>	
					</div>
					
				</div>
			</div>
		</div>
	</div>
</template>

<style>
	/* Фон */
	#ch-topbar .ch-background {
		display: block!important;
		margin-right: auto!important;
	    margin-left: auto!important;
		position: fixed;
		width: 100%;
	}

	/* Слой логотипа */	
	#ch-topbar .ch-logotip {
		position: absolute;
		top: 0px;
		height: 50px;
		width: 100vw;
		padding: 0px;
	}
		@media (max-width: 414px) {
			#ch-topbar .ch-logotip {
				padding-left: 40px;
				padding-right: 120px;
			}
		}
			/* Изображение */
			#ch-topbar .ch-logo-image {
				display: block!important;
				margin-left: auto!important;
				margin-right: auto!important;		
				margin-top: -20px;
				width: 160px;
				height: auto;
			}

	/* Параметры панели */
	#ch-topbar {
		position: fixed;
		top: 0px;
		width: 100%;
		z-index: 100;
	}
		#ch-topbar .ch-content {
			height: 50px;
			padding: 5px 0px 5px 0px;
			border-bottom: 1px solid #724A2E;
			overflow: hidden;
		}

		/* Левая часть навигационных кнопок */
		#ch-topbar .ch-content-block:first-child {
			position: relative;
			float: left;
			display: flex;
			margin-left: 5px;
		}
		/* Правая часть навигационных кнопок */
		#ch-topbar .ch-content-block:last-child {
			position: relative;
			float: right;
			display: flex;
			margin-right: 5px;
		}
			/* Псевдокнопки */
			#ch-topbar .ch-content-block > div {
				vertical-align: middle;
				text-align: center;
				width: 40px;
				height: 40px;
				border: none;
				padding: 8px 3px 8px 3px;
				-webkit-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				user-select: none;		
				cursor: pointer;
			}
			#ch-topbar .ch-content-block > div:active, 
			#ch-topbar .ch-content-block > div:hover 
			{
				padding: 5px 0px 5px 0px;
				border: 3px solid rgba(var(--ch-color-01-rgb), 0.5);
				border-radius: 5px 10px 5px 10px;
			}
			
			
				#ch-topbar .ch-content-block > div > i {
					font-size: 20px;
					line-height: 1.3;
					color: rgb(var(--ch-color-01-rgb));
				}
	
		/* Бандж */
		#ch-topbar .ch-badge {
			vertical-align: top;
			color: #fff;
			background: #FF6C60;
			border-radius: 10px;
			-webkit-border-radius: 10px;
			font-size: 10px;
			font-weight: normal;
			line-height: 13px;
			padding: 2px 5px;
			position: absolute;
			right: 0px;
			top: 0px;
			white-space: nowrap;
			text-align: center;
		}

</style>

<script>
	module.exports = {
		props: ['cartcount', 'issidebar'],
		data: function() {
			return {
			}	
		},
		methods: {
			/* Отдаем в родителя событие об открытии SideBar */
			ShowSideBar() {
				this.issidebar = true;
				this.$emit('issidebar', this.issidebar);
			},
			/* Отдаем в родителя количество товаров в корзине */
			GetCartCount() {
				this.$emit('cartcount', parseInt(this.cartcount));
			}
			
		},
		mounted: function(){
			fetch('/core/get_data.php?type=topbar')
				.then(response => response.json())
				.then(json => this.cartcount = json.data.cartcount)
			;
		},
		updated: function() {
			this.GetCartCount();
		}
	}
</script>

