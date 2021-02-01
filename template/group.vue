<template>
	<div id="ch-group">
		<div class="vuebar" v-bar>
			<div>
				<div class="container">
					<div class="row"> 
						<div class="ch-content col">
							<div class="container ch-content-body">
								<!-- Заголовок -->
								<div class="row ch-title"
									v-if="typeof data.title.name !== 'undefined'">
									<div class="col-4">{{data.title.name}}</div>
									<div class="col-8"><img src="/images/choco-title.jpg"></div>	
								</div>	

								<!-- Хлебные крошки -->
								<div class="row ch-breadcrumb"
									v-if="typeof data.title.name !== 'undefined'">
									<div class="col">
										<nav aria-label="breadcrumb">
											<ol class="breadcrumb">
												<li class="breadcrumb-item">
													<a href="/?type=group&id=0">Продукция</a>
												</li>
												<li class="breadcrumb-item" 
													v-for="itembreadcrumb in data.breadcrumb">
													<a :href="'/?type=group&id=' + itembreadcrumb.id">{{itembreadcrumb.name}}</a>
												</li>
												<li class="breadcrumb-item">
													<a :href="'/?type=group&id=' + data.title.id">{{data.title.name}}</a>
												</li>
											</ol>
										</nav>		
									</div>	
								</div>
								

								<!-- Карусель групп -->
								<div class="row ch-group-categories">
									<div class="owl-carousel owl-theme ch-group-owl">
										<div class="ch-group-categories-content" 
											v-for="itemgroup in data.group">
											<div v-on:click="window.location.href = '/?type=group&id=' + itemgroup.id;">
												<img v-if="itemgroup.image > 0" :src="'/core/get_image.php?id=' + itemgroup.image">
												<img v-else src="/images/no_foto.jpg">
											</div>
											<div>
												<div v-on:click="window.location.href = '/?type=group&id=' + itemgroup.id;">
													<span class="ch-tooltip" 
														data-toggle="tooltip" 
														data-placement="top" 
														:title="itemgroup.name">
														{{itemgroup.name}}
													</span>	
													<span>({{itemgroup.pr_count}})</span>
												</div>
												<div>
													<div class="vuebar" v-bar>
														<div>
															<ul v-if="itemgroup.subgroup.length > 0">
																<li v-for="itemsubgroup in itemgroup.subgroup"
																	v-on:click="window.location.href = '/?type=group&id=' + itemsubgroup.id;">
																	<span>{{itemsubgroup.name}}</span>
																	<span>({{itemsubgroup.pr_count}})</span>
																</li>
															</ul>
															<span v-else>{{itemgroup.description}}</span>
														</div>
													</div>
												</div>
											</div>
										</div>	
									</div>
								</div>

								<!-- Карточки продуктов -->
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 ch-card" v-for="itemproduct in data.product">
										<div>
											<img src="/images/prod_up.svg" class="ch-prod-up mx-auto d-block">
										</div>
										<div class="ch-product-card container">
											<div class="row ch-card-height">
												<div class="ch-product-card-content col">
													<!-- Карусель изображений -->
													<div class="row">
														<div class="col">
															<div class="owl-carousel owl-theme ch-product-owl">
																<div v-if="itemproduct.image.length == 0">
																	<img src="/images/no_foto.jpg">
																</div>
																<div v-for="itemimageproduct in itemproduct.image">
																	<img :src="'/core/get_image.php?id=' + itemimageproduct">
																</div>
															</div>	
														</div>	
													</div>	
													<!-- Наименование -->
													<div class="row">
														<div>
															<img src="/images/prod_down.svg" class="ch-prod-down mx-auto d-block">
														</div>
														<div class="col-8">
															<span class="ch-product-name">{{itemproduct.name}}</span>
														</div>
														<div class="col-4">
															<span class="ch-product-volume">{{itemproduct.weight}} {{itemproduct.weight_measure_name}}</span>
														</div>
													</div>
													<!-- Состав -->
													<div class="row">
														<div class="col">
															<span class="ch-product-structure-head">Состав:</span>
															<span class="ch-product-structure-body" v-html="itemproduct.structure"></span>
														</div>
													</div>
												</div>
											</div>
											
											<div class="row ch-product-cart-cost">
												<!-- Цена продукта -->
												<div class="col-4 ch-product-price-old">
												</div>
												<div class="col-8 ch-product-price">
													<span>{{PriceFormat(itemproduct.price)}} <i class="icon-rouble"></i></span>
													<span v-if="itemproduct.count > 0">
														&nbsp;({{itemproduct.count}} {{itemproduct.count_measure_name}})
													</span>
												</div>
											</div>									

											<!-- Блок добавления продукции в корзину -->		
											<div class="row ch-product-card-cart" v-show="!itemproduct.cart.is_cart">
												<div class="col-12">
													<!-- Кнопка "- 1" -->
													<div class="ch-product-card-cart-btn ch-product-card-cart-btn-left" 
														v-on:click="itemproduct = SetCount(itemproduct, (itemproduct.cart.count - 1));">
														<i class="icon-minus"></i>
													</div>
													<!-- Поле ввода количества -->
													<div class="ch-product-card-cart-btn ch-product-card-cart-btn-left">
														<input type="text" size="2" maxlength="4" class="ch-product-card-cart-input" 
														v-model="itemproduct.cart.count" 
														v-on:change="itemproduct = SetCount(itemproduct, itemproduct.cart.count);">
													</div>
													<!-- Кнопка "+ 1" -->
													<div class="ch-product-card-cart-btn ch-product-card-cart-btn-left" 
														v-on:click="itemproduct = SetCount(itemproduct, (itemproduct.cart.count + 1));">
														<i class="icon-plus"></i>
													</div>
													<!-- Кнопка добавления в корзину -->
													<div class="ch-product-card-cart-btn ch-product-card-cart-btn-right ch-product-card-cart-total" 
														v-on:click="itemproduct = SaveProductCart(itemproduct)">
														<div class="ch-product-card-cart-total-image">
															<i class="icon-basket"></i>
														</div>
														<div class="ch-product-card-cart-total-price">
															{{PriceFormat(itemproduct.cart.price)}}
															<i class="icon-rouble"></i>
														</div>
													</div>
												</div>
											</div>

											<!-- Информационный блок -->		
											<div class="row ch-product-card-info" v-show="itemproduct.cart.is_cart">
												<div class="col-12">
													<div class="ch-product-card-info-text">
														<div>В корзине: {{itemproduct.cart.count}} {{itemproduct.count_measure_name}}</div>
														<div>На сумму: {{PriceFormat(itemproduct.cart.price)}}<i class="icon-rouble"></i></div>		
													</div>
												</div>	
											</div>


										</div>	
									</div>
								</div>	

							</div>	
						</div>
					</div>	

					<!-- ПОДВАЛ -->
					<div class="row"> 
						<div class="ch-footer col">
							<div>
								<span>&copy; 2020г.</span>
							</div>
						</div>
					</div>
					<!-- END ПОДВАЛ -->

				</div>
			</div>	
		</div>

					

</template>

<style>
	/* Параметры панели */
	#ch-group {
		position: fixed;
		top: 50px;
		width: 100%;
		z-index: 100;
		color: rgb(var(--ch-color-01-rgb));
		height: calc(100vh - 50px);
	}
		#ch-group .row {
			-ms-flex-pack: center!important;	
		}
		#ch-group .ch-content {
			min-height: calc(100vh - 80px);
			padding: 0px 0px 0px 0px;	
			border: none;
			border-radius: 0px; 
			overflow: hidden;
		}
		
	/* Ползунок прокрутки (кастомизация) */
	#ch-group .vb-dragger {
		z-index: 110;
	}	

	/* кастомизация font-chocosun */
	#ch-group [class^="icon-"]:before, 
	#ch-group [class*=" icon-"]:before {
		margin-right: 0px;
		margin-left: 0px;
	}


	/* Заголовок выбранной категории */
	#ch-group .ch-title {
		padding-top: 5px;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;		
	}
		#ch-group .ch-title > div:first-child {
			position: relative;
			float: left;
			width: 100%;
			height: 50px;
			margin: 0px;
			color: rgb(var(--ch-color-01-rgb));
			font-weight: 600;
			padding: 15px;
			overflow: hidden;
			display: flex;
			align-items: center;
		}
	
		/* Изображение заголовка */
		#ch-group .ch-title > div:last-child {
			position: relative;
			width: 100%;
			height: 50px;
			margin: 0px;
			padding: 0px;
			overflow: hidden;
			display: flex;
			align-items: center;
			-webkit-clip-path: polygon(0% 0%, 100% 0, 100% 100%, 25px 100%);
			clip-path: polygon(0% 0%, 100% 0, 100% 100%, 25px 100%);
		}
	
	/* Хлебные крошки */
	#ch-group .ch-breadcrumb {
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;		
	}
		#ch-group .ch-breadcrumb a {
			color: rgb(var(--ch-color-01-rgb))!important;
			text-decoration: none;
		}
			#ch-group .ch-breadcrumb a:hover {
				color: rgb(var(--ch-color-02-rgb))!important;
				text-decoration: underline;
				text-shadow: -1px -1px rgb(var(--ch-color-04-rgb));
			}
		
		#ch-group .ch-breadcrumb ol.breadcrumb {
			font-size: 0.8em;
			border-bottom: 1px solid rgb(var(--ch-color-01-rgb));
			border-radius: 0px;
			background-color: transparent;
			padding-left: 0px;
			padding-right: 0px;
		}	
			/* кастомизация разделителя */
			#ch-group .ch-breadcrumb li.breadcrumb-item:not(:first-child):before {
				content: "\\";
				color: rgb(var(--ch-color-01-rgb));
			}	
		
	/* Карточки групп */
	#ch-group .ch-group-categories {
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;		
	}
		#ch-group .ch-group-categories .ch-group-categories-content {
			display: flex;
			justify-content: flex-start;		
			padding: 5px;
			margin: 10px;
			border: 2px solid rgba(var(--ch-color-04-rgb));
			border-radius: 5px;
			box-shadow: 4px 4px 2px rgba(var(--ch-color-00-rgb), 0.2);
		}
			/* изображение группы */
			#ch-group .ch-group-categories .ch-group-categories-content > div:first-child {
				height: 100px;
				width: 100px;
				cursor: pointer;
			}
				#ch-group .ch-group-categories .ch-group-categories-content > div:first-child > img {
					width: inherit;
					height: inherit;
					border: 5px solid rgb(var(--ch-color-02-rgb));
					border-radius: 15px 15px 15px 15px;
				}	
			/* контент группы */
			#ch-group .ch-group-categories .ch-group-categories-content > div:last-child {
				height: 100px;
				width: 100%;
				margin-left: 10px;
			}
				/* заголовок группы */
				#ch-group .ch-group-categories .ch-group-categories-content > div:last-child > div:first-child {
					color: rgba(var(--ch-color-01-rgb), 1);
					border-bottom: 1px dashed rgba(var(--ch-color-01-rgb), 0.3);
					height: 26px;
					font-weight: 600;
					overflow: hidden;
				}
					#ch-group .ch-group-categories .ch-group-categories-content > div:last-child > div:first-child span:first-child:hover {
						cursor: pointer;
						color: rgb(var(--ch-color-02-rgb))!important;
						text-decoration: underline;
						text-shadow: -1px -1px rgb(var(--ch-color-04-rgb));
					}
					/*
					#ch-group .ch-group-categories .ch-group-categories-content > div:last-child > div:first-child > span:last-child {
						font-weight: normal;
						font-size: 10px;
						vertical-align: top;
						padding-left: 3px;
						padding-right: 4px;
						background-color: rgba(var(--ch-color-00-rgb), 0.25);
						border-radius: 6px;
						color: rgba(var(--ch-color-04-rgb));					
					}*/
					#ch-group .ch-group-categories .ch-group-categories-content > div:last-child > div:first-child > span:last-child {
						font-weight: normal;
						font-size: 12px;
						vertical-align: top;
					}

				
				/* описание группы/список подгрупп */
				#ch-group .ch-group-categories .ch-group-categories-content > div:last-child > div:last-child {
					height: calc(100% - 30px);
					overflow: hidden;
					color: rgba(var(--ch-color-01-rgb), 0.5);
					font-size: 0.8em;
					line-height: 1.5;
					font-weight: normal;
				}
					#ch-group .ch-group-categories .ch-group-categories-content > div:last-child > div:last-child ul {
						padding: 5px 10px 0px 20px;	
					}
					#ch-group .ch-group-categories .ch-group-categories-content > div:last-child > div:last-child span:first-child {
					}
					#ch-group .ch-group-categories .ch-group-categories-content > div:last-child > div:last-child span:last-child {
						padding-left: 2px;
						font-weight: normal;
						font-size: 10px;
						vertical-align: top;	
					}


					#ch-group .ch-group-categories .ch-group-categories-content > div:last-child > div:last-child li {
						color: rgb(var(--ch-color-01-rgb));
					}
						#ch-group .ch-group-categories .ch-group-categories-content > div:last-child > div:last-child li > span:hover{
							cursor: pointer;
							color: rgb(var(--ch-color-02-rgb))!important;
							text-decoration: underline;
							text-shadow: -1px -1px rgb(var(--ch-color-04-rgb));
						}
						
		/* owl-карусель карточки групп (кастомизация)*/
		#ch-group .ch-group-owl *:focus {
			outline: none!important;
		}	
			/* показывем owl-dots всегда */
			#ch-group .ch-group-owl .owl-dots {
				display: block !important;
			}
			#ch-group .ch-group-owl .owl-dots {
				margin-top: -5px;
			}
				#ch-group .ch-group-owl .owl-dot span {
					border-radius: 3px;
					width: 20px;
					height: 6px;
					background: rgba(var(--ch-color-01-rgb), 0.25);
				}						
					#ch-group .ch-group-owl .owl-dot.active span,
					#ch-group .ch-group-owl .owl-dot:hover span {
						background: rgba(var(--ch-color-01-rgb), 0.75);
					}
	
	/* Карточка продукта */
	#ch-group .ch-card {
		margin-bottom: 30px;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;		
	
	}

		#ch-group .ch-prod-up {
			position: absolute;
			width: calc(100% / 2);
			z-index: 1;
			border-radius: 10px 0px 0px 0px;
		}

		#ch-group .ch-prod-down {
			position: absolute;
			width: calc(100% / 3 * 2);
			right: 0px;
			bottom: -36px;
			border-radius: 10px 0px 0px 0px;
		}

	#ch-group .ch-product-card {
		border-radius: 10px;
		width: 100%;
		height: 100%;
		display: block;
		box-shadow: 4px 4px 2px rgba(var(--ch-color-00-rgb), 0.2);
	}
		#ch-group .ch-card-height {
			height: calc(100% - 36px - 80px);
		}
		#ch-group .ch-product-card-content {
			background: rgb(var(--ch-color-01-rgb));
			border: none;
			border-radius: 10px 10px 0px 0px;
			padding-top: 15px;
		}
			#ch-group span.ch-product-name {
				text-align: left;
				vertical-align: middle;
				color: rgb(var(--ch-color-02-rgb));
				line-height: 1.3;
				-webkit-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				user-select: none;	
				float: left;
				font-size: 1.2em;
				text-shadow: 1px 1px 0px rgba(var(--ch-color-00-rgb), 0.5);
				overflow: hidden;
				margin-bottom: 5px;
			}
			#ch-group span.ch-product-volume {
				text-align: right;
				vertical-align: middle;
				color: rgb(var(--ch-color-02-rgb));
				white-space: nowrap;
				line-height: 1.3;
				-webkit-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				user-select: none;	
				float: right;
				font-size: 1.2em;
				text-shadow: 1px 1px 0px rgba(var(--ch-color-00-rgb), 0.5);
				overflow: hidden;
				margin-bottom: 5px;
			}

		#ch-group .ch-product-card-content > div:last-child {
			padding-left: 10px;
		}
		
		#ch-group .ch-product-structure-head,
		#ch-group .ch-product-structure-body 
		{
			color: rgba(var(--ch-color-02-rgb), 0.75);
		}
		
		#ch-group .ch-product-cart-cost {
    		background: rgb(var(--ch-color-01-rgb));
    		padding: 10px 0px 5px 0px;
			color: rgb(var(--ch-color-02-rgb));
			text-shadow: 1px 1px 0px rgba(var(--ch-color-00-rgb), 0.5);
		}
			#ch-group .ch-product-price {
				font-weight: bold;
				white-space: nowrap;
				text-align: right;
				text-shadow: 1px 1px 0px rgba(var(--ch-color-00-rgb), 0.5);
			}


		/* Блок добавления в корзину */
		/* Информационный блок */
		#ch-group .ch-product-card-cart,
		#ch-group .ch-product-card-info {
			height: 80px;
    		background: rgb(var(--ch-color-04-rgb));
    		border-radius: 0px 0px 10px 10px;
			-ms-flex-align: center!important;
			align-items: center!important;
		}

			/* Блок добавления в корзину */
			/* Кнопки (базовый стиль) */
			#ch-group .ch-product-card-cart-btn {
				background: rgb(var(--ch-color-01-rgb));
				color: rgb(var(--ch-color-02-rgb));
				text-align: center;
				border-radius: 10px;
				height: 36px;
				padding-left: 10px;
				padding-right: 10px;
				font-weight: bold;
				line-height: 38px;
				font-size: 1em;
				cursor: pointer;
			}

				/* Выравнивание кнопок */
				#ch-group .ch-product-card-cart-btn-left {
					float: left;
					margin-right: 5px; 
				}
				#ch-group .ch-product-card-cart-btn-right {
					float: right;
					margin-left: 5px; 
				}

			/* Поле ввода */
			#ch-group .ch-product-card-cart-input {
				height: 100%;
				width: 20px;
				font-weight: bold;
				text-align: center;
				background: transparent;
				border: none;
				color: rgb(var(--ch-color-02-rgb));
				outline: none;
			}

			/* Итоговая сумма продукта */
			#ch-group .ch-product-card-cart-total {
				width: 120px;
			}
				#ch-group .ch-product-card-cart-total-image {
					float: left;
				}
				#ch-group .ch-product-card-cart-total-price {
					float: right;
				}	

			/* Информационный блок */
			#ch-group .ch-product-card-info-text {
				background: transparent;
				text-align: center;
				font-weight: bold;
				font-size: 1.2em;
				color: rgb(var(--ch-color-01-rgb));
				cursor: auto;
			}
					
	/* owl-карусель продуктов (кастомизация)*/
	#ch-group .ch-product-owl{
		margin-left: 2px;
	}
		/* показывем owl-dots всегда */
		#ch-group .ch-product-owl .owl-dots {
			display: block !important;
		}

		#ch-group .ch-product-owl *:focus {
			outline: none!important;
		}	
		#ch-group .ch-product-owl .owl-stage-outer {
			background: rgba(var(--ch-color-03-rgb), 0.25);
			width: calc(100% - 5px);
			border-radius: 10px;
			border: 2px solid rgb(var(--ch-color-02-rgb));
		}
		#ch-group .ch-product-owl .owl-stage {
			display: flex;
			align-items: center;
		}
		#ch-group .ch-product-owl .owl-dots {
			margin-top: 0px;
		}
			#ch-group .ch-product-owl .owl-dot span {
				border-radius: 3px;
				width: 20px;
				height: 6px;
				background: rgba(var(--ch-color-02-rgb), 0.25);
			}
				#ch-group .ch-product-owl .owl-dot.active span,
				#ch-group .ch-product-owl .owl-dot:hover span {
					background: rgba(var(--ch-color-02-rgb), 0.75);
				}
	


	/* Подвал */
	#ch-group .ch-footer {
		-ms-flex-pack: centerimportant;
		justify-content: center!important;
	    line-height: 1.5;
		font-size: 1.1em;
		font-weight: normal;
		height: 34px;
		margin-top: 10px;
	}	
		#ch-group .ch-footer > div {
			border-top: 1px solid rgb(var(--ch-color-01-rgb));
		    text-align: end;
			color: rgb(var(--ch-color-01-rgb));
		}
		
</style>

<script>

	Vue.config.devtools = false;
	Vue.config.performance = false;

	module.exports = {
		props:['type', 'id', 'cartcount'],
		data: function() {
			return {
				data: {
					title: {},
					breadcrumb: [],
					group: [],
					product: []
				}
			}	
		},
		methods: {
			/* Форматирование суммы продукции */
			PriceFormat: function(price) {
				return (numeral(price).format('0,0.00'));
			},
			/* Установка количества продукции (от 1 до 99 единиц) */
			SetCount(item, count) {
				/* если продукция не в корзине */
				if(!item.cart.is_cart) { 
					count = (count < 1) ?  1: count;
					count = (count > 99) ? 99: count;
					item.cart.count = parseInt(count);
					item.cart.price = item.price * item.cart.count;
				}
				return item;
			},
			/* Сохраняем покупку в корзине */
			SaveProductCart(item) {
				if(!item.cart.is_cart) {
					axios
						.post('/core/set_data.php', {
							type: 'cart',
							id: item.id,
							count: item.cart.count						
						})
						.then(response => {
							if ((response.status == 200) && (response.data.state == 'ok')) {
								item.cart.is_cart = true;
								this.cartcount = this.cartcount + 1;
								this.$emit('cartcount', this.cartcount);
							} else {
								item.cart.is_cart = false;
								console.log(response);
							}
						})
						.catch(error => {
							item.cart.is_cart = false;
							console.log(error);
						});
				}
				return item;
			}
		},		
		mounted: function(){
			fetch('/core/get_data.php?type=' + this.type + '&id=' + this.id)
				.then(response => response.json())
				.then(json => this.data = json.data)
			;
		},
		updated: function(){
			/* инициализация карусели продуктов */
			$('.ch-product-owl').owlCarousel({
				items: 1,
				dots: true,
				loop: false,
				margin: 5,
				center: true,
			});
			/* инициализация карусели групп продуктов */
			$('.ch-group-owl').owlCarousel({
                loop: false,
				dots: true,
                autoplay: false,
				margin: 5,
                responsive: {
                    0:{
                        items: 1
                    },
                    768: {
                        items: 2
                    },
                    991: {
                        items: 3
                    }
                }				
			});
			/* инициализация всплывающих подсказок */
			$('.ch-tooltip').tooltip();		
		}
	}
</script>

