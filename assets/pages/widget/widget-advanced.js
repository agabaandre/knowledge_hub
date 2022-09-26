'use strict';

$(document).ready(function() {

    //real-time update
    $(function() {
        // We use an inline data source in the example, usually data would
        // be fetched from a server
        var data = [],
            totalPoints = 300;

        function getRandomData() {
            if (data.length > 0)
                data = data.slice(1);
            // Do a random walk
            while (data.length < totalPoints) {
                var prev = data.length > 0 ? data[data.length - 1] : 50,
                    y = prev + Math.random() * 10 - 5;
                if (y < 0) {
                    y = 0;
                } else if (y > 100) {
                    y = 100;
                }
                data.push(y);
            }
            // Zip the generated y values with the x values
            var res = [];
            for (var i = 0; i < data.length; ++i) {
                res.push([i, data[i]])
            }
            return res;
        }
        // Set up the control widget
        var updateInterval = 30;
        $("#updateInterval").val(updateInterval).change(function() {
            var v = $(this).val();
            if (v && !isNaN(+v)) {
                updateInterval = +v;
                if (updateInterval < 1) {
                    updateInterval = 1;
                } else if (updateInterval > 2000) {
                    updateInterval = 2000;
                }
                $(this).val("" + updateInterval);
            }
        });
        var plot = $.plot("#realtime-profit", [getRandomData()], {

            lines: {
                show: true,
                fill: true,
                lineWidth: 1,
                borderWidth: 0,
            },
            shadowSize: 5,
            highlightColor: "rgba(0,0,0,0.5)",
            points: {
                show: true,
                radius: 0,
                fill: true,
                fillColor: '#fff'
            },
            curvedLines: {
                apply: false,
            },
            legend: {
                show: false
            },
            series: {
                label: "",
                color: "#66bb6a",
                curvedLines: {
                    active: true,
                    nrSplinePoints: 20
                },
            },
            tooltip: {
                show: true,
                content: "x : %x | y : %y"
            },
            grid: {
                hoverable: true,
                borderWidth: 0,
                minBorderMargin: 0,
            },
            yaxis: {
                min: 0,
                max: 100,
            },
            xaxis: {
                font: {
                    size: 0,
                }
            }
        });

        function update() {
            plot.setData([getRandomData()]);
            // Since the axes don't change, we don't need to call plot.setupGrid()
            plot.draw();
            setTimeout(update, updateInterval);
        }
        update();
    });
    // sale-diff
    var chart = AmCharts.makeChart("sale-diff", {
        "type": "serial",
        "theme": "light",
        "dataDateFormat": "YYYY-MM-DD",
        "precision": 2,
        "valueAxes": [{
            "id": "v1",
            "fontSize": 0,
            "axisAlpha": 0,
            "lineAlpha": 0,
            "gridAlpha": 0,
            "position": "left",
            "autoGridCount": false,
            "labelFunction": function(value) {
                return "$" + Math.round(value) + "M";
            }
        }],
        "graphs": [{
            "id": "g3",
            "valueAxis": "v1",
            "lineColor": "#66bb6a",
            "fillColors": "#66bb6a",
            "fillAlphas": 0.3,
            "type": "column",
            "title": "Actual Sales",
            "valueField": "sales2",
            "columnWidth": 0.5,
            "legendValueText": "$[[value]]M",
            "balloonText": "[[title]]<br /><b style='font-size: 130%'>$[[value]]M</b>"
        }, {
            "id": "g4",
            "valueAxis": "v1",
            "lineColor": "#66bb6a",
            "fillColors": "#66bb6a",
            "fillAlphas": 1,
            "type": "column",
            "title": "Target Sales",
            "valueField": "sales1",
            "columnWidth": 0.5,
            "legendValueText": "$[[value]]M",
            "balloonText": "[[title]]<br /><b style='font-size: 130%'>$[[value]]M</b>"
        }],
        "chartCursor": {
            "pan": true,
            "valueLineEnabled": true,
            "valueLineBalloonEnabled": true,
            "cursorAlpha": 0,
            "valueLineAlpha": 0.2
        },
        "categoryField": "date",
        "categoryAxis": {
            "parseDates": true,
            "axisAlpha": 0,
            "lineAlpha": 0,
            "gridAlpha": 0,
            "minorGridEnabled": true,
        },
        "balloon": {
            "borderThickness": 1,
            "shadowAlpha": 0
        },
        "export": {
            "enabled": true
        },
        "dataProvider": [{
            "date": "2013-01-16",
            "sales1": 5,
            "sales2": 8
        }, {
            "date": "2013-01-17",
            "sales1": 4,
            "sales2": 6
        }, {
            "date": "2013-01-18",
            "sales1": 5,
            "sales2": 2
        }, {
            "date": "2013-01-19",
            "sales1": 8,
            "sales2": 9
        }, {
            "date": "2013-01-20",
            "sales1": 9,
            "sales2": 6
        }]
    });
    // pageview and prod sale end
    floatchart()
    $(window).on('resize', function() {
        floatchart();
    });
    $('#mobile-collapse').on('click', function() {
        setTimeout(function() {
            floatchart();
        }, 700);
    });
});

function floatchart() {
    $(function() {
        //flot options
        var options = {
            legend: {
                show: false
            },
            series: {
                label: "",
                curvedLines: {
                    active: true,
                    nrSplinePoints: 20
                },
            },
            tooltip: {
                show: true,
                content: "x : %x | y : %y"
            },
            grid: {
                hoverable: true,
                borderWidth: 0,
                labelMargin: 0,
                axisMargin: 0,
                minBorderMargin: 0,
            },
            yaxis: {
                min: 0,
                max: 30,
                color: 'transparent',
                font: {
                    size: 0,
                }
            },
            xaxis: {
                color: 'transparent',
                font: {
                    size: 0,
                }
            }
        };


        // sale order start
        $.plot($("#seo-anlytics1"), [{
            data: [
                [0, 10],
                [1, 25],
                [2, 15],
                [3, 26],
                [4, 15],
                [5, 15],
                [6, 20],
                [7, 25],
                [8, 20],
                [9, 25],
                [10, 10],
                [11, 12],
                [12, 27],
                [13, 1],
            ],
            color: "#448aff",
            lines: {
                show: true,
                fill: false,
                lineWidth: 2
            },
            points: {
                show: true,
                radius: 3,
                fill: true,
                fillColor: '#448aff'
            },
            curvedLines: {
                apply: false,
            }
        }], options);
        $.plot($("#seo-anlytics2"), [{
            data: [
                [0, 10],
                [1, 15],
                [2, 25],
                [3, 15],
                [4, 26],
                [5, 20],
                [6, 15],
                [7, 20],
                [8, 25],
                [9, 10],
                [10, 25],
                [11, 27],
                [12, 12],
                [13, 1],
            ],
            color: "#9ccc65",
            lines: {
                show: true,
                fill: false,
                lineWidth: 2
            },
            points: {
                show: true,
                radius: 3,
                fill: true,
                fillColor: '#9ccc65'
            },
            curvedLines: {
                apply: false,
            }
        }], options);
        $.plot($("#seo-anlytics3"), [{
            data: [
                [0, 10],
                [1, 25],
                [2, 15],
                [3, 26],
                [4, 15],
                [5, 15],
                [6, 20],
                [7, 25],
                [8, 20],
                [9, 25],
                [10, 10],
                [11, 12],
                [12, 27],
                [13, 1],
            ],
            color: "#ff5252",
            lines: {
                show: true,
                fill: false,
                lineWidth: 2
            },
            points: {
                show: true,
                radius: 3,
                fill: true,
                fillColor: '#ff5252'
            },
            curvedLines: {
                apply: false,
            }
        }], options);
        $.plot($("#seo-anlytics4"), [{
            data: [
                [0, 10],
                [1, 15],
                [2, 25],
                [3, 15],
                [4, 26],
                [5, 20],
                [6, 15],
                [7, 20],
                [8, 25],
                [9, 10],
                [10, 25],
                [11, 27],
                [12, 12],
                [13, 1],
            ],
            color: "#536dfe",
            lines: {
                show: true,
                fill: false,
                lineWidth: 2
            },
            points: {
                show: true,
                radius: 3,
                fill: true,
                fillColor: '#536dfe'
            },
            curvedLines: {
                apply: false,
            }
        }], options);


        // value statustic start
        $.plot($("#total-value-graph-1"), [{
            data: [
                [0, 20],
                [20, 10],
                [35, 18],
                [50, 12],
                [65, 25],
                [75, 10],
                [90, 20],
            ],
            color: "#fff",
            lines: {
                show: true,
                fill: true,
                lineWidth: 3
            },
            points: {
                show: false
            },
            //curve the line  (old pre 1.0.0 plotting function)
            curvedLines: {
                apply: true,
            }
        }], options);
        $.plot($("#total-value-graph-2"), [{
            data: [
                [0, 10],
                [20, 20],
                [35, 18],
                [50, 25],
                [65, 12],
                [75, 10],
                [90, 20],
            ],
            color: "#fff",
            lines: {
                show: true,
                fill: true,
                lineWidth: 3
            },
            points: {
                show: false
            },
            //curve the line  (old pre 1.0.0 plotting function)
            curvedLines: {
                apply: true,
            }
        }], options);
        $.plot($("#total-value-graph-3"), [{
            data: [
                [0, 20],
                [20, 10],
                [35, 25],
                [50, 18],
                [65, 18],
                [75, 10],
                [90, 12],
            ],
            color: "#fff",
            lines: {
                show: true,
                fill: true,
                lineWidth: 3
            },
            points: {
                show: false
            },
            //curve the line  (old pre 1.0.0 plotting function)
            curvedLines: {
                apply: true,
            }
        }], options);
        $.plot($("#total-value-graph-4"), [{
            data: [
                [0, 18],
                [20, 10],
                [35, 20],
                [50, 10],
                [65, 12],
                [75, 25],
                [90, 20],
            ],
            color: "#fff",
            lines: {
                show: true,
                fill: true,
                lineWidth: 3
            },
            points: {
                show: false
            },
            //curve the line  (old pre 1.0.0 plotting function)
            curvedLines: {
                apply: true,
            }
        }], options);

        // user chart card start
        $.plot($("#client-map-1"), [{
            data: [
                [0, 20],
                [20, 10],
                [35, 18],
                [50, 12],
                [65, 25],
                [75, 10],
                [90, 20],
            ],
            color: "#448aff",
            lines: {
                show: true,
                fill: true,
                lineWidth: 3
            },
            points: {
                show: false
            },
            //curve the line  (old pre 1.0.0 plotting function)
            curvedLines: {
                apply: true,
            }
        }], options);
        $.plot($("#client-map-2"), [{
            data: [
                [0, 0],
                [1, 10],
                [2, 20],
                [3, 10],
                [4, 27],
                [5, 15],
                [6, 20],
                [7, 24],
                [8, 20],
                [9, 16],
                [10, 18],
                [11, 10],
                [12, 20],
                [13, 10],
                [14, 27],
                [15, 20],
                [16, 10],
                [17, 15],
                [18, 12],
                [19, 27],
                [20, 20],
                [21, 15],
                [22, 0],
            ],
            color: "#ff5252",
            lines: {
                show: true,
                fill: true,
                lineWidth: 2
            },
            points: {
                show: true,
                radius: 3,
                fill: true,
                fillColor: '#ff5252'
            },
            curvedLines: {
                apply: false,
            }
        }], options);
        $.plot($("#client-map-3"), [{
            data: [
                [0, 2],
                [1, 10],
                [2, 20],
                [3, 10],
                [4, 27],
                [5, 15],
                [6, 20],
                [7, 24],
                [8, 20],
                [9, 16],
                [10, 18],
                [11, 10],
                [12, 20],
                [13, 10],
                [14, 5],
            ],
            color: "#9ccc65",
            bars: {
                show: true,
                lineWidth: 1,
                fill: true,
                fillColor: {
                    colors: [{
                        opacity: 1
                    }, {
                        opacity: 1
                    }]
                },
                barWidth: 0.5,
                align: 'center',
                horizontal: false
            },
            points: {
                show: false
            },
        }], options);

        // sale order start
        $.plot($("#monthlyprofit-1"), [{
            data: [
                [0, 10],
                [1, 25],
                [2, 15],
                [3, 26],
                [4, 15],
                [5, 15],
                [6, 20],
                [7, 25],
                [8, 20],
                [9, 25],
                [10, 10],
                [11, 12],
                [12, 27],
                [13, 20],
                [14, 25],
                [15, 20],
                [16, 25],
                [17, 10],
                [18, 12],
                [19, 27],
                [20, 1],
            ],
            color: "#448aff",
            lines: {
                show: true,
                fill: true,
                lineWidth: 2
            },
            points: {
                show: true,
                radius: 2,
                fill: true,
                fillColor: '#448aff'
            },
            curvedLines: {
                apply: false,
            }
        }], options);
        $.plot($("#monthlyprofit-2"), [{
            data: [
                [0, 10],
                [1, 15],
                [2, 25],
                [3, 15],
                [4, 26],
                [5, 20],
                [6, 15],
                [7, 20],
                [8, 25],
                [9, 10],
                [10, 25],
                [11, 27],
                [12, 12],
                [13, 1],
            ],
            color: "#9ccc65",
            lines: {
                show: true,
                fill: true,
                lineWidth: 2
            },
            points: {
                show: true,
                radius: 2,
                fill: true,
                fillColor: '#9ccc65'
            },
            curvedLines: {
                apply: false,
            }
        }], options);
        $.plot($("#monthlyprofit-3"), [{
            data: [
                [0, 10],
                [1, 25],
                [2, 15],
                [3, 26],
                [4, 15],
                [5, 15],
                [6, 20],
                [7, 25],
                [8, 20],
                [9, 25],
                [10, 10],
                [11, 12],
                [12, 20],
                [13, 25],
                [14, 10],
                [15, 12],
                [16, 27],
                [17, 1],
            ],
            color: "#ff5252",
            lines: {
                show: true,
                fill: true,
                lineWidth: 2
            },
            points: {
                show: true,
                radius: 2,
                fill: true,
                fillColor: '#ff5252'
            },
            curvedLines: {
                apply: false,
            }
        }], options);
       // sale start
       $.plot($("#sec-ecommerce-chart-line"), [{
           data: [
               [0, 18],
               [1, 10],
               [2, 20],
               [3, 10],
               [4, 27],
               [5, 15],
               [6, 20],
               [7, 24],
               [8, 20],
               [9, 16],
               [10, 18],
               [11, 10],
               [12, 20],
               [13, 10],
               [14, 27],
           ],
           color: "#66bb6a",
           lines: {
               show: true,
               fill: false,
               lineWidth: 2
           },
           points: {
               show: true,
               radius: 3,
               fill: true,
               fillColor: '#fff'
           },
           curvedLines: {
               apply: false,
           }
       }], options);
       $.plot($("#sec-ecommerce-chart-bar"), [{
           data: [
               [0, 18],
               [1, 10],
               [2, 20],
               [3, 10],
               [4, 27],
               [5, 15],
               [6, 20],
               [7, 24],
               [8, 20],
               [9, 16],
               [10, 18],
               [11, 10],
               [12, 20],
               [13, 10],
               [14, 27],
           ],
           color: "#e0f1e1",
           bars: {
               show: true,
               lineWidth: 1,
               fill: true,
               fillColor: {
                   colors: [{
                       opacity: 1
                   }, {
                       opacity: 1
                   }]
               },
               barWidth: 0.6,
               align: 'center',
               horizontal: false
           },
           points: {
               show: false
           },
       }], options);
   });
}
