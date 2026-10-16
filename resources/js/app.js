// Bootstrap
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// jQuery + Select2
import $ from 'jquery';
window.$ = window.jQuery = $;
import select2 from 'select2';
select2($);

// DataTables
import jszip from 'jszip';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.colVis.mjs';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-colreorder-bs5';
DataTable.Buttons.jszip(jszip);
window.DataTable = DataTable;

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

// DataTables defaults
const dtLanguages = {
    fr: {
        decimal: ',',
        emptyTable: 'Aucune donnée disponible',
        info: '_START_ à _END_ sur _TOTAL_ éléments',
        infoEmpty: '0 élément',
        infoFiltered: '(filtré de _MAX_ éléments au total)',
        lengthMenu: '_MENU_ éléments par page',
        loadingRecords: 'Chargement...',
        search: 'Rechercher :',
        zeroRecords: 'Aucun élément correspondant trouvé',
        paginate: { first: '«', last: '»', next: '›', previous: '‹' },
        buttons: { colvis: 'Colonnes', copy: 'Copier', print: 'Imprimer' },
    },
    de: {
        decimal: ',',
        emptyTable: 'Keine Daten vorhanden',
        info: '_START_ bis _END_ von _TOTAL_ Einträgen',
        infoEmpty: '0 Einträge',
        infoFiltered: '(gefiltert von _MAX_ Einträgen)',
        lengthMenu: '_MENU_ Einträge pro Seite',
        loadingRecords: 'Wird geladen...',
        search: 'Suchen:',
        zeroRecords: 'Keine passenden Einträge gefunden',
        paginate: { first: '«', last: '»', next: '›', previous: '‹' },
        buttons: { colvis: 'Spalten', copy: 'Kopieren', print: 'Drucken' },
    },
};
const lang = (document.documentElement.lang || 'en').substring(0, 2);
if (dtLanguages[lang]) {
    DataTable.defaults.language = dtLanguages[lang];
}

/**
 * Initialise une table DataTables.
 * Les options peuvent être passées via les attributs HTML5 data-* supportés
 * nativement par DataTables (data-page-length, data-order, data-searching,
 * data-paging, data-info...) ou via le paramètre options.
 */
function initDataTable(el, options = {}) {
    if (DataTable.isDataTable(el)) {
        return new DataTable(el);
    }
    const title = el.dataset.title || document.title;
    const withButtons = el.dataset.buttons !== 'false';
    return new DataTable(el, Object.assign({
        // Réorganisation des colonnes par glisser-déposer des en-têtes
        colReorder: true,
        // Colonnes masquées par défaut (affichables via le bouton "Colonnes")
        columnDefs: [
            { targets: 'dt-hidden', visible: false },
        ],
        // Mémorise nombre de lignes, tri, ordre et visibilité des colonnes (pas la recherche ni la page)
        stateSave: true,
        stateDuration: 0,
        stateSaveParams: function (settings, data) {
            data.start = 0;
            data.search.search = '';
            (data.columns || []).forEach(c => { if (c.search) c.search.search = ''; });
        },
        autoWidth: false,
        pageLength: 100,
        lengthMenu: [10, 25, 50, 100, 250, 500],
        layout: withButtons ? {
            topStart: ['buttons', 'pageLength'],
        } : undefined,
        buttons: withButtons ? [
            { extend: 'colvis', className: 'btn-sm btn-light' },
            { extend: 'copy', className: 'btn-sm btn-light' },
            { extend: 'csv', className: 'btn-sm btn-light', title },
            { extend: 'excel', className: 'btn-sm btn-light', title },
            { extend: 'print', className: 'btn-sm btn-light', title },
        ] : [],
    }, options));
}
window.initDataTable = initDataTable;

// Les menus des boutons DataTables (ex. "Colonnes") portent la classe
// .dropdown-menu : le gestionnaire clavier global de Bootstrap tente alors d'y
// créer un Dropdown sans bouton déclencheur et lève une erreur (Échap, flèches).
// On intercepte ces touches avant Bootstrap (qui écoute en capture sur document,
// d'où l'écoute en capture sur window) ; DataTables ferme le menu sur keyup.
window.addEventListener('keydown', (e) => {
    if (['Escape', 'ArrowUp', 'ArrowDown'].includes(e.key)
        && e.target instanceof Element && e.target.closest('.dt-button-collection')) {
        e.stopPropagation();
    }
}, true);

/**
 * Initialise Select2 sur un élément (ou une collection jQuery).
 */
function initSelect2(el) {
    $(el).each(function () {
        if ($(this).hasClass('select2-hidden-accessible')) {
            return;
        }
        const tags = $(this).hasClass('select2-free');
        // Effacement possible uniquement si une option vide existe
        const emptyOption = Array.from(this.options).find(o => o.value === '');
        const allowClear = !this.required && !this.multiple && $(this).data('allow-clear') !== false
            && !!emptyOption;
        $(this).select2({
            theme: 'bootstrap-5',
            width: '100%',
            // Le texte de l'option vide ("-- Choisir ... --") sert de placeholder
            placeholder: $(this).data('placeholder') || (emptyOption ? (emptyOption.text.trim() || ' ') : ''),
            allowClear: allowClear,
            tags: tags,
            minimumResultsForSearch: $(this).data('search') === false ? Infinity : 0,
            dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal') : $(document.body),
        });
        // Select2 déclenche un 'change' jQuery uniquement : on le relaie en
        // événement natif pour les écouteurs posés avec addEventListener.
        $(this).on('change', function (e) {
            if (!e.originalEvent) {
                this.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    });
}
window.initSelect2 = initSelect2;

window.editors = {};
document.addEventListener("DOMContentLoaded", function () {
    // EasyMDE
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

    // Select2
    initSelect2('select.select2');

    // DataTables
    document.querySelectorAll('table.datatable').forEach(el => initDataTable(el));

    // Tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
        .forEach(el => new bootstrap.Tooltip(el));

    // Sidebar : bouton d'affichage / masquage
    document.getElementById('toggle-sidebar-btn')
        ?.addEventListener('click', toggleSidebar);

    // Lignes cliquables : <tr data-href="..."> ouvre la page indiquée
    // (Ctrl/Cmd + clic ou clic du milieu : nouvel onglet). Les liens, boutons
    // et champs présents dans la ligne gardent leur propre comportement.
    const openRow = (e) => {
        const row = e.target.closest('tr[data-href]');
        if (!row || e.target.closest('a, button, input, select, textarea, label')) {
            return;
        }
        if (e.type === 'auxclick' && e.button !== 1) {
            return;
        }
        // Ne pas naviguer si l'utilisateur sélectionne du texte
        if (window.getSelection().toString() !== '') {
            return;
        }
        if (e.ctrlKey || e.metaKey || e.type === 'auxclick') {
            window.open(row.dataset.href, '_blank');
        } else {
            window.location.href = row.dataset.href;
        }
    };
    document.addEventListener('click', openRow);
    document.addEventListener('auxclick', openRow);

    // Raccourcis clavier
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.getElementById('btn-cancel')?.click();
        }
    });
});

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content-home');
    sidebar.classList.toggle('sidebar-hidden');
    content.classList.toggle('content-expanded');
    try {
        localStorage.setItem('sidebar_hidden', sidebar.classList.contains('sidebar-hidden'));
    } catch (_) {
        // Storage indisponible
    }
}
window.toggleSidebar = toggleSidebar;

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

// Maximize the font contrast of the text with the given background color
function getContrastColor(hexColor) {
    // Convertit #rrggbb en composantes 0-255
    const r = parseInt(hexColor.slice(1, 3), 16);
    const g = parseInt(hexColor.slice(3, 5), 16);
    const b = parseInt(hexColor.slice(5, 7), 16);

    // Linéarisation sRGB (WCAG 2.1)
    const toLinear = c => {
        const s = c / 255;
        return s <= 0.04045 ? s / 12.92 : Math.pow((s + 0.055) / 1.055, 2.4);
    };

    const L = 0.2126 * toLinear(r)
        + 0.7152 * toLinear(g)
        + 0.0722 * toLinear(b);

    return L > 0.8 ? '#2d2d2d' : '#ffffff';
    // return L > 0.179 ? '#000000' : '#ffffff';
}

window.getContrastColor = getContrastColor;
