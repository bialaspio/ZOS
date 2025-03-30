// --- Modules ---

// --- Ajax.js ---
const Ajax = {
  async fetchData(url, data) {
    const response = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams(data),
    });
    if (!response.ok) {
      throw new Error(`Błąd HTTP! status: ${response.status}`);
    }
    return await response.json();
  },
};

// --- Autocomplete.js ---
const Autocomplete = {
  initialize(inputSelector, url, requestDataCallback, responseDataCallback) {
    $(inputSelector).autocomplete({
      source: async function (request, response) {
        try {
          const data = await Ajax.fetchData(url, requestDataCallback(request));
          response(responseDataCallback(data));
        } catch (error) {
          console.error("Autocomplete error:", error);
          response([]);
        }
      },
      select: function (event, ui) {
        $(inputSelector).val(ui.item.value);
        if (inputSelector === DOM_SELECTORS.IMIE) {
          $(DOM_SELECTORS.NAZWISKO).val(ui.item.nazwisko);
          $(DOM_SELECTORS.ULICA_ADR_WLSZAMBA).val(ui.item.ulica);
          $(DOM_SELECTORS.NR_ADR_WLSZAMBA).val(ui.item.numer);
          $(DOM_SELECTORS.KOD_ADR_WLSZAMBA).val(ui.item.kod_pocztowy);
          $(DOM_SELECTORS.MIEJSCOWOSC_ADR_WLSZAMBA).val(ui.item.miejscowosc);
          id_osoby_wlasciciel = ui.item.id_osoby;
        }
        event.preventDefault();
      },
      minLength: 0,
    });
  },
};

// --- DynamicFields.js ---
const DynamicFields = {
  createInputFieldWithDeleteButton(containerSelector, inputClass, inputPlaceholder, inputIdPrefix, buttonIdPrefix, initAutocomplete = null) {
    let idCounter = 0;
    return function () {
      event.preventDefault();
      idCounter++;

      const newDiv = document.createElement('div');
      newDiv.className = 'inputContainer';

      const newInput = document.createElement('input');
      newInput.type = 'text';
      newInput.className = inputClass + ' form-control basicAutoComplete dynamic-field';
      newInput.placeholder = inputPlaceholder;
      newInput.style.marginTop = "10px";
      newInput.id = inputIdPrefix + '_' + idCounter;

      const buttonMinus = document.createElement('button');
      buttonMinus.className = 'button-minus';
      buttonMinus.style.marginLeft = "10px";
      buttonMinus.style.marginTop = "10px";
      buttonMinus.id = buttonIdPrefix + '_' + idCounter;

      const iconImage = document.createElement('img');
      iconImage.src = 'kosz20x20.png';
      iconImage.alt = 'Usuń';
      buttonMinus.appendChild(iconImage);

      newDiv.appendChild(newInput);
      newDiv.appendChild(buttonMinus);

      document.querySelector(containerSelector).insertBefore(newDiv, document.querySelector(containerSelector).lastChild);

      if (initAutocomplete) {
        initAutocomplete(newInput.id);
      }

      if (idCounter > 1){
        let idCounMin1 = idCounter-1;
        let butt_prev = document.getElementById(buttonIdPrefix + '_' + idCounMin1);
        butt_prev.disabled = true
      }

      buttonMinus.addEventListener('click', function () {
        newDiv.parentNode.removeChild(newDiv);
        idCounter--;
        let butt_prev = document.getElementById(buttonIdPrefix + '_' + idCounter);
        butt_prev.disabled = false;
      });
    };
  },
};

// --- Filters.js ---
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

// --- Clock.js ---
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

// --- Cookie.js ---
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
};

// --- Alerts.js ---
const Alerts = {
  customAlert(message) {
    alert(message);
  },
};

// --- Constants and Configuration ---

const API_URLS = {
  DANE_OSOBA_ADRES_PO_IMIE: "ajax/ajax_daneOsoAdrePoImie.php",
  ULICE: "ajax/ajax_Ulice.php",
  NUMER: "ajax/ajax_Numer.php",
  KOD_POCZ: "ajax/ajax_KodPocz.php",
  MIEJSCOWOSC: "ajax/ajax_Miejscowosc.php",
  OSOBA_KON: "ajax/ajax_Osoba_kon.php",
  POBIERZ_OSOBY_DO_SZAMBO: "ajax/ajax_pobierz_osoby_do_szambo.php",
};

const DOM_SELECTORS = {
  IMIE: "#Imie",
  NAZWISKO: "#Nazwisko",
  ULICA_ADR_NIER: "#UlicaAdrNier",
  NR_ADR_NIER: "#NrAdrNier",
  KOD_ADR_NIER: "#KodAdrNier",
  MIEJSCOWOSC_ADR_NIER: "#MiejscowoscAdrNier",
  OSOBA_KONTR: "#texOsobaKontr",
  DIV_OSOBA_KONTROLUJACA: ".divOsobyKontrolujace",
  DIV_OSOBA_BIOR_UW_KONTR: ".divDivOsobyBiorUwKontr",
  DIV_OKAZANE_DOKUMENTY: ".divDivOkazaneDokumenty",
  ADD_INPUT: "#addInput",
  ADD_INPUT_OBUWK: "#addInputOBUWK",
  ADD_INPUT_DOK: "#addInputDok",
  EXAMPLE_TABLE_LISTA_OSOB: "#example-table-lista-osob",
  DIV_ADR_NIER: '.divAdrNier',
};

// --- Autocomplete Initialization ---

// Initialize Autocomplete for various fields
Autocomplete.initialize(
  DOM_SELECTORS.IMIE,
  API_URLS.DANE_OSOBA_ADRES_PO_IMIE,
  (request) => ({ Imie: request.term }),
  (data) => data.map((osoba) => ({
    label: `${osoba.imie} ${osoba.nazwisko} Ul.${osoba.ulica} ${osoba.numer} ${osoba.kod_pocztowy} ${osoba.miejscowosc}`,
    value: osoba.imie,
    nazwisko: osoba.nazwisko,
    ulica: osoba.ulica,
    numer: osoba.numer,
    kod_pocztowy: osoba.kod_pocztowy,
    miejscowosc: osoba.miejscowosc,
    id_osoby: osoba.id_osoby,
  }))
);

Autocomplete.initialize(
  DOM_SELECTORS.ULICA_ADR_NIER,
  API_URLS.ULICE,
  (request) => ({ Ulica: request.term, Miejscowosc: $(DOM_SELECTORS.MIEJSCOWOSC_ADR_NIER).val(), Kod: $(DOM_SELECTORS.KOD_ADR_NIER).val() }),
  (data) => data.map((item) => item.ulica)
);

Autocomplete.initialize(
  DOM_SELECTORS.NR_ADR_NIER,
  API_URLS.NUMER,
  (request) => ({ Numer: request.term, Ulica: $(DOM_SELECTORS.ULICA_ADR_NIER).val(), Miejscowosc: $(DOM_SELECTORS.MIEJSCOWOSC_ADR_NIER).val(), Kod: $(DOM_SELECTORS.KOD_ADR_NIER).val() }),
  (data) => data.map((item) => item.numer)
);

Autocomplete.initialize(
  DOM_SELECTORS.KOD_ADR_NIER,
  API_URLS.KOD_POCZ,
  (request) => ({ KodPocz: request.term, Miejscowosc: $(DOM_SELECTORS.MIEJSCOWOSC_ADR_NIER).val() }),
  (data) => data.map((item) => item.kod_pocztowy)
);

Autocomplete.initialize(
  DOM_SELECTORS.MIEJSCOWOSC_ADR_NIER,
  API_URLS.MIEJSCOWOSC,
  (request) => ({ Miejscowosc: request.term, Kod: $(DOM_SELECTORS.KOD_ADR_NIER).val() }),
  (data) => data.map((item) => item.miejscowosc)
);

Autocomplete.initialize(
  DOM_SELECTORS.OSOBA_KONTR,
  API_URLS.OSOBA_KON,
  (request) => ({ ImNaz: request.term }),
  (data) => data.map((osoba) => ({
    label: osoba.imnaz,
    value: osoba.imnaz,
    id_oso_kon: osoba.id_oso_kon,
  }))
);

// --- Dynamic Input Fields ---

// Create functions for adding new input fields
const addOsobaKontrolujaca = DynamicFields.createInputFieldWithDeleteButton(DOM_SELECTORS.DIV_OSOBA_KONTROLUJACA, 'textOsobyKontrolujace', 'Osoba kontrolująca.', 'texOsobaKontr', 'butOsobaKontr', Autocomplete.initialize);
const addOsobaBiorUwKontr = DynamicFields.createInputFieldWithDeleteButton(DOM_SELECTORS.DIV_OSOBA_BIOR_UW_KONTR, 'textOsobyBUWK', 'Osoba biorąca udział w kontroli.', 'texOsobaBiorUwKontr', 'butOsobaBiorUwKontr');
const addOkazaneDokumenty = DynamicFields.createInputFieldWithDeleteButton(DOM_SELECTORS.DIV_OKAZANE_DOKUMENTY, 'textOkazaneDokumenty', 'Dodaj dokument', 'texOkazaneDokumenty', 'butOkazaneDokumenty');

// Add event listeners for adding new input fields
document.querySelector(DOM_SELECTORS.ADD_INPUT).addEventListener('click', addOsobaKontrolujaca);
document.querySelector(DOM_SELECTORS.ADD_INPUT_OBUWK).addEventListener('click', addOsobaBiorUwKontr);
document.querySelector(DOM_SELECTORS.ADD_INPUT_DOK).addEventListener('click', addOkazaneDokumenty);

// --- Data Fetching and Display ---

/**
 * Fetches and displays the list of people.
 * @param {string} kod - The postal code.
 * @param {string} miejscowosc - The city.
 * @param {string} ulica - The street.
 * @param {string} numer - The number.
 */
async function listaOsob(kod, miejscowosc, ulica, numer) {
  try {
    const dane = await $.ajax({
      url: API_URLS.POBIERZ_OSOBY_DO_SZAMBO,
      type: "POST",
      data: {
        KodAdrWlSzamba: kod.replace(/\u00A0/g, ' '),
        MiejscowoscAdrWlSzamba: miejscowosc.replace(/\u00A0/g, ' '),
        UlicaAdrWlSzamba: ulica.replace(/\u00A0/g, ' '),
        NrAdrWlSzamba: numer.replace(/\u00A0/g, ' '),
      },
    });

    const dane_osob = JSON.parse(dane);

    // Check if the label already exists
    let existingLabel = document.querySelector('.labKontrolaNagTabOso');
    if (!existingLabel) {
        // Add label before the table
        let label = document.createElement('label');
        label.className = 'labKontrolaNagTabOso';
        label.textContent = 'Lista osób zamieszujących nieruchomość.';
        
        let div = document.createElement('div');
        div.className = 'divAdrNier';
        div.appendChild(label);
        
        document.querySelector(DOM_SELECTORS.EXAMPLE_TABLE_LISTA_OSOB).before(div);
    }

    // Create the table
    table_tmp = new Tabulator(DOM_SELECTORS.EXAMPLE_TABLE_LISTA_OSOB, {
      data: dane_osob,
      layout: "fitColumns",
      rowHeight: 20,
      frozenRows: 0,
      height: "100%",
      selectable: true,
      pagination: "local",
      paginationSize: 25,
      movableColumns: true,
      paginationCounter: "rows",
      columns: [
        { title: "Imie", field: "imie" },
        { title: "Nazwisko", field: "nazwisko" },
        { title: "Ulica", field: "ulica" },
        { title: "Numer", field: "numer" },
        { title: "Kod pocztowy", field: "kod_pocztowy" },
        { title: "Miejscowość", field: "miejscowosc" },
      ],
    });
  } catch (error) {
    console.error("Error fetching or displaying data:", error);
  }
}

// --- Input Field Validation ---

/**
 * Checks if all required fields are filled and triggers data fetching.
 */
function checkFields() {
  const kod = document.getElementById('KodAdrNier').value;
  const miejscowosc = document.getElementById('MiejscowoscAdrNier').value;
  const ulica = document.getElementById('UlicaAdrNier').value;
  const numer = document.getElementById('NrAdrNier').value;

  if (kod && miejscowosc && ulica && numer) {
    listaOsob(kod, miejscowosc, ulica, numer);
  }
}

// --- Event Listeners ---

// Add event listeners to check fields
document.getElementById('KodAdrNier').addEventListener('input', checkFields);
document.getElementById('MiejscowoscAdrNier').addEventListener('input', checkFields);
document.getElementById('UlicaAdrNier').addEventListener('input', checkFields);
document.getElementById('NrAdrNier').addEventListener('input', checkFields);

// --- Initialization ---

// Initialize the clock
Clock.initialize(".czas");
