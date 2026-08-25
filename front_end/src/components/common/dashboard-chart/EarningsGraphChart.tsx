"use client";
import React, { useEffect } from 'react';
import dynamic from 'next/dynamic';
import { ApexOptions } from 'apexcharts';

// Dynamically import ApexCharts to prevent SSR issues
const ApexCharts = dynamic(() => import('react-apexcharts'), { ssr: false });

const EarningsGraphChart = () => {
    const options: ApexOptions = {
        series: [
            {
                name: 'Net Profit',
                data: [44, 55, 57, 56, 61, 58, 63, 60, 66],
            },
            {
                name: 'Revenue',
                data: [76, 85, 101, 98, 87, 105, 91, 114, 94],
            },
            {
                name: 'Free Cash Flow',
                data: [35, 41, 36, 26, 45, 48, 52, 53, 41],
            },
        ],
        chart: {
            type: 'bar',
            height: 350,
        },
        colors: ['#07A169', '#FFB800', '#0dcaf0'],
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent'],
        },
        xaxis: {
            categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
        },
        yaxis: {
            title: {
                text: '$ (thousands)',
                style: {
                    color: undefined,
                    fontSize: '14px',
                    fontWeight: 600,
                },
            },
            labels: {
                style: {
                    fontSize: '12px',
                },
            },
        },
        fill: {
            opacity: 1,
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "$ " + val + " thousands";
                },
            },
        },
    };

    useEffect(() => {
        // Any additional initialization if needed.
    }, []);

    return (
        <div id="earningsGraphChart">
            <ApexCharts options={options} series={options.series} type="bar" height={350} />
        </div>
    );
};

export default EarningsGraphChart;
