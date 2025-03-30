// Funkcja zegara
function inicjalizujZegar(selector) {
  const zegarContent = document.querySelector(selector);

  function zegar() {
    const d = new Date();
    const day = addLeadingZero(d.getDate());
    const month = addLeadingZero(d.getMonth() + 1);
    const year = d.getFullYear();
    const hour = addLeadingZero(d.getHours());
    const minutes = addLeadingZero(d.getMinutes());
    const seconds = addLeadingZero(d.getSeconds());

    zegarContent.innerHTML = `${year}.${month}.${day} ${hour}:${minutes}:${seconds}`;
  }

  function addLeadingZero(number) {
    return number < 10 ? "0" + number : number;
  }

  zegar();
  setInterval(zegar, 1000);
}

// Funkcja do inicjalizacji autouzupełniania
function initializeAutocomplete(inputSelector, url, requestDataCallback, responseDataCallback) {
  $(inputSelector).autocomplete({
    source: function (request, response) {
      $.ajax({
        url,
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
    minLength: 0,
  });
}

// Inicjalizacja autouzupełniania dla pól
initializeAutocomplete(
  "#KodAdrOsoba",
  "ajax/ajax_KodPocz.php",
  (request) => ({ KodPocz: request.term, Miejscowosc: $("#MiejscowoscAdrOsoba").val() }),
  (data) => data.map((item) => item.kod_pocztowy)
);

initializeAutocomplete(
  "#MiejscowoscAdrOsoba",
  "ajax/ajax_Miejscowosc.php",
  (request) => ({ Miejscowosc: request.term, Kod: $("#KodAdrOsoba").val() }),
  (data) => data.map((item) => item.miejscowosc)
);

initializeAutocomplete(
  "#UlicaAdrOsoba",
  "ajax/ajax_Ulice.php",
  (request) => ({
    Ulica: request.term,
    Miejscowosc: $("#MiejscowoscAdrOsoba").val(),
    Kod: $("#KodAdrOsoba").val(),
  }),
  (data) => data.map((item) => item.ulica)
);

initializeAutocomplete(
  "#NrAdrOsoba",
  "ajax/ajax_Numer.php",
  (request) => ({
    Numer: request.term,
    Ulica: $("#UlicaAdrOsoba").val(),
    Miejscowosc: $("#MiejscowoscAdrOsoba").val(),
    Kod: $("#KodAdrOsoba").val(),
  }),
  (data) => data.map((item) => item.numer)
);

// Funkcja do obsługi zapisu osoby
function zapiszOsobe() {
  const Imie = $("#Imie").val();
  const Nazwisko = $("#Nazwisko").val();
  const KodAdrSzamba = $("#KodAdrOsoba").val();
  const MiejscowoscAdrSzamba = $("#MiejscowoscAdrOsoba").val();
  const UlicaAdrSzamba = $("#UlicaAdrOsoba").val();
  const NrAdrSzamba = $("#NrAdrOsoba").val();

  const chbox_innyAdrZam = $("#chkInnyAdrZamieszkania").is(":checked");
  const chbox_WlascicielSzamba = $("#chkWlascicielSzamba").is(":checked");

  const KodAdrZamSzamba = chbox_innyAdrZam ? $("#KodZamAdrWlSzamba").val() : KodAdrSzamba;
  const MiejscowoscAdrZamSzamba = chbox_innyAdrZam ? $("#MiejscowoscZamAdrWlSzamba").val() : MiejscowoscAdrSzamba;
  const UlicaAdrZamSzamba = chbox_innyAdrZam ? $("#UlicaZamAdrWlSzamba").val() : UlicaAdrSzamba;
  const NrAdrZamSzamba = chbox_innyAdrZam ? $("#NrZamAdrWlSzamba").val() : NrAdrSzamba;

  $.ajax({
    url: "ajax/ajax_dodajOsoba.php",
    type: "POST",
    data: {
      Imie,
      Nazwisko,
      KodAdrSzamba,
      MiejscowoscAdrSzamba,
      UlicaAdrSzamba,
      NrAdrSzamba,
      WlascicielSzamba: chbox_WlascicielSzamba,
      KodAdrZamSzamba,
      MiejscowoscAdrZamSzamba,
      UlicaAdrZamSzamba,
      NrAdrZamSzamba,
    },
    success: function (response) {
      const dane = response.replace(/\r?\n/g, "");
      if (dane === "Error" || dane === "false") {
        customAlert("Nie udało się danych dodać do bazy.");
      } else if (dane === "true") {
        customAlert("Dane dodane do bazy.");
        window.close();
      } else {
        customAlert("Błąd ajax-a.");
      }
    },
    error: function (error) {
      console.error("Błąd zapisu osoby:", error);
      customAlert("Wystąpił błąd podczas zapisu osoby.");
    },
  });
}

// Przypisanie zdarzenia do przycisku zapisu
$(".bt_ZapiszOsosba").on("click", zapiszOsobe);

// Inicjalizacja zegara
inicjalizujZegar(".czas");