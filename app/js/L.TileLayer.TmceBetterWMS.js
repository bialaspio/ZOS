L.TileLayer.TmceBetterWMS = L.TileLayer.WMS.extend({

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
      console.log("menu Szambo info");
      console.log({showResults});
      
  $.ajax({
      url: url,
      success: function (data, status, xhr) {
        var err = typeof data === 'string' ? null : data;
        console.log ({data});
        console.log (data.features.length);
        console.log (data.features[0].properties.id_szamba);
        
        let tmp = data.features[0].properties;
        let data_to_cooke=[];
          
        for (const property in tmp) {
          console.log(`${property}: ${tmp[property]}`);
          if (property == 'id_szamba')
            data_to_cooke.push(tmp[property])
        }
      
        if (data.features.length > 0){
          var cookie =JSON.parse(JSON.stringify(data_to_cooke));
          console.log ({cookie});
          document.cookie = "id_szamba="+cookie;
          console.log (document.cookie);
        
        L.popup()
          .setLatLng(evt.latlng)
          .setContent('<div style = "display: grid; "><button class = "buttonMenu" onclick="ButtonMenuProt()">Protokół wywozu</button><button class = "buttonMenu" onclick="ProtokolWywozy_v02()">Protokół wywozu2</button><button class = "buttonMenu" onclick="ButtonMenuHistWyw()">Historia wywozu</button><button class = "buttonMenu" onclick="ButtonMenuCharZbior()">Zbior char</button></div>')
          .openOn(map);
        }
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

L.tileLayer.tmceBetterWMS = function (url, options) {
  return new L.TileLayer.TmceBetterWMS(url, options);  
};

//--------------------------------------------------------------------------------------------
  
	  
function ButtonMenuProt(){
  map.closePopup();
  window.open('../app/szamba/ProtWyw.php','_blank');
}

function ButtonMenuHistWyw(){
	map.closePopup();
  console.log("ButtonMenuHistWyw");
	window.open('../app/szamba/HistWyw.php','_blank');
}

function ProtokolWywozy_v02(){
  map.closePopup();
	window.open('../app/firma/ProtWyw_v02.php','_blank');
}

function ButtonMenuCharZbior(){
  map.closePopup();
	window.open('../app/firma/charakt_zbiornika.php','_blank');
}
