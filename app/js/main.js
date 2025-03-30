// --- Inicjalizacja mapy ---
const map = L.map("map", {
	zoomSnap: 0.1,
	zoomDelta: 0.1,
	zoomControl: false,
	editable: true,
	doubleClickZoom: false,
	measureControl: true,
  }).setView([50.32, 19.63], 11.4);
  
  map.options.minZoom = 11;
  
  // Ustawienie granic przesuwania mapy
  const bounds = L.latLngBounds(
	L.latLng(50.171, 19.197), // Dolno-zachodni punkt granic
	L.latLng(50.467, 20.075)  // Górno-wschodni punkt granic
  );
  map.setMaxBounds(bounds);
  map.on("drag", () => map.panInsideBounds(bounds, { animate: false }));
  
  // Dodanie przycisku z kawą
  L.easyButton('<img src="css/images/coffe.png" title="Wpadnij do nas na kawę">', function () {
	map.flyTo([50.02658, 19.929859], 19);
  }).addTo(map);
  
  // --- Definicje warstw WMS ---
  const ms_url = "http://geoportal.tmce.pl:8080/geoserver/test_Arek/wms?";
  const ms_url_PG = "http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/wms?";
  
  const layers = {
	powiat: createWMSLayer(ms_url, "test_Arek:powiat", 0.6, 14),
	jednostka_ewidencyjna: createWMSLayer(ms_url_PG, "PG_L_Layers:PG_L_jednostki_ewidencyjne", 0.8, 14),
	obreb: createWMSLayer(ms_url_PG, "PG_L_Layers:PG_L_obreby", 0.8, 25),
	adresy: createWMSLayer(ms_url_PG, "PG_L_Layers:PG_L_adresy", 1, 25, 15),
	energetyka: createWMSLayer(ms_url_PG, "PG_L_Layers:PG_L_energetyka", 1, 25, 15),
	szamba: createCustomLayer(ms_url_PG, "PG_L_Layers:PG_L_szamba_adresy", 1, 25, 15),
	dzialki: createCustomLayer(ms_url_PG, "PG_L_Layers:PG_L_dzialki_label", 1, 25, 15),
	budynki: createCustomLayer(ms_url_PG, "PG_L_Layers:PG_L_budynki_egib", 1, 25, 15),
	pozostale_sieci: createWMSLayer(ms_url_PG, "PG_L_Layers:PG_L_pozostale_sieci", 1, 25, 15),
	LinieKanalizacji: createWMSLayer(ms_url_PG, "PG_L_Layers:PG_L_LinieKanalizacji", 1, 25, 15),
  };
  
  // Funkcja do tworzenia warstw WMS
  function createWMSLayer(url, layerName, opacity, maxZoom, minZoom = 0) {
	return L.tileLayer.wms(url, {
	  layers: layerName,
	  format: "image/png",
	  transparent: true,
	  opacity,
	  maxZoom,
	  minZoom,
	});
  }
  
  // Funkcja do tworzenia niestandardowych warstw
  function createCustomLayer(url, layerName, opacity, maxZoom, minZoom = 0) {
	return L.tileLayer.tmceBetterWMS(url, {
	  layers: layerName,
	  format: "image/png",
	  transparent: true,
	  opacity,
	  maxZoom,
	  minZoom,
	});
  }
  
  // --- Warstwy bazowe ---
  const baseLayers = {
	"Google Hybryda": createGoogleLayer("s,h"),
	"Google Streets": createGoogleLayer("m"),
	"OpenStreet": L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
	  maxZoom: 20,
	  attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
	}),
	"Ortofotomapa 25cm": createWMSLayer(ms_url, "test_Arek:ORTOFOTOMAPA", 1, 22, 17),
	"Brak": L.tileLayer("", { maxZoom: 20 }),
  };
  
  // Funkcja do tworzenia warstw Google
  function createGoogleLayer(type) {
	return L.tileLayer(`https://{s}.google.com/vt/lyrs=${type}&x={x}&y={y}&z={z}`, {
	  maxZoom: 20,
	  subdomains: ["mt0", "mt1", "mt2", "mt3"],
	});
  }
  
  // --- Ikona TMCE ---
  const TMCEicon = L.icon({
	iconUrl: "css/images/logoTMCE1.jpg",
	iconSize: [100, 50],
	iconAnchor: [22, 94],
	popupAnchor: [0, -94],
  });
  
  const TMCEmarker = L.marker([50.02658, 19.929859], { icon: TMCEicon })
	.addTo(map)
	.bindPopup("<h3>Już za niedługo to będzie najlepszy portal mapowy na świecie;)</h3> <center><img src='./css/images/champions2.jpg'/><center>");
  
  // --- Grupy warstw ---
  const groupedOverlays = {
	"TMCE": {
	  "<img src='legenda/TMCE_pinezka.png' align='center' style='margin:3px'>Pinezka TMCE</img>": TMCEmarker,
	},
	"GRANICE ADMINISTRACYJNE": {
	  "<img src='legenda/powiat.png' align='top' style='margin:3px'>Granica powiatu</img>": layers.powiat,
	  "<img src='legenda/gminy.png' align='center' style='margin:3px'>Granica gmin</img>": layers.jednostka_ewidencyjna,
	  "<img src='legenda/obreby.png' align='center' style='margin:3px'>Granica obrębów</img>": layers.obreb,
	},
	"GEODEZJA": {
	  "<img src='legenda/dzialki.png' align='top' style='margin:3px'>Działki</img>": layers.dzialki,
	  "<img src='legenda/budynki.png' align='top' style='margin:3px'>Budynki EGiB</img>": layers.budynki,
	  "<img src='legenda/adresy.png' align='center' style='margin:2px'>Punkty adresowe</img>": layers.adresy,
	  "<img src='legenda/kanalizacyjna.png' align='top' style='margin:3px'>Linie kanalizacji</img>": layers.LinieKanalizacji,
	  "<img src='legenda/szamba.png' align='top' style='margin:3px'>Szamba</img>": layers.szamba,
	},
  };
  
  // Dodanie kontrolera warstw
  L.control.groupedLayers(baseLayers, groupedOverlays).addTo(map);
  
  // Dodanie skali
  L.control.scale({ imperial: false }).addTo(map);

// Nasłuchuj na poruszenie myszką na mapie
map.on("mousemove", function (e) {
  const markerPlaceWSP_WGS_84 = document.querySelector(".WSP_WGS_84");
  const x = e.latlng.lat.toFixed(6);
  const y = e.latlng.lng.toFixed(6);
  markerPlaceWSP_WGS_84.innerHTML = `${x}&nbsp&nbsp${y}`;

  // Obsługa comboboxa do wyboru projekcji
  const comboBox = document.getElementById("ComboProj");
  const selectedValue = comboBox.value;
  const markerPlaceWSP_Inne = document.querySelector(".WSP_inne");

  // Definicje projekcji
  const projections = {
    PUWG2000s6: "+proj=tmerc +lat_0=0 +lon_0=18 +k=0.999923 +x_0=6500000 +y_0=0 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs",
    PUWG1992: "+proj=tmerc +lat_0=0 +lon_0=19 +k=0.9993 +x_0=500000 +y_0=-5300000 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs",
    UTM: "+proj=utm +zone=34 +ellps=WGS72 +towgs84=0,0,1.9,0,0,0.814,-0.38 +units=m +no_defs",
    WKID: "+proj=merc +a=6378137 +b=6378137 +lat_ts=0 +lon_0=0 +x_0=0 +y_0=0 +k=1 +units=m +nadgrids=@null +wktext +no_defs",
  };

  // Przetwarzanie współrzędnych na podstawie wybranej projekcji
  if (projections[selectedValue]) {
    const projectedCoords = proj4(projections[selectedValue], [e.latlng.lng, e.latlng.lat]);
    markerPlaceWSP_Inne.innerHTML = `${projectedCoords[0].toFixed(2)}&nbsp&nbsp${projectedCoords[1].toFixed(2)}`;
  }
});

//------------------------------------------------------
//zwijanie rozwijanie grupy warstw
// --- Funkcje pomocnicze ---
function toggleLayerGroup(button, labels) {
	button.classList.toggle("up");
	const isHidden = button.className === "down";
	labels.forEach(label => label.style.display = isHidden ? "none" : "block");
  }
  
  function createButtonWithToggle(className, labels, container) {
	const button = document.createElement("div");
	button.classList = className;
	container.before(button);
	button.addEventListener("click", () => toggleLayerGroup(button, labels));
	return button;
  }
  
  // --- Zwijanie i rozwijanie grup warstw ---
  const labelsPRG = document.querySelectorAll("#leaflet-control-layers-group-2 > label:nth-child(n+2)");
  const labelsGeodezja = document.querySelectorAll("#leaflet-control-layers-group-3 > label:nth-child(n+2)");
  
  const buttonPRG = createButtonWithToggle("up down", labelsPRG, document.querySelector("#leaflet-control-layers-group-2"));
  const buttonGeodezja = createButtonWithToggle("up down", labelsGeodezja, document.querySelector("#leaflet-control-layers-group-3"));
  
  // --- Kontroler warstw on/off ---
  const buttonPokaz = document.querySelector(".pokaz");
  const zawartoscMapy = document.querySelector(".leaflet-control-layers");
  const strzalki = document.querySelector("#strzalki-btn");
  
  buttonPokaz.addEventListener("click", () => {
	buttonPokaz.classList.toggle("ukryjLayers");
	zawartoscMapy.classList.toggle("ukryj-leaflet-control");
	strzalki.textContent = buttonPokaz.classList.contains("ukryjLayers") ? "<<" : ">>";
  });
  
  // --- Stop "click" na mapie dla kontenerów ---
  const stopPropagationElements = [
	document.getElementById("header"),
	document.getElementById("leftpanel"),
	document.getElementById("leftpanelSzukaj"),
	document.getElementById("leftpanelAtrybuty")
  ];
  
  stopPropagationElements.forEach(el => {
	L.DomEvent.disableScrollPropagation(el);
	L.DomEvent.disableClickPropagation(el);
  });
  
  // --- PRG wyszukiwarka przez WFS ---
  const akceptujGminaEl = document.querySelector("div.gmina > button.akceptuj");
  const akceptujObrebEl = document.querySelector("div.obreb > button.akceptuj");
  const buttonWyczyscElGm = document.querySelector("div.gmina>.wyczysc");
  const buttonwyczyscElOb = document.querySelector("div.obreb>.wyczysc");
  let selectElObreb = document.querySelector("#obreby");
  let selectELGmina = document.querySelector("#gmina").value;
  
  function wybierzGmina(url, layerName, button) {
	$.getJSON(url).then(res => {
	  const layerWFS = L.geoJson(res).addTo(map);
	  button.addEventListener("click", () => map.removeLayer(layerWFS));
	});
  }
  
  akceptujGminaEl.addEventListener("click", () => {
	const url = `http://geoportal.tmce.pl:8080/geoserver/test_Arek/ows?service=WFS&version=1.0.0&request=GetFeature&CQL_FILTER=TERYT='${selectELGmina}'&typeName=test_Arek%3AJednostka%20ewidencyjna&maxFeatures=10&outputFormat=application%2Fjson&srsName=epsg:4326`;
	wybierzGmina(url, "test_Arek:Jednostka ewidencyjna", buttonWyczyscElGm);
  });
  
  akceptujObrebEl.addEventListener("click", () => {
	const url = `http://geoportal.tmce.pl:8080/geoserver/test_Arek/ows?service=WFS&version=1.0.0&request=GetFeature&CQL_FILTER=TERYT='${selectElObreb.value}'&typeName=test_Arek%3Aobreby&maxFeatures=20&outputFormat=application%2Fjson&srsName=epsg:4326`;
	wybierzGmina(url, "test_Arek:Obreby", buttonwyczyscElOb);
  });
  
  // --- Funkcja do zmiany gminy ---
  function changeGmina() {
	selectELGmina = document.querySelector("#gmina").value;
	selectElObreb.innerHTML = "";
	const obreby = {
	  "121203_2": obrebyBoleslaw,
	  "121204_2": obrebyKlucze,
	  "121205_5": obrebyOlkusz,
	  "121206_2": obrebyTrzyciaz
	};
	createOptionElements(obreby[selectELGmina] || []);
  }
  
  function createOptionElements(obreb) {
	obreb.forEach(item => {
	  const option = document.createElement("option");
	  option.value = item.substring(0, 13);
	  option.text = item;
	  selectElObreb.appendChild(option);
	});
  }
  
  changeGmina();
  
  // --- Zegar ---
  function zegar() {
	const d = new Date();
	const formattedTime = `${d.getFullYear()}.${addLeadingZero(d.getMonth() + 1)}.${addLeadingZero(d.getDate())} ${addLeadingZero(d.getHours())}:${addLeadingZero(d.getMinutes())}:${addLeadingZero(d.getSeconds())}`;
	document.querySelector(".czas").innerHTML = formattedTime;
  }
  setInterval(zegar, 1000);
  
  function addLeadingZero(number) {
	return number < 10 ? "0" + number : number;
  }
  
  // --- Wyszukiwarka adresów ---
  function getCities(gmina, dropdownId) {
	const citiesDropDown = document.querySelector(dropdownId);
	if (!gmina.trim()) {
	  citiesDropDown.disabled = true;
	  citiesDropDown.selectedIndex = 0;
	  return;
	}
	const miejscowosci = gm_mc[gmina];
	citiesDropDown.innerHTML = miejscowosci.map(miejscowosc => `<option value="${miejscowosc}">${miejscowosc}</option>`).join("");
	citiesDropDown.disabled = false;
  }
  
  function adresyLista(url, searchTextId, controlSearch) {
	document.getElementById(searchTextId).disabled = true;
	document.getElementById(searchTextId).placeholder = "Ładuję adresy...";
	$.getJSON(url).then(res => {
	  res.features.forEach(feature => {
		feature.properties.pelenadres = `${feature.properties.kod_pocztowy} ${feature.properties.miejscowosc}, ${feature.properties.ulica} ${feature.properties.numer}`;
	  });
	  const layerWFSAdresy = L.geoJson(res);
	  controlSearch.setLayer(layerWFSAdresy);
	}).then(() => {
	  document.getElementById(searchTextId).placeholder = "Wyszukaj adres";
	  document.getElementById(searchTextId).disabled = false;
	});
  }
  
  // --- Dodanie kontrolerów ---
  L.control.browserPrint().addTo(map);
  L.control.measure().addTo(map);


// --- Funkcje pomocnicze ---
function addLeadingZero(number) {
	return number < 10 ? "0" + number : number;
  }
  
  function createDropdownOptions(data, dropdownId) {
	const dropdown = document.querySelector(dropdownId);
	if (!data || data.trim() === "") {
	  dropdown.disabled = true;
	  dropdown.selectedIndex = 0;
	  return;
	}
	const options = gm_mc[data].map(
	  (item) => `<option value="${item}">${item}</option>`
	);
	dropdown.innerHTML = options.join("");
	dropdown.disabled = false;
  }
  
  function fetchGeoJson(url, callback) {
	$.getJSON(url).then((res) => {
	  const layer = L.geoJson(res);
	  callback(layer, res);
	});
  }
  
  // --- Zegar ---
  function initializeClock() {
	const zegarContent = document.querySelector(".czas");
	function zegar() {
	  const d = new Date();
	  const formattedTime = `${d.getFullYear()}.${addLeadingZero(
		d.getMonth() + 1
	  )}.${addLeadingZero(d.getDate())} ${addLeadingZero(
		d.getHours()
	  )}:${addLeadingZero(d.getMinutes())}:${addLeadingZero(d.getSeconds())}`;
	  zegarContent.innerHTML = formattedTime;
	}
	zegar();
	setInterval(zegar, 1000);
  }
  
  // --- Wyszukiwarka adresów ---
  function initializeAddressSearch() {
	const controlSearchAdresy = new L.Control.Search({
	  layer: new L.geoJson(adresInicjalny),
	  collapsed: false,
	  initial: false,
	  hideMarkerOnCollapse: true,
	  container: "szukaj-adres",
	  textPlaceholder: "Wyszukaj adres",
	  propertyName: "pelenadres",
	});
  
	map.addControl(controlSearchAdresy);
  
	function adresyLista() {
	  const miejscowosc = document.querySelector("#miejscowosci").value;
	  const searchText = document.getElementById("searchtext14");
	  searchText.disabled = true;
	  searchText.placeholder = "Ładuję adresy...";
	  const url = `http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=1.0.0&request=GetFeature&CQL_FILTER=miejscowosc%20LIKE%20%27${miejscowosc}%25%27&typeName=PG_L_Layers:PG_L_adresy&outputFormat=application%2Fjson&srsName=epsg:4326`;
  
	  fetchGeoJson(url, (layer) => {
		controlSearchAdresy.setLayer(layer);
		searchText.placeholder = "Wyszukaj adres";
		searchText.disabled = false;
	  });
	}
  
	return adresyLista;
  }
  
  // --- Wyszukiwarka szamb ---
  function initializeSzambaSearch() {
	const controlSearchAdresySz = new L.Control.Search({
	  layer: new L.geoJson(),
	  collapsed: false,
	  initial: false,
	  hideMarkerOnCollapse: true,
	  container: "szukaj-szambo",
	  textPlaceholder: "Wyszukaj szamba",
	  propertyName: "pelenadres",
	  marker: false,
	});
  
	map.addControl(controlSearchAdresySz);
  
	function adresyListaSz() {
	  const miejscowosc = document.querySelector("#miejscowosci_sz").value;
	  const searchText = document.getElementById("searchtext15");
	  searchText.disabled = true;
	  searchText.placeholder = "Ładuję adresy...";
	  const url = `http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=1.0.0&request=GetFeature&CQL_FILTER=miejscowosc%20LIKE%20%27${miejscowosc}%25%27&typeName=PG_L_Layers:PG_L_szamba_adresy&outputFormat=application%2Fjson&srsName=epsg:4326`;
  
	  fetchGeoJson(url, (layer) => {
		controlSearchAdresySz.setLayer(layer);
		searchText.placeholder = "Wyszukaj adres";
		searchText.disabled = false;
	  });
	}
  
	return adresyListaSz;
  }
  
  // --- Wyszukiwarka działek ---
  function initializeDzialkiSearch() {
	const controlSearch = new L.Control.Search({
	  layer: new L.geoJson(),
	  collapsed: false,
	  initial: false,
	  hideMarkerOnCollapse: true,
	  container: "szukaj-PRG",
	  textPlaceholder: "Wyszukaj działkę",
	  propertyName: "id_dzialki",
	  marker: false,
	});
  
	map.addControl(controlSearch);
  
	function changeObreb() {
	  const obreb = document.querySelector("#obreby").value.substring(0, 13);
	  const searchText = document.getElementById("searchtext16");
	  searchText.disabled = true;
	  searchText.placeholder = "Ładuję listę działek...";
	  const url = `http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=1.0.0&request=GetFeature&CQL_FILTER=id_dzialki%20LIKE%20%27${obreb}%25%27&typeName=PG_L_Layers:PG_L_dzialki&outputFormat=application%2Fjson&srsName=epsg:4326`;
  
	  fetchGeoJson(url, (layer) => {
		controlSearch.setLayer(layer);
		searchText.placeholder = "Wyszukaj działkę";
		searchText.disabled = false;
	  });
	}
  
	return changeObreb;
  }
  
  // --- Inicjalizacja ---
  initializeClock();
  const adresyLista = initializeAddressSearch();
  const adresyListaSz = initializeSzambaSearch();
  const changeObreb = initializeDzialkiSearch();
  //---------------------------------------------------------

//Działki
const closePrgEl=document.getElementById("close-PRG");
const wyszukajPrgEl=document.querySelector("#szukaj-PRG");

// Punkty adresowe 
const closeAdrEl=document.getElementById("close-ADR");
const wyszukajAdrEl=document.querySelector("#szukaj-adres");

// Szamba 
const closeAdrSzam=document.getElementById("close-SZAMBA");
const wyszukajAdrSzam=document.querySelector("#szukaj-szambo");

const closeAtrybutyEl=document.getElementById("close-Atrybuty");
const AtrybutyEl=document.querySelector("#atrybuty");

const closeWspEl=document.getElementById("close-WSP");
const wyszukajWspEl=document.querySelector("#szukaj-wsp");

const szukajEl=document.querySelector(".szukaj");

	
szukajEl.addEventListener("click",()=>{
	if(leftpanelEl.classList.contains("ukryj"))
	{
		leftpanelEl.classList.remove("ukryj")
		szukajEl.classList.add("on")
	}
	else 
	{
		leftpanelEl.classList.add("ukryj")
		szukajEl.classList.remove("on")
	}
})
	
closePrgEl.addEventListener("click", ()=>{	
	wyszukajPrgEl.classList.remove("ukryj");
	wyszukajAdrEl.classList.add("ukryj");
	wyszukajAdrSzam.classList.add("ukryj");
	wyszukajWspEl.classList.add("ukryj");
	closePrgEl.classList.add("on");
	closeAdrEl.classList.remove("on");
	closeAdrSzam.classList.remove("on")
	closeWspEl.classList.remove("on")
});

closeAdrEl.addEventListener("click",()=>{
	wyszukajPrgEl.classList.add("ukryj");
	wyszukajAdrEl.classList.remove("ukryj");
	wyszukajAdrSzam.classList.add("ukryj");
	wyszukajWspEl.classList.add("ukryj");
	closePrgEl.classList.remove("on");
	closeAdrEl.classList.add("on");
	closeAdrSzam.classList.remove("on")
	closeWspEl.classList.remove("on");
});


closeWspEl.addEventListener("click",()=>{
	wyszukajPrgEl.classList.add("ukryj");
	wyszukajAdrEl.classList.add("ukryj");
	wyszukajAdrSzam.classList.add("ukryj");
	wyszukajWspEl.classList.remove("ukryj");	
	closePrgEl.classList.remove("on");
	closeAdrSzam.classList.remove("on")
	closeAdrEl.classList.remove("on");
	closeWspEl.classList.add("on");
});

closeAdrSzam.addEventListener("click",()=>{
	wyszukajPrgEl.classList.add("ukryj");
	wyszukajAdrEl.classList.add("ukryj");
	wyszukajAdrSzam.classList.remove("ukryj");
	wyszukajWspEl.classList.add("ukryj");
	closePrgEl.classList.remove("on");
	closeAdrEl.classList.remove("on");
	closeAdrSzam.classList.add("on")
	closeWspEl.classList.remove("on");
});

closeAtrybutyEl.addEventListener("click",()=>{
	if (AtrybutyEl.classList.contains("ukryj")){
		AtrybutyEl.classList.remove("ukryj");
		closeAtrybutyEl.classList.add("on")
	}
	else
	{
		AtrybutyEl.classList.add("ukryj");
		closeAtrybutyEl.classList.remove("on")
	}
});

//-------------------------------------------------------------
let markerXY;

function wyszukajWspolrzedne(){
	if(markerXY!= null)
	{
		markerXY.remove();
	}
	let x=document.getElementById("X").value;
	let y=document.getElementById("Y").value;
	markerXY= L.marker([x, y]);
	markerXY.addTo(map);
	map.setView([x, y], 19);
};

let idzDoEl=document.getElementById("btnXY");
idzDoEl.addEventListener("click",wyszukajWspolrzedne);

//-------------------------------------------------------------	
// --- Funkcje do obsługi atrybutów ---
//tabela atrybutow dla pkt adresowych
function inactive(){
	atrybutyWyswietlEl.removeEventListener("click",GetAttribute);
	atrybutyWyswietlEl.classList.add("inactive");
};

function active(){
	atrybutyWyswietlEl.addEventListener("click",GetAttribute);
	atrybutyWyswietlEl.classList.remove("inactive");
}

function checkLayers(){
	let selectedLayer= document.getElementById("wybierz-warstwe").value;
	
	if(selectedLayer==='')
	{
		atrybutyZabijEl.classList.add("inactive");
		inactive();
	}
	else 
	{
		atrybutyWyswietlEl.addEventListener("click",GetAttribute);
		atrybutyWyswietlEl.classList.remove("inactive");
		atrybutyZabijEl.classList.remove("inactive");
	}
	return selectedLayer;
};

let prostokat;
let adrGeojson;

// --- Funkcja pomocnicza do tworzenia tabeli atrybutów ---
function createAttributeTable(features, container) {
  if (features.length === 0) return;

  const headers = Object.keys(features[0].properties);
  headers.unshift("ID");

  const headerRowHTML = headers.map(header => `<th>${header}</th>`).join("");
  const allRecordsHTML = features.map(feature => {
    const rowHTML = headers.map((header, index) => {
      if (index === 0) {
        return `<td>${feature.geometry.coordinates}</td>`;
      }
      return `<td>${feature.properties[header]}</td>`;
    }).join("");
    return `<tr>${rowHTML}</tr>`;
  }).join("");

  const table = document.createElement("table");
  table.setAttribute("class", "pure-table pure-table-bordered atrybuty");
  table.innerHTML = `<thead><tr>${headerRowHTML}</tr></thead>${allRecordsHTML}`;
  container.appendChild(table);
}

// --- Funkcja do obsługi zaznaczania prostokąta ---
function GetAttribute() {
  const selectedLayer = document.getElementById("wybierz-warstwe").value;
  const tabelaAtrybutowusun = document.querySelector(".atrybuty");

  if (prostokat) prostokat.remove();
  if (tabelaAtrybutowusun) tabelaAtrybutowusun.remove();
  if (adrGeojson) adrGeojson.remove();

  let oneCorner, twoCorner;

  L.DomUtil.addClass(map._container, "crosshair-cursor-enabled");

  map.on("mousedown", setOneCorner);
  map.on("mouseup", setTwoCorner);

  function setOneCorner(e) {
    map.dragging.disable();
    oneCorner = e.latlng;
    map.on("mousemove", drawRectangle);
  }

  function drawRectangle(e) {
    if (prostokat) prostokat.remove();
    twoCorner = e.latlng;
    const bounds = [oneCorner, twoCorner];
    prostokat = L.rectangle(bounds, { color: "#ff7800", weight: 1 }).addTo(map);
  }

  function setTwoCorner(e) {
    map.off("mousemove", drawRectangle);
    map.dragging.enable();
    if (prostokat) prostokat.remove();

    twoCorner = e.latlng;
    const bounds = [oneCorner, twoCorner];
    prostokat = L.rectangle(bounds, { color: "#ff7800", weight: 1 }).addTo(map);

    const bbox = `${Math.min(oneCorner.lng, twoCorner.lng)},${Math.min(oneCorner.lat, twoCorner.lat)},${Math.max(oneCorner.lng, twoCorner.lng)},${Math.max(oneCorner.lat, twoCorner.lat)}`;
    const url = `http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=2.0.0&request=GetFeature&BBOX=${bbox},epsg:4326&typeName=PG_L_Layers%3A${selectedLayer}&outputFormat=application%2Fjson&srsName=epsg:4326`;

    fetchGeoJson(url, res => {
      adrGeojson = L.geoJson(res).addTo(map);
      if (res.features.length > 0) {
        createAttributeTable(res.features, AtrybutyEl);
      }
    });

    map.off("mousedown");
    map.off("mouseup");
    L.DomUtil.removeClass(map._container, "crosshair-cursor-enabled");
  }
}

// --- Funkcja pomocnicza do pobierania GeoJSON ---
function fetchGeoJson(url, callback) {
  $.getJSON(url).then(callback);
}

// --- Funkcja do obsługi kliknięcia w wiersz tabeli ---
function handleRowClick(e, selectedLayer) {
  if (adrGeojson) adrGeojson.remove();

  const tr = e.currentTarget;
  const coordinates = tr.innerText.split("\t")[0].split(",");
  const properties = {
    miejscowosc: tr.innerText.split("\t")[1],
    ulica: tr.innerText.split("\t")[2],
    numer: tr.innerText.split("\t")[3],
    kod_pocztowy: tr.innerText.split("\t")[4]
  };

  const geojsonFeature = {
    type: "Feature",
    geometry: { type: "Point", coordinates },
    properties
  };

  adrGeojson = L.geoJSON(geojsonFeature).addTo(map);
  map.fitBounds(adrGeojson.getBounds(), { paddingBottomRight: [250, 250], maxZoom: 19 });

  const tooltipData = `
    <leaflet-tooltip-adres>
      <ttp_naglowek>Punkt adresowy</ttp_naglowek>
      <hr style="width:98%;border-width:1px;margin-left:auto;margin-right:auto;height:1px">
      <table class="ttp_table">
        <tbody>
          <tr><td class="ttp-tytuly">Miejscowość:</td><td class="ttp-dane">${properties.miejscowosc}</td></tr>
          <tr><td class="ttp-tytuly">Ulica:</td><td class="ttp-dane">${properties.ulica}</td></tr>
          <tr><td class="ttp-tytuly">Numer:</td><td class="ttp-dane">${properties.numer}</td></tr>
          <tr><td class="ttp-tytuly">Kod pocztowy:</td><td class="ttp-dane">${properties.kod_pocztowy}</td></tr>
        </tbody>
      </table>
    </leaflet-tooltip-adres>
  `;

  adrGeojson.bindTooltip(tooltipData).openTooltip();
}

// --- Funkcja do usuwania tabeli i prostokąta ---
function clearAttributes() {
  const tabelaAtrybutow = document.querySelector(".pure-table");
  if (prostokat) prostokat.remove();
  if (adrGeojson) adrGeojson.remove();
  if (tabelaAtrybutow) tabelaAtrybutow.remove();
  map.off("mousedown");
  map.off("mouseup");
}

// --- Obsługa przycisków ---
document.querySelector(".atrybutyZabij").addEventListener("click", clearAttributes);

// --- Inicjalizacja kontrolerów ---
L.control.browserPrint().addTo(map);
L.control.measure().addTo(map);