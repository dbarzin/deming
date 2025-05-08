//import moment from "moment";

// Metro 4.5.12
// import '../metroui45/metro.js';

// Metro 5.0
import '@olton/metroui/lib/metro.js';
// import "@olton/metroui/lib/metro.all.css";

// Metro 5.1
// import '../metroui51/metro.js';

// Metro 5.1 Specifi build
/*
import "@olton/metroui/source/reset/index.js";
import "@olton/metroui/source/runtime.js";

// add common css
import "@olton/metroui/source/common-css/index.js";

// add colors
import "@olton/metroui/source/colors-css/index.js";

// Icons
import "@olton/metroui/source/icons/index.js";

// add components
import "@olton/metroui/source/components/app-bar/index.js";
import "@olton/metroui/source/components/badges/index.js";
import "@olton/metroui/source/components/button/index.js";
import "@olton/metroui/source/components/cloak/index.js";
import "@olton/metroui/source/components/container/index.js";
import "@olton/metroui/source/components/datepicker/index.js";
import "@olton/metroui/source/components/dropdown/index.js";
import "@olton/metroui/source/components/form/index.js";
import "@olton/metroui/source/components/grid/index.js";
import "@olton/metroui/source/components/info-box/index.js";
import "@olton/metroui/source/components/input/index.js";
import "@olton/metroui/source/components/pagination/index.js";
import "@olton/metroui/source/components/navview/index.js";
import "@olton/metroui/source/components/panel/index.js";
import "@olton/metroui/source/components/sidebar/index.js";
import "@olton/metroui/source/components/table/index.js";
import "@olton/metroui/source/components/theme-switcher/index.js";
import "@olton/metroui/source/components/validator/index.js";
*/

//-----------------------------------------------------------
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
