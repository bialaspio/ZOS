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

// Funkcja do generowania tabeli z osobami zameldowanymi
function generujTabeleOsob(url, tabelaId, przyciskiId, kolumny) {
    const id_budynku = pobierzCiasteczko("id_budynku");
    if (!id_budynku) {
        console.error("Brak identyfikatora budynku w ciasteczkach.");
        return;
    }

    $.ajax({
        url: url,
        type: "POST",
        data: { id_budynku },
        success: function (dane) {
            const daneOsob = JSON.parse(dane);

            const tabela = new Tabulator(tabelaId, {
                data: daneOsob,
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
        },
        error: function () {
            console.error("Błąd podczas pobierania danych z serwera.");
        },
    });
}

// Generowanie tabeli dla osób zameldowanych
function ListaOsobZameldowanych() {
    generujTabeleOsob(
        "ajax/ajax_pobierz_list_osob_zameldowanych.php",
        "#lista_osob_zameldowanych-table",
        "download-lista_osob_zameldowanych",
        [
            { title: "Właściciel", field: "wlasciciel", visible: false },
            { title: "Imię", field: "imie", headerFilter: "input" },
            { title: "Nazwisko", field: "nazwisko", headerFilter: "input" },
            { title: "Ulica", field: "ulica", headerFilter: "input" },
            { title: "Numer", field: "numer", headerFilter: "input" },
            { title: "Kod pocztowy", field: "kod_pocztowy", headerFilter: "input" },
            { title: "Miejscowość", field: "miejscowosc", headerFilter: "input" },
        ]
    );
}

// Generowanie tabeli dla osób zamieszkałych
function ListaOsobZamieszkalych() {
    generujTabeleOsob(
        "ajax/ajax_pobierz_list_osob_zamieszkalych.php",
        "#lista_osob_zamieszkalych-table",
        "download-lista_osob_zamieszkalych",
        [
            { title: "Imię", field: "imie", headerFilter: "input" },
            { title: "Nazwisko", field: "nazwisko", headerFilter: "input" },
            { title: "Ulica", field: "ulica", headerFilter: "input" },
            { title: "Numer", field: "numer", headerFilter: "input" },
            { title: "Kod pocztowy", field: "kod_pocztowy", headerFilter: "input" },
            { title: "Miejscowość", field: "miejscowosc", headerFilter: "input" },
        ]
    );
}

// Funkcja zegara
function zegar() {
    const zegarContent = document.querySelector(".czas");
    const d = new Date();
    const year = d.getFullYear();
    const month = addLeadingZero(d.getMonth() + 1);
    const day = addLeadingZero(d.getDate());
    const hour = addLeadingZero(d.getHours());
    const minutes = addLeadingZero(d.getMinutes());
    const seconds = addLeadingZero(d.getSeconds());

    zegarContent.innerHTML = `${year}.${month}.${day} ${hour}:${minutes}:${seconds}`;
}

// Dodanie zera wiodącego
function addLeadingZero(number) {
    return number < 10 ? "0" + number : number;
}

// Inicjalizacja zegara
zegar();
setInterval(zegar, 1000);

// Inicjalizacja tabel
ListaOsobZameldowanych();
ListaOsobZamieszkalych();