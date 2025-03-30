(function () {
  let mapa;
  let kwadrat;
  let wspolrzednaX = 0;
  let wspolrzednaY = 0;
  let mapaZainicjowana = false;

  //-------------------------------------------------------------------------------------------------------------------
  // Funkcje pomocnicze
  //-------------------------------------------------------------------------------------------------------------------

  function dodajZeroWiodace(liczba) {
    return liczba < 10 ? "0" + liczba : liczba;
  }

  function wyswietlAlert(wiadomosc) {
    alert(wiadomosc);
  }

  function stworzKwadrat(lat, lng) {
    if (kwadrat) {
      mapa.removeLayer(kwadrat);
    }

    const latLng1 = L.latLng(lat, lng);
    const latLng2 = L.latLng(lat + 0.00001825, lng);
    const latLng3 = L.latLng(lat + 0.00001825, lng + 0.00002799);
    const latLng4 = L.latLng(lat, lng + 0.00002799);
    kwadrat = L.polygon([latLng1, latLng2, latLng3, latLng4, latLng1]).addTo(mapa);
  }

  async function pobierzDane(url, dane = {}) {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams(dane)
    });
    if (!response.ok) {
      throw new Error(`Błąd HTTP! status: ${response.status}`);
    }
    return await response.json();
  }

  //-------------------------------------------------------------------------------------------------------------------
  // Inicjalizacja i zarządzanie mapą
  //-------------------------------------------------------------------------------------------------------------------

  function zainicjalizujMape() {
    mapa = L.map("mapaSzamba").setView([51.505, -0.09], 13);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      maxZoom: 20,
    }).addTo(mapa);

    const adresUrlGeoserver = "http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/wms?";

    const budynki = L.tileLayer.wms(adresUrlGeoserver, {
      layers: "PG_L_Layers:PG_L_budynki_egib",
      format: "image/png",
      transparent: true,
      minZoom: 15,
      maxZoom: 25,
    });

    const dzialki = L.tileLayer.wms(adresUrlGeoserver, {
      layers: "PG_L_Layers:PG_L_dzialki_label",
      format: "image/png",
      transparent: true,
      minZoom: 15,
      maxZoom: 25,
    });

    const szamba = L.tileLayer.wms(adresUrlGeoserver, {
      layers: "PG_L_Layers:PG_L_szamba_adresy",
      format: "image/png",
      transparent: true,
      opacity: 1,
      maxZoom: 25,
    });

    const linieKanalizacji = L.tileLayer.wms(adresUrlGeoserver, {
      layers: "PG_L_Layers:PG_L_LinieKanalizacji",
      format: "image/png",
      transparent: true,
      opacity: 1,
      minZoom: 15,
      maxZoom: 25,
    });

    const warstwyBazowe = [budynki, dzialki, szamba, linieKanalizacji];
    warstwyBazowe.forEach(warstwa => warstwa.addTo(mapa));

    mapaZainicjowana = true;
  }

  function obslugaKliknieciaMapy(e) {
    wspolrzednaX = e.latlng.lng;
    wspolrzednaY = e.latlng.lat;
    console.log({ wspolrzednaX, wspolrzednaY });
    stworzKwadrat(wspolrzednaY, wspolrzednaX);
  }

  //-------------------------------------------------------------------------------------------------------------------
  // Wywołania AJAX
  //-------------------------------------------------------------------------------------------------------------------

  async function pobierzIdDzialki(kod, miejscowosc, ulica, numer, funkcjaZwrotna) {
    try {
      const dane = await pobierzDane("ajax/ajax_pobierz_id_dzialki_po_adresie.php", {
        KodAdrWlSzamba: kod.replace(/\u00A0/g, " "),
        MiejscowoscAdrWlSzamba: miejscowosc.replace(/\u00A0/g, " "),
        UlicaAdrWlSzamba: ulica.replace(/\u00A0/g, " "),
        NrAdrWlSzamba: numer.replace(/\u00A0/g, " "),
      });
      const idDzialki = dane.map((element) => element.id_dzialki);
      funkcjaZwrotna(idDzialki.length > 0 ? idDzialki : 0);
    } catch (blad) {
      funkcjaZwrotna(-1);
    }
  }

  function pobierzIdOsoba(Imie, Nazwisko, KodAdrOsoba, MiejscowoscAdrOsoba, UlicaAdrOsoba, NrAdrOsoba, callback) {
    $.ajax({
      url: "ajax/ajax_pobierz_id_osoba_imie_i_naz.php",
      type: "POST",
      data: {
        Imie: Imie.replace(/\u00A0/g, ' '),
        Nazwisko: Nazwisko.replace(/\u00A0/g, ' '),
        KodAdrOsoba: KodAdrOsoba.replace(/\u00A0/g, ' '),
        MiejscowoscAdrOsoba: MiejscowoscAdrOsoba.replace(/\u00A0/g, ' '),
        UlicaAdrOsoba: UlicaAdrOsoba.replace(/\u00A0/g, ' '),
        NrAdrOsoba: NrAdrOsoba.replace(/\u00A0/g, ' '),
      },
      success: function (data) {
        const daneOsoba = JSON.parse(data);
        const idOsoba = daneOsoba.map((idOsoby) => idOsoby.id_osoby);

        if (idOsoba.length > 0) {
          callback(idOsoba);
        } else {
          callback(0);
        }
      },
      error: function (error) {
        callback(-1);
      }
    });
  }

  //-------------------------------------------------------------------------------------------------------------------
  // Obsługa zdarzeń
  //-------------------------------------------------------------------------------------------------------------------

  async function zaladujMape(kod, miejscowosc, ulica, numer) {
    if (!mapaZainicjowana) {
      zainicjalizujMape();
    }

    pobierzIdDzialki(kod, miejscowosc, ulica, numer, async function (idDzialki) {
      if (idDzialki === -1) {
        wyswietlAlert('Wystąpił błąd podczas pobierania danych działki.');
      } else if (idDzialki === 0) {
        wyswietlAlert("Brak w bazie działki dla tego adresu !!!");
      } else {
        const filtrCql = `id_dzialki%20in%20(%27${idDzialki[0]}%27)`;
        const adresUrlWfs = `http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=1.0.0&request=GetFeature&CQL_FILTER=${filtrCql}&typeName=PG_L_Layers%3APG_L_dzialki&outputFormat=application%2Fjson&srsName=epsg:4326`;

        try {
          const dane = await pobierzDane(adresUrlWfs);
          const geojson = L.geoJson(dane).addTo(mapa);
          mapa.fitBounds(geojson.getBounds());
          mapa.on("click", obslugaKliknieciaMapy);
        } catch (error) {
          console.error("Błąd pobierania geometrii działki:", error);
        }
      }
    });
  }

  //-------------------------------------------------------------------------------------------------------------------
  // Inicjalizacja
  //-------------------------------------------------------------------------------------------------------------------

  const ulica = sessionStorage.getItem('SS_ulica_szambo');
  const numer = sessionStorage.getItem('SS_numer_szambo');
  const miejscowosc = sessionStorage.getItem('SS_miejscowosc_szambo');
  const kod = sessionStorage.getItem('SS_kod_szambo');

  zaladujMape(kod, miejscowosc, ulica, numer);

  // Event listener for the "Zapisz Szambo" button
  $('#bt_ZapiszSzambo').click(async function (e) {
    e.preventDefault();

    let Imie = $('#Imie').val();
    let Nazwisko = $('#Nazwisko').val();
    let KodAdrOsoba = $('#KodAdrOsoba').val();
    let MiejscowoscAdrOsoba = $('#MiejscowoscAdrOsoba').val();
    let UlicaAdrOsoba = $('#UlicaAdrOsoba').val();
    let NrAdrOsoba = $('#NrAdrOsoba').val();

    let KodAdrSzamba = $('#KodAdrSzamba').val();
    let MiejscowoscAdrSzamba = $('#MiejscowoscAdrSzamba').val();
    let UlicaAdrSzamba = $('#UlicaAdrSzamba').val();
    let NrAdrSzamba = $('#NrAdrSzamba').val();
    let PojSzamba = $('#Pojemnosc').val();
    let Rodzaj = $('#RodzajNieczystosci').val();

    pobierzIdOsoba(Imie, Nazwisko, KodAdrOsoba, MiejscowoscAdrOsoba, UlicaAdrOsoba, NrAdrOsoba, function (idOsoba) {
      if (idOsoba == -1) {
        wyswietlAlert('Wystąpił błąd podczas pobierania danych osoby.');
      } else if (idOsoba == 0) {
        let text = "Brak w bazie podanych danych osobowych. \nW celu dodania umowy należy najpierw dodać dane osobowe.\nCzy chcesz teraz dodać osobę do bazy?";
        dodaj_osobe(text);
      } else {
        // Zapisz szambo z id_osoba
        console.log("Zapisz szambo ()");
        zapisz_szambo();
      }
    });
  });

  // Autocomplete initialization
  initializeAutocomplete(
    "#KodAdrSzamba",
    "ajax/ajax_KodPocz.php",
    (request) => ({ KodPocz: request.term, Miejscowosc: $("#MiejscowoscAdrSzamba").val() }),
    (data) => data.map((item) => item.kod_pocztowy)
  );

  initializeAutocomplete(
    "#MiejscowoscAdrSzamba",
    "ajax/ajax_Miejscowosc.php",
    (request) => ({ Miejscowosc: request.term, Kod: $("#KodAdrSzamba").val() }),
    (data) => data.map((item) => item.miejscowosc)
  );

  initializeAutocomplete(
    "#UlicaAdrSzamba",
    "ajax/ajax_Ulice.php",
    (request) => ({
      Ulica: request.term,
      Miejscowosc: $("#MiejscowoscAdrSzamba").val(),
      Kod: $("#KodAdrSzamba").val(),
    }),
    (data) => data.map((item) => item.ulica)
  );

  initializeAutocomplete(
    "#NrAdrSzamba",
    "ajax/ajax_Numer.php",
    (request) => ({
      Numer: request.term,
      Ulica: $("#UlicaAdrSzamba").val(),
      Miejscowosc: $("#MiejscowoscAdrSzamba").val(),
      Kod: $("#KodAdrSzamba").val(),
    }),
    (data) => data.map((item) => item.numer)
  );

  // Initialize the clock
  const zegarContent = document.querySelector(".czas");
  function zegar() {
    const d = new Date();
    const day = dodajZeroWiodace(d.getDay() - 1);
    const month = dodajZeroWiodace(d.getMonth() + 1);
    const year = d.getFullYear();
    const hour = dodajZeroWiodace(d.getHours());
    const minutes = dodajZeroWiodace(d.getMinutes());
    const seconds = dodajZeroWiodace(d.getSeconds());
    zegarContent.innerHTML = `${year}.${month}.${day} ${hour}:${minutes}:${seconds}`;
  }
  zegar();
  setInterval(zegar, 1000);
})();
