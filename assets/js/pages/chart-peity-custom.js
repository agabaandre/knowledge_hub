'use strict';
$(document).ready(function() {
    setTimeout(function() {
        // [ updating-Chart ] start
        var updatingChart = $(".updating-chart").peity("line", {
            fill: "rgba(229, 45, 39, 0.4)",
            stroke: "rgb(229, 45, 39)"
        });
        setInterval(function() {
            var random = Math.round(Math.random() * 10)
            var values = updatingChart.text().split(",")
            values.shift()
            values.push(random)
            updatingChart
                .text(values.join(","))
                .change()
        }, 1000);
        // [ updating-Chart ] end

        // [ updating1-Chart ] start
        $(document).ready(function() {
            var updatingChart1 = $(".updating-chart1").peity("line", {
                fill: "rgba(44, 169, 97, 0.32)",
                stroke: "rgba(44, 169, 97, 0.90)"
            });
        });
        // [ updating1-Chart ] end

        // [ line-Chart ] start
        var updatingChart2 = $(".updating-chart2").peity("line", {
            fill: "rgba(57, 73, 171, 0.45)",
            stroke: "rgba(57, 73, 171, 0.91)"
        });

        var updatingChart3 = $(".updating-chart3").peity("line", {
            fill: "rgba(245, 124, 0, 0.39)",
            stroke: "rgba(245, 124, 0, 0.94)"
        });

        $(".bar-colours-1").peity("bar", {
            fill: ["rgba(57, 73, 171, 0.65)", "rgba(44, 169, 97, 0.65)"]
        });

        $(".bar-colours-2").peity("bar", {
            fill: ["rgba(57, 73, 171, 0.65)", "rgba(245, 124, 0, 0.65)"]
        });

        // [ Data-Attributes Charts ] start
        $(".data-attributes span").peity("donut");
        $("span.pie_1").peity("pie", {
            fill: ["#e52d27", "#3949AB"]
        });
        $("span.pie_2").peity("pie", {
            fill: ["#f57c00", "#2ca961"]
        });
        $("span.pie_3").peity("pie", {
            fill: ["#3949AB", "#463699"]
        });
        $("span.pie_4").peity("pie", {
            fill: ["#2ca961", "#e52d27"]
        });
        $("span.pie_5").peity("pie", {
            fill: ["#f57c00", "#3949AB"]
        });
        $("span.pie_6").peity("pie", {
            fill: ["#e52d27", "#463699"]
        });
        $("span.pie_7").peity("pie", {
            fill: ["#2ca961", "#f57c00"]
        });

        // [ Pie Charts ] start
        $("span.pie_1").peity("pie", {
            fill: ["#e52d27", "#3949AB"]
        });
        $("span.pie_2").peity("pie", {
            fill: ["#f57c00", "#2ca961"]
        });
        $("span.pie_3").peity("pie", {
            fill: ["#3949AB", "#463699"]
        });
        $("span.pie_4").peity("pie", {
            fill: ["#2ca961", "#e52d27"]
        });
        $("span.pie_5").peity("pie", {
            fill: ["#f57c00", "#3949AB"]
        });
        $("span.pie_6").peity("pie", {
            fill: ["#e52d27", "#463699"]
        });
        $("span.pie_7").peity("pie", {
            fill: ["#2ca961", "#f57c00"]
        });
        // [ line-Chart ] end
    }, 700);
});
