(function () {
  let mapa;
  let wspolrzednaX;
  let wspolrzednaY;
  let kwadrat;
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

  //-------------------------------------------------------------------------------------------------------------------
  // Inicjalizacja autouzupełniania
  //-------------------------------------------------------------------------------------------------------------------

  function zainicjalizujAutouzupełnianie(selektorPola, adresUrl, funkcjaDanychZapytania, funkcjaDanychOdpowiedzi) {
    $(selektorPola).autocomplete({
      source: function (zapytanie, odpowiedz) {
        $.ajax({
          url: adresUrl,
          type: "POST",
          data: funkcjaDanychZapytania(zapytanie),
          success: function (dane) {
            const przetworzoneDane = JSON.parse(dane);
            odpowiedz(funkcjaDanychOdpowiedzi(przetworzoneDane));
          },
          error: function (blad) {
            console.error("Błąd autouzupełniania:", blad);
          },
        });
      },
      select: function (zdarzenie, ui) {
        $(selektorPola).val(ui.item.value);
      },
      minLength: 0,
      open: function () {
        $(this).autocomplete("widget").css("z-index", 1000); // Niższy z-index
        return false;
      },
    });
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

    const adresy = L.tileLayer.wms(adresUrlGeoserver, {
      layers: "PG_L_Layers:PG_L_adresy",
      format: "image/png",
      transparent: true,
      opacity: 1,
      minZoom: 15,
      maxZoom: 25,
    });

    const warstwyBazowe = [budynki, dzialki, szamba, linieKanalizacji, adresy];
    warstwyBazowe.forEach(warstwa => warstwa.addTo(mapa));

    function obslugaKliknieciaMapy(e) {
      wspolrzednaX = e.latlng.lng;
      wspolrzednaY = e.latlng.lat;
      console.log({ wspolrzednaX, wspolrzednaY });

      if (kwadrat) {
        mapa.removeLayer(kwadrat);
      }

      const latLng1 = L.latLng(wspolrzednaY - 0.000009125, wspolrzednaX - 0.000013995);
      const latLng2 = L.latLng(wspolrzednaY + 0.000009125, wspolrzednaX - 0.000013995);
      const latLng3 = L.latLng(wspolrzednaY + 0.000009125, wspolrzednaX + 0.000013995);
      const latLng4 = L.latLng(wspolrzednaY - 0.000009125, wspolrzednaX + 0.000013995);
      kwadrat = L.polygon([latLng1, latLng2, latLng3, latLng4, latLng1]).addTo(mapa);
    }

    mapa.on("click", obslugaKliknieciaMapy);
    mapaZainicjowana = true;
  }

  function zaktualizujMape(idDzialki) {
    if (!mapaZainicjowana) {
        zainicjalizujMape();
    }
    
    const filtrCql = `id_dzialki%20in%20(%27${idDzialki[0]}%27)`;
    const adresUrlWfs = `http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=1.0.0&request=GetFeature&CQL_FILTER=${filtrCql}&typeName=PG_L_Layers%3APG_L_dzialki&outputFormat=application%2Fjson&srsName=epsg:4326`;

    mapa.eachLayer(function(warstwa) {
      if (warstwa instanceof L.TileLayer) {
        return;
      }
      mapa.removeLayer(warstwa);
    });
    
    fetch(adresUrlWfs)
      .then((odpowiedz) => odpowiedz.json())
      .then((dane) => {
        const geojson = L.geoJson(dane).addTo(mapa);
        mapa.fitBounds(geojson.getBounds());
      });
  }

  //-------------------------------------------------------------------------------------------------------------------
  // Wywołania AJAX
  //-------------------------------------------------------------------------------------------------------------------

  function pobierzIdDzialki(kod, miejscowosc, ulica, numer, funkcjaZwrotna) {
    $.ajax({
      url: "ajax/ajax_pobierz_id_dzialki_po_adresie.php",
      type: "POST",
      data: {
        KodAdrWlSzamba: kod.replace(/\u00A0/g, " "),
        MiejscowoscAdrWlSzamba: miejscowosc.replace(/\u00A0/g, " "),
        UlicaAdrWlSzamba: ulica.replace(/\u00A0/g, " "),
        NrAdrWlSzamba: numer.replace(/\u00A0/g, " "),
      },
      success: function (dane) {
        const daneDzialki = JSON.parse(dane);
        const idDzialki = daneDzialki.map((element) => element.id_dzialki);
        funkcjaZwrotna(idDzialki.length > 0 ? idDzialki : 0);
      },
      error: function (blad) {
        funkcjaZwrotna(-1);
      },
    });
  }

  //-------------------------------------------------------------------------------------------------------------------
  // Obsługa zdarzeń
  //-------------------------------------------------------------------------------------------------------------------

  async function sprawdzPola() {
    const kod = document.getElementById("KodAdrSzamba").value;
    const miejscowosc = document.getElementById("MiejscowoscAdrSzamba").value;
    const ulica = document.getElementById("UlicaAdrSzamba").value;
    const numer = document.getElementById("NrAdrSzamba").value;

    if (kod && miejscowosc && ulica && numer) {
      document.getElementById("mapaSzamba").style.height = "400px";
      pobierzIdDzialki(kod, miejscowosc, ulica, numer, zaktualizujMape);
    }
  }

  //-------------------------------------------------------------------------------------------------------------------
  // Inicjalizacja
  //-------------------------------------------------------------------------------------------------------------------
  
  // Inicjalizacja autouzupełniania
  zainicjalizujAutouzupełnianie(
    "#KodAdrSzamba",
    "ajax/ajax_KodPocz.php",
    (zapytanie) => ({ KodPocz: zapytanie.term, Miejscowosc: $("#MiejscowoscAdrSzamba").val() }),
    (dane) => dane.map((element) => element.kod_pocztowy)
  );

  zainicjalizujAutouzupełnianie(
    "#MiejscowoscAdrSzamba",
    "ajax/ajax_Miejscowosc.php",
    (zapytanie) => ({ Miejscowosc: zapytanie.term, Kod: $("#KodAdrSzamba").val() }),
    (dane) => dane.map((element) => element.miejscowosc)
  );

  zainicjalizujAutouzupełnianie(
    "#UlicaAdrSzamba",
    "ajax/ajax_Ulice.php",
    (zapytanie) => ({
      Ulica: zapytanie.term,
      Miejscowosc: $("#MiejscowoscAdrSzamba").val(),
      Kod: $("#KodAdrSzamba").val(),
    }),
    (dane) => dane.map((element) => element.ulica)
  );

  zainicjalizujAutouzupełnianie(
    "#NrAdrSzamba",
    "ajax/ajax_Numer.php",
    (zapytanie) => ({
      Numer: zapytanie.term,
      Ulica: $("#UlicaAdrSzamba").val(),
      Miejscowosc: $("#MiejscowoscAdrSzamba").val(),
      Kod: $("#KodAdrSzamba").val(),
    }),
    (dane) => dane.map((element) => element.numer)
  );

  // Nasłuchiwanie zdarzeń dla pól wejściowych
  ["KodAdrSzamba", "MiejscowoscAdrSzamba", "UlicaAdrSzamba", "NrAdrSzamba"].forEach((id) => {
    document.getElementById(id).addEventListener("input", sprawdzPola);
  });

  document.getElementById("NrAdrSzamba").addEventListener("blur", sprawdzPola);

  // Nasłuchiwanie zdarzenia dla pola wyboru RodzajNieczystosci
  document.getElementById("RodzajNieczystosci").addEventListener("change", function () {
    const wybranaWartosc = this.value;
    const element = document.querySelector(".select-style");
    element.style.height = wybranaWartosc === "Przemysłowe" || wybranaWartosc === "Bytowe" ? "30px" : "50px";
  });

  // Inicjalizacja zegara
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

  //-------------------------------------------------------------------------------------------------------------------
  // Walidacja
  //-------------------------------------------------------------------------------------------------------------------
  
  // blibioteka validation 
  $(document).ready(function() {
    // Funkcja sprawdzająca błędy
    function czySaBledy(formularz) {
      const bledy = formularz.validate().errors;
      return Object.keys(bledy).length > 0;
    }
  
    // Dodanie reguł walidacji
    $('#formDodSza').validate({
      rules: {
        Imie: {
          required: true,
          minlength: 2,
        },
        Nazwisko: {
          required: true,
          minlength: 2,
        },
        KodAdrOsoba: {
          required: true,
          minlength: 6,
          maxlength: 6,
        },
        MiejscowoscAdrOsoba: {
          required: true,
          minlength: 2,
        },
        UlicaAdrOsoba: {
          required: true,
          minlength: 2,
        },
        NrAdrOsoba: {
          required: true,
          minlength: 1,
        },
        KodAdrSzamba: {
          required: true,
          minlength: 6,
          maxlength: 6,
        },
        MiejscowoscAdrSzamba: {
          required: true,
          minlength: 2,
        },
        UlicaAdrSzamba: {
          required: true,
          minlength: 2,
        },
        NrAdrSzamba: {
          required: true,
          minlength: 1,
        },
        Pojemnosc: {
          required: true,
          number: true,
          min: 1,
        },
        RodzajNieczystosci: {
          required: true,
        },
      },
      messages: {
        Imie: "* Wprowadzić imię",
        Nazwisko: "* Wprowadzić nazwisko",
        KodAdrOsoba: "* Wprowadzić kod ",
        MiejscowoscAdrOsoba: "* Wprowadzić miejscowość",
        UlicaAdrOsoba: "* Wprowadzić uicę",
        NrAdrOsoba: "* Wprowadzić numer",
        KodAdrSzamba: "* Wprowadzić kod ",
        MiejscowoscAdrSzamba: "* Wprowadzić miejscowość",
        UlicaAdrSzamba: "* Wprowadzić ulię",
        NrAdrSzamba: "* Wprowadzić numer",
        Pojemnosc: {
          required: "* Proszę wprowadzić pojemność zbiornika",
          number: "* Proszę wprowadzić prawidłową liczbę",
          min: "* Pojemność zbiornika musi być większa od 0",
        },
        RodzajNieczystosci: "* Proszę wybrać rodzaj nieczystości",
      },
    });
  });
})();
