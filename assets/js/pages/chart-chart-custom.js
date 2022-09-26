'use strict';
$(document).ready(function() {
    // [ bar-chart ] start
    var bar = document.getElementById("chart-bar-1").getContext('2d');
    var data1 = {
        labels: [0, 1, 2, 3],
        datasets: [{
            label: "Data 1",
            data: [25, 45, 74, 85],
            borderColor: '#3949AB',
            backgroundColor: '#3949AB',
            hoverborderColor:'#3949AB',
            hoverBackgroundColor: '#3949AB',
        }, {
            label: "Data 2",
            data: [30, 52, 65, 65],
            borderColor: '#463699',
            backgroundColor: '#463699',
            hoverborderColor:'#463699',
            hoverBackgroundColor: '#463699',
        }]
    };
    var myBarChart = new Chart(bar, {
        type: 'bar',
        data: data1,
        options: {
            barValueSpacing: 20
        }
    });
    // [ bar-chart ] end

    // [ bar-stacked-chart ] start
    var bar = document.getElementById("chart-bar-2").getContext('2d');
    var data1 = {
        labels: [0, 1, 2, 3],
        datasets: [{
            label: "Data 1",
            data: [25, 45, 74, 85],
            borderColor: '#3949AB',
            backgroundColor: '#3949AB',
            hoverborderColor:'#3949AB',
            hoverBackgroundColor: '#3949AB',
        }, {
            label: "Data 2",
            data: [30, 52, 65, 65],
            borderColor: '#463699',
            backgroundColor: '#463699',
            hoverborderColor:'#463699',
            hoverBackgroundColor: '#463699',
        }]
    };
    var myBarChart = new Chart(bar, {
        type: 'bar',
        data: data1,
        options: {
            barValueSpacing: 20,
            scales: {
                xAxes: [{
                    stacked: true,
                }],
                yAxes: [{
                    stacked: true
                }]
            }
        },
    });
    // [ bar-stacked-chart ] end

    // [ bar-Horizontal-chart ] start
    var bar = document.getElementById("chart-bar-3").getContext('2d');
    var theme_g1 = bar.createLinearGradient(0, 300, 0, 0);
    var data1 = {
        labels: [0, 1, 2, 3],
        datasets: [{
            label: "Data 1",
            data: [25, 45, 74, 85],
            borderColor: '#3949AB',
            backgroundColor: '#3949AB',
            hoverborderColor:'#3949AB',
            hoverBackgroundColor: '#3949AB',
        }, {
            label: "Data 2",
            data: [30, 52, 65, 65],
            borderColor: '#463699',
            backgroundColor: '#463699',
            hoverborderColor:'#463699',
            hoverBackgroundColor: '#463699',
        }]
    };
    var myBarChart = new Chart(bar, {
        type: 'horizontalBar',
        data: data1,
        options: {
            barValueSpacing: 20
        }
    });
    // [ bar-Horizontal-chart ] end

    // [ line-chart ] start
    var bar = document.getElementById("chart-line-1").getContext('2d');
    var data1 = {
        labels: [0, 1, 2, 3, 4, 5, 6],
        datasets: [{
            label: "D1",
            data: [55, 70, 62, 81, 56, 70, 90],
            fill: false,
            borderWidth: 4,
            lineTension: 0,
            borderDash: [15, 10],
            borderColor: '#463699',
            backgroundColor: '#463699',
            hoverborderColor: '#463699',
            hoverBackgroundColor: '#463699',
        }, {
            label: "D2",
            data: [85, 55, 70, 50, 75, 45, 60],
            fill: true,
            cubicInterpolationMode: 'monotone',
            borderWidth: 0,
            borderColor: '#3949AB',
            backgroundColor: '#3949AB',
            hoverborderColor: '#3949AB',
            hoverBackgroundColor: '#3949AB',
        }, {
            label: "D3",
            data: [50, 75, 80, 70, 85, 80, 70],
            fill: false,
            borderWidth: 4,
            borderColor: '#2ca961',
            backgroundColor: '#2ca961',
            hoverborderColor: '#2ca961',
            hoverBackgroundColor: '#2ca961',
        }]
    };
    var myBarChart = new Chart(bar, {
        type: 'line',
        data: data1,
        responsive: true,
        options: {
            barValueSpacing: 20,
            maintainAspectRatio: false,
        }
    });
    // [ line-chart ] end

    // [ area-origin-chart ] start
    var bar = document.getElementById("chart-area-2").getContext('2d');
    var data1 = {
        labels: [0, 1, 2, 3, 4, 5, 6],
        datasets: [{
            label: "D1",
            data: [85, 55, 70, 50, 75, 45, 60],
            borderWidth: 1,
            borderColor: '#0288d1',
            backgroundColor:'#0288d1',
            hoverborderColor: '#0288d1',
            hoverBackgroundColor:'#0288d1',
            fill: 'origin',
        }]
    };
    var myBarChart = new Chart(bar, {
        type: 'line',
        data: data1,
        responsive: true,
        options: {
            barValueSpacing: 20,
            maintainAspectRatio: false,
        }
    });
    // [ area-origin-chart ] end

    // [ area-end-chart ] start
    var bar = document.getElementById("chart-area-3").getContext('2d');
    var data1 = {
        labels: [0, 1, 2, 3, 4, 5, 6],
        datasets: [{
            label: "D1",
            data: [85, 55, 70, 50, 75, 45, 60],
            borderWidth: 1,
            borderColor: '#2ca961',
            backgroundColor: '#2ca961',
            hoverborderColor: '#2ca961',
            hoverBackgroundColor:'#2ca961',
            fill: 'end',
        }]
    };
    var myBarChart = new Chart(bar, {
        type: 'line',
        data: data1,
        responsive: true,
        options: {
            barValueSpacing: 20,
            maintainAspectRatio: false,
        }
    });
    // [ area-end-chart ] end

    // [ area-chart ] Start
    var bar = document.getElementById("chart-area-1").getContext('2d');
    var data1 = {
        labels: [0, 1, 2, 3, 4, 5, 6],
        datasets: [{
            label: "D1",
            data: [45, 60, 45, 80, 60, 80, 45],
            fill: true,
            borderWidth: 4,
            borderColor: '#3949AB',
            backgroundColor: '#3949AB',
            hoverborderColor: '#3949AB',
            hoverBackgroundColor: '#3949AB',
        }, {
            label: "D2",
            data: [45, 80, 45, 45, 60, 45, 80],
            fill: true,
            cubicInterpolationMode: 'monotone',
            borderWidth: 0,
            borderColor: '#463699',
            backgroundColor: '#463699',
            hoverborderColor: '#463699',
            hoverBackgroundColor: '#463699',
        }, {
            label: "D3",
            data: [83, 45, 60, 45, 45, 55, 45],
            fill: true,
            borderWidth: 4,
            borderColor: '#2ca961',
            backgroundColor: '#2ca961',
            hoverborderColor: '#2ca961',
            hoverBackgroundColor: '#2ca961',
        }]
    };
    var myBarChart = new Chart(bar, {
        type: 'line',
        data: data1,
        responsive: true,
        options: {
            barValueSpacing: 20,
            maintainAspectRatio: false,
        }
    });
    // [ area-chart ] end

    // [ radar-chart1 ] Start
    var bar = document.getElementById("chart-radar-1").getContext('2d');
    var data1 = {
        labels: [0, 1, 2, 3, 4, 5, 6],
        datasets: [{
            label: "D1",
            data: [60, 60, 45, 80, 60, 80, 45],
            fill: true,
            borderWidth: 4,
            borderColor: '#3949AB',
            backgroundColor: '#3949AB',
            hoverborderColor: '#3949AB',
            hoverBackgroundColor: '#3949AB',
        }, {
            label: "D2",
            data: [40, 80, 40, 65, 60, 50, 95],
            fill: true,
            cubicInterpolationMode: 'monotone',
            borderWidth: 0,
            borderColor: '#463699',
            backgroundColor: '#463699',
            hoverborderColor: '#463699',
            hoverBackgroundColor: '#463699',
        }, {
            label: "D3",
            data: [20, 40, 80, 60, 85, 60, 20],
            fill: true,
            borderWidth: 4,
            borderColor: '#2ca961',
            backgroundColor: '#2ca961',
            hoverborderColor: '#2ca961',
            hoverBackgroundColor: '#2ca961',
        }]
    };
    var myBarChart = new Chart(bar, {
        type: 'radar',
        data: data1,
        responsive: true,
        options: {
            barValueSpacing: 20,
            maintainAspectRatio: false,
        }
    });
    // [ radar-chart1 ] end

    // [ radar-chart2 ] Start
    var bar = document.getElementById("chart-radar-2").getContext('2d');
    var data1 = {
        labels: [0, 1, 2, 3, 4, 5, 6],
        datasets: [{
            label: "D1",
            data: [60, 60, 45, 80, 60, 80, 45],
            fill: true,
            borderWidth: 4,
            borderColor: '#2ca961',
            backgroundColor: '#2ca961',
            hoverborderColor: '#2ca961',
            hoverBackgroundColor: '#2ca961',
        }, {
            label: "D2",
            data: [40, 80, 40, 65, 60, 50, 95],
            fill: true,
            cubicInterpolationMode: 'monotone',
            borderWidth: 0,
            borderColor: '#e52d27',
            backgroundColor: '#e52d27',
            hoverborderColor: '#e52d27',
            hoverBackgroundColor: '#e52d27',
        }, {
            label: "D3",
            data: [20, 40, 80, 60, 85, 60, 20],
            fill: true,
            borderWidth: 4,
            borderColor: '#f57c00',
            backgroundColor: '#f57c00',
            hoverborderColor: '#f57c00',
            hoverBackgroundColor: '#f57c00',
        }]
    };
    var BarChart = new Chart(bar, {
        type: 'radar',
        data: data1,
        responsive: true,
        options: {
            barValueSpacing: 20,
            maintainAspectRatio: false,
        }
    });
    // [ radar-chart2 ] end

    // [ radar-chart3 ] start
    var bar = document.getElementById("chart-radar-3").getContext('2d');
    var data1 = {
        labels: [0, 1, 2, 3, 4, 5, 6],
        datasets: [{
            label: "D1",
            data: [60, 60, 45, 80, 60, 80, 45],
            fill: true,
            borderWidth: 4,
            borderColor: '#3949AB',
            backgroundColor: "transparent",
            hoverborderColor: '#3949AB',
            hoverBackgroundColor: "transparent",
        }, {
            label: "D2",
            data: [40, 80, 40, 65, 60, 50, 95],
            fill: true,
            cubicInterpolationMode: 'monotone',
            borderWidth: 0,
            borderColor: '#463699',
            backgroundColor: "transparent",
            hoverborderColor: '#463699',
            hoverBackgroundColor: "transparent",
        }, {
            label: "D3",
            data: [20, 40, 80, 60, 85, 60, 20],
            fill: true,
            borderWidth: 4,
            borderColor: '#2ca961',
            backgroundColor: "transparent",
            hoverborderColor: '#2ca961',
            hoverBackgroundColor: "transparent",
        }]
    };
    var BarChart = new Chart(bar, {
        type: 'radar',
        data: data1,
        responsive: true,
        options: {
            barValueSpacing: 20,
            maintainAspectRatio: false,
        }
    });
    // [ radar-chart3 ] end

    // [ pie-chart ] start
    var bar = document.getElementById("chart-pie-1").getContext('2d');
    var data4 = {
        labels: [
            "Data 1",
            "Data 2",
            "Data 3"
        ],
        datasets: [{
            data: [30, 30, 40],
            backgroundColor: [
                '#2ca961',
                '#463699',
                '#3949AB'
            ],
            hoverBackgroundColor: [
                '#2ca961',
                '#463699',
                '#3949AB'
            ]
        }]
    };
    var myPieChart = new Chart(bar, {
        type: 'pie',
        data: data4,
        responsive: true,
        options: {
            maintainAspectRatio: false,
        }
    });
    // [ pie-chart ] end

    // [ Donut-chart ] start
    var bar = document.getElementById("chart-donut-1").getContext('2d');
    var data4 = {
        labels: [
            "Data 1",
            "Data 2",
            "Data 3"
        ],
        datasets: [{
            data: [30, 30, 40],
            backgroundColor: [
                '#2ca961',
                '#f57c00',
                '#e52d27'
            ],
            hoverBackgroundColor: [
                '#2ca961',
                '#f57c00',
                '#e52d27'
            ]
        }]
    };
    var myPieChart = new Chart(bar, {
        type: 'doughnut',
        data: data4,
        responsive: true,
        options: {
            maintainAspectRatio: false,
        }
    });
    // [ Donut-chart ] end
});
