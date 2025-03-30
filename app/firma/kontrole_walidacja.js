// --- Walidacja formularza ---
$(document).ready(function () {
  $('#formKontrole').validate({
    rules: {
      selRodzKontroli: { required: true },
      Imie: { required: true, minlength: 2 },
      Nazwisko: { required: true, minlength: 2 },
      KodAdrWlSzamba: { required: true, minlength: 6, maxlength: 6 },
      MiejscowoscAdrWlSzamba: { required: true, minlength: 2 },
      UlicaAdrWlSzamba: { required: true, minlength: 2 },
      NrAdrWlSzamba: { required: true, minlength: 1, startWith: /^\d/ },
      KodAdrNier: { required: true, minlength: 6, maxlength: 6 },
      MiejscowoscAdrNier: { required: true, minlength: 2 },
      UlicaAdrNier: { required: true, minlength: 2 },
      NrAdrNier: { required: true, minlength: 1, startWith: /^\d/ },
      texOsobaKontr: { required: true, minlength: 1 },
      texOsobaBiorUwKontr: { required: true, minlength: 1 },
      texOkazaneDokumenty: { required: true, minlength: 1 },
      fileInput: { required: true },
    },
    messages: {
      selRodzKontroli: "Należy wybrać rodzaj kontroli",
      Imie: "* Wprowadź imię.",
      Nazwisko: "* Wprowadź nazwisko.",
      KodAdrWlSzamba: "* Wprowadź kod.",
      MiejscowoscAdrWlSzamba: "* Wprowadź miejscowość.",
      UlicaAdrWlSzamba: "* Wprowadź ulicę.",
      NrAdrWlSzamba: "* Wprowadź numer.",
      KodAdrNier: "* Wprowadź kod.",
      MiejscowoscAdrNier: "* Wprowadź miejscowość.",
      UlicaAdrNier: "* Wprowadź ulicę.",
      NrAdrNier: "* Wprowadź numer.",
      texOsobaKontr: "* Wprowadź dane kontrolującego.",
      texOsobaBiorUwKontr: "* Wprowadź dane osoby biorącej udział w kontroli.",
      texOkazaneDokumenty: "* Wprowadź nazwę dokumentu.",
      fileInput: "* Wybierz plik.",
    },
  });

  $('#bt_Zapisz_kon').click(async function (e) {
    e.preventDefault();
    if ($('#formKontrole').valid()) {
      zapisz(id_osoby_wlasciciel);
    } else {
      customAlert("Proszę poprawić wskazane błędy.");
    }
  });
});

// --- Funkcje pomocnicze ---
async function ajaxRequest(url, data) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url,
      type: "POST",
      data,
      success: (response) => resolve(JSON.parse(response)),
      error: (error) => reject(error),
    });
  });
}

// --- Pobieranie danych ---
async function pobierzIdRodzKontroli() {
  const naz_kontroli = $("#selRodzKontroli").val();
  const dane = await ajaxRequest("ajax/ajax_pobierz_id_rodz_kon.php", { naz_kontroli });
  return dane.map((item) => item.id) || [];
}

async function pobierzIdAdresu() {
  const data = {
    KodAdr: $("#KodAdrNier").val(),
    MiejscowoscAdr: $("#MiejscowoscAdrNier").val(),
    UlicaAdr: $("#UlicaAdrNier").val(),
    NrAdr: $("#NrAdrNier").val(),
  };
  const dane = await ajaxRequest("ajax/ajax_pobierz_id_adresu.php", data);
  return dane.map((item) => item.ogc_fid) || [];
}

async function sprOsobaKontrolujaca(osoba) {
  const dane = await ajaxRequest("ajax/ajax_sprawdz_osobe_kont.php", { osoba_kontrolujaca: osoba });
  return dane === true;
}

async function pobierzWartosciPolTexOsobaKontr() {
  const inputy = Array.from(document.querySelectorAll("input[id^='texOsobaKontr']"));
  const wartosciPol = [];
  for (const input of inputy) {
    const isValid = await sprOsobaKontrolujaca(input.value);
    if (isValid) {
      wartosciPol.push(input.value);
    } else {
      customAlert("Nieprawidłowa wartość pola. Brak w bazie osoby kontrolującej o podanych danych.");
      return "err";
    }
  }
  return wartosciPol;
}

function pobierzWartosciPolOBUWK() {
  return Array.from(document.querySelectorAll("input[id^='texOsobaBiorUwKontr']")).map((input) => input.value);
}

async function pobierzOkazaneDokumenty() {
  const inputy = Array.from(document.querySelectorAll("input"));
  const daneOkazanychDokumentow = [];
  for (const input of inputy) {
    if (input.id.includes("texOkazaneDokumenty")) {
      daneOkazanychDokumentow.push({ naz_dok: input.value });
    }
    if (input.id.includes("fileInput") && input.files.length > 0) {
      const arrayBuffer = await input.files[0].arrayBuffer();
      const base64 = btoa(String.fromCharCode(...new Uint8Array(arrayBuffer)));
      daneOkazanychDokumentow[daneOkazanychDokumentow.length - 1].zalacznik = base64;
      daneOkazanychDokumentow[daneOkazanychDokumentow.length - 1].naz_zalacznik = input.files[0].name;
    }
  }
  return daneOkazanychDokumentow;
}

// --- Zapis danych ---
async function zapisz(id_osoby_wlasciciel) {
  const spr_wlasciciel = await spr_wlasciciel_f();
  if (spr_wlasciciel != id_osoby_wlasciciel) {
    customAlert("W bazie brak danych właściciela. Popraw dane i spróbuj ponownie.");
    return -1;
  }

  const id_rodz_kont = await pobierzIdRodzKontroli();
  const data_kon_d = $("#data_cal").val();
  const nr_kon = $("#zlecen_nr").val().split("/")[1];
  const osoby_kontrolujace = await pobierzWartosciPolTexOsobaKontr();
  if (osoby_kontrolujace === "err") return -1;
  const osoby_b_u_w_k = pobierzWartosciPolOBUWK();
  const id_adresu_nier = await pobierzIdAdresu();
  if (!id_adresu_nier.length) {
    customAlert("W bazie brak nieruchomości pod podanym adresem. Popraw dane i spróbuj ponownie.");
    return -1;
  }
  const okazane_dokumenty = await pobierzOkazaneDokumenty();
  const zalecenia_pokontr = $("#texZalecenia").val();
  const uwagi = $("#texUwagi").val();

  try {
    const dane = await ajaxRequest("ajax/ajax_dodaj_Kontrole.php", {
      id_rodz_kont: id_rodz_kont[0],
      data_kon_d,
      nr_kon,
      id_osoby_wlasciciel,
      osoby_kontrolujace,
      osoby_b_u_w_k,
      id_adresu_nier: id_adresu_nier[0],
      json: JSON.stringify(okazane_dokumenty),
      zalecenia_pokontr,
      uwagi,
    });

    if (dane.replace(/\r?\n/g, "") === "true") {
      customAlertOpenWin("Dane dodane do bazy.", "lista_kontrole.php");
    } else {
      customAlert("Nie udało się danych dodać do bazy.");
    }
  } catch (error) {
    console.error("Błąd zapisu:", error);
    customAlert("Wystąpił błąd podczas zapisu danych.");
  }
}