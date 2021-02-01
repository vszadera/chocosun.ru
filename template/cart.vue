<template>
	<div id="ch-cart">
		<div class="vuebar" v-bar>
			<div>
				<div class="container">
					<div class="row"> 
						<div class="ch-content col">
							<!-- ЗАКАЗ ОФОРМЛЕН -->
							<div class="container ch-content-body"
								v-if="zakaz.is_ok">
								<div class="row ch-section-head">
									<div class="col-12">Заказ оформлен.</div>
								</div>	
								<div class="row ch-section-body ch-product-cart-zakaz">
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
										<div>
											<div>
												<div><span>Номер заказа: </span><span>{{zakaz.id}}</span></div>
												<div><span>Статус: </span><span>Обрабатывается оператором.</span></div>
											</div>	
											<div>
												<div>
													<span>Сумма заказа</span>
													<span></span>
													<span v-html="SetAlternatePrice(calculate.product)"></span>
												</div>
												<div>		
													<span>Сумма доставки</span>
													<span></span>
													<span v-html="SetAlternatePrice(calculate.delivery)"></span>
												</div>
												<div>		
													<span>Итого к оплате</span>
													<span></span>
													<span v-html="SetAlternatePrice(calculate.total)"></span>
												</div>	
											</div>
											<div>
												<div
													v-on:click="window.location.href = '/';">
													<i class="icon-basket"></i>
													<span>Продолжить покупки</span>
												</div>	
											</div>
											<div>
												Спасибо, что обратились за услугой в нашу мастерскую сладостей «ChocoSun».
											</div>
										</div>	
									</div>
								</div>	
							</div>	
							<!-- ЗАКАЗ ОФОРМЛЕН //-->
						
							<!-- ПУСТАЯ КОРЗИНА -->
							<div class="container ch-content-body"
								v-if="data.product.length == 0 && !zakaz.is_ok">
								<div class="row ch-section-head">
									<div class="col-12">Корзина товаров пуста.</div>
								</div>	
							</div>	
							<!-- ПУСТАЯ КОРЗИНА //-->
						
							<!-- КОРЗИНА С ВЫБРАННЫМИ ПРОДУКТАМИ -->
							<div class="container ch-content-body"
								v-if="data.product.length != 0 && !zakaz.is_ok">
								<!-- Раздел корзины товаров -->
								<div class="row ch-section-head">
									<div class="col-12">Корзина товаров</div>
								</div>	
								<!-- Строка с товаром -->
								<div class="row ch-section-body ch-product-cart-item" 
									v-for="(item_product, index) in data.product">
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
										<div>
											<div class="ch-product-cart-item-image">
												<img v-if="item_product.image_id != null" :src="'/core/get_image.php?id=' + item_product.image_id">
												<img v-else src="/images/no_foto.jpg">
											</div>	
											<div class="ch-product-cart-item-name">
												<div>{{item_product.name}} ({{item_product.weight}} {{item_product.weight_measure_name}})</div>
												<div>Цена за {{item_product.count}} {{item_product.count_measure_name}}: <span v-if="item_product.is_actual">{{SetPriceFormat(item_product.price)}} <i class="icon-rouble"></i></span><span v-else> - </span></div>
											</div>
										</div>
									</div>
									<!-- Товар актуален и НЕ удален из корзины -->
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"
										v-if="item_product.is_actual === true && item_product.cart.is_cart === true">
										<div>
											<!-- кнопка -1 -->
											<div class="ch-product-cart-item-btn ch-product-cart-item-btn-left" 
												v-on:click="item_product = ProductItemChangeCount(item_product, (item_product.cart.count - 1));">
												<i class="icon-minus"></i>
											</div>
											<!-- поле ввода количества -->
											<div class="ch-product-cart-item-btn ch-product-cart-item-btn-left">
												<input type="text" size="2" maxlength="2" class="ch-product-cart-item-input" 
													v-model="item_product.cart.count" 
													v-on:change="item_product = ProductItemChangeCount(item_product, item_product.cart.count);">
											</div>
											<!-- кнопка +1 -->
											<div class="ch-product-cart-item-btn ch-product-cart-item-btn-left"
												v-on:click="item_product = ProductItemChangeCount(item_product, (item_product.cart.count + 1));">
												<i class="icon-plus"></i>
											</div>
											<!-- итоговая цена -->
											<div class="ch-product-cart-item-total">
												<div>Итого:</div>
												<div>{{SetPriceFormat(item_product.cart.price)}} <i class="icon-rouble"></i></div>
											</div>
										</div>
										<div>
											<!-- кнопка удаления товара --> 
											<div class="ch-product-cart-item-btn ch-product-cart-item-btn-right"
												v-on:click="item_product = ProductItemDelete(item_product);">
												<i class="icon-trash"></i>
											</div>	
										</div>	
									</div>
									<!-- Товар НЕ актуален -->
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"
										v-if="item_product.is_actual === false">
										<div>
											<!-- уведомление о снятии товара с производства -->
											<div class="ch-product-cart-item-info"> 
												Товар снят с производства
											</div>
										</div>
										<div>
											<!-- кнопка удаления товара --> 
											<div class="ch-product-cart-item-btn ch-product-cart-item-btn-right"
												v-on:click="item_product = ProductItemDelete(item_product); data.product.splice(index, 1);">
												<i class="icon-trash"></i>
											</div>	
										</div>	
									</div>		
									<!-- Товар актуален и удален из корзины -->
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"
										v-if="item_product.is_actual === true && item_product.cart.is_cart === false">
										<div>
											<!-- уведомление о возврате товара в корзину -->
											<div class="ch-product-cart-item-info"> 
												Вернуть товар в корзину
											</div>
										</div>
										<div>
											<!-- кнопка возврата товара в корзину --> 
											<div class="ch-product-cart-item-btn ch-product-cart-item-btn-right"
												v-on:click="item_product = ProductItemReturn(item_product);">
												<i class="icon-remove animate-spin"></i>
											</div>	
										</div>	
									</div>		
								</div>	
								<!-- итоговая сумма товара -->
								<div class="row ch-section-body ch-product-cart-grand-total">
									<div class="col-12">
										ИТОГО: {{SetPriceFormat(calculate.product)}} <i class="icon-rouble"></i>
									</div>
								</div>

								<!-- Выбор способа доставки товаров -->
								<div class="row ch-section-head">
									<div class="col-12">Способ получения товара</div>
								</div>	
								<div class="row"> 
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ch-product-cart-delivery-item"
										v-for="item_delivery in data.delivery"
										v-on:click="DeliverySelect(item_delivery);"
										v-bind:class="[zakaz.delivery_id == item_delivery.id ? 'ch-product-cart-delivery-item-selected' : '']">
										<div>				
											<div>
												<i v-bind:class="[zakaz.delivery_id == item_delivery.id ? 'icon-dot-circled' : 'icon-circle-empty']"></i>
											</div>
											<div>
												<div>
													{{item_delivery.name}}
												</div>
												<div>
													Стоимость доставки: 
													<span v-if="item_delivery.prices.length == 1">
														<span v-html="SetAlternatePrice(item_delivery.prices[0].price)"></span>
													</span>
												</div>
												<div v-if="item_delivery.prices.length > 1">
													<ul>
														<li v-for="item_delivery_price in item_delivery.prices">
															<span v-if="item_delivery_price.this_min_summ == 0">
																менее {{SetPriceFormat(item_delivery_price.next_min_summ)}} <i class="icon-rouble"></i> - 
																<span v-html="SetAlternatePrice(item_delivery_price.price)"></span>
															</span>	
															<span v-else-if="item_delivery_price.this_min_summ != item_delivery_price.next_min_summ">
																от {{SetPriceFormat(item_delivery_price.this_min_summ)}} до {{SetPriceFormat(item_delivery_price.next_min_summ)}} <i class="icon-rouble"></i> - 
																<span v-html="SetAlternatePrice(item_delivery_price.price)"></span>
															</span>	
															<span v-else>
																свыше {{SetPriceFormat(item_delivery_price.this_min_summ)}} <i class="icon-rouble"></i> - 
																<span v-html="SetAlternatePrice(item_delivery_price.price)"></span>
															</span>	
														</li>	
													</ul>
												</div>
												<div>
													{{SetPriceFormat(item_delivery.price)}} <i class="icon-rouble"></i> 
												</div>
											</div>
											<!-- 
											<div>
												{{SetPriceFormat(item_delivery.price)}} <i class="icon-rouble"></i> 
											</div>
											-->
										</div>
									</div>
								</div>		

								<!-- Заголовок -->
								<div class="row ch-section-head"
									v-if="zakaz.is_address">
									<div class="col-12">Адрес получения товара</div>
								</div>	
								<div class="row ch-section-body ch-product-cart-address"
									v-if="zakaz.is_address">
									<div class="col-12">
										<div>
											<input type="text" 
												placeholder="Укажите адрес доставки"
												v-model="data.personal.address"
												v-on:change="Personal('address', data.personal.address);">
											<span>Город, улица, дом *</span>
										</div>	
									</div>			
								</div>

								<!-- Заголовок -->
								<div class="row ch-section-head">
									<div class="col-12">Контактные данные</div>
								</div>	
								<div class="row ch-section-body ch-product-cart-personal">
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
										<div>
											<input type="text" 
												placeholder="Укажите Ваше имя"
												v-model="data.personal.name"
												v-on:change="Personal('name', data.personal.name);">
											<span>Ваше имя</span>
										</div>	
									</div>			
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
										<div>
											<input type="text" 
												placeholder="+7(999) 999-99-99"
												v-mask="'+7(###) ###-##-##'"
												v-model="data.personal.phone"
												v-on:change="Personal('phone', data.personal.phone);">
											<span>Номер телефона *</span>
										</div>	
									</div>			
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div>
											<textarea v-model="zakaz.comment"></textarea> 
											<span>Комментарий к заказу</span>
										</div>	
									</div>			
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
										<div v-on:click="zakaz.is_callback = !zakaz.is_callback;">
											<i v-bind:class="[zakaz.is_callback ? 'icon-check' : 'icon-check-empty']"></i>
											<span>Перезвоните мне</span>
										</div>	
									</div>			
								</div>


								<!-- Заголовок -->
								<div class="row ch-section-head">
									<div class="col-12">Оформление заказа</div>
								</div>
								<div class="row ch-section-body ch-product-cart-decoration">
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
										<div>
											<div>Ваш заказ:</div>
											<div>
												<div>
													<span>Сумма заказа</span>
													<span></span>
													<span v-html="SetAlternatePrice(calculate.product)"></span>
												</div>
												<div>		
													<span>Сумма доставки</span>
													<span></span>
													<span v-html="SetAlternatePrice(calculate.delivery)"></span>
												</div>
												<div>		
													<span>Итого к оплате</span>
													<span></span>
													<span v-html="SetAlternatePrice(calculate.total)"></span>
												</div>	
											</div>
											<div>
												<div
													v-bind:class="{disable: !zakaz.is_active}"
													v-on:click="if(zakaz.is_active) {CreateZakaz()}">
													<i class="icon-wallet"></i>
													<span>Оформить заказ</span>
												</div>	
											</div>
											<div v-show="!zakaz.is_active"
												v-html="FieldValidate()"></div>
											<div> 
												Нажимая кнопку «Оформить заказ», я даю свое согласие на обработку персональных данных<!--, в соответствии с условиями -->
											</div>
										</div>	
									</div>
								</div>	
							</div>
							<!-- END КОРЗИНА С ВЫБРАННЫМИ ПРОДУКТАМИ -->
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

				</div>
			</div>	
		</div>
	</div>				
</template>

<style>

	/* Локальная кастомизация контролов */
		/* Ползунок прокрутки */
		#ch-cart .vb-dragger {
			z-index: 110;
		}	

		/* шрифты font-chocosun */
		#ch-cart [class^="icon-"]:before, 
		#ch-cart [class*=" icon-"]:before {
			margin-right: 0px;
			margin-left: 0px;
		}

		/* Стили для плейсхолдера полей ввода */
		#ch-cart input::-webkit-input-placeholder		{color: rgba(var(--ch-color-01-rgb), 0.3)} 
		#ch-cart input::-moz-placeholder				{color: rgba(var(--ch-color-01-rgb), 0.3)}
		#ch-cart input:-moz-placeholder					{color: rgba(var(--ch-color-01-rgb), 0.3)}
		#ch-cart input:-ms-input-placeholder			{color: rgba(var(--ch-color-01-rgb), 0.3)}
			
		#ch-cart input:focus::-webkit-input-placeholder	{color: transparent}
		#ch-cart input:focus::-moz-placeholder			{color: transparent}
		#ch-cart input:focus:-moz-placeholder			{color: transparent}
		#ch-cart input:focus:-ms-input-placeholder		{color: transparent}

	/* Основные параметры */
	/* Параметры панели */
	#ch-cart {
		position: fixed;
		top: 50px;
		width: 100%;
		z-index: 100;
		color: rgb(var(--ch-color-01-rgb));
		height: calc(100vh - 50px);
	}
		#ch-cart .row {
			-ms-flex-pack: center!important;	
		}
		
		/* Основной контент */	
		#ch-cart .ch-content {
			min-height: calc(100vh - 80px);
			padding: 0px 0px 0px 0px;	
			border: none;
			border-radius: 0px; 
			overflow: hidden;
			padding-top: 5px;
			padding-bottom: 5px;
		}
			/* Заголовок раздела */
			#ch-cart .ch-section-head {
				margin-top: 5px;
				font-size: 1.2em;
				font-weight: bold;
			}
			/* Тело раздела */
			#ch-cart .ch-section-body {
				padding: 5px 10px 5px 10px;
				margin-left: -5px;
				margin-right: -5px;
			}

			/* Единичная позиция товара */
			#ch-cart .ch-product-cart-item	{
				border-top: 1px solid rgb(var(--ch-color-01-rgb));
			}
				/* выравнивание по вертикали */
				#ch-cart .ch-product-cart-item > div,
				#ch-cart .ch-product-cart-item > div > div
				{
					display: flex;
					align-items: center;			
					padding-left: 0px;
					padding-right: 0px;
				}
		
				/* изображение товара */
				#ch-cart .ch-product-cart-item-image {
					position: relative;
					float: left;
					width: 80px;
					height: 80px;
					margin-top: 5px;
					margin-bottom: 5px;
					display: inline-block;
				}
					#ch-cart .ch-product-cart-item-image > img {
						width: inherit;
						height: inherit;
						border: 5px solid rgb(var(--ch-color-04-rgb));
						border-radius: 5px 30px 5px 30px;
					}
			
				/* Наименование, вес и цена за единицу товара */	
				#ch-cart .ch-product-cart-item-name {
					float: left;
					width: auto;
					height: auto;
					margin-top: 5px;
					margin-bottom: 5px;
					margin-left: 10px;
					display: inline-block;
				}

				/* Блок управления ценой */	
				#ch-cart .ch-product-cart-item > div:last-child > div:first-child {
					width: 100%;
				} 

				/* Блок управления товаром */	
				#ch-cart .ch-product-cart-item > div:last-child > div:last-child {
					width: auto;
				} 
		
					/* Кнопки управления количеством товара */	
					#ch-cart .ch-product-cart-item-btn {
						height: 36px;
						font-size: 1em;
						font-weight: bold;
						line-height: 30px;
						text-align: center;
						padding-left: 8px;
						padding-right: 8px;
						background: transparent;
						border-radius: 5px 15px 5px 15px;
						border: 2px solid rgb(var(--ch-color-01-rgb));
						cursor: pointer;
					}

						/* размер иконок в кнопках */
						/* цвет иконок в кнопках */
						#ch-cart .ch-product-cart-item-btn i[class^="icon-"]:before, 
						#ch-cart .ch-product-cart-item-btn i[class*=" icon-"]:before {
							font-size: 16px!important;
							color: rgb(var(--ch-color-01-rgb))!important;
							vertical-align: middle!important;
						}	

						/* выравнивание кнопок */
						#ch-cart .ch-product-cart-item-btn-left {
							float: left;
							margin-right: 5px; 
						}
						#ch-cart .ch-product-cart-item-btn-right {
							float: right;
							margin-left: 5px; 
						}

						/* поле ввода количества */
						#ch-cart .ch-product-cart-item-input {
							height: 34px;
							width: 20px;
							font-weight: bold;
							text-align: center;
							background: transparent;
							border: none;
							color: rgba(var(--ch-color-01-rgb));
							outline: none;
							vertical-align: top;
						}

				/* Блок суммы товара */
				/* Блок информирования суммы о товаре */
				#ch-cart .ch-product-cart-item-total,
				#ch-cart .ch-product-cart-item-info {
					width: 100%;
					text-align: center;
					overflow: hidden;
				}

			/* Итоговая сумма корзины */
			#ch-cart .ch-product-cart-grand-total {
				border-top: 3px double rgba(var(--ch-color-01-rgb));
			}
				#ch-cart .ch-product-cart-grand-total > div {
					padding-left: 0px;
					padding-right: 0px;
					text-align: end;
					font-size: 1.2em;
				}

			/* Тип доставки */
			/* Карточка единичного типа услуги */
			#ch-cart .ch-product-cart-delivery-item {
				display: grid;
				padding: 5px 10px 5px 10px;
				font-size: 1.1em;
				line-height: 1.5;
				font-weight: normal;
				cursor: pointer;
			}
				#ch-cart .ch-product-cart-delivery-item > div {
					display: flex;
					justify-content: flex-start;
					padding: 5px;
					border: 2px solid rgba(var(--ch-color-04-rgb));
					border-radius: 5px;
					box-shadow: 4px 4px 2px rgba(var(--ch-color-00-rgb), 0.2);
				}
					#ch-cart .ch-product-cart-delivery-item > div > div:first-child {
						flex-grow: 0; 
						flex-shrink: 0;
					}
					#ch-cart .ch-product-cart-delivery-item > div > div:last-child {
						display: flex;
						flex-direction: column;
						flex-grow: 1; 
						flex-shrink: 1;
					}
			
					#ch-cart .ch-product-cart-delivery-item > div > div:last-child > div:not(:first-child):not(:last-child) {
						font-size: .7em;
						opacity: .5;
					}
					#ch-cart .ch-product-cart-delivery-item > div > div:last-child > div:last-child {
						margin-top: auto;
						border-top: 1px solid rgba(var(--ch-color-00-rgb), 0.2);
						text-align: end;
					}
		
					#ch-cart .ch-product-cart-delivery-item i.icon-circle-empty,
					#ch-cart .ch-product-cart-delivery-item i.icon-dot-circled {
						font-size: 1.4em;
						font-weight: normal;
						color: rgb(var(--ch-color-01-rgb));
						margin-left: 5px;
						margin-right: 5px;
					}

				#ch-cart .ch-product-cart-delivery-item-selected > div {
					background-color: rgb(var(--ch-color-04-rgb));
				}

			/* Адрес доставки */
			#ch-cart .ch-product-cart-address > div {
				padding: 5px;
			}
				#ch-cart .ch-product-cart-address > div > div {
					width: 100%;
					padding-left: 8px;
					padding-right: 8px;
					background: transparent;
					border-radius: 5px 15px 5px 15px;
					border: 2px solid rgba(var(--ch-color-01-rgb), 0.3);
					cursor: pointer;
				}
					#ch-cart .ch-product-cart-address input {
						height: 32px;
						width: 100%;
						margin-top: 6px;
						margin-bottom: 2px;
						font-weight: normal;
						text-align: left;
						background: transparent;	
						border: none;
						color: rgb(var(--ch-color-01-rgb));
						outline: none;
						vertical-align: top;
					}

					#ch-cart .ch-product-cart-address span {
						position: absolute;
						left: 18px;
						top: 0px;		
						padding-left: 6px;
						padding-right: 6px;
						color: rgba(var(--ch-color-01-rgb), 0.3);
						background: rgb(var(--ch-color-03-rgb));
						line-height: 1;
					}	

			/* Персональные данные покупателя */
			#ch-cart .ch-product-cart-personal > div {
				padding: 5px;
			}
				/* Поле имени и номера телефона */
				#ch-cart .ch-product-cart-personal > div:not(:last-child) > div {
					width: 100%;
					padding-left: 8px;
					padding-right: 8px;
					background: transparent;
					border-radius: 5px 15px 5px 15px;
					border: 2px solid;
					border-color: rgba(var(--ch-color-01-rgb), 0.3);
				}
					#ch-cart .ch-product-cart-personal input {
						height: 32px;
						width: 100%;
						margin-top: 6px;
						margin-bottom: 2px;
						font-weight: normal;
						text-align: left;
						background: transparent;
						border: none;
						color: rgb(var(--ch-color-01-rgb));
						outline: none;
						vertical-align: top;
					}
					#ch-cart .ch-product-cart-personal > div:not(:last-child) span {
						position: absolute;
						left: 18px;
						top: 0px;		
						padding-left: 6px;
						padding-right: 6px;
						color: rgba(var(--ch-color-01-rgb), 0.3);
						background: rgb(var(--ch-color-03-rgb));
						line-height: 1;
					}	

					/* Комментарий к заказу */
					#ch-cart .ch-product-cart-personal textarea {
						height: 108px;
						width: 100%;
						margin-top: 6px;
						margin-bottom: 2px;
						font-weight: normal;
						text-align: left;
						background: transparent;
						border: none;
						resize: none;
						color: rgb(var(--ch-color-01-rgb));
						outline: none;
						vertical-align: top;
					}

					/* Признак необходимости перезвонить */
					#ch-cart .ch-product-cart-personal i[class^="icon-"]:before, 
					#ch-cart .ch-product-cart-personal i[class*=" icon-"]:before {
						font-size: 1.4em;
						font-weight: normal;
						color: rgb(var(--ch-color-01-rgb));
						margin-left: 5px;
						margin-right: 5px;
					}	

			/* Оформление заказа */
			#ch-cart .ch-product-cart-decoration {
				-ms-flex-pack: center!important;
				justify-content: center!important;
				line-height: 1.5;
				font-size: 1.1em;
				font-weight: normal;
				background: rgb(var(--ch-color-04-rgb));
				border: none;
				border-radius: 5px;
				box-shadow: 4px 4px 2px rgba(var(--ch-color-00-rgb), 0.2);
			}	
				#ch-cart .ch-product-cart-decoration > div {
				}
				/* Заголовок чека */
				#ch-cart .ch-product-cart-decoration > div > div > div:nth-child(1) {
					padding: 15px 0px 15px 0px;
					text-transform: uppercase;
					border-bottom: 1px solid rgba(var(--ch-color-00-rgb), 0.3);
				}	
				/* Тело чека */
				#ch-cart .ch-product-cart-decoration > div > div > div:nth-child(2) {
					padding: 10px 0px 10px 0px;
				}	
					#ch-cart .ch-product-cart-decoration > div > div > div:nth-child(2) > div {
						display: flex;
					}	
						#ch-cart .ch-product-cart-decoration > div > div > div:nth-child(2) > div > span:nth-child(1) {
							text-align: left;
							white-space: nowrap;
						}	
						#ch-cart .ch-product-cart-decoration > div > div > div:nth-child(2) > div > span:nth-child(2) {
							text-align: left;
							width: 100%;
							white-space: nowrap;
							margin: 0px 5px 5px 5px;
							border-bottom: 1px dotted rgba(var(--ch-color-01-rgb),0.3);						
						}	
						#ch-cart .ch-product-cart-decoration > div > div > div:nth-child(2) > div > span:nth-child(3) {
							text-align: right;
							white-space: nowrap;
						}	
						#ch-cart .ch-product-cart-decoration > div > div > div:nth-child(2) > div:nth-child(3) > span {
							padding-top: 10px;
							font-weight: bolder;
						}	

				/* Кнопка оформления заказа */
				#ch-cart .ch-product-cart-decoration > div > div > div:nth-child(3) {
					display: flex;
					justify-content: center;
					padding: 10px 0px 10px 0px;
				}	
					#ch-cart .ch-product-cart-decoration > div > div > div:nth-child(3) > div {
						height: 36px;
						font-size: 1em;
						font-weight: bold;
						line-height: 30px;
						text-align: center;
						padding-left: 8px;
						padding-right: 8px;
						background: transparent;
						border-radius: 5px 15px 5px 15px;
						border: 2px solid rgb(var(--ch-color-01-rgb));
						cursor: pointer;
						box-shadow: 4px 4px 2px rgba(var(--ch-color-00-rgb), 0.2);
					}
					#ch-cart .ch-product-cart-decoration > div > div > div:nth-child(3) > div:hover {
						background: rgba(var(--ch-color-02-rgb), 0.5);
						box-shadow: none;
						transition: 0.2s linear;
					}

					#ch-cart .ch-product-cart-decoration > div > div > div:nth-child(3) > div.disable {
						cursor: not-allowed;
						border: 2px solid rgba(var(--ch-color-01-rgb), 0.3);
						box-shadow: none;
						color: rgba(var(--ch-color-01-rgb), 0.3);				
					}

				/* Примечание */
				#ch-cart .ch-product-cart-decoration > div > div > div:nth-child(4) {
					font-size: 0.7em;
					color: rgba(var(--ch-color-00-grb), 0.5);
					padding: 0px 0px 10px 0px;
				}	
					#ch-cart .ch-product-cart-decoration > div > div > div:nth-child(4) > ul {
						font-weight: bolder;
						margin: 0px;
					}	

				/* Согласие */
				#ch-cart .ch-product-cart-decoration > div > div > div:nth-child(5) {
					font-size: 0.7em;
					color: rgba(var(--ch-color-00-grb), 0.5);
					padding: 0px 0px 10px 0px;
				}	

			/* Итоговый чек - Заказ оформлен */
			#ch-cart .ch-product-cart-zakaz {
				-ms-flex-pack: center!important;
				justify-content: center!important;
				line-height: 1.5;
				font-size: 1.1em;
				font-weight: normal;
				background: rgb(var(--ch-color-04-rgb));
				border: none;
				border-radius: 5px;
				box-shadow: 4px 4px 2px rgba(var(--ch-color-00-rgb), 0.2);
			}	
				#ch-cart .ch-product-cart-zakaz > div {
				}
				/* Заголовок чека */
				#ch-cart .ch-product-cart-zakaz > div > div > div:nth-child(1) {
					padding: 15px 0px 15px 0px;
					border-bottom: 1px solid rgba(var(--ch-color-00-rgb), 0.3);
				}	
					#ch-cart .ch-product-cart-zakaz > div > div > div:nth-child(1) > div > span:first-child {
					}	
					#ch-cart .ch-product-cart-zakaz > div > div > div:nth-child(1) > div > span:last-child {
						font-weight: bolder;
						color: rgba(var(--ch-color-01-rgb), 0.5);
					}	
				/* Тело чека */
				#ch-cart .ch-product-cart-zakaz > div > div > div:nth-child(2) {
					padding: 10px 0px 10px 0px;
				}	
					#ch-cart .ch-product-cart-zakaz > div > div > div:nth-child(2) > div {
						display: flex;
					}	
						#ch-cart .ch-product-cart-zakaz > div > div > div:nth-child(2) > div > span:nth-child(1) {
							text-align: left;
							white-space: nowrap;
						}	
						#ch-cart .ch-product-cart-zakaz > div > div > div:nth-child(2) > div > span:nth-child(2) {
							text-align: left;
							width: 100%;
							white-space: nowrap;
							margin: 0px 5px 5px 5px;
							border-bottom: 1px dotted rgba(var(--ch-color-01-rgb),0.3);						
						}	
						#ch-cart .ch-product-cart-zakaz > div > div > div:nth-child(2) > div > span:nth-child(3) {
							text-align: right;
							white-space: nowrap;
						}	
						#ch-cart .ch-product-cart-zakaz > div > div > div:nth-child(2) > div:nth-child(3) > span {
							padding-top: 10px;
							font-weight: bolder;
						}	

			/* Кнопка перехода к покупкам */
			#ch-cart .ch-product-cart-zakaz > div > div > div:nth-child(3) {
				display: flex;
				justify-content: center;
			    padding: 10px 0px 10px 0px;
			}	
				#ch-cart .ch-product-cart-zakaz > div > div > div:nth-child(3) > div {
					height: 36px;
					font-size: 1em;
					font-weight: bold;
					line-height: 30px;
					text-align: center;
					padding-left: 8px;
					padding-right: 8px;
					background: transparent;
					border-radius: 5px 15px 5px 15px;
					border: 2px solid rgb(var(--ch-color-01-rgb));
					cursor: pointer;
					box-shadow: 4px 4px 2px rgba(var(--ch-color-00-rgb), 0.2);
				}
				#ch-cart .ch-product-cart-zakaz > div > div > div:nth-child(3) > div:hover {
					background: rgba(var(--ch-color-02-rgb), 0.5);
					box-shadow: none;
					transition: 0.2s linear;
				}
			/* Примечание */
			#ch-cart .ch-product-cart-zakaz > div > div > div:last-child {
				font-size: 0.7em;
				color: rgba(var(--ch-color-00-grb), 0.5);
		        padding: 10px 0px 10px 0px;
				text-align: center;
			}	
		
	/* Подвал */
	#ch-cart .ch-footer {
		-ms-flex-pack: centerimportant;
		justify-content: center!important;
	    line-height: 1.5;
		font-size: 1.1em;
		font-weight: normal;
		height: 34px;
		margin-top: 10px;
	}	
		#ch-cart .ch-footer > div {
			border-top: 1px solid rgb(var(--ch-color-01-rgb));
		    text-align: end;
			color: rgb(var(--ch-color-01-rgb));
		}

</style>

<script>
	Vue.config.devtools = true;
	Vue.config.performance = true;

	module.exports = {
		props:['type', 'cartcount'],
		data: function() {
			return {
				data: {},
				calculate: {
					product: 0,
					delivery: 0,
					total: 0
				},
				zakaz: {
					delivery_id: null,
					is_address:  false,
					comment: null,
					is_callback: false,
					is_active: false,
					is_ok: false,
					id: 0
				}
			}	
		},
		methods: {
			/* Форматирование суммы продукции */
			SetPriceFormat: function(price) {
				return (numeral(price).format('0,0.00'));
			},
		
			/* Альтернативное отображение суммы продукции */
			SetAlternatePrice: function(price) {
				if(parseFloat(price) == 0) {
					return ('бесплатно');
				} else {
					return ('' + this.SetPriceFormat(parseFloat(price)) + '<i class="icon-rouble"></i>');
				}	
			},													

			/* Проверка заполнения всех необходимых полей формы */
			FieldValidate: function() {
				tmp_zakaz_is_active = true;
				tmp_result = '* Для оформления заказа, необходимо заполнить поля отмеченные звездочкой :';
				tmp_field = '';

				/* Тип доставки */
				if(this.zakaz.delivery_id === null) {
					tmp_zakaz_is_active = false;
					tmp_field = tmp_field + '<li>способ получение товара</li>';
				}

				/* Поле адреса доставки, если доставка предусмотрена */
				if((this.zakaz.is_address) && (this.data.personal.address == '')) {
					tmp_zakaz_is_active = false;
					tmp_field = tmp_field + '<li>адрес получения товара</li>';
				}

				/* Номер телефона */
				if((this.data.personal.phone == '')  || (this.data.personal.phone.replace(/(-|\(|\)|\s|^\+7)/g, '').length != 10)) {
					tmp_zakaz_is_active = false;
					tmp_field = tmp_field + '<li>номер телефона</li>';
				}

				this.zakaz.is_active = tmp_zakaz_is_active;
				if(!tmp_zakaz_is_active) {
					tmp_result = tmp_result + '<ul>' + tmp_field + '</ul>';
				} else {
					tmp_result = '';
				}

				return tmp_result;
			},
			
			/* Изменение количества продукции (от 1 до 99 единиц) */
			/* И сохраняем изменения в корзине */
			ProductItemChangeCount(item, count) {
				/* изменим количество */
				count = (count < 1) ?  1: count;
				count = (count > 99) ? 99: count;

				/* сохраним в куках новое значение */
				axios
					.post('/core/set_data.php', {
						type: 'cart',
						id: item.id,
						count: parseInt(count)
					})
					.then(response => {
						if ((response.status == 200) && (response.data.state == 'ok')) {
							/* подтверждаем актуальность товара */
							item.cart.is_cart = true;
						
							/* фиксируем новое количество товара  */
							item.cart.count = parseInt(count);
							
							/* пересчитаем сумму текущего продукта */
							item.cart.price = item.price * item.cart.count;

							/* пересчитаем итоговую сумму корзины */
							this.CalculateProduct();

							/* пересчитаем стоимость все видов доставки */
							this.CalculateDelivery(); 

							/* пересчитаем итоговую сумму */
							this.CalculateTotal();

						} else {
							console.log(response);
						}
					})
					.catch(error => {
						console.log(error);
					});

				return item;
			},
			/* Удаление товара из козины (запись хранится до перезагруки страницы, для возможности восстановления) */
			ProductItemDelete(item) {
				/* сохраним в куках новое значение */
				axios
					.post('/core/set_data.php', {
						type: 'cart',
						id: item.id,
						count: 0
					})
					.then(response => {
						if ((response.status == 200) && (response.data.state == 'ok')) {
							/* изменяем банж с количеством товара в корзине */
							this.cartcount = parseInt(this.cartcount) - 1;
							this.$emit('cartcount', this.cartcount);

							/* устанавливаем признак актуальности покупки */
							item.cart.is_cart = false;

							/* пересчитаем итоговую сумму корзины */
							this.CalculateProduct();

							/* пересчитаем стоимость все видов доставки */
							this.CalculateDelivery(); 

							/* пересчитаем итоговую сумму */
							this.CalculateTotal();

						} else {
							console.log(response);
						}
					})
					.catch(error => {
						console.log(error);
					});

				return item;
			},
			
			/* Возврат удаленного товара в корзину */
			ProductItemReturn(item) {
				/* вновь фиксируем количество товара в корзине */
				item = this.ProductItemChangeCount(item, item.cart.count)

				/* изменяем банж с количеством товара в корзине */
				this.cartcount = parseInt(this.cartcount) + 1;
				this.$emit('cartcount', this.cartcount);
				
				return item;
			},
			
			/* Выбор способа доставки товаров */
			DeliverySelect(item) {
				/* зафиксируем выбранный вариант доставки */
				this.zakaz.delivery_id = item.id;
				
				/* Сформируем признак, отражающий поле ввода адреса доставки */
				this.zakaz.is_address = item.is_address;
				
				/* пересчитаем итоговую сумму */
				this.CalculateTotal();
			},
			
			/* Ввод персональных данных заказчика */
			Personal(key, value) {
				tmp_option = {};
				tmp_option['type'] = 'personal';
				tmp_option[key] = value;

				/* сохраним в куках новое значение */
				axios
					.post('/core/set_data.php', tmp_option)
					.then(response => {
						if (response.status != 200) {
							console.log(response);
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			
			/* Подсчет итоговой суммы корзины */
			CalculateProduct() {
				var tmp_sub_total = 0;
				for(var product_index in this.data.product) {
					var tmp_product_item = this.data.product[product_index]; 
					var tmp_is_flag = true;

					if (!tmp_product_item.is_actual) { tmp_is_flag = false; }
					if (!tmp_product_item.cart.is_cart) { tmp_is_flag = false; }

					if (tmp_is_flag) {
						tmp_sub_total = tmp_sub_total + tmp_product_item.cart.price;
					}	
				}
				this.calculate.product = tmp_sub_total; 
			},
			
			/* Подсчет суммы доставки, с учетом суммы корзины */
			CalculateDelivery() {
				for(var delivery_index in this.data.delivery) {
					var delivery_item = this.data.delivery[delivery_index];
					for(var prices_index in delivery_item.prices) {
						var prices_item = delivery_item.prices[prices_index]
						if((prices_item.this_min_summ == prices_item.next_min_summ) && (prices_item.this_min_summ == 0)) {
							if(this.calculate.product > prices_item.this_min_summ) {
								this.data.delivery[delivery_index].price = parseFloat(prices_item.price);
							}
						} else if((prices_item.this_min_summ == prices_item.next_min_summ) && (prices_item.this_min_summ > 0)) {
							if(this.calculate.product > prices_item.this_min_summ) {
								this.data.delivery[delivery_index].price = parseFloat(prices_item.price);
							}
						} else {
							if((this.calculate.product > prices_item.this_min_summ) && (this.calculate.product < prices_item.next_min_summ)) {
								this.data.delivery[delivery_index].price = prices_item.price;
							}
						}
					}
				}
			},
			
			/* Подсчет итоговой суммы */
			CalculateTotal() {
				if(this.zakaz.delivery_id !== null) {
					tmp_delivery_array = this.data.delivery.filter(item => item.id == this.zakaz.delivery_id);
					this.calculate.delivery = parseFloat(tmp_delivery_array[0].price);
				}
				this.calculate.total = parseFloat(this.calculate.delivery) + parseFloat(this.calculate.product);
			},

			/* Оформление заказа */
			CreateZakaz() {
				/* Сохраняем заказ в БД */
				axios
					.post('/core/set_data.php', {
						type: 'zakaz',
						cart: this.data,
						calculate: this.calculate,
						zakaz: this.zakaz
					})
					.then(response => {
						if ((response.status == 200) && (response.data.state == 'ok')) {
							/* получаем номер заказа */
							this.zakaz.id = response.data.data.zakaz_id;
							
							/* изменяем банж с количеством товара в корзине */
							this.cartcount = 0;
							this.$emit('cartcount', this.cartcount);
							
							/* ставим признак что заказ зарегистрирован */
							this.zakaz.is_ok = true;
							
							/* Отправляем сообщение оператору */
							if(this.zakaz.is_ok) {
								axios	
									.post('/core/send_mail.php', {
										mail_to: 'zakaz@chocosun.ru',
										mail_subject: 'Заказ № ' + this.zakaz.id,
										mail_message: response.data.data.mail
									})
									.catch(error => {
										console.log(error);
									});
							}
						}
					})
					.catch(error => {
						console.log(error);
					});

			}
			

			
		},		
		mounted: function(){
			fetch('/core/get_data.php?type=' + this.type)
				.then(response => response.json())
				.then(json => this.data = json.data)
			;
		},
		updated: function(){
			/* Расчет суммы корзины */
			this.CalculateProduct();
			this.CalculateDelivery(); 
			this.CalculateTotal();
		}
	

	}
</script>

