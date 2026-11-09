import { useEffect, useRef } from 'react';
import Highcharts from 'highcharts';
import 'highcharts/modules/map';
import africaTopology from '@highcharts/map-collection/custom/africa.topo.json';

/**
 * Reusable Highcharts wrapper with the hub's chart conventions baked in:
 * no credits, brand green marks, one axis, recessive grid, tooltips on,
 * thin rounded bars. Pass regular Highcharts `options`; they merge over
 * these defaults. Use <AfricaMapChart> for member-state choropleths.
 */
Highcharts.setOptions({
  credits: { enabled: false },
  colors: ['#006239'],
  chart: { style: { fontFamily: 'inherit' }, backgroundColor: 'transparent', spacing: [8, 8, 8, 8] },
  title: { style: { fontSize: '0.95rem', fontWeight: '700', color: '#18181b' } },
  xAxis: { lineColor: '#e4e4e7', tickColor: '#e4e4e7', labels: { style: { color: '#71717a', fontSize: '0.72rem' } } },
  yAxis: { gridLineColor: '#f1f3f5', title: { text: null }, labels: { style: { color: '#71717a', fontSize: '0.72rem' } } },
  legend: { itemStyle: { color: '#3f3f46', fontWeight: '500', fontSize: '0.78rem' } },
  tooltip: { backgroundColor: '#ffffff', borderColor: '#e4e4e7', style: { color: '#18181b', fontSize: '0.8rem' } },
  plotOptions: {
    column: { maxPointWidth: 26, borderRadius: 3, borderWidth: 0 },
    bar: { maxPointWidth: 22, borderRadius: 3, borderWidth: 0 },
    line: { lineWidth: 2, marker: { radius: 3, states: { hover: { radius: 5 } } } },
    series: { animation: { duration: 300 } },
  },
  accessibility: { enabled: false },
});

const useHighchart = (factory, dependencies) => {
  const hostRef = useRef(null);
  useEffect(() => {
    if (!hostRef.current) return undefined;
    const chart = factory(hostRef.current);
    return () => chart.destroy();
  }, dependencies); // eslint-disable-line react-hooks/exhaustive-deps
  return hostRef;
};

export const Chart = ({ options, height = 260 }) => {
  const hostRef = useHighchart(
    (host) => Highcharts.chart(host, { chart: { height, ...options.chart }, ...options }),
    [JSON.stringify(options), height],
  );
  return <div ref={hostRef} />;
};

/** Africa choropleth: sequential brand-green ramp over the member states. */
export const AfricaMapChart = ({ title, data, seriesName, height = 480 }) => {
  const hostRef = useHighchart(
    (host) => Highcharts.mapChart(host, {
      chart: { map: africaTopology, height },
      title: { text: title ?? null },
      mapNavigation: { enabled: true, buttonOptions: { verticalAlign: 'bottom' } },
      colorAxis: { min: 0, minColor: '#e8f5ec', maxColor: '#00492b', labels: { style: { color: '#71717a' } } },
      legend: { layout: 'horizontal', align: 'center', verticalAlign: 'bottom' },
      series: [{
        name: seriesName,
        data, // [['ng', 12], …] — hc-key is the lowercase ISO code
        borderColor: '#ffffff',
        borderWidth: 0.6,
        nullColor: '#f4f4f5',
        states: { hover: { color: '#9f2241' } },
        dataLabels: { enabled: false },
        tooltip: { pointFormat: '{point.name}: <b>{point.value}</b>' },
      }],
    }),
    [JSON.stringify(data), seriesName, title, height],
  );
  return <div ref={hostRef} />;
};
