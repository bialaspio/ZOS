(function () {
  let mapa;
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

  function stworzKwadrat(lat, lng) {
    if (kwadrat) {
      mapa.removeLayer(kwadrat);
    }

    const latLng1 = L.latLng(lat - 0.00000912, lng - 0.00001399);
    const latLng2 = L.latLng(lat + 0.00000912, lng - 0.00001399);
    const latLng3 = L.latLng(lat + 0.00000912, lng + 0.00001399);
    const latLng4 = L.latLng(lat - 0.00000912, lng + 0.00001399);
    kwadrat = L.polygon([latLng1, latLng2, latLng3, latLng4, latLng1]).addTo(mapa);

    kwadrat.setStyle({
      color: 'red',
      fillColor: '#4B5320',
      fillOpacity: 0.5,
      weight: 2
    });
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

  function transformujWspolrzedne(lat, lng) {
    const WKIDprojection = "+proj=merc +a=6378137 +b=6378137 +lat_ts=0 +lon_0=0 +x_0=0 +y_0=0 +k=1 +units=m +nadgrids=@null +wktext +no_defs";
    const WKIDxy = proj4(WKIDprojection, [lng, lat]);
    return { x: WKIDxy[0].toFixed(2), y: WKIDxy[1].toFixed(2) };
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

  //-------------------------------------------------------------------------------------------------------------------
  // Wywołania AJAX
  //-------------------------------------------------------------------------------------------------------------------

  async function pobierzDaneWlasciciela(idDzialki) {
    try {
      const dane = await pobierzDane("ajax/ajax_pobierz_dane_osoby_po_dzialka.php", { IdDzialki: idDzialki });
      if (dane.length > 0) {
        document.getElementById('Imie').value = dane[0].imie;
        document.getElementById('Nazwisko').value = dane[0].nazwisko;
        document.getElementById('KodAdrOsoba').value = dane[0].kod_pocztowy;
        document.getElementById('MiejscowoscAdrOsoba').value = dane[0].miejscowosc;
        document.getElementById('UlicaAdrOsoba').value = dane[0].ulica;
        document.getElementById('NrAdrOsoba').value = dane[0].numer;
        document.getElementById('KodAdrSzamba').value = dane[0].kod_pocztowy;
        document.getElementById('MiejscowoscAdrSzamba').value = dane[0].miejscowosc;
        document.getElementById('UlicaAdrSzamba').value = dane[0].ulica;
        document.getElementById('NrAdrSzamba').value = dane[0].numer;
      } else {
        console.log("brak osoby przypisanej do działki");
      }
    } catch (error) {
      console.error("Błąd pobierania danych właściciela:", error);
    }
  }

  async function pobierzDaneAdresDlaSzamba(idDzialki) {
    try {
      const dane = await pobierzDane("ajax/ajax_pobierz_dane_p_a_po_dzialka.php", { IdDzialki: idDzialki });
      if (dane.length > 0) {
        document.getElementById('KodAdrSzamba').value = dane[0].kod_pocztowy;
        document.getElementById('MiejscowoscAdrSzamba').value = dane[0].miejscowosc;
        document.getElementById('UlicaAdrSzamba').value = dane[0].ulica;
        document.getElementById('NrAdrSzamba').value = dane[0].numer;
      } else {
        console.log("brak adresu przypisanego do działki");
      }
    } catch (error) {
      console.error("Błąd pobierania danych adresu:", error);
    }
  }

  function pobierzIdOsoba(Imie, Nazwisko, KodAdrSzamba, MiejscowoscAdrSzamba, UlicaAdrSzamba, NrAdrSzamba, callback) {
    $.ajax({
      url: "ajax/ajax_pobierz_id_osoba_imie_i_naz.php",
      type: "POST",
      data: {
        Imie: Imie.replace(/\u00A0/g, ' '),
        Nazwisko: Nazwisko.replace(/\u00A0/g, ' '),
        KodAdrOsoba: KodAdrSzamba.replace(/\u00A0/g, ' '),
        MiejscowoscAdrOsoba: MiejscowoscAdrSzamba.replace(/\u00A0/g, ' '),
        UlicaAdrOsoba: UlicaAdrSzamba.replace(/\u00A0/g, ' '),
        NrAdrOsoba: NrAdrSzamba.replace(/\u00A0/g, ' '),
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

  async function zaladujDane() {
    const lat = parseFloat(sessionStorage.getItem('lat'));
    const lng = parseFloat(sessionStorage.getItem('lng'));
    const idDzialki = sessionStorage.getItem('id_dzialki');

    if (!mapaZainicjowana) {
      zainicjalizujMape();
    }

    stworzKwadrat(lat, lng);

    await pobierzDaneWlasciciela(idDzialki);
    await pobierzDaneAdresDlaSzamba(idDzialki);

    const filtrCql = `id_dzialki%20in%20(%27${idDzialki}%27)`;
    const adresUrlWfs = `http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=1.0.0&request=GetFeature&CQL_FILTER=${filtrCql}&typeName=PG_L_Layers%3APG_L_dzialki&outputFormat=application%2Fjson&srsName=epsg:4326`;

    try {
      const dane = await pobierzDane(adresUrlWfs);
      const geojson = L.geoJson(dane).addTo(mapa);
      mapa.fitBounds(geojson.getBounds());
    } catch (error) {
      console.error("Błąd pobierania geometrii działki:", error);
    }
  }

  async function zapiszSzambo() {
    let Imie = document.getElementById("Imie").value;
    let Nazwisko = document.getElementById("Nazwisko").value;
    let KodAdrOsoba = document.getElementById("KodAdrOsoba").value;
    let MiejscowoscAdrOsoba = document.getElementById("MiejscowoscAdrOsoba").value;
    let UlicaAdrOsoba = document.getElementById("UlicaAdrOsoba").value;
    let NrAdrOsoba = document.getElementById("NrAdrOsoba").value;

    let KodAdrSzamba = document.getElementById("KodAdrSzamba").value;
    let MiejscowoscAdrSzamba = document.getElementById("MiejscowoscAdrSzamba").value;
    let UlicaAdrSzamba = document.getElementById("UlicaAdrSzamba").value;
    let NrAdrSzamba = document.getElementById("NrAdrSzamba").value;

    let Pojemnosc = document.getElementById("Pojemnosc").value;
    let Rodzaj = document.getElementById("RodzajNieczystosci").value;

    Pojemnosc = Pojemnosc.replace(/,/g, ".");

    if (!isNaN(Pojemnosc) && Pojemnosc > 0) {
      if (Rodzaj !== 'initial') {
        const lat = parseFloat(sessionStorage.getItem('lat'));
        const lng = parseFloat(sessionStorage.getItem('lng'));
        if (lat !== 0 && lng !== 0) {
          const { x, y } = transformujWspolrzedne(lat, lng);

          try {
            const dane = await pobierzDane("ajax/ajax_dodaj_szambo_z_umowa.php", {
              Imie,
              Nazwisko,
              KodAdrOsoba,
              MiejscowoscAdrOsoba,
              UlicaAdrOsoba,
              NrAdrOsoba,
              KodAdrSzamba,
              MiejscowoscAdrSzamba,
              UlicaAdrSzamba,
              NrAdrSzamba,
              Pojemnosc,
              Rodzaj,
              x,
              y
            });

            if (dane === "Error" || dane === "false") {
              wyswietlAlert("Nie udało się danych dodać do bazy.");
            } else if (dane === 'true') {
              wyswietlAlert("Dane dodane do bazy.");
              window.close();
            } else {
              wyswietlAlert("Nie udało się danych dodać do bazy.");
            }
          }
          catch (error) {
            wyswietlAlert(error.responseText);
          }
        }
      }
      else {
        wyswietlAlert("W polu Rodzaj nieczystosci należy\nwybrać rodzaj nieczystości.");
      }
    }
    else {
      wyswietlAlert("Pole Pojemność zbiornika w m3 musi\nbyć liczbą większą od zera.");
    }
  }

  //-------------------------------------------------------------------------------------------------------------------
  // Inicjalizacja
  //-------------------------------------------------------------------------------------------------------------------

  zaladujDane();

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
    let PojSzamba = $('#PojSzamba').val();

    pobierzIdOsoba(Imie, Nazwisko, KodAdrOsoba, MiejscowoscAdrOsoba, UlicaAdrOsoba, NrAdrOsoba, function (idOsoba) {
      if (idOsoba == -1) {
        wyswietlAlert('Wystąpił błąd podczas pobierania danych osoby.');
      } else if (idOsoba == 0) {
        let text = "Brak w bazie podanych danych osobowych. \nW celu dodania umowy należy najpierw dodać dane osobowe.\nCzy chcesz teraz dodać osobę do bazy?";
        dodaj_osobe(text);
      } else {
        zapiszSzambo();
      }
    });
  });

  // Event listener for the "Dodaj Osobe" button
  $('#bt_DodajOsobe').click(async function (e) {
    e.preventDefault();
    dodanie_osoby_PHP();
  });

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

  //--------------------------------------------------------------------------------------
  // Dodaj osobe 
  //--------------------------------------------------------------------------------------
  function dodaj_osobe(text){
    var dialog = document.createElement("div");
    styleDialog(dialog);
    
    var message = document.createElement("p");
    message.innerText = text;
    dialog.appendChild(message);
    message.style.fontWeight ="bold";
    message.style.color = "#424949";
    
    var buttonYes = document.createElement("button");
    buttonYes.innerText = "Tak";
    buttonYes.onclick = function() {
      dodanie_osoby_PHP();
      dialog.remove();
    };
    styleButton(buttonYes);
    dialog.appendChild(buttonYes);
    
    var buttonNo = document.createElement("button");
    buttonNo.innerText = "Nie";
    buttonNo.onclick = function() {
      dialog.remove();
    };
    styleButton(buttonNo);
    dialog.appendChild(buttonNo);
    
    document.body.appendChild(dialog);
  }		

  //-------------------------------------------------------------------------------------------------------------------------------------
  // Dodanie osoby 
  //-------------------------------------------------------------------------------------------------------------------------------------
  async function dodanie_osoby_PHP(){
    let imie, nazwisko, ulica, miejscowosc, numer, kod;
    
    imie = document.getElementById("Imie").value;
    nazwisko = document.getElementById("Nazwisko").value;
    ulica = document.getElementById("UlicaAdrOsoba").value;
    miejscowosc = document.getElementById("MiejscowoscAdrOsoba").value;
    numer = document.getElementById("NrAdrOsoba").value;
    kod = document.getElementById("KodAdrOsoba").value;
    
    var dodaj_osobe = window.open("dodaj_osobe_z_dzailka.php?source=onload");
    dodaj_osobe.onload = function() {
      dodaj_osobe.document.getElementById("Imie").value = imie;
      dodaj_osobe.document.getElementById("Nazwisko").value = nazwisko;
      dodaj_osobe.document.getElementById("UlicaAdrOsoba").value = ulica;
      dodaj_osobe.document.getElementById("NrAdrOsoba").value = numer;
      dodaj_osobe.document.getElementById("MiejscowoscAdrOsoba").value = miejscowosc;
      dodaj_osobe.document.getElementById("KodAdrOsoba").value = kod;
    };
  }
})();
