<template>
	<div id="ch-delivery">
			<div>
				<div class="container">
					<div class="row"> 
						<div class="ch-content col">
									<v-jstree 
										id="v-jstree"
										:data="data.data"
										allow-batch 
										draggable
										@item-click="itemClick"	
									></v-jstree>
						
						</div>
					</div>	
					<div class="row"> 
						<div class="ch-footer col">
							Подвал
						</div>
					</div>	
				</div>
			</div>



	</div>				

</template>

<style>

	/* Параметры панели */
	#ch-delivery {
		position: fixed;
		top: 50px;
		width: 100%;
		z-index: 100;
		color: rgb(var(--ch-color-01-rgb));
		height: calc(100vh - 50px);
	}
		#ch-delivery .row {
			-ms-flex-pack: center!important;	
		}
	
	
	/* Основной контент */	
	#ch-delivery .ch-content {
		min-height: calc(100vh - 100px);
	}
	
	/* Подвал */
	#ch-delivery .ch-footer {
	
	}

		
</style>

<script>

	Vue.config.devtools = true;
	Vue.config.performance = true;

	module.exports = {
		props:['type'],
		components: {
		},
		data: function() {
			return {
				data: {
					vuebar: {
						suppressScrollY: false,
						suppressScrollX: true,
						wheelPropagation: false,
						minScrollbarLength: 20,
						useBothWheelAxes: true
					},
					data: [
						{
							"text": "Same but with checkboxes",
							"icon": "icon-instagram",
							"opened": true,
							"children": [
								{
									"text": "initially selected",
									"selected": true
								},
								{
									"text": "custom icon",
									"icon": "icon-instagram"
								},
								{
									"text": "initially open",
									"icon": "fa fa-folder icon-state-default",
									"opened": true,
									"children": [
										{
											"text": "Another node"
										}
									]
								},
								{
									"text": "custom icon",
									"icon": "fa fa-warning icon-state-warning"
								},
								{
									"text": "disabled node",
									"icon": "fa fa-check icon-state-success",
									"disabled": true
								}
							]
						},
						{
							"text": "Same but with checkboxes",
							"icon": "icon-instagram",
							"opened": true,
							"children": [
								{
									"text": "initially selected",
									"selected": true
								},
								{
									"text": "custom icon",
									"icon": "icon-instagram"
								},
								{
									"text": "initially open",
									"icon": "fa fa-folder icon-state-default",
									"opened": true,
									"children": [
										{
											"text": "Another node"
										}
									]
								},
								{
									"text": "custom icon",
									"icon": "fa fa-warning icon-state-warning"
								},
								{
									"text": "disabled node",
									"icon": "fa fa-check icon-state-success",
									"disabled": true
								}
							]
						},
						{
							"text": "Same but with checkboxes",
							"icon": "icon-instagram",
							"opened": true,
							"children": [
								{
									"text": "initially selected",
									"selected": true
								},
								{
									"text": "custom icon",
									"icon": "icon-instagram"
								},
								{
									"text": "initially open",
									"icon": "fa fa-folder icon-state-default",
									"opened": true,
									"children": [
										{
											"text": "Another node"
										}
									]
								},
								{
									"text": "custom icon",
									"icon": "fa fa-warning icon-state-warning"
								},
								{
									"text": "disabled node",
									"icon": "fa fa-check icon-state-success",
									"disabled": true
								}
							]
						}
					
					]				
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
			itemClick (node) {
				console.log(node.model.text + ' clicked !')
			}
		},
		mounted: function(){
			/*
			fetch('/core/get_data.php?type=' + this.type)
				.then(response => response.json())
				.then(json => this.data = json.data)
			;
			*/
		},
		updated: function(){
		}
	

	}
</script>

