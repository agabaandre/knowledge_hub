
"use client";
import React, { useEffect, useMemo } from 'react';
import dynamic from 'next/dynamic';
import { ApexOptions } from 'apexcharts';

const ApexCharts = dynamic(() => import('react-apexcharts'), { ssr: false });

const CoursesAnalyticsChart = () => {
    const options: ApexOptions = useMemo(() => ({
        series: [25, 30, 50],
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
        labels: ["Pending", "Processing", "Complete"],
        colors: ["#0dcaf0", "#FFB800", "#07A169"],
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
    }), []); // Empty dependency array means options will be memoized once

    useEffect(() => {
        // Chart initialization logic if needed
        // No need for jQuery, react-apexcharts handles everything.
    }, []);

    return (
        <div id="coursesAnalyticsChart">
            <ApexCharts options={options} series={options.series} type="donut" height={230} />
        </div>
    );
};

export default CoursesAnalyticsChart;
