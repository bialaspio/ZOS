// Zegar
const zegarContent = document.querySelector(".czas");

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

// Uruchomienie zegara
zegar();
setInterval(zegar, 1000);

// Walidacja NIP
function validateNIP() {
  const nip = document.getElementById("nazNIP").value;

  $.ajax({
    url: "ajax/ajax_validate_nip.php",
    type: "POST",
    data: { nip },
    success: function (data) {
      if (data === "true") {
        customAlert("NIP już istnieje w bazie danych!");
        disableButton(buttonRaportEl, true);
      } else {
        disableButton(buttonRaportEl, false);
      }
    },
    error: function (error) {
      console.error("Błąd walidacji NIP:", error);
    },
  });
}

function disableButton(button, disable) {
  button.disabled = disable;
  button.style.backgroundColor = disable ? "gray" : "cornflowerblue";
}

// Dodawanie firmy
function DodajFirme() {
  const fields = {
    naz_firmy: document.getElementById("nazFirmy").value,
    nip: document.getElementById("nazNIP").value,
    ulica: document.getElementById("nazUlic").value,
    nr: document.getElementById("nazNr").value,
    kod: document.getElementById("nazKod").value,
    miasto: document.getElementById("nazMiejs").value,
    ch_inny_adr_kor: document.getElementById("chkInnyAdrKor").value,
    inny_adr_kor: document.getElementById("InnyAdrKor").value,
    dod_info: document.getElementById("nazDodInfo").value,
    email: document.getElementById("nazEmailFir").value,
    nr_tel: document.getElementById("nazTelFir").value,
  };

  // Walidacja
  if (!fields.naz_firmy) return customAlert("Nazwa firmy jest wymagana!");
  if (!/^[0-9]{10}$/.test(fields.nip)) return customAlert("NIP powinien składać się z 10 cyfr!");
  if (!fields.ulica) return customAlert("Ulica jest wymagana!");
  if (!fields.nr) return customAlert("Numer jest wymagany!");
  if (!/^[0-9]{2}-[0-9]{3}$/.test(fields.kod)) return customAlert("Kod pocztowy powinien być w formacie XX-XXX!");
  if (!fields.miasto) return customAlert("Miasto jest wymagane!");
  if (fields.email && !/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(fields.email)) {
    return customAlert("Podaj prawidłowy adres e-mail!");
  }
  if (fields.nr_tel && !/^[0-9]{9}$/.test(fields.nr_tel)) {
    return customAlert("Numer telefonu powinien składać się z 9 cyfr!");
  }

  // Wysłanie danych do serwera
  $.ajax({
    url: "ajax/ajax_dodaj_firme.php",
    type: "POST",
    data: fields,
    success: function (data) {
      if (data === "true") {
        customAlert("Nie udało się danych dodać do bazy.");
      } else {
        customAlert("Dane dodane do bazy.");
      }
    },
    error: function (error) {
      console.error("Błąd dodawania firmy:", error);
      customAlert("Wystąpił błąd podczas dodawania firmy.");
    },
  });
}

// Inicjalizacja autouzupełniania
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
    minLength: 1,
  });
}

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

// Przypisanie zdarzenia do przycisku
const buttonRaportEl = document.querySelector(".butZapisz");
buttonRaportEl.addEventListener("click", DodajFirme);