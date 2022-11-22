$(function() {
	
	/*---ChartJS (#barChart) ---*/
	var ctx = document.getElementById( "barChart" );
    ctx.height = 10;
	var barChart = new Chart(ctx, {
      type: 'bar',
	  data: {
          labels: ["Rose", "Harry", "Sophie", "James", "Jasmine"],
          datasets: [{
              label: 'Project Task',
              data: [19, 15, 17, 14,10,15],
			  barGap:0,
			  barSizeRatio:1,
			   borderRadius: 20,
              backgroundColor: [
                  'rgba(68, 112, 247, 0.2)',
                  'rgba(68, 112, 247, 0.2)',
                  '#285cf7',
                  'rgba(68, 112, 247, 0.2)',
                  'rgba(68, 112, 247, 0.2)',
                  'rgba(68, 112, 247, 0.2) '
              ],
              borderColor: [
                  'rgba(68, 112, 247, 0.5)',
                  'rgba(68, 112, 247, 0.5)',
                  '#285cf7',
                  'rgba(68, 112, 247, 0.5)',
                  'rgba(68, 112, 247, 0.5)',
                  'rgba(68, 112, 247, 0.5) '
              ],
              borderWidth: 1
          }]
      },
      options: {
			
			responsive: true,
			maintainAspectRatio: false,
			barRadius: 4,
			scales: {
				xAxes: [{
					categoryPercentage: 1.0,
					barPercentage: 1.0,
					barValueSpacing : 0,        // doesn't work; find another way
					barDatasetSpacing : 0, 
					display: false,
					barThickness : 5,
					cornerRadius: 20,
					borderRadius: 20,
					ticks: {
						fontColor: "transparent",
					 },
					gridLines: {
						display: false,
					},
					scaleShowHorizontalLines: false,
				}],
				yAxes: [{
					display: false,
					ticks: {
						beginAtZero: true,
						fontColor: "transparent",
					},
					gridLines: {
						display: false
					},
				}]
			},
			legend: {
				display: false,
			},
		}
    });
	/*---ChartJS (#barChart) closed---*/
	
	
	/*---ChartJS (#barChart2) ---*/
	var ctx = document.getElementById( "barChart2" );
    ctx.height = 40;
	var barChart = new Chart(ctx, {
      type: 'bar',
	  data: {
          labels: ["Rose", "Harry", "Sophie", "James", "Jasmine"],
          datasets: [{
              label: 'Project Task',
              data: [14, 10, 12, 17,11],
			  barGap:0,
			  barSizeRatio:1,
              backgroundColor: [
                  'rgba(245, 91, 165,0.2)',
                  'rgba(245, 91, 165,0.2)',
                  'rgba(245, 91, 165,0.2)',
                  '#f10075',
                  'rgba(245, 91, 165,0.2)'
              ],
              borderColor: [
                  'rgba(245, 91, 165,0.5)',
                  'rgba(245, 91, 165,0.5)',
                  'rgba(245, 91, 165,0.5)',
                  '#f10075',
                  'rgba(245, 91, 165,0.5)'
              ],
              borderWidth: 1
          }]
      },
      options: {
			responsive: true,
			maintainAspectRatio: false,
			scales: {
				xAxes: [{
					categoryPercentage: 1.0,
					barPercentage: 1.0,
					barValueSpacing : 0,        // doesn't work; find another way
					barDatasetSpacing : 0, 
					display: false,
					barThickness : 5,
					cornerRadius: 20,
					borderRadius: 20,
					ticks: {
						fontColor: "transparent",
					 },
					gridLines: {
						display: false,
					},
					scaleShowHorizontalLines: false,
				}],
				yAxes: [{
					display: false,
					ticks: {
						beginAtZero: true,
						fontColor: "transparent",
					},
					gridLines: {
						display: false
					},
				}]
			},
			legend: {
				display: false,
			},
		}
    });
	/*---ChartJS (#barChart2) closed---*/
	
	/*---ChartJS (#barChart3) ---*/
	var ctx = document.getElementById( "barChart3" );
    ctx.height = 40;
	var barChart = new Chart(ctx, {
      type: 'bar',
	  data: {
          labels: ["Rose", "Harry", "Sophie", "James", "Jasmine"],
          datasets: [{
              label: 'Project Task',
              data: [19, 15, 17, 14,10],
			  barGap:0,
			  barSizeRatio:1,
              backgroundColor: [
                  'rgba(34, 192, 60,0.2)',
                  '#22c03c',
                  'rgba(34, 192, 60,0.2)',
                  'rgba(34, 192, 60,0.2)',
                  'rgba(34, 192, 60,0.2)',
                  'rgba(34, 192, 60,0.2)'
              ],
              borderColor: [
                  'rgba(34, 192, 60,0.5)',
                  '#22c03c',
                  'rgba(34, 192, 60,0.5)',
                  'rgba(34, 192, 60,0.5)',
                  'rgba(34, 192, 60,0.5)',
                  'rgba(34, 192, 60,0.5)'
              ],
              borderWidth: 1
          }]
      },
      options: {
			
			
			responsive: true,
			maintainAspectRatio: false,
			scales: {
				xAxes: [{
					categoryPercentage: 1.0,
					barPercentage: 1.0,
					barValueSpacing : 0,        // doesn't work; find another way
					barDatasetSpacing : 0, 
					display: false,
					barThickness : 5,
					cornerRadius: 20,
					borderRadius: 20,
					ticks: {
						fontColor: "transparent",
					 },
					gridLines: {
						display: false,
					},
					scaleShowHorizontalLines: false,
				}],
				yAxes: [{
					display: false,
					ticks: {
						beginAtZero: true,
						fontColor: "transparent",
					},
					gridLines: {
						display: false
					},
				}]
			},
			legend: {
				display: false,
			},
		}
    });
	/*---ChartJS (#barChart3) closed---*/
	
	
	/*---ChartJS (#barChart4) ---*/
	var ctx = document.getElementById( "barChart4" );
    ctx.height = 40;
	var barChart = new Chart(ctx, {
      type: 'bar',
	  data: {
          labels: ["Rose", "Harry", "Sophie", "James", "Jasmine"],
          datasets: [{
              label: 'Project Task',
              data: [19, 15, 17, 14,10],
			  barGap:0,
			  barSizeRatio:1,
              backgroundColor: [
                  '#673ab7',
                  'rgba(103, 58, 183,0.3)',
                  'rgba(103, 58, 183,0.3)',
                  'rgba(103, 58, 183,0.3)',
                  'rgba(103, 58, 183,0.3)',
                  'rgba(103, 58, 183,0.3)'
              ],
              borderColor: [
                  '#673ab7',
                  'rgba(103, 58, 183,0.5)',
                  'rgba(103, 58, 183,0.5)',
                  'rgba(103, 58, 183,0.5)',
                  'rgba(103, 58, 183,0.5)',
                  'rgba(103, 58, 183,0.5)'
              ],
              borderWidth: 1
          }]
      },
      options: {
			responsive: true,
			maintainAspectRatio: false,
			scales: {
				xAxes: [{
					categoryPercentage: 1.0,
					barPercentage: 1.0,
					barValueSpacing : 0,        // doesn't work; find another way
					barDatasetSpacing : 0, 
					display: false,
					barThickness : 5,
					cornerRadius: 20,
					borderRadius: 20,
					ticks: {
						fontColor: "transparent",
					 },
					gridLines: {
						display: false,
					},
					scaleShowHorizontalLines: false,
				}],
				yAxes: [{
					display: false,
					ticks: {
						beginAtZero: true,
						fontColor: "transparent",
					},
					gridLines: {
						display: false
					},
				}]
			},
			legend: {
				display: false,
			},
		}
    });
	/*---ChartJS (#barChart4) closed---*/
	
	
	/* Chart-js (#audiencee-metric) */
	var myCanvas = document.getElementById("audiencee-metric");
	myCanvas.height="274";
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
						color: 'rgba(234, 240, 247,1)',
						zeroLineColor: 'rgba(234, 240, 247,1)'
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
	/* Chart-js (#audiencee-metric) closed */
	
	
	/*--- Apex (#chart) ---*/
	var options = {
		series: [17],
		chart: {
		height: 180,
		type: 'radialBar',
		offsetY: 0
	},
	plotOptions: {
	  radialBar: {
		startAngle: -135,
		endAngle: 135,
		size: 105,
		 track: {	
		 strokeWidth: "80%",	
		 background: '#fff',	
		},
		dataLabels: {
		  name: {
			fontSize: '16px',
			color: undefined,
			offsetY: 30,
		  },
		  hollow: {	
			 size: "60%"	
			},
		  value: {
			offsetY: -10,
			fontSize: '22px',
			color: undefined,
			formatter: function (val) {
			  return val + "%";
			}
		  }
		}
	  }
	},
	colors: '#3969fb',
	fill: {
	  type: 'gradient',
	  gradient: {
		  shade: 'dark',
		  shadeIntensity: 0.15,
		   gradientToColors:undefined,
		  inverseColors: false,
		  opacityFrom: 1,
		  opacityTo: 1,
		  stops: [0, 50, 65, 91]
	  },
	},
	stroke: {
	  dashArray: 4
	},
   series: [78],	
		labels: ["Growth"]
	};

	var chart = new ApexCharts(document.querySelector("#chart"), options);
	chart.render();
	/*--- Apex (#chart)closed ---*/
	
		
	/* Chartjs (#traffic) */
	var myCanvas = document.getElementById("traffic");
	curvature: 5,
	myCanvas.height="195";

	var myCanvasContext = myCanvas.getContext("2d");
	var gradientStroke1 = myCanvasContext.createLinearGradient(0, 0, 0, 500);
	gradientStroke1.addColorStop(0, '#3969fb');

	var gradientStroke2 = myCanvasContext.createLinearGradient(0, 0, 0, 390);
	gradientStroke2.addColorStop(0, '#ef4296');

	var gradientStroke3 = myCanvasContext.createLinearGradient(0, 0, 0, 390);
	gradientStroke3.addColorStop(0, 'rgba(68, 112, 247, 0.1)');


    var myChart = new Chart( myCanvas, {
		type: 'bar',
		data: {
			labels: ["0", "1", "2", "3", "4", "5" ,"6","7","8"],
			datasets: [{
				label: 'Open traffic',
				data: [7, 6, 10, 6, 8, 10, 9, 5, 10, 4],
				backgroundColor: '#3969fb',
				borderWidth: 1,
				hoverBackgroundColor: '#3969fb',
				hoverBorderWidth: 0,
				borderColor: '#3969fb',
				hoverBorderColor: '#3969fb',
				pointBorderWidth: 0,
				pointHoverRadius: 4,
				pointHoverBorderColor: "#fff",
				pointHoverBorderWidth: 0,
				pointRadius: 0,
				pointHitRadius: 0,
				radius:25,
				cornerRadius: 20,
				hitRadius:1,
			}, {

			    label: 'Close Leads',
				data: [5, 5, 7, 4, 6, 3, 5, 7, 5, 3,4,14],
				backgroundColor: '#ef4296',
				borderWidth: 1,
				hoverBackgroundColor: '#ef4296',
				hoverBorderWidth: 0,
				borderColor: '#ef4296',
				hoverBorderColor: '#ef4296',
				pointBorderWidth: 0,
				pointHoverRadius: 4,
				pointHoverBorderColor: "#fff",
				pointHoverBorderWidth: 0,
				pointRadius: 0,
				pointHitRadius: 0,
				hitRadius:1,
			},
			{

			    label: 'Total Leads',
				data: [12, 12, 12, 12, 12, 12, 12, 12, 12, 12],
				backgroundColor: 'rgba(68, 112, 247, 0.1)',
				borderWidth: 1,
				hoverBackgroundColor: 'rgba(68, 112, 247, 0.1)',
				hoverBorderWidth: 0,
				borderColor: 'rgba(68, 112, 247, 0.1)',
				hoverBorderColor: 'rgba(68, 112, 247, 0.1)',
				pointBorderWidth: 0,
				pointHoverRadius: 4,
				pointHoverBorderColor: "#fff",
				pointHoverBorderWidth: 0,
				pointRadius: 0,
				pointHitRadius: 0,

			}
		  ]
		},
		options: {

			responsive: true,
			 cornerRadius: 20,
			tooltips: {
				mode: 'index',
				titleFontSize: 12,
				titleFontColor: '#000',
				bodyFontColor: '#000',
				backgroundColor: '#fff',
				
				intersect: false,
			},
			maintainAspectRatio: true,
			layout: {
				padding: {
					left: 0,
					right: 0,
					top: 0,
					bottom: 0
				}
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
	/* Chartjs (#traffic) closed */
	
	// Line chart
	$('.peity-line').peity('line');
	
	// Bar charts
	$('.peity-bar').peity('bar');
	
	// Bar charts
	$('.peity-donut').peity('donut');


});