"use client";
import React, { useEffect } from 'react';
import dynamic from 'next/dynamic';
import { ApexOptions } from 'apexcharts';

// Dynamically import ApexCharts to prevent SSR issues
const ApexCharts = dynamic(() => import('react-apexcharts'), { ssr: false });

const QuizMarkChart = () => {
    const options: ApexOptions = {
        series: [30, 30],
        chart: {
            type: 'donut',  // Donut chart type
            width: 320,
        },
        stroke: {
            show: true,
            width: 0,
        },
        dataLabels: {
            style: {
                colors: ["#fff"],
                fontSize: '12px',
                fontWeight: 600,
            },
        },
        plotOptions: {
            pie: {
                startAngle: 0,
                endAngle: 360,
                expandOnClick: true,
                customScale: 1,
                donut: {
                    size: '55%',
                    background: '#f7f7f7',
                },
            }
        },
        labels: ["Complete Quiz", "Incomplete Quiz"],
        colors: ["#07A169", "#FFB800"],
        legend: {
            show: true,
            position: 'bottom',
            fontSize: '14px',
            fontWeight: 400,
            labels: {
                colors: ["#7C7C7C"],
            },
            markers: {
                strokeWidth: 0,
                offsetX: -2,
                offsetY: 0,
            },
            itemMargin: {
                horizontal: 5,
                vertical: 0
            },
            onItemClick: {
                toggleDataSeries: true
            },
            onItemHover: {
                highlightDataSeries: true
            },
        },
    };

    useEffect(() => {
        // The chart rendering will happen here after the component is mounted.
        // Use react-apexcharts component to render the chart.
    }, []);

    return (
        <div id="quizMarkChart">
            {/* Render ApexChart with the specified options */}
            <ApexCharts options={options} series={options.series} type="donut" height={230} />
        </div>
    );
};

export default QuizMarkChart;
