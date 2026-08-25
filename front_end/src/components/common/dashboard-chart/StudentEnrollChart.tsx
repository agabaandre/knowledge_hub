"use client"
import React from 'react';
import dynamic from 'next/dynamic';
import { ApexOptions } from 'apexcharts';

const ApexCharts = dynamic(() => import('react-apexcharts'), { ssr: false });

const StudentEnrollChart = () => {
    const options: ApexOptions = {
        series: [{
            name: 'Student Batch 1',
            data: [31, 60, 50, 71, 55, 90, 100]
        }, {
            name: 'Student Batch 2',
            data: [30, 65, 40, 60, 45, 100, 90]
        }],
        chart: {
            height: 350,
            type: 'area', // This is now typed correctly
        },
        colors: ["#07A169", "#FFB800"],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            categories: ["Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct"],
            offsetY: 0,
            offsetX: 5,
            labels: {
                style: {
                    fontSize: '14px',
                    fontWeight: 400,
                    cssClass: 'apexcharts-xaxis-label',
                },
            },
            axisBorder: {
                show: false
            },
        },
        yaxis: {
            labels: {
                style: {
                    fontSize: '14px',
                    fontWeight: 100,
                },
                formatter: function (y: number) {
                    return y.toFixed(0) + "k";
                }
            },
        },
        tooltip: {
            x: {
                format: 'dd/MM/yy HH:mm'
            },
        },
    };

    return (
        <div id="studentEnrollChart">
            <ApexCharts options={options} series={options.series} type="area" height={350} />
        </div>
    );
};

export default StudentEnrollChart;
