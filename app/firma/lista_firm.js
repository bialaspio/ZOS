// --- Moduły ---

// --- Filters.js (Przeniesione z tabele_tabul.js) ---
const Filters = {
    minMaxFilterEditor(cell, onRendered, success, cancel, editorParams) {
        var end;
        var container = document.createElement("span");

        //create and style inputs
        var start = document.createElement("input");
        start.setAttribute("type", "number");
        start.setAttribute("placeholder", "Min");
        start.setAttribute("min", 0);
        start.setAttribute("max", 100);
        start.style.padding = "4px";
        start.style.width = "50%";
        start.style.boxSizing = "border-box";

        start.value = cell.getValue();

        function buildValues() {
            success({
                start: start.value,
                end: end.value,
            });
        }

        function keypress(e) {
            if (e.keyCode == 13) {
                buildValues();
            }

            if (e.keyCode == 27) {
                cancel();
            }
        }

        end = start.cloneNode();
        end.setAttribute("placeholder", "Max");

        start.addEventListener("change", buildValues);
        start.addEventListener("blur", buildValues);
        start.addEventListener("keydown", keypress);

        end.addEventListener("change", buildValues);
        end.addEventListener("blur", buildValues);
        end.addEventListener("keydown", keypress);

        container.appendChild(start);
        container.appendChild(end);

        return container;
    },

    minMaxFilterFunction(headerValue, rowValue, rowData, filterParams) {
        if (rowValue) {
            if (headerValue.start != "") {
                if (headerValue.end != "") {
                    return rowValue >= headerValue.start && rowValue <= headerValue.end;
                } else {
                    return rowValue >= headerValue.start;
                }
            } else {
                if (headerValue.end != "") {
                    return rowValue <= headerValue.end;
                }
            }
        }

        return true;
    },
};

// --- Pobieranie ciasteczka po nazwie ---
const Cookie = {
    get(name) {
        const cookies = document.cookie.split("; ");
        for (const cookie of cookies) {
            const [key, value] = cookie.split("=");
            if (key === name) {
                return value;
            }
        }
        return null;
    },
    set(name, value) {
        document.cookie = `${name}=${value}; path=/`;
    },
};

// --- Clock.js 
const Clock = {
    addLeadingZero(number) {
        return number < 10 ? "0" + number : number;
    },

    initialize(selector) {
        const zegarContent = document.querySelector(selector);
        function zegar() {
            const d = new Date();
            const day = Clock.addLeadingZero(d.getDay() - 1);
            const month = Clock.addLeadingZero(d.getMonth() + 1);
            const year = d.getFullYear();
            const hour = Clock.addLeadingZero(d.getHours());
            const minutes = Clock.addLeadingZero(d.getMinutes());
            const seconds = Clock.addLeadingZero(d.getSeconds());
            zegarContent.innerHTML = `${year}.${month}.${day} ${hour}:${minutes}:${seconds}`;
        }
        zegar();
        setInterval(zegar, 1000);
    },
};

// --- Main Logic ---

let table_tmp;

// --- Helper Functions ---

function EdytFirma() {
    const selectedRow = table_tmp.getSelectedRows();
    if (selectedRow.length > 0) {
        for (const obj of selectedRow) {
            const nip = obj.getData().nip;
            Cookie.set("nip", nip);
            window.open("edytuj_firme.php");
        }
    } else {
        customAlert("Najpierw wybierz firmę !");
    }
}

function ListaFirm() {
    const url = 'http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=PG_L_Layers%3APG_L_firmy_adresy&outputFormat=application%2Fjson';

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Błąd HTTP! status: ${response.status}`);
            }
            return response.json();
        })
        .then(dane => {
            if (dane.type === "FeatureCollection") {
                const data_from_FC = dane.features.map(feature => feature.properties);
                table_tmp = new Tabulator("#example-table", {
                    data: data_from_FC,
                    layout: "fitColumns",
                    rowHeight: 20,
                    frozenRows: 0,
                    height: "100%",
                    selectable: true,
                    selectable: 1,
                    pagination: "local",
                    paginationSize: 25,
                    paginationSizeSelector: [25, 50, 75, 100],
                    movableColumns: true,
                    paginationCounter: "rows",
                    rowContextMenu: rowMenu,
                    columns: [
                        { title: "Nazwa", field: "nazwa", headerFilter: "input" },
                        { title: "NIP", field: "nip", headerFilter: "input" },
                        { title: "Adres ", field: "adres", headerFilter: "input" },
                    ],
                });

                // --- Download Handlers ---
                const downloadHandlers = {
                    "download-csv": "csv",
                    "download-json": "json",
                    "download-xlsx": "xlsx",
                    "download-pdf": "pdf",
                    "download-html": "html",
                };

                for (const [buttonId, format] of Object.entries(downloadHandlers)) {
                    document.getElementById(buttonId).addEventListener("click", () => {
                        table_tmp.download(format, `data.${format}`, {
                            sheetName: "My Data",
                            orientation: "portrait",
                            title: "Example Report",
                            style: true,
                        });
                    });
                }
            } else {
                console.error("Nieprawidłowy format danych.");
            }
        })
        .catch(error => {
            console.error("Błąd pobierania danych:", error);
        });
}

// --- Row Context Menu ---

const rowMenu = [
    {
        label: "<i class='fas fa-plus-circle'></i> Dodaj",
        action: function (e, row) {
            window.open("dodaj_firme.php");
        },
    },
    {
        label: "<i class='far fa-edit'></i> Edytuj",
        action: function (e, row) {
            EdytFirma();
        },
    },
    {
        label: "<i class='fas fa-trash'></i> Usuń",
        action: function (e, row) {
            row.delete();
        },
    },
];

// --- Initialization ---

ListaFirm();
Clock.initialize(".czas");
