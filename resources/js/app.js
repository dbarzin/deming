
import moment from "moment";

// Metro 5
// import '@olton/metroui/lib/metro.js';
// import "@olton/metroui/lib/metro.all.css";

// Metro 4.5.12
import '../metroui/metro.js';

// DropZone
import Dropzone from 'dropzone';
import "dropzone/dist/dropzone.css";
window.Dropzone = Dropzone;

// Chart.js
import { Chart, registerables } from 'chart.js';
import 'chartjs-adapter-date-fns';
Chart.register(...registerables);
window.Chart = Chart;

// EasyMDE
import EasyMDE from 'easymde';
window.EasyMDE = EasyMDE;

window.chartColors = {
	red: 'rgb(255, 99, 132)',
	orange: 'rgb(255, 159, 64)',
	yellow: 'rgb(255, 205, 86)',
	green: 'rgb(75, 192, 192)',
	blue: 'rgb(54, 162, 235)',
	purple: 'rgb(153, 102, 255)',
	grey: 'rgb(201, 203, 207)'
};
