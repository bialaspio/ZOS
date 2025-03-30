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

// Funkcja do obsługi autouzupełniania
function inicjalizujAutocomplete(inputId, url, dodatkoweDane = {}, mapowanie = (item) => item) {
    const input = $(inputId);
    input.autocomplete({
        source: function (request, response) {
            const dane = { ...dodatkoweDane, term: request.term };
            $.ajax({
                url: url,
                type: "POST",
                data: dane,
                success: function (data) {
                    const wyniki = JSON.parse(data).map(mapowanie);
                    response(wyniki);
                },
                error: function () {
                    console.error("Błąd podczas pobierania danych dla autouzupełniania.");
                },
            });
        },
        select: function (event, ui) {
            input.val(ui.item.value);
            console.log(ui.item.value);
        },
        minLength: 0,
    });
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

// Funkcja do inicjalizacji zegara
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

// Inicjalizacja autouzupełniania
inicjalizujAutocomplete("#KodAdrSzamba", "ajax/ajax_KodPocz.php", { Miejscowosc: $("#MiejscowoscAdrSzamba").val() }, (item) => item.kod_pocztowy);
inicjalizujAutocomplete("#MiejscowoscAdrSzamba", "ajax/ajax_Miejscowosc.php", { Kod: $("#KodAdrSzamba").val() }, (item) => item.miejscowosc);
inicjalizujAutocomplete("#UlicaAdrSzamba", "ajax/ajax_Ulice.php", { Kod: $("#KodAdrSzamba").val(), Miejscowosc: $("#MiejscowoscAdrSzamba").val() }, (item) => item.ulica);
inicjalizujAutocomplete("#NrAdrSzamba", "ajax/ajax_Numer.php", { Kod: $("#KodAdrSzamba").val(), Miejscowosc: $("#MiejscowoscAdrSzamba").val(), Ulica: $("#UlicaAdrSzamba").val() }, (item) => item.numer);

// Inicjalizacja zegara
inicjalizujZegar(".czas");

// Funkcja do obsługi tabeli z osobami
async function ListaOsobDoSzamba(id_szamba) {
    try {
        const dane = await pobierzDane("ajax/ajax_pobierz_list_miesz_po_id_szam.php", { id_szamba });
        generujTabeleTabulator("#lista_osob-table", dane, [
            { title: "Imię", field: "imie", headerFilter: "input" },
            { title: "Nazwisko", field: "nazwisko", headerFilter: "input" },
            { title: "Ulica", field: "ulica", headerFilter: "input" },
            { title: "Numer", field: "numer", headerFilter: "input" },
            { title: "Kod pocztowy", field: "kod_pocztowy", headerFilter: "input" },
            { title: "Miejscowość", field: "miejscowosc", headerFilter: "input" },
        ], "download-list-osob");
    } catch (error) {
        console.error("Błąd podczas pobierania danych dla tabeli osób:", error);
    }
}