// Metro from NPM
import '@olton/metroui/lib/metro.js';
// Metro 5.1.3
// import '../metro5.1.3/metro.js';

// DropZone
import Dropzone from 'dropzone';
import "dropzone/dist/dropzone.css";
window.Dropzone = Dropzone;

// Chart.js
import { Chart, registerables } from 'chart.js';
import 'chartjs-adapter-date-fns';
import ChartDataLabels from 'chartjs-plugin-datalabels';
Chart.register(...registerables, ChartDataLabels);
window.Chart = Chart;

// EasyMDE
import EasyMDE from 'easymde';
import 'easymde/dist/easymde.min.css';
window.EasyMDE = EasyMDE;

window.editors = {};
document.addEventListener("DOMContentLoaded", function () {
	document.querySelectorAll('.easymde').forEach(function (textarea) {
	    const instance = new EasyMDE({
	        element: textarea,
	        minHeight: "200px",
	        maxHeight: "200px",
	        status: false,
	        spellChecker: false,
			toggleFullscreen: false,
			sideBySideFullscreen: false,
	    });
        // Stocke l'instance par id pour un accès futur
        if (textarea.id) {
            editors[textarea.id] = instance;
        }
	});
});

// Chart colors
window.chartColors = {
	red: 'rgb(255, 99, 132)',
	orange: 'rgb(255, 159, 64)',
	yellow: 'rgb(255, 205, 86)',
	green: 'rgb(75, 192, 192)',
	blue: 'rgb(54, 162, 235)',
	purple: 'rgb(153, 102, 255)',
	grey: 'rgb(201, 203, 207)'
};
