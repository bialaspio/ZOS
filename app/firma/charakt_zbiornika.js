// Funkcja do pobierania konkretnego ciasteczka po nazwie
function pobierzCiasteczko(nazwa) {
    const ciasteczka = document.cookie.split("; ");
    for (const ciasteczko of ciasteczka) {
        const [klucz, wartosc] = ciasteczko.split("=");
        if (klucz === nazwa) {
            return wartosc;
        }
    }
    return null;
}

// Funkcja do generowania tabeli Tabulator
function generujTabeleTabulator(tabelaId, dane, kolumny, przyciskiId) {
    const tabela = new Tabulator(tabelaId, {
        data: dane,
        layout: "fitColumns",
        rowHeight: 20,
        height: "100%",
        selectable: true,
        pagination: "local",
        paginationSize: 25,
        paginationSizeSelector: [25, 50, 75, 100],
        movableColumns: true,
        paginationCounter: "rows",
        columns: kolumny,
    });

    // Obsługa przycisków do pobierania danych
    const formaty = ["csv", "json", "xlsx", "pdf", "html"];
    formaty.forEach((format) => {
        document
            .getElementById(`${przyciskiId}-${format}`)
            .addEventListener("click", function () {
                tabela.download(format, `data.${format}`, {
                    sheetName: "My Data",
                    orientation: "portrait",
                    title: "Example Report",
                    style: true,
                });
            });
    });
}

// Funkcja do pobierania danych z serwera
async function pobierzDane(url, daneWejsciowe) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            type: "POST",
            data: daneWejsciowe,
            success: function (data) {
                resolve(JSON.parse(data));
            },
            error: function (error) {
                reject(error);
            },
        });
    });
}

// Funkcja do generowania tabeli z historią wywozów
async function RenderHistWyw() {
    const id_szamba = pobierzCiasteczko("id_szamba");
    if (!id_szamba) {
        console.error("Brak identyfikatora szamba w ciasteczkach.");
        return;
    }

    const url = `http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=1.0.0&request=GetFeature&CQL_FILTER=id_szamba=${id_szamba}&typeName=PG_L_Layers%3APG_L_hist_wywozu&outputFormat=application%2Fjson`;

    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error("Błąd podczas pobierania danych z serwera.");
        }

        const dane = await response.json();
        if (dane.type === "FeatureCollection") {
            const data_from_FC = dane.features.map((feature) => feature.properties);

            generujTabeleTabulator(
                "#hist_wyw-table",
                data_from_FC,
                [
                    { title: "Nr protokołu", field: "nr_prot", headerFilter: "input" },
                    { title: "Firma", field: "firma", headerFilter: "input" },
                    { title: "Adres", field: "adres", headerFilter: "input" },
                    { title: "Płatność", field: "platnosc", headerFilter: "input" },
                    { title: "Data wywozu", field: "data_wywozu", headerFilter: "input" },
                    { title: "Ilość ścieków", field: "ilosc_sciekow", headerFilter: "input" },
                    { title: "Oczyszczalnia", field: "nazwa_oczysz", headerFilter: "input" },
                ],
                "download"
            );
        } else {
            console.error("Nieprawidłowy format danych.");
        }
    } catch (error) {
        console.error("Błąd podczas pobierania danych:", error);
    }
}

// Funkcja do generowania tabeli z listą osób
async function ListaOsobDoSzamba() {
    const id_szamba = pobierzCiasteczko("id_szamba");
    if (!id_szamba) {
        console.error("Brak identyfikatora szamba w ciasteczkach.");
        return;
    }

    try {
        const dane = await pobierzDane("ajax/ajax_pobierz_list_miesz_po_id_szam.php", { id_szamba });

        generujTabeleTabulator(
            "#lista_osob-table",
            dane,
            [
                { title: "Imię", field: "imie", headerFilter: "input" },
                { title: "Nazwisko", field: "nazwisko", headerFilter: "input" },
                { title: "Ulica", field: "ulica", headerFilter: "input" },
                { title: "Numer", field: "numer", headerFilter: "input" },
                { title: "Kod pocztowy", field: "kod_pocztowy", headerFilter: "input" },
                { title: "Miejscowość", field: "miejscowosc", headerFilter: "input" },
                { title: "Właściciel", field: "wlasciciel", visible: false },
            ],
            "download-list-osob"
        );
    } catch (error) {
        console.error("Błąd podczas pobierania danych dla tabeli osób:", error);
    }
}

// Funkcja zegara
function inicjalizujZegar(selector) {
    const zegarContent = document.querySelector(selector);
    function zegar() {
        const d = new Date();
        const year = d.getFullYear();
        const month = addLeadingZero(d.getMonth() + 1);
        const day = addLeadingZero(d.getDate());
        const hour = addLeadingZero(d.getHours());
        const minutes = addLeadingZero(d.getMinutes());
        const seconds = addLeadingZero(d.getSeconds());
        zegarContent.innerHTML = `${year}.${month}.${day} ${hour}:${minutes}:${seconds}`;
    }
    zegar();
    setInterval(zegar, 1000);
}

// Dodanie zera wiodącego
function addLeadingZero(number) {
    return number < 10 ? "0" + number : number;
}

// Inicjalizacja
inicjalizujZegar(".czas");
RenderHistWyw();
ListaOsobDoSzamba();