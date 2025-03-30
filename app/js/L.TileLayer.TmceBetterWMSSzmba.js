let lat ; 
let lng ;
let id_dzialki;

 L.TileLayer.TmceBetterWMSSzamba = L.TileLayer.WMS.extend({

  onAdd: function (map) {
    // Triggered when the layer is added to a map.
    //   Register a onmouseover listener, then do all the upstream WMS things
    L.TileLayer.WMS.prototype.onAdd.call(this, map);
    map.on('click', this.getFeatureInfo, this);
	  map.on('contextmenu',this.pokarz_menu, this);
  },
  
  onRemove: function (map) {
    // Triggered when the layer is removed from a map.
    //   Unregister a onmouseover listener, then do all the upstream WMS things
    L.TileLayer.WMS.prototype.onRemove.call(this, map);
    map.off('click', this.getFeatureInfo, this);
	  map.off('contextmenu',this.pokarz_menu, this);
  },
  
  //--------------------------------------------------------------------------------------------
  pokarz_menu: function (evt) {
	var url = this.getFeatureInfoMenuUrl(evt.latlng), 
      showResults = L.Util.bind(this.showGetFeatureInfo, this);
	console.log("menu Dodanie szamba");
  console.log({showResults});
  const marker = L.marker(evt.latlng);
  lat = marker.getLatLng().lat; 
  lng = marker.getLatLng().lng;
  
    $.ajax({
      url: url,
      success: async function (data, status, xhr) {
        // Pobieranie identyfikatora działki
     //   let id_dzialki = null;
        for (const property in data.features[0].properties) {
          if (property === 'id_dzialki') {
            id_dzialki = `${data.features[0].properties[property]}`;
            break;
          }
        }
        const sem_p_a = czy_dzialka_ma_p_a(id_dzialki)
        console.log({sem_p_a}); 
        
        sem_p_a.then(function(value) {
          // value to tablica z jednym obiektem
          var obj = value[0];
          // teraz możesz uzyskać dostęp do właściwości czy_p_a
          console.log(obj.czy_p_a);

          if (obj.czy_p_a === true ) {
            L.popup()
              .setLatLng(evt.latlng)
              .setContent('<div style="display: grid;"><button class="buttonMenu" onclick="ButtonMenuDodajSzambo()">Dodaj szambo.</button></div>')
              .openOn(map);
          }

        }).catch(function(error) {
          // Obsługa błędów
          console.log(error);
        });


/*
        sem_p_a.then(function(value) {
          // `value` to wartość spełnionej obietnicy
          // Wyświetlanie komunikatu
          if (sem_p_a.length > 0) {
            L.popup()
              .setLatLng(evt.latlng)
              .setContent('<div style="display: grid;"><button class="buttonMenu" onclick="ButtonMenuDodajSzambo()">Dodaj szambo.</button></div>')
              .openOn(map);
          }
        }).catch(function(error) {
          // Obsługa błędów
          console.log(error);
        });*/
      },
      error: function (xhr, status, error) {
        showResults(error);
      }
    });
  },
  
  //--------------------------------------------------------------------------------------------
  // obsługa wyswietlana tabeli z danymi 
  getFeatureInfo: function (evt) {
    // Make an AJAX request to the server and hope for the best
    var url = this.getFeatureInfoUrl(evt.latlng),
        showResults = L.Util.bind(this.showGetFeatureInfo, this);
		console.log({showResults});
    $.ajax({
      url: url,
      success: function (data, status, xhr) {
        var err = typeof data === 'string' ? null : data;
    //Fix for blank popup window
        var doc = (new DOMParser()).parseFromString(data, "text/html"); 
        if (doc.body.innerHTML.trim().length > 0){
			showResults(err, evt.latlng, data);
		}
      },
      error: function (xhr, status, error) {
		showResults(error);  
      }
    });
  },

 //----------------------------------------------------------------------------------------------------------
  getFeatureInfoUrl: function (latlng) {
    // Construct a GetFeatureInfo request URL given a point
    var point = this._map.latLngToContainerPoint(latlng, this._map.getZoom()),
        size = this._map.getSize(),
        params = {
          request: 'GetFeatureInfo',
          service: 'WMS',
          srs: 'EPSG:4326',
          styles: this.wmsParams.styles,
          transparent: this.wmsParams.transparent,
          version: this.wmsParams.version,      
          format: this.wmsParams.format,
          bbox: this._map.getBounds().toBBoxString(),
          feature_count: 10,
          height: size.y,
          width: size.x,
          layers: this.wmsParams.layers,
          query_layers: this.wmsParams.layers,
          info_format: 'text/html'
        };
    
    params[params.version === '1.3.0' ? 'i' : 'x'] = Math.round(point.x);
    params[params.version === '1.3.0' ? 'j' : 'y'] = Math.round(point.y);
    
    return this._url + L.Util.getParamString(params, this._url, true);
  },
  
//-----------------------------------------------------------------------------------------------------------
getFeatureInfoMenuUrl: function (latlng) {
    // Construct a GetFeatureInfo request URL given a point
    var point = this._map.latLngToContainerPoint(latlng, this._map.getZoom()),
        size = this._map.getSize(),
        params = {
          request: 'GetFeatureInfo',
          service: 'WMS',
          srs: 'EPSG:4326',
          styles: this.wmsParams.styles,
          transparent: this.wmsParams.transparent,
          version: this.wmsParams.version,      
          format: this.wmsParams.format,
          bbox: this._map.getBounds().toBBoxString(),
          feature_count: 10,
          height: size.y,
          width: size.x,
          layers: this.wmsParams.layers,
          query_layers: this.wmsParams.layers,
          info_format: 'application/json'
        };
    
    params[params.version === '1.3.0' ? 'i' : 'x'] = Math.round(point.x);
    params[params.version === '1.3.0' ? 'j' : 'y'] = Math.round(point.y);
    
    return this._url + L.Util.getParamString(params, this._url, true);
		
  },
  
//-----------------------------------------------------------------------------------------------------------  
  
  showGetFeatureInfo: function (err, latlng, content) {
    if (err) { console.log({err}); return; } // do nothing if there's an error
    
    // Otherwise show the content in a popup, or something.
    L.popup({ maxWidth: 1100})
      .setLatLng(latlng)
      .setContent(content)
      .openOn(this._map);
  }
});


//-------------------------------------------------------------------------------------------------------------------
//
//-------------------------------------------------------------------------------------------------------------------
/*async function czy_dzialka_ma_p_a(id_dzialki){
    // Wysyłamy wartość do serwera
    $.ajax({
      url: "../app/firma/ajax/ajax_czy_dza_ma_p_a.php",
      type: "POST",
      data: {
        id_dzialki: id_dzialki,
      },
      success: function(data) {
        // Jeśli wartość znajduje się w tabeli
        var czy_dzialka_ma_p_a = JSON.parse(data);
        const czy_ma_p_a = czy_dzialka_ma_p_a.map((czy_p_a) => czy_p_a.czy_p_a);
        return czy_ma_p_a;
        
      },
      error: function(error) {
        return -1; // Błąd
      }
    });
}

async function czy_dzialka_ma_p_a(id_dzialki){
  // Wysyłamy wartość do serwera
  const response = await $.ajax({
    url: "../app/firma/ajax/ajax_czy_dza_ma_p_a.php",
    type: "POST",
    data: {
      id_dzialki: id_dzialki,
    },
  });

  // Parsujemy odpowiedź
  const data = JSON.parse(response);
  console.log (data.czy_p_a);
  console.log ({data});
  // Zwracamy wartość
  return data.czy_p_a;
}
*/

async function czy_dzialka_ma_p_a(id_dzialki) {
  // Wysyłamy wartość do serwera
  const response = await $.ajax({
    url: "../app/firma/ajax/ajax_czy_dza_ma_p_a.php",
    type: "POST",
    data: {
      id_dzialki: id_dzialki,
    },
  });

  // Parsujemy odpowiedź
  try {
    const data = JSON.parse(response);
    // Zwracamy wartość
    return data;
  } catch (error) {
    console.error("Error parsing response:", error);
    // Zwróć wartość domyślna w przypadku błędu parsowania
    return false; // Or any other default value
  }
}

//-------------------------------------------------------------------------------------------------------------------
//
//-------------------------------------------------------------------------------------------------------------------

L.tileLayer.tmceBetterWMSSzamba = function (url, options) {
  return new L.TileLayer.TmceBetterWMSSzamba(url, options);  
};

//-------------------------------------------------------------------------------------------------------------------
//Funkcja otwierająca nowa strone i przekazująca jej w sesji parametry 
//-------------------------------------------------------------------------------------------------------------------
function ButtonMenuDodajSzambo(){
//  function ButtonMenuDodajSzambo(){
    map.closePopup();
    sessionStorage.setItem('lat', lat);
    sessionStorage.setItem('lng', lng);
    sessionStorage.setItem('id_dzialki', id_dzialki);
    
    window.open('../app/firma/dodaj_szambo_z_dziala.php','_blank');
}