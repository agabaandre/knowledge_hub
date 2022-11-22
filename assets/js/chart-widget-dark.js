$(function() {
	
 
	/* Chartjs (#areachart1) */
	var ctx = document.getElementById("areaChart1");
	var myChart = new Chart(ctx, {
		type: 'line',
		data: {
			labels: ['Date 1', 'Date 2', 'Date 3', 'Date 4', 'Date 5', 'Date 6', 'Date 7', 'Date 8', 'Date 9', 'Date 10', 'Date 11', 'Date 12', 'Date 13', 'Date 14', 'Date 15', 'Date 16', 'Date 17'],
			type: 'line',
			datasets: [{
				data: [45, 23, 32, 67, 49, 72, 52, 55, 46, 54, 32, 74, 88, 36, 36, 32, 48, 54,110],
				label: 'Admissions',
				backgroundColor: 'transparent',
				borderColor: '#4d78fb',
				borderWidth: '2.5',
				pointBorderColor: 'transparent',
				pointBackgroundColor: 'transparent',
			}]
		},
		options: {
			maintainAspectRatio: false,
			legend: {
				display: false
			},
			responsive: true,
			tooltips: {
				 enabled: false
			},
			scales: {
				xAxes: [{
					gridLines: {
						color: 'transparent',
						zeroLineColor: 'transparent'
					},
					ticks: {
						fontSize: 2,
						fontColor: 'transparent'
					}
				}],
				yAxes: [{
					display: false,
					ticks: {
						display: false,
					}
				}]
			},
			title: {
				display: false,
			},
			elements: {
				line: {
					borderWidth: 1
				},
				point: {
					radius: 4,
					hitRadius: 10,
					hoverRadius: 4
				}
			}
		}
	});
	/* Chartjs (#areachart1) closed */

	/* Chartjs (#areachart2) */
	var ctx = document.getElementById("areaChart2");
	var myChart = new Chart(ctx, {
		type: 'line',
		data: {
			labels: ['Date 1', 'Date 2', 'Date 3', 'Date 4', 'Date 5', 'Date 6', 'Date 7', 'Date 8', 'Date 9', 'Date 10', 'Date 11', 'Date 12', 'Date 13', 'Date 14', 'Date 15', 'Date 16', 'Date 17'],
			type: 'line',
			datasets: [{
				data: [28, 56, 36, 32, 48, 54, 37,58, 66, 53, 21, 24, 14, 45, 0, 32, 67, 49, 52, 55, 46, 54,130],
				label: 'Admissions',
				backgroundColor: 'transparent',
				borderColor: '#f10075',
				borderWidth: '2.5',
				pointBorderColor: 'transparent',
				pointBackgroundColor: 'transparent',
			}]
		},
		options: {
			maintainAspectRatio: false,
			legend: {
				display: false
			},
			responsive: true,
			tooltips: {
				 enabled: false
			},
			scales: {
				xAxes: [{
					gridLines: {
						color: 'transparent',
						zeroLineColor: 'transparent'
					},
					ticks: {
						fontSize: 2,
						fontColor: 'transparent'
					}
				}],
				yAxes: [{
					display: false,
					ticks: {
						display: false,
					}
				}]
			},
			title: {
				display: false,
			},
			elements: {
				line: {
					borderWidth: 1
				},
				point: {
					radius: 4,
					hitRadius: 10,
					hoverRadius: 4
				}
			}
		}
	});
	/* Chartjs (#areachart2) closed */
	
	/* Chartjs (#areachart3) */
	var ctx = document.getElementById("areaChart3");
	var myChart = new Chart(ctx, {
		type: 'line',
		data: {
			labels: ['Date 1', 'Date 2', 'Date 3', 'Date 4', 'Date 5', 'Date 6', 'Date 7', 'Date 8', 'Date 9', 'Date 10', 'Date 11', 'Date 12', 'Date 13', 'Date 14', 'Date 15', 'Date 16', 'Date 17','Date 18', 'Date 19'],
			type: 'line',
			datasets: [{
				data: [58, 78, 55, 41,31, 45, 54, 51, 32, 48, 88, 66, 36, 32, 48, 24, 14, 45, 0, 32, 67, 49, 54, 87,130],
				label: 'Admissions',
				backgroundColor: 'transparent',
				borderColor: '#09b0ec',
				borderWidth: '2.5',
				pointBorderColor: 'transparent',
				pointBackgroundColor: 'transparent',
			}]
		},
		options: {
			maintainAspectRatio: false,
			legend: {
				display: false
			},
			responsive: true,
			tooltips: {
				 enabled: false
			},
			scales: {
				xAxes: [{
					gridLines: {
						color: 'transparent',
						zeroLineColor: 'transparent'
					},
					ticks: {
						fontSize: 2,
						fontColor: 'transparent'
					}
				}],
				yAxes: [{
					display: false,
					ticks: {
						display: false,
					}
				}]
			},
			title: {
				display: false,
			},
			elements: {
				line: {
					borderWidth: 1
				},
				point: {
					radius: 4,
					hitRadius: 10,
					hoverRadius: 4
				}
			}
		}
	});
	/* Chartjs (#areachart3) closed */

	/* Chartjs (#areachart4) */
	var ctx = document.getElementById("areaChart4");
	var myChart = new Chart(ctx, {
		type: 'line',
		data: {
			labels: ['Date 1', 'Date 2', 'Date 3', 'Date 4', 'Date 5', 'Date 6', 'Date 7', 'Date 8', 'Date 9', 'Date 10', 'Date 11', 'Date 12', 'Date 13', 'Date 14', 'Date 15', 'Date 16', 'Date 17'],
			type: 'line',
			datasets: [{
				data: [28, 56, 36, 52, 48, 24, 14, 45, 80, 32, 45, 54, 51, 52, 48, 54, 67, 49, 58, 78,54,120],
				label: 'Admissions',
				backgroundColor: 'transparent',
				borderColor: '#1dab2d',
				borderWidth: '3',
				pointBorderColor: 'transparent',
				pointBackgroundColor: 'transparent',
			}]
		},
		options: {
			maintainAspectRatio: false,
			legend: {
				display: false
			},
			responsive: true,
			tooltips: {
				 enabled: false
			},
			scales: {
				xAxes: [{
					gridLines: {
						color: 'transparent',
						zeroLineColor: 'transparent'
					},
					ticks: {
						fontSize: 2,
						fontColor: 'transparent'
					}
				}],
				yAxes: [{
					display: false,
					ticks: {
						display: false,
					}
				}]
			},
			title: {
				display: false,
			},
			elements: {
				line: {
					borderWidth: 1
				},
				point: {
					radius: 4,
					hitRadius: 10,
					hoverRadius: 4
				}
			}
		}
	});
	/* Chartjs (#areachart4 closed) */
	
	
	/* Chartjs (#donut)  */
	if ($('#crypto-donut').length) {
		var ctx = document.getElementById("crypto-donut").getContext("2d");
		new Chart(ctx, {
			type: 'doughnut',
			data: {
				labels: ["Buy", 'Sell', 'Exchange'],
				datasets: [{
					data: [50, 30, 30],
					backgroundColor: [
					   '#4d78fb','#ffc107','#22c03c'
					],
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				legend: {
					display: false
				},
				cutoutPercentage: 75,
			}
		});
	}
	/* Chartjs (#donut) closed */
	
	$.plot('#flotChart3', [{
		data: flotSampleData5,
		color: '#4d78fb',
		fillColor:'rgb(0, 123, 255,0.5)',
	}], {
		series: {
			shadowSize: 0,
			lines: {
				show: true,
				lineWidth: 2,
				fill: true,
				fillColor: {
					colors: [{
						opacity: 0
					}, {
						opacity: 0.4,
					}]
				}
			}
		},
		grid: {
			borderWidth: 0,
			labelMargin: 0
		},
		yaxis: {
			show: false,
			min: 0,
			max: 110,
			color: '#e2eaf9'
		},
		xaxis: {
			show: false,
			color: '#e2eaf9'
		}
	});
	
	$.plot('#flotChart5', [{
		data: flotSampleData2,
		color: '#f10075',
		fillColor:'rgba(255, 89, 89,0.5)'
	}], {
		series: {
			shadowSize: 0,
			lines: {
				show: true,
				lineWidth: 2,
				fill: true,
				fillColor: {
					colors: [{
						opacity: 0
					}, {
						opacity: 0.4,
					}]
				}
			}
		},
		grid: {
			borderWidth: 0,
			labelMargin: 0
		},
		yaxis: {
			show: false,
			min: 0,
			max: 130,
			color: '#e2eaf9'
		},
		xaxis: {
			show: false,
			color: '#e2eaf9'
		}
	});
	
	
	$.plot('#flotChart1', [{
		data: flotSampleData4,
		color: '#0acf97',
		fillColor:'rgb(15, 189, 102,0.1)',
	}], {
		series: {
			shadowSize: 0,
			lines: {
				show: true,
				lineWidth: 2,
				fill: true,
				fillColor: {
					colors: [{
						opacity: 0
					}, {
						opacity: 0.4,
					}]
				}
			}
		},
		grid: {
			borderWidth: 0,
			labelMargin: 0
		},
		yaxis: {
			show: false,
			min: 0,
			max: 110,
			color: '#e2eaf9'
		},
		xaxis: {
			show: false,
			color: '#e2eaf9'
		}
	});
	
	/* Chartjs (#statistics) */
	var myCanvas = document.getElementById("statistics");
	myCanvas.height="260";

	var myCanvasContext = myCanvas.getContext("2d");
	var gradientStroke1 = myCanvasContext.createLinearGradient(0, 0, 0, 300);
	gradientStroke1.addColorStop(0, '#3969fb');
	
	var gradientStroke2 = myCanvasContext.createLinearGradient(0, 0, 0, 390);
	gradientStroke2.addColorStop(0, '#ef4296');
	
	var gradientStroke3 = myCanvasContext.createLinearGradient(0, 0, 0, 390);
	gradientStroke3.addColorStop(0, 'rgba(231, 239, 249, 0.15)');

    var myChart = new Chart( myCanvas, {
		type: 'bar',
		data: {
			labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul","Aug"],
			datasets: [{
				label: 'Page Load time',
				data: [15, 18, 12, 14, 10, 15, 7, 14,15,17,18],
				backgroundColor: gradientStroke1,
				hoverBackgroundColor: gradientStroke1,
				hoverBorderWidth: 2,
				hoverBorderColor: 'gradientStroke1'
			},
			{

			    label: 'Avg time on Page',
				data: [10, 14, 10, 15, 9, 14, 15, 20,13,15,16],
				backgroundColor: gradientStroke2,
				hoverBackgroundColor: gradientStroke2,
				hoverBorderWidth: 2,
				hoverBorderColor: 'gradientStroke2'
			},
			{

			    label: 'Avg time on Page',
				data: [10, 14, 10, 15, 9, 14, 15, 20,23,13,12],
				backgroundColor: gradientStroke3,
				hoverBackgroundColor: gradientStroke3,
				hoverBorderWidth: 2,
				hoverBorderColor: 'gradientStroke3'
			}
		  ]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			tooltips: {
				mode: 'index',
				titleFontSize: 12,
				titleFontColor: '#000',
				bodyFontColor: '#000',
				backgroundColor: '#fff',
				cornerRadius: 3,
				intersect: false,

			},
			legend: {
				display: false,
				labels: {
					usePointStyle: true,
					fontFamily: 'Montserrat',
				},
			},
			scales: {
				yAxes: [{
					display: false,
					hitRadius:1,
					gridLines: {
						display: true,
						color: "#eaf0f7",
					},
					stacked: true,
					radius:25,
					 cornerRadius: 20,
					ticks: {
						beginAtZero: true,
						fontColor: "#8492a6"
					},
				}],
				xAxes: [{
                    barPercentage: 0.23,
					barValueSpacing :3,
					barDatasetSpacing : 3,
					stacked: true,
					radius:25,
					 cornerRadius: 20,
					ticks: {
						beginAtZero: true,
						fontColor: "#8492a6"
					},
					gridLines: {
						color: "#eaf0f7",
						display: false
					},

				}]
			},
			legend: {
				display: false
			},
			elements: {
				point: {
					radius:25,
				}
			}
		}
	});
	/* Chartjs (#statistics) closed */
	
	
	$('#unique-visitors').sparkline('html', {
		lineColor: '#285cf7',
		lineWidth: 1.5,
		spotColor: false,
		minSpotColor: false,
		maxSpotColor: false,
		highlightSpotColor: null,
		highlightLineColor: null,
		fillColor: 'rgba(40, 92, 247, 0.09) ',
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
		fillColor: 'rgba(255, 193, 7, 0.07) ',
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
		fillColor: 'rgba(0, 204, 204, 0.09) ',
		chartRangeMin: 0,
		chartRangeMax: 10,
		width: '50%',
		height: 20,
		disableTooltips: true
	});
	
	$('#bounce-rate').sparkline('html', {
		lineColor: '#f10075',
		lineWidth: 1.5,
		spotColor: false,
		minSpotColor: false,
		maxSpotColor: false,
		highlightSpotColor: null,
		highlightLineColor: null,
		fillColor: 'rgba(241, 0, 117, 0.08) ',
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
		fillColor: 'rgba(220, 53, 69, 0.08) ',
		chartRangeMin: 0,
		chartRangeMax: 10,
		width: '50%',
		height: 20,
		disableTooltips: true
	});
	
	$('#session').sparkline('html', {
		lineColor: '#673ab7',
		lineWidth: 1.5,
		spotColor: false,
		minSpotColor: false,
		maxSpotColor: false,
		highlightSpotColor: null,
		highlightLineColor: null,
		fillColor: 'rgba(103, 58, 183, 0.08) ',
		chartRangeMin: 0,
		chartRangeMax: 10,
		width: '50%',
		height: 20,
		disableTooltips: true
	});
	
	// Donut chart
  $('.peity-donut').peity('donut');
  
  
  /* Chartjs (#flotPie) */
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
 		color: '#007bff'
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
 		color: '#f10075'
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
	/* Chartjs (#flotPie) closed */
	
	/* Chart-js (#audiencee) */
	var myCanvas = document.getElementById("audiencee");
	myCanvas.height="217";
	var myCanvasContext = myCanvas.getContext("2d");
	var gradientStroke1 = myCanvasContext.createLinearGradient(0, 0, 0, 200);
	gradientStroke1.addColorStop(0, '#1d53f7');

	var gradientStroke2 = myCanvasContext.createLinearGradient(0, 0, 0, 190);
	gradientStroke2.addColorStop(0, '#f10075');
	var myChart = new Chart( myCanvas, {
		type: 'line',
		lineDashType: "dash",
		data : {
			labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul' ,'Aug',"Sep"],
			
			 datasets: [
			{
				label: "Bounce rate",
				data: [2,7,3,5,4,5,2,8,4,6,5,2],
				backgroundColor:'transparent',
				borderColor: '#1d53f7' ,
				pointBackgroundColor:'#fff',
				pointHoverBackgroundColor:gradientStroke1,
				pointBorderColor :gradientStroke1,
				pointHoverBorderColor :gradientStroke1,
				pointBorderWidth :0,
				pointRadius :0,
				pointHoverRadius :0,
				labelColor:gradientStroke1,
				borderWidth: 2,
				

			}, {
				label: "Sessions",
				data: [5,9,5,9,5,9,7,4,5,2,5,3,10],
				backgroundColor: 'transparent',
				borderColor: '#f10075',
				pointBackgroundColor:'#fff',
				pointHoverBackgroundColor:gradientStroke2,
				pointBorderColor :gradientStroke2,
				pointHoverBorderColor :gradientStroke2,
				pointBorderWidth :0,
				pointRadius :0,
				pointHoverRadius :0,
				borderWidth: 2,
				 borderDash: [8,3]

			}
		  ]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			legend: {
			    labels: {
					fontColor: '#8492a6'
			    }
			},
			tooltips: {
				show: true,
				showContent: true,
				alwaysShowContent: true,
				triggerOn: 'mousemove',
				trigger: 'axis',
				axisPointer:
				{
					label: {
						show: false,
						color: '#8295af',
					},
				}
			},

			scales: {
				xAxes: [ {
					gridLines: {
						color: 'rgb(234, 240, 247,0.1)',
						zeroLineColor: 'rgb(234, 240, 247,0.1)'
					},
					ticks: {
						fontSize: 12,
						fontColor: '#8295af',
						beginAtZero: true,
						padding: 0
					}
				} ],
				yAxes: [ {
					gridLines: {
						color: 'transparent',
						zeroLineColor: '#879ebd'
					},
					ticks: {
						fontSize: 12,
						fontColor: '#8295af',
						beginAtZero: false,
						padding: 0
					}
				} ]
			},
			title: {
				display: false,
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
	/* Chart-js (#audiencee) closed */

	
	
});