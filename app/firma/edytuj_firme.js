// --- Stałe i funkcje pomocnicze ---

/**
 * Dodaje zero wiodące do liczb mniejszych niż 10.
 * @param {number} number - Liczba do sformatowania.
 * @returns {string} Sformatowana liczba.
 */
function addLeadingZero(number) {
    return number < 10 ? "0" + number : number;
}

/**
 * Inicjalizuje pole autouzupełniania jQuery UI.
 * @param {string} inputSelector - Selektor CSS dla pola wejściowego.
 * @param {string} url - URL dla żądania AJAX.
 * @param {function} requestDataCallback - Funkcja formatująca dane żądania.
 * @param {function} responseDataCallback - Funkcja formatująca dane odpowiedzi.
 */
function initializeAutocomplete(inputSelector, url, requestDataCallback, responseDataCallback) {
    $(inputSelector).autocomplete({
        source: function (request, response) {
            $.ajax({
                url: url,
                type: "POST",
                data: requestDataCallback(request),
                success: function (data) {
                    const parsedData = JSON.parse(data);
                    response(responseDataCallback(parsedData));
                },
                error: function (error) {
                    console.error("Błąd autouzupełniania:", error);
                },
            });
        },
        select: function (event, ui) {
            $(inputSelector).val(ui.item.value);
        },
        minLength: 1,
    });
}

const ERROR_MESSAGES = {
    MISSING_COMPANY_NAME: "Nazwa firmy jest wymagana!",
    INVALID_NIP: "NIP powinien składać się z 10 cyfr!",
    MISSING_STREET: "Ulica jest wymagana!",
    MISSING_NUMBER: "Numer jest wymagany!",
    INVALID_POSTAL_CODE: "Kod pocztowy powinien być w formacie XX-XXX!",
    MISSING_CITY: "Miasto jest wymagane!",
    INVALID_EMAIL: "Podaj prawidłowy adres e-mail!",
    INVALID_PHONE: "Numer telefonu powinien składać się z 9 cyfr!",
    AJAX_ERROR: "Nie udało się zapisać zmian!",
    AJAX_DATA_ERROR: "Nie udało się danych dodać do bazy.",
};

const VALIDATION_RULES = {
    nip: /^[0-9]{10}$/,
    postalCode: /^[0-9]{2}-[0-9]{3}$/,
    email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
    phone: /^[0-9]{9}$/,
};

/**
 * Waliduje pole na podstawie podanej reguły.
 * @param {string} value - Wartość do walidacji.
 * @param {RegExp} rule - Reguła walidacji (wyrażenie regularne).
 * @param {string} errorMessage - Komunikat błędu do wyświetlenia w przypadku niepowodzenia.
 * @returns {boolean} - True, jeśli poprawne, false w przeciwnym razie.
 */
function validateField(value, rule, errorMessage) {
    if (!rule.test(value)) {
        customAlert(errorMessage);
        return false;
    }
    return true;
}

/**
 * Waliduje wymagane pole.
 * @param {string} value - Wartość do walidacji.
 * @param {string} errorMessage - Komunikat błędu do wyświetlenia w przypadku niepowodzenia.
 * @returns {boolean} - True, jeśli poprawne, false w przeciwnym razie.
 */
function validateRequiredField(value, errorMessage) {
    if (!value) {
        customAlert(errorMessage);
        return false;
    }
    return true;
}

// --- Główna logika ---

/**
 * Dodaje lub aktualizuje dane firmy.
 */
function addOrUpdateCompany() {
    const companyData = {
        companyName: document.getElementById("nazFirmy").value,
        nip: document.getElementById("nazNIP").value,
        street: document.getElementById("nazUlic").value,
        number: document.getElementById("nazNr").value,
        postalCode: document.getElementById("nazKod").value,
        city: document.getElementById("nazMiejs").value,
        isDifferentAddress: document.getElementById("chkInnyAdrKor").value,
        differentAddress: document.getElementById("InnyAdrKor").value,
        additionalInfo: document.getElementById("nazDodInfo").value,
        email: document.getElementById("nazEmailFir").value,
        phoneNumber: document.getElementById("nazTelFir").value,
    };

    // --- Walidacja ---

    if (!validateRequiredField(companyData.companyName, ERROR_MESSAGES.MISSING_COMPANY_NAME)) return;
    if (!validateField(companyData.nip, VALIDATION_RULES.nip, ERROR_MESSAGES.INVALID_NIP)) return;
    if (!validateRequiredField(companyData.street, ERROR_MESSAGES.MISSING_STREET)) return;
    if (!validateRequiredField(companyData.number, ERROR_MESSAGES.MISSING_NUMBER)) return;
    if (!validateField(companyData.postalCode, VALIDATION_RULES.postalCode, ERROR_MESSAGES.INVALID_POSTAL_CODE)) return;
    if (!validateRequiredField(companyData.city, ERROR_MESSAGES.MISSING_CITY)) return;

    if (companyData.email && !validateField(companyData.email, VALIDATION_RULES.email, ERROR_MESSAGES.INVALID_EMAIL)) return;
    if (companyData.phoneNumber && !validateField(companyData.phoneNumber, VALIDATION_RULES.phone, ERROR_MESSAGES.INVALID_PHONE)) return;

    // --- Żądanie AJAX ---

    $.ajax({
        url: "ajax/ajax_edytuj_firme.php",
        type: "POST",
        data: companyData,
    })
        .done(function (response) {
            const data = response.replace(/\r?\n/g, "");
            if (data === "Error" || data === "false") {
                customAlert(ERROR_MESSAGES.AJAX_DATA_ERROR);
            } else if (data === "true") {
                customAlert("Dane dodane do bazy.");
                window.close();
            } else {
                customAlert("Błąd ajax-a.");
            }
        })
        .fail(function (xhr, status, error) {
            customAlert(ERROR_MESSAGES.AJAX_ERROR);
            console.error("Błąd:", error);
        });
}

// --- Inicjalizacja autouzupełniania ---

// Autouzupełnianie dla kodu pocztowego
initializeAutocomplete(
    "#nazKod",
    "ajax/ajax_KodPocz.php",
    (request) => ({ KodPocz: request.term, Miejscowosc: $("#nazMiejs").val() }),
    (data) => data.map((item) => item.kod_pocztowy)
);

// Autouzupełnianie dla miejscowości
initializeAutocomplete(
    "#nazMiejs",
    "ajax/ajax_Miejscowosc.php",
    (request) => ({ Miejscowosc: request.term, Kod: $("#nazKod").val() }),
    (data) => data.map((item) => item.miejscowosc)
);

// Autouzupełnianie dla ulicy
initializeAutocomplete(
    "#nazUlic",
    "ajax/ajax_Ulice.php",
    (request) => ({ Ulica: request.term, Miejscowosc: $("#nazMiejs").val(), Kod: $("#nazKod").val() }),
    (data) => data.map((item) => item.ulica)
);

// Autouzupełnianie dla numeru
initializeAutocomplete(
    "#nazNr",
    "ajax/ajax_Numer.php",
    (request) => ({
        Numer: request.term,
        Ulica: $("#nazUlic").val(),
        Miejscowosc: $("#nazMiejs").val(),
        Kod: $("#nazKod").val(),
    }),
    (data) => data.map((item) => item.numer)
);

// --- Zdarzenia ---

// Przypisanie zdarzenia do przycisku
const buttonSaveEl = document.querySelector(".butZapisz");
buttonSaveEl.addEventListener("click", addOrUpdateCompany);

// --- Zegar ---

const clockContent = document.querySelector(".czas");

function clock() {
    const d = new Date();
    const day = addLeadingZero(d.getDate());
    const month = addLeadingZero(d.getMonth() + 1);
    const year = d.getFullYear();
    const hour = addLeadingZero(d.getHours());
    const minutes = addLeadingZero(d.getMinutes());
    const seconds = addLeadingZero(d.getSeconds());

    clockContent.innerHTML = `${year}.${month}.${day} ${hour}:${minutes}:${seconds}`;
}

// Uruchomienie zegara
clock();
setInterval(clock, 1000);

// --- Pobieranie danych firmy do edycji ---
async function loadCompanyData() {
    const nip = pobierzCiasteczko("nip");
    console.log({ nip });
    if (!nip) {
        console.error("Brak identyfikatora NIP w ciasteczkach.");
        return;
    }
    try {
        const dane = await pobierzDane("ajax/ajax_pobierz_dane_firmy_po_nip.php", { nip: nip });
        if (dane.length > 0) {
            document.getElementById("nazFirmy").value = dane[0].nazwa;
            document.getElementById("nazNIP").value = dane[0].nip;
            document.getElementById("nazUlic").value = dane[0].ulica;
            document.getElementById("nazNr").value = dane[0].numer;
            document.getElementById("nazKod").value = dane[0].kod_pocztowy;
            document.getElementById("nazMiejs").value = dane[0].miejscowosc;
            document.getElementById("nazDodInfo").value = dane[0].dod_info;
            document.getElementById("nazEmailFir").value = dane[0].email;
            document.getElementById("nazTelFir").value = dane[0].nr_tel;
        } else {
            console.log("Brak danych dla firmy o podanym NIP.");
        }
    } catch (error) {
        console.error("Błąd pobierania danych firmy:", error);
    }
}

// --- Pobieranie ciasteczka po nazwie ---
function pobierzCiasteczko(nazwa) {
    const ciasteczka = document.cookie.split("; ");
    for (let i = 0; i < ciasteczka.length; i++) {
        const para = ciasteczka[i].split("=");
        if (para[0] === nazwa) {
            return para[1];
        }
    }
    return null;
}

// --- Pobieranie danych ---
async function pobierzDane(url, dane = {}) {
    const response = await fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams(dane),
    });
    if (!response.ok) {
        throw new Error(`Błąd HTTP! status: ${response.status}`);
    }
    return await response.json();
}

loadCompanyData();