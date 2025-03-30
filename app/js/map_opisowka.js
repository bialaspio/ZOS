// Pobierz dane z cookie
var cookie = document.cookie;
console.log({cookie});
const parts = cookie.split(`;`);
console.log({parts});
const part= parts[0].split(`^`);

const warstwa = part[0];
const selectedLayerGrid  = part[0];
console.log({warstwa});

const elementy = part[1].split(`,`);
//console.log({elementy});

let ilosc_elementow = elementy.length;
//console.log(ilosc_elementow);

let lista_elementow = '\'';

for (var licznik = 0; licznik < ilosc_elementow; licznik ++){

	if (licznik ==0){
		lista_elementow=lista_elementow+elementy[licznik]+'\'';
	}
	else{
		lista_elementow=lista_elementow+',\''+elementy[licznik]+'\'';
	}
}
//console.log({lista_elementow});


let map = L.map('map',{
		zoomSnap: 0.1,	
		zoomDelta: 0.1,
		zoomControl: false,
		editable: true,
		doubleClickZoom: false,
		measureControl: true,
});

map.setView([50.32, 19.63], 11.0);

map.options.minZoom = 11;

let zoom_bar = new L.Control.ZoomBar({position: 'topleft'}).addTo(map);

// Ustawienie granic przesuwania mapy
let southWest = L.latLng(50.171,19.197 ); // Dolno-zachodni punkt granic
let northEast = L.latLng(50.467, 20.075);  // Górno-wschodni punkt granic
let bounds = L.latLngBounds(southWest, northEast);


map.setMaxBounds(bounds);  // Ustawienie granic
map.on('drag', function() {
    map.panInsideBounds(bounds, { animate: false });
});

	
L.easyButton('<img src="../css/images/coffe.png" title="Wpadnij do nas na kawę">', function(btn, map){
			map.flyTo([50.02658, 19.929859],19);
}).addTo(map);

const ms_url = "http://geoportal.tmce.pl:8080/geoserver/test_Arek/wms?";
const ms_url_PG = "http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/wms?";

const powiat = L.tileLayer.wms(ms_url, {
	layers: 'test_Arek:powiat',
	format: 'image/png',
	transparent: true,
	opacity: 0.6,
	maxZoom: 14
}).addTo(map);

const jednostka_ewidencyjna = L.tileLayer.wms(ms_url_PG, {
	layers: 'PG_L_Layers:PG_L_jednostki_ewidencyjne',
	format: 'image/png',
	transparent: true,
	opacity: 0.8,
	maxZoom: 14,
	}).addTo(map);
	
const obreb = L.tileLayer.wms(ms_url_PG, {
	layers: 'PG_L_Layers:PG_L_obreby',
	format: 'image/png',
	transparent: true,
	opacity: 0.8,
	maxZoom: 25,
}).addTo(map);

console.log({obreb});

const dzialki = L.tileLayer.betterWms(ms_url_PG, {
	layers: 'PG_L_Layers:PG_L_dzialki_label',
	format: 'image/png',
	transparent: true,
	opacity: 0.8,
	minZoom: 15,
	maxZoom: 25,
}).addTo(map);

const budynki = L.tileLayer.betterWms(ms_url_PG, {
	layers: 'PG_L_Layers:PG_L_budynki_egib',
	format: 'image/png',
	transparent: true,
	opacity: 1,
	minZoom: 15,
	maxZoom: 25,
}).addTo(map);

const adresy = L.tileLayer.betterWms(ms_url_PG, {
	layers: 'PG_L_Layers:PG_L_adresy',
	format: 'image/png',
	transparent: true,
	opacity: 1,
	minZoom: 15,
	maxZoom: 25,
}).addTo(map);
const energetyka = L.tileLayer.betterWms(ms_url_PG, {
	layers: 'PG_L_Layers:PG_L_energetyka',
	format: 'image/png',
	transparent: true,
	opacity: 1,
	minZoom: 15,
	maxZoom: 25,
}).addTo(map);

//const energetyka_nN = L.tileLayer.betterWms(ms_url_PG, {
//	layers: 'PG_L_Layers:PG_energetyka_nN_Tauron',
//	format: 'image/png',
//	transparent: true,
//	opacity: .8,
//	minZoom: 15,
//	maxZoom: 25,
//});

const pozostale_sieci = L.tileLayer.betterWms(ms_url_PG, {
	layers: 'PG_L_Layers:PG_L_pozostale_sieci',
	format: 'image/png',
	transparent: true,
	opacity: 1,
	minZoom: 15,
	maxZoom: 25,
}).addTo(map);

const szamba = L.tileLayer.betterWms(ms_url_PG, {
	layers: 'PG_L_Layers:PG_L_szamba_adresy',
	format: 'image/png',
	transparent: true,
	opacity: 1,
	maxZoom: 25,
}).addTo(map);
						
const googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{
	maxZoom: 20,
	subdomains:['mt0','mt1','mt2','mt3']
});
				
const googleSat = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',{
	maxZoom: 20,
	subdomains:['mt0','mt1','mt2','mt3']
});
		
const googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',{
	maxZoom: 20,
	subdomains:['mt0','mt1','mt2','mt3']
});

googleStreets.addTo(map);


//------------------------------------------------------------------------------------------------------



    console.log(selectedLayerGrid);
    let cqlFilter='';
    let selectedData = lista_elementow;
   // preloader.style.zIndex= "12010";
   // console.log(preloader);
    console.log({selectedData});
    let str='';
    
    
	let idSelected =selectedData;
    if(selectedLayerGrid==='Jednostka%20ewidencyjna'){
        cqlFilter=`TERYT%20in%20${idSelected}`
    }
    else if(selectedLayerGrid==='PG_L_obreby_nazwa'){
		cqlFilter=`TERYT%20in%20(${idSelected})`
    }
    else if(selectedLayerGrid==='PG_L_dzialk_pol'){
        cqlFilter=`id_dzialki%20in%20(${idSelected})`
    }
    else if(selectedLayerGrid==='PG_L_budynki_opisowka'){
        cqlFilter=`id_budynku%20in%20(${idSelected})`
    }
    else if(selectedLayerGrid==='PG_L_adresy_id'){
        cqlFilter=`ident%20in%20(${idSelected})`
    }
    else if(selectedLayerGrid==='PG_L_slupy_geo_sz'){
        cqlFilter=`ogc_fid%20in%20(${idSelected})`
    }
    else if(selectedLayerGrid==='PG_L_slup_nn'){
        cqlFilter=`num_slup%20in%20(${idSelected})`
    }
	else if(selectedLayerGrid==='PG_L_szamba_adresy'){
        cqlFilter=`id_szamba%20in%20(${idSelected})`
    }
    console.log({cqlFilter});
	
    
	let urlWFSObreb=`http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=1.0.0&request=GetFeature&CQL_FILTER=${cqlFilter}&typeName=PG_L_Layers%3A${selectedLayerGrid}&maxFeatures=50&outputFormat=application%2Fjson&srsName=epsg:4326`;
	
    $.getJSON(urlWFSObreb).then((res)=>{
       
        layerWFSObreb=L.geoJson(res,{
            style: { radius: 1, fillColor: "red", color: "Crimson", weight: 4,  opacity: 0.8,  fillOpacity: 0.4}
        })
		
			.bindPopup("<h4>Nie pamiętasz co zaznaczyłeś w opisówce?<br> To tam wróć<h4><center><img style='width:50px' src='./css/images/emotikon.svg'/><center>").addTo(map);
		}).then(()=>{
            map.fitBounds(layerWFSObreb.getBounds().pad(0.1));
			console.log({map});
			console.log({layerWFSObreb});
		});





//------------------------------------------------------------------------------------------------------

const openStreet=L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
	maxZoom: 20,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
});

const ortofoto = L.tileLayer.betterWms(ms_url, {
	layers: 'test_Arek:ORTOFOTOMAPA',
	format: 'image/png',
	transparent: true,
	opacity: 1,
	maxZoom: 22,
	maxNativeZoom: 17
});
		
const TMCEicon = L.icon({
	iconUrl: 'css/images/logoTMCE1.jpg',
	iconSize: [100, 50],
	iconAnchor: [22, 94],
	popupAnchor: [0, -94],
});

const beztla = L.tileLayer('',{maxZoom: 20});
		
let TMCEmarker = L.marker([50.02658, 19.929859], {icon: TMCEicon}).addTo(map).bindPopup("<h3>Już za niedługo to będzie najlepszy portal mapowy na świecie;)</h3> <center><img src='./css/images/champions2.jpg'/><center>");

L.control.scale({
	  imperial: false,
}).addTo(map);

//map.addControl(new L.Control.SwitchScaleControl({updateWhenIdle: true, scales: [1000, 2000, 5000, 10000, 25000, 50000, 100000, 200000, 500000] })); 
	
let baseLayers = {
    "Google Hybryda": googleHybrid,
    "Google Streets": googleStreets,
	"OpenStreet": openStreet,
	"Ortofotomapa 25cm": ortofoto,
	"Brak": beztla};
	
let groupedOverlays= {
	"TMCE": {
			"<img src='../legenda/TMCE_pinezka.png' align='center' style='margin:3px'>Pinezka TMCE</img>": TMCEmarker},
		"GRANICE ADMINISTRACYJNE": {
			"<img src='../legenda/powiat.png' align='top' style='margin:3px'>Granica powiatu</img>": powiat,
			"<img src='../legenda/gminy.png' align='center' style='margin:3px'>Granica gmin</img>": jednostka_ewidencyjna,
			"<img src='../legenda/obreby.png' align='center' style='margin:3px'>Granica obrębów</img>": obreb},
		"GEODEZJA": {
			"<img src='../legenda/dzialki.png' align='top' style='margin:3px'>Działki</img>":dzialki,
			"<img src='../legenda/budynki.png' align='top' style='margin:3px'>Budynki EGiB</img>": budynki,
			"<img src='../legenda/adresy.png' align='center' style='margin:2px'>Punkty adresowe</img>": adresy,
			"<span style='text-decoration:underline; font-size:14px'>GESUT - Energetyka</span><br><img src='../legenda/energetyka_nN.png' align='top' style='margin: 3px 3px 3px 40px'/><em>Nn<br><img src='../legenda/energetyka_SN.png' align='top' style='margin: 3px 3px 3px 40px'/><em>Sn<br><img src='../legenda/energetyka_WN.png' align='top' style='margin: 3px 3px 3px 40px'/><em>Wn<br><img src='../legenda/slupimasztpoligon.png' align='top' style='margin: 3px 3px 3px 40px'/><em>Słup<br><img src='../legenda/slupimasztpunkt.png' align='top' style='margin: 3px 3px 3px 40px'/><em>Słup - punkt": energetyka,
			"<span style='text-decoration:underline; font-size:14px'>GESUT - Pozostale sieci</span><br><img src='../legenda/cieplownicza.png' align='top' style='margin: 3px 3px 3px 40px'/><em>Ciepłownicza<br><img src='../legenda/gazowa.png' align='top' style='margin: 3px 3px 3px 40px'/><em>Gazowa<br><img src='../legenda/kanalizacyjna.png' align='top' style='margin: 3px 3px 3px 40px'/><em>Kanalizacyjna<br><img src='../legenda/telekomunikacyjna.png' align='top' style='margin: 3px 3px 3px 40px'/><em>Telekomunikacyjna<br><img src='../legenda/wodociagowa.png' align='top' style='margin: 3px 3px 3px 40px'/><em>Wodociągowa":pozostale_sieci,"<img src='../legenda/szamba.png' align='top' style='margin:3px'>Szamba</img>": szamba}
		/*"TAURON DYSTRYBUCJA S.A.": {
			"<span style='text-decoration:underline; font-size:14px'>nN</span><br><img src='../legenda/odcinek_nN.png' align='top' style='margin: 3px 3px 3px 40px'/><em>Odcinek Nn<br><img src='../legenda/slup_nN.png' align='top' style='margin: 3px 3px 3px 40px'/><em>Słup nN<br><img src='../legenda/stacja_SN_nN.png' align='top' style='margin: 3px 3px 3px 40px'/><em>Stacja SN/nN<br><img src='../legenda/zlacze_szafka_nN.png' align='top' style='margin: 3px 3px 3px 40px'/><em>Zlacze szafka nN<br>":energetyka_nN,
				}*/
		};

L.control.groupedLayers(baseLayers, groupedOverlays).addTo(map);

function LayerTMCE(){
	let group_1=document.querySelector("#leaflet-control-layers-group-1 > label.leaflet-control-layers-group-label > input");
	group_1.setAttribute("checked","true");
};

function LayerAdministrationDisabled() {
	let group_2 = document.getElementById("leaflet-control-layers-group-2");
	let groupLabel = document.querySelector("#leaflet-control-layers-group-2 > label.leaflet-control-layers-group-label > input");
	let labelpowiat = document.querySelector("#leaflet-control-layers-group-2 > label:nth-child(2) > input");
	let labelgmina= document.querySelector("#leaflet-control-layers-group-2 > label:nth-child(3) > input");
	let labelObreb = document.querySelector("#leaflet-control-layers-group-2 > label:nth-child(4) > span");
	groupLabel.checked=false;
	labelpowiat.checked=false;
	labelgmina.checked=false;
	/*powiat.remove(),
	jednostka_ewidencyjna.remove(),*/
	groupLabel.setAttribute("disabled", "true");
	labelpowiat.setAttribute("disabled", "true");
	labelgmina.setAttribute("disabled", "true");
	group_2.style.color=("#999");
	labelObreb.style.color=("#333");
};

function LayerAdministrationEnabled() {
	let group_2 = document.getElementById("leaflet-control-layers-group-2");
	const labels=document.querySelectorAll("#leaflet-control-layers-group-2>label>input");
	labels.forEach(label=>{
		label.removeAttribute("disabled", "true");
		label.checked=true;
		label.style.color=("#333");
	});
	group_2.style.color=("#333");
};

function LayerGeodesyDisabled() {
	let group_3 = document.getElementById("leaflet-control-layers-group-3");
	let labels = document.querySelectorAll("#leaflet-control-layers-group-3 > label > input");
	labels.forEach(label =>{
		if(label.checked!=false){label.click()};
		label.setAttribute("disabled","true")
	})
	group_3.style.color=("#999");
};

function LayerGeodesyEnabled() {
	let group_3 = document.getElementById("leaflet-control-layers-group-3");
	const labels = document.querySelectorAll("#leaflet-control-layers-group-3 >label>input");
	labels.forEach(label =>{
		label.removeAttribute("disabled","true");			
	});

	group_3.style.color=("#333");
};
/*
//function LayerTauronDisabled() {
	let group_4 = document.getElementById("leaflet-control-layers-group-4");
			let labels = document.querySelectorAll("#leaflet-control-layers-group-4 > label > input");
			labels.forEach(label=>{
				if(label.checked!=false){label.click()};
				label.setAttribute("disabled", "true");
				})
			group_4.style.color=("#999");
};


//function LayerTauronEnabled() {
	let group_4 = document.getElementById("leaflet-control-layers-group-4");
	let groupLabel = document.querySelector("#leaflet-control-layers-group-4 > label.leaflet-control-layers-group-label > input");
	let labelEnergetykanN = document.querySelector("#leaflet-control-layers-group-4 > label:nth-child(2) > input");
	groupLabel.removeAttribute("disabled", "true");
	labelEnergetykanN.removeAttribute("disabled", "true");
	group_4.style.color=("#333");
};
*/
LayerTMCE();
LayerAdministrationEnabled();
LayerGeodesyDisabled();
//LayerTauronDisabled();

let clik_check_szamba = 0;
map.on("zoomend", function (e) {
	let currentzoom = map.getZoom();
	console.log({currentzoom});
	console.log({clik_check_szamba});
	if (currentzoom > 16) 
	{
		LayerAdministrationDisabled(),
		LayerGeodesyEnabled();
		const check_szamba = document.querySelector("#leaflet-control-layers-group-3 > label:nth-child(7) > input:nth-child(1)");
		if (clik_check_szamba < 1){
			check_szamba.click();
			clik_check_szamba = 1;
		}
		//LayerTauronEnabled();
		//layerWFSObreb.bringToFront();
	}
	else 
	{
		LayerAdministrationEnabled(),
		LayerGeodesyDisabled();
		if (clik_check_szamba == 1){
			clik_check_szamba = -1;
			check_szamba.click();
		}
		//LayerTauronDisabled();
		//layerWFSObreb.bringToFront();
	}
});




// Nasłuchuj na poruszenie myszką na mapie
map.on("mousemove", function (e) {
	const markerPlaceWSP_WGS_84= document.querySelector(".WSP_WGS_84");
	var x=e.latlng.lat;
	var y=e.latlng.lng;
	markerPlaceWSP_WGS_84.innerHTML=x.toFixed(6)+'&nbsp&nbsp'+y.toFixed(6);
	
	//---------------------------------------------------------------------------------------------------------------------------------------------------
	// Obsługa comboboxa do wyboru projekcji 
	//---------------------------------------------------------------------------------------------------------------------------------------------------
	// Pobierz element select
	var comboBox = document.getElementById("ComboProj");
	// Pobierz element, gdzie wyświetlimy wynik
	var resultElement = document.getElementById("WSP_inne");
	
		
	var selectedValue = comboBox.value;
	if (selectedValue === "PUWG2000s6") {
//	+proj=tmerc +lat_0=0 +lon_0=18 +k=0.999923 +x_0=6500000 +y_0=0 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs
		const markerPlaceWSP_Inne = document.querySelector(".WSP_inne");
		//var UTMprojection ="+proj=utm +zone=34 +datum=WGS84 +units=m +no_defs";
		var s62000projection ="+proj=tmerc +lat_0=0 +lon_0=18 +k=0.999923 +x_0=6500000 +y_0=0 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs"
		var s62000xy = proj4( s62000projection, [e.latlng.lng,e.latlng.lat]);
		//console.log ("s62000 wsp: "+s62000xy[0].toFixed(2)+'  '+s62000xy[1].toFixed(2));
		markerPlaceWSP_Inne.innerHTML=s62000xy[0].toFixed(2)+'&nbsp&nbsp'+s62000xy[1].toFixed(2);

	} 
	else if (selectedValue === "PUWG1992") {
		// obsługa wyświetalnia wartości dla projekcji PUIWG1992
		//console.log ("comboBox.value; "+ comboBox.value);
		// obsługa wyświetalnia wartości dla projekcji PUIWG1992
		const markerPlaceWSP_Inne = document.querySelector(".WSP_inne");
		var crs1992projection = "+proj=tmerc +lat_0=0 +lon_0=19 +k=0.9993 +x_0=500000 +y_0=-5300000 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs";
		var crs1992 = proj4(crs1992projection, [e.latlng.lng,e.latlng.lat]);
		//console.log ("wsp: "+crs1992[0].toFixed(2)+'  '+crs1992[1].toFixed(2));
		markerPlaceWSP_Inne.innerHTML=crs1992[0].toFixed(2)+'&nbsp&nbsp&nbsp'+' '+crs1992[1].toFixed(2)
	} 
	else if (selectedValue === "UTM") {
		
		const markerPlaceWSP_Inne = document.querySelector(".WSP_inne");
		//var UTMprojection ="+proj=utm +zone=34 +datum=WGS84 +units=m +no_defs";
		var UTMprojection ="+proj=utm +zone=34 +ellps=WGS72 +towgs84=0,0,1.9,0,0,0.814,-0.38 +units=m +no_defs"
		var UTMxy = proj4( UTMprojection, [e.latlng.lng,e.latlng.lat]);
		//console.log ("UTM wsp: "+UTMxy[0].toFixed(2)+'  '+UTMxy[1].toFixed(2));
		markerPlaceWSP_Inne.innerHTML=UTMxy[0].toFixed(2)+'&nbsp&nbsp&nbsp'+UTMxy[1].toFixed(2)
	
	} 
	else if (selectedValue === "WKID") {
		//+proj=merc +a=6378137 +b=6378137 +lat_ts=0 +lon_0=0 +x_0=0 +y_0=0 +k=1 +units=m +nadgrids=@null +wktext +no_defs
		const markerPlaceWKID_Inne = document.querySelector(".WSP_inne");
		var WKIDprojection ="+proj=merc +a=6378137 +b=6378137 +lat_ts=0 +lon_0=0 +x_0=0 +y_0=0 +k=1 +units=m +nadgrids=@null +wktext +no_defs"
		var WKIDxy = proj4( WKIDprojection, [e.latlng.lng,e.latlng.lat]);
		//console.log ("WKID wsp: "+WKIDxy[0].toFixed(2)+'  '+WKIDxy[1].toFixed(2));
		markerPlaceWKID_Inne.innerHTML=WKIDxy[0].toFixed(2)+'&nbsp&nbsp'+WKIDxy[1].toFixed(2)
	}
});


//zwijanie rozwijanie grupy warstw
const labelsPRG=document.querySelectorAll("#leaflet-control-layers-group-2 > label:nth-child(n+2)");
const labelsGeodezja=document.querySelectorAll("#leaflet-control-layers-group-3 > label:nth-child(n+2)");
//const labelTauronnN=document.querySelector("#leaflet-control-layers-group-4 > label:nth-child(2)");

const buttonPRG=document.createElement("div")
	  buttonPRG.classList="up down";

const checkBoxAdministracja=document.querySelector("#leaflet-control-layers-group-2");
	  checkBoxAdministracja.before(buttonPRG);

const buttonGeodezja=document.createElement("div");
	  buttonGeodezja.classList="up down";

const checkBoxGeodezja=document.querySelector("#leaflet-control-layers-group-3");
	  checkBoxGeodezja.before(buttonGeodezja);

//const buttonTauron=document.createElement("div");
//	  buttonTauron.classList="up down";

//const checkBoxTauron=document.querySelector("#leaflet-control-layers-group-4");
//	  checkBoxTauron.before(buttonTauron);

function zwinGraniceAdministracyjne(){
	labelsPRG.forEach(label=>label.style.display="none");
};

function rozwinGraniceAdministracyjne(){
	labelsPRG.forEach(label=>label.style.display="block");
};

function zwinGeodezja(){
	labelsGeodezja.forEach(label=>{
		label.style.display="none"
	})
};

function rozwinGeodezja(){
	labelsGeodezja.forEach(label=>{
		label.style.display="block";
	})	
};

//function zwinTauron(){
//	labelTauronnN.style.display="none";
//};

 //function rozwinTauron(){
//	labelTauronnN.style.display="block";
//};

function toggleClassAdministracja(){
	buttonPRG.classList.toggle("up");

	if (buttonPRG.className==="down"){
		zwinGraniceAdministracyjne();
	}
	else {
		rozwinGraniceAdministracyjne();
	}
};

function toggleClassGeodezja(){
	buttonGeodezja.classList.toggle("up");
	if (buttonGeodezja.className==="down"){
		zwinGeodezja();
	}
	else {
		rozwinGeodezja();
	}
};

//function toggleClassTauron(){
//	buttonTauron.classList.toggle("up");
//	if (buttonTauron.className==="down")
//	{
//		zwinTauron();
//	}
//	else {
//		rozwinTauron();
//	}
//};

buttonPRG.addEventListener("click", toggleClassAdministracja);


buttonGeodezja.addEventListener("click", toggleClassGeodezja);
//buttonTauron.addEventListener("click", toggleClassTauron);

// kontroler warstw on/off
const buttonPokaz=document.querySelector(".pokaz");
const zawartoscMapy=document.querySelector(".leaflet-control-layers");
const strzalki=document.querySelector("#strzalki-btn");
/*
console.log ('warstwa_szukana2');
console.log ({warstwa_szukana});
warstwa_szukana.addTo(map);
warstwa_szukana.bringToFront();
*/
buttonPokaz.addEventListener("click", ()=>{
	buttonPokaz.classList.toggle("ukryjLayers");
	zawartoscMapy.classList.toggle("ukryj-leaflet-control");
	if(buttonPokaz.classList.contains("ukryjLayers"))
	{
		strzalki.textContent="<<";
	}
	else{
		strzalki.textContent=">>";
	};
});



// Stop "click" na mapie dla contenerów:

const headerEl=document.getElementById("header");
const leftpanelEl=document.getElementById("leftpanel");
const leftpanelSzukajEl=document.getElementById("leftpanelSzukaj");
const leftpanelAtrybutyEl=document.getElementById("leftpanelAtrybuty");

L.DomEvent.disableScrollPropagation(zawartoscMapy); 

L.DomEvent.disableClickPropagation(headerEl);

//zegar
let zegarContent=document.querySelector(".czas")
function zegar(){
	let d =new Date();
	let day = d.getDay()-1;
	let month = d.getMonth()+1;
	let year = d.getFullYear(); 
	let hour= d.getHours();
	let minutes= d.getMinutes();
	let seconds= d.getSeconds();
	hour=hour<10 ? "0" + hour: hour;
	minutes=minutes<10 ? "0" + minutes: minutes;
	seconds=seconds<10 ? "0" + seconds: seconds;
	zegarContent.innerHTML= year+'.'+addLeadingZero(month)+'.'+addLeadingZero(day)+' '+hour + ':'+ minutes+':'+seconds
};
//console.log('po zegaer');
zegar();
//console.log('po funkcja');
setInterval(zegar,1000);
let myArray = ['a', 1, 'a', 2, '1'];
//console.log(myArray);
let unique = [...new Set(myArray)];
//console.log(unique);

function addLeadingZero(number) {
  return number < 10 ? "0" + number : number;
}

