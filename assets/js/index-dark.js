$(function() {
	
 //flotChart1
	$.plot('#summary-chart', [{
		data: dashData10,
		color: '#ff2790',
		lines: {
			lineWidth: 1.8
		}
	}, {
		data: dashData11,
		color: '#4f94fb',
		lines: {
			lineWidth: 1.8
		}
	}], {
		series: {
			stack: 0,
			shadowSize: 0,
			lines: {
				show: true,
				lineWidth: 1.8,
				fill: true,
				fillColor: {
					colors: [{
						opacity: 0
					}, {
						opacity: 0.4
					}]
				}
			}
		},
		grid: {
			borderWidth: 0,
			labelMargin: 5,
			hoverable: true
		},
		yaxis: {
			show: true,
			color: 'rgba(72, 94, 144, .1)',
			min: 0,
			max: 130,
			font: {
				size: 10,
				color: '#8392a5'
			}
		},
		xaxis: {
			show: true,
			color: 'rgba(72, 94, 144, .1)',
			ticks: [
				[0, '10'],
				[10, '20'],
				[20, '30'],
				[30, '40'],
				[40, '50'],
				[50, '60'],
				[60, '70'],
				[70, '80'],
				[80, '90'],
				[90, '100'],
				[100, '110'],
				[120, '120'],
			],
			font: {
				size: 10,
				color: '#8392a5'
			},
			reserveSpace: false
		}
	});
  
 $.plot('#flotPie', [{
 		label: 'Very Satisfied',
 		data: [
 			[1, 25]
 		],
 		color: '#6f42c1'
 	}, {
 		label: 'Satisfied',
 		data: [
 			[1, 38]
 		],
 		color: '#4f94fb '
 	}, {
 		label: 'Not Satisfied',
 		data: [
 			[1, 20]
 		],
 		color: '#00cccc'
 	}, {
 		label: 'Very Unsatisfied',
 		data: [
 			[1, 15]
 		],
 		color: '#ff2790'
 	}], {
 		series: {
 			pie: {
 				show: true,
 				radius: 1,
 				innerRadius: 0.5,
 				label: {
 					show: true,
 					radius: 3 / 4,
 					formatter: labelFormatter
 				}
 			}
 		},
 		legend: {
 			show: false
 		}
 	});
	
	function labelFormatter(label, series) {
 		return '<div style="font-size:11px; font-weight:500; text-align:center; padding:2px; color:white;">' + Math.round(series.percent) + '%<\/div>';
 	}
	
	
	$('#unique-visitors').sparkline('html', {
		lineColor: '#4f94fb',
		lineWidth: 1.5,
		spotColor: false,
		minSpotColor: false,
		maxSpotColor: false,
		highlightSpotColor: null,
		highlightLineColor: null,
		fillColor: 'rgba(40, 92, 247, 0.2) ',
		chartRangeMin: 0,
		chartRangeMax: 10,
		width: '50%',
		height: 20,
		disableTooltips: true
	});
	
	$('#page-views').sparkline('html', {
		lineColor: '#ffc107',
		lineWidth: 1.5,
		spotColor: false,
		minSpotColor: false,
		maxSpotColor: false,
		highlightSpotColor: null,
		highlightLineColor: null,
		fillColor: 'rgba(255, 193, 7, 0.2) ',
		chartRangeMin: 0,
		chartRangeMax: 10,
		width: '50%',
		height: 20,
		disableTooltips: true
	});
	
	$('#visit').sparkline('html', {
		lineColor: '#00cccc',
		lineWidth: 1.5,
		spotColor: false,
		minSpotColor: false,
		maxSpotColor: false,
		highlightSpotColor: null,
		highlightLineColor: null,
		fillColor: 'rgba(0, 204, 204, 0.2) ',
		chartRangeMin: 0,
		chartRangeMax: 10,
		width: '50%',
		height: 20,
		disableTooltips: true
	});
	
	$('#bounce-rate').sparkline('html', {
		lineColor: '#ff2790',
		lineWidth: 1.5,
		spotColor: false,
		minSpotColor: false,
		maxSpotColor: false,
		highlightSpotColor: null,
		highlightLineColor: null,
		fillColor: 'rgba(241, 0, 117, 0.2) ',
		chartRangeMin: 0,
		chartRangeMax: 10,
		width: '50%',
		height: 20,
		disableTooltips: true
	});
	
	$('#total-visit').sparkline('html', {
		lineColor: '#dc3545',
		lineWidth: 1.5,
		spotColor: false,
		minSpotColor: false,
		maxSpotColor: false,
		highlightSpotColor: null,
		highlightLineColor: null,
		fillColor: 'rgba(220, 53, 69, 0.2) ',
		chartRangeMin: 0,
		chartRangeMax: 10,
		width: '50%',
		height: 20,
		disableTooltips: true
	});
	
	$('#session').sparkline('html', {
		lineColor: '#8e5aea',
		lineWidth: 1.5,
		spotColor: false,
		minSpotColor: false,
		maxSpotColor: false,
		highlightSpotColor: null,
		highlightLineColor: null,
		fillColor: 'rgba(142, 90, 234, 0.2) ',
		chartRangeMin: 0,
		chartRangeMax: 10,
		width: '50%',
		height: 20,
		disableTooltips: true
	});
	
	var ctx4 = document.getElementById('ecom-chart').getContext('2d');
	new Chart(ctx4, {
		type: 'bar',
		data: {
			labels: ['Sales Report' ,' Revenue' ,'Total profit'],
			datasets: [{
				label: ['Monthly'],
				data: [60, 45, 35,20],
				backgroundColor: ['#4f94fb ', '#ff2790', '#ffc107']
			}]
		},
		 options: {
			responsive: true,
			maintainAspectRatio: false,
			legend: {
			   display:false,
			},
			scales: {
				xAxes: [{
					display: true,
					barPercentage:0.5,
					gridLines: {
						display: false,
						color: 'rgba(142, 156, 173,0.1)',
						drawBorder: false
					},
					ticks: {
                            fontColor: '#8e9cad',
                            autoSkip: true,
                            maxTicksLimit: 9,
                            maxRotation: 0,
                            labelOffset: 10
                        },
					scaleLabel: {
						display: false,
						labelString: 'Month',
						fontColor: 'transparent'
					}
				}],
				yAxes: [{
					ticks: {
						fontColor: "#8e9cad",
					 },
					display: true,
					gridLines: {
						display: true,
						color: 'rgba(142, 156, 173,0.1)',
						drawBorder: false
					},
					scaleLabel: {
						display: false,
						labelString: 'sales',
						fontColor: 'transparent'
					}
				}]
			},
			title: {
				display: false,
				text: 'Normal Legend'
			},

			elements: {
				line: {
					borderWidth: 2
				},
				point: {
					radius: 0,
					hitRadius: 10,
					hoverRadius: 4
				}
			}
		}
	})
		
	$.plot('#flotChart1', [{
		data: flotSampleData1,
		color: '#494c57'
	}], {
		series: {
			shadowSize: 0,
			lines: {
				show: true,
				lineWidth: 2,
				fill: false,
				fillColor: {
					colors: [{
						opacity: 0
					}, {
						opacity: 0.2
					}]
				}
			}
		},
		grid: {
			borderWidth: 0,
			labelMargin: 0,
			aboveData: true
		},
		yaxis: {
			show: false,
			reserveSpace: false,
			min: 0,
			max: 120
		},
		xaxis: {
			show: true,
			color: 'rgba(102,16,242,.1)',
			reserveSpace: false,
			ticks: [
				[0, '1h'],
				[20, '12h'],
				[40, '1d'],
				[60, '1w'],
				[80, '1m']
			],
			font: {
				size: 11,
				weight: '700',
				family: 'Archivo,Arial,sans-serif',
				color: '#969dab'
			}
		}
	});
	
	
	
	
});