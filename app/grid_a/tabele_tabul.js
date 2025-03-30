let buttonRaportEl=document.querySelector(".raport");
//let buttonElDel=document.querySelector(".delete");

buttonRaportEl.addEventListener("click",generujRaport);
//buttonElDel.addEventListener("click",clear);

let buttongetSelectedRows=document.querySelector(".getSelectedRows");
buttongetSelectedRows.addEventListener("click",GestSelectetData);

let warstwa = "";

//-----------------------------------------------------------------------------------------------
function clear(){
  let contener=document.getElementById("wrapper");
    while (contener.firstChild) {
     contener.removeChild(contener.firstChild);
	}
};

//-----------------------------------------------------------------------------------------------
//custom max min header filter
var minMaxFilterEditor = function(cell, onRendered, success, cancel, editorParams){
	var end;
	var container = document.createElement("span");

	//create and style inputs
	var start = document.createElement("input");
	start.setAttribute("type", "number");
	start.setAttribute("placeholder", "Min");
	start.setAttribute("min", 0);
	start.setAttribute("max", 100);
	start.style.padding = "4px";
	start.style.width = "50%";
	start.style.boxSizing = "border-box";

	start.value = cell.getValue();

	function buildValues(){
		success({
			start:start.value,
			end:end.value,
		});
	}

	function keypress(e){
		if(e.keyCode == 13){
			buildValues();
		}

		if(e.keyCode == 27){
			cancel();
		}
	}

	end = start.cloneNode();
	end.setAttribute("placeholder", "Max");

	start.addEventListener("change", buildValues);
	start.addEventListener("blur", buildValues);
	start.addEventListener("keydown", keypress);

	end.addEventListener("change", buildValues);
	end.addEventListener("blur", buildValues);
	end.addEventListener("keydown", keypress);

	container.appendChild(start);
	container.appendChild(end);

	return container;
}

//-----------------------------------------------------------------------------------------------
//custom max min filter function
//-----------------------------------------------------------------------------------------------
function minMaxFilterFunction(headerValue, rowValue, rowData, filterParams){
	//headerValue - the value of the header filter element
	//rowValue - the value of the column in this row
	//rowData - the data for the row being filtered
	//filterParams - params object passed to the headerFilterFuncParams property

		if(rowValue){
			if(headerValue.start != ""){
				if(headerValue.end != ""){
					return rowValue >= headerValue.start && rowValue <= headerValue.end;
				}else{
					return rowValue >= headerValue.start;
				}
			}else{
				if(headerValue.end != ""){
					return rowValue <= headerValue.end;
				}
			}
		}

	return true; //must return a boolean, true if it passes the filter.
}

//-----------------------------------------------------------------------------------------------

function generujRaport(){
	let selectedEl=document.getElementById("wybierz-warstwe").value;
	console.log(selectedEl);
	
	/*console.log('D_1');
	const loader = document.createElement("div");
	loader.id = "loader";
	loader.innerHTML = "<img src='../css/images/loader_2.gif' alt='Ładowanie danych...'>";
	document.body.appendChild(loader);
	loader.style.position = "absolute";
	loader.style.top = '50%';
	loader.style.left = '50%';
	loader.style.transform = "translate(-50%, -50%)";
	console.log('D_2');*/
	
	if(selectedEl=='PG_adresy_id'){
		renderAdresy(); 
	}
	else if(selectedEl=='PG_obreby_nazwa'){
		renderObreby();
	}
	else if(selectedEl=='PG_dzialk_pol'){
		console.log('renderDzialki_1');
		renderDzialki();
		console.log('renderDzialki_2');
	}
	else if(selectedEl=='PG_budynki_opisowka'){
		renderBudyni();
	}
	else if(selectedEl=='PG_slup_nn'){
		renderSlupyTauron();
	}
	else if(selectedEl=='PG_slupy_geo_sz')	{
		renderSlupyGeodezja();
	}
	else if(selectedEl=='PG_szamba_adresy')	{
		renderSzamba();
	}
	else if(selectedEl=='initial'){
		customAlert("Wybierz najpierw warstwę, to wygeneruję Ci tabelę z danymi :)")
	}
/*	console.log('D_3');
	loader.style.display = "none";*/
};

//-----------------------------------------------------------------------------------------------

function clear(){
  let contener=document.getElementById("wrapper");
    while (contener.firstChild) {
     contener.removeChild(contener.firstChild);
}};

var table_tmp;

//-----------------------------------------------------------------------------------------------
// funkcja do generowania tabeli z gminami przy wczytaniu strony
//-----------------------------------------------------------------------------------------------

function pobierzDane() {
	
	warstwa= "gmina";
	
	const url = '//geoportal.tmce.pl:8080/geoserver/wloszczowski_Arek/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=wloszczowski_Arek%3Agminy&outputFormat=application%2Fjson';
	const req = new XMLHttpRequest();
	req.open('GET', url);
	req.send();
	// Obsługujemy odpowiedź serwera
	req.onload = () => {
	if (req.status === 200) {
		// Pobieramy dane z odpowiedzi serwera
		const dane = JSON.parse(req.responseText);
		
		if (dane.type === "FeatureCollection") {
			const data_from_FC = dane.features.map(feature => feature.properties);
			console.log ({data_from_FC});
			table_tmp = new Tabulator("#example-table",{
				data:data_from_FC,
				layout:"fitColumns",
				rowHeight:20, //set rows to 40px height
				frozenRows:0,
				height:"100%",
				selectable:true, //make rows selectable
				columns:[
						{title:"TERYT", field:"JPT_KOD_JE",headerFilter:"input"},
						{title:"GMINA", field:"JPT_NAZWA_",headerFilter:"input"},
						{title:"POWIAT", field:"powiat",headerFilter:"input"},
						],
			});
			///----------------------------------------
				//trigger download of data.csv file
				document.getElementById("download-csv").addEventListener("click", function(){
					table_tmp.download("csv", "data.csv");
				});

				//trigger download of data.json file
				document.getElementById("download-json").addEventListener("click", function(){
					table_tmp.download("json", "data.json");
				});

				//trigger download of data.xlsx file
				document.getElementById("download-xlsx").addEventListener("click", function(){
					table_tmp.download("xlsx", "data.xlsx", {sheetName:"My Data"});
				});

				//trigger download of data.pdf file
				document.getElementById("download-pdf").addEventListener("click", function(){
					table_tmp.download("pdf", "data.pdf", {
						orientation:"portrait", //set page orientation to portrait
						title:"Example Report", //add title to report
					});
				});

				//trigger download of data.html file
				document.getElementById("download-html").addEventListener("click", function(){
					table_tmp.download("html", "data.html", {style:true});
				});
				
				///----------------------------------------

			} 
		else {
			  // Wyświetlamy błąd
			  console.error(req.statusText);
			}
	} 
	else{
		return [];
	}
  };
}

//-----------------------------------------------------------------------------------------------
// funkcja do generowania tabeli z obrebami 
//-----------------------------------------------------------------------------------------------
function renderObreby() {
  warstwa= "PG_L_obreby_nazwa";
  const url = 'http://geoportal.tmce.pl:8080/geoserver/test_Arek/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=test_Arek%3Aobreby&outputFormat=application%2Fjson';
  const req = new XMLHttpRequest();
  req.open('GET', url);
  req.send();
  // Obsługujemy odpowiedź serwera
  req.onload = () => {
    if (req.status === 200) {
	  // Pobieramy dane z odpowiedzi serwera
     const dane = JSON.parse(req.responseText);
	 console.log({dane});
	 if (dane.type === "FeatureCollection") {
		const data_from_FC = dane.features.map(feature => feature.properties);
		console.log ({data_from_FC});

		table_tmp = new Tabulator("#example-table",{
			data:data_from_FC,
			layout:"fitColumns",
			rowHeight:20, //set rows to 40px height
			frozenRows:0,
			height:"100%",
			selectable:true, //make rows selectable
			pagination:"local",
			paginationSize:25,
			paginationSizeSelector:[25, 50, 75, 100],
			movableColumns:true,
			paginationCounter:"rows",
			columns:[
						{title:"TERYT", field:"TERYT",headerFilter:"input"},
						{title:"OBRĘB", field:"OBREB",headerFilter:"input"},
					],
		});
		
		///----------------------------------------
				//trigger download of data.csv file
				document.getElementById("download-csv").addEventListener("click", function(){
					table_tmp.download("csv", "data.csv");
				});

				//trigger download of data.json file
				document.getElementById("download-json").addEventListener("click", function(){
					table_tmp.download("json", "data.json");
				});

				//trigger download of data.xlsx file
				document.getElementById("download-xlsx").addEventListener("click", function(){
					table_tmp.download("xlsx", "data.xlsx", {sheetName:"My Data"});
				});

				//trigger download of data.pdf file
				document.getElementById("download-pdf").addEventListener("click", function(){
					table_tmp.download("pdf", "data.pdf", {
						orientation:"portrait", //set page orientation to portrait
						title:"Example Report", //add title to report
					});
				});

				//trigger download of data.html file
				document.getElementById("download-html").addEventListener("click", function(){
					table_tmp.download("html", "data.html", {style:true});
				});
				
				///----------------------------------------
		
		console.log({table_tmp});
		} else {
		  // Wyświetlamy błąd
		  console.error(req.statusText);
		}
	} 
	else{
		return [];
	}
  };
}

//-----------------------------------------------------------------------------------------------
// funkcja do generowania tabeli z dzialkami
//-----------------------------------------------------------------------------------------------
function renderDzialki() {
	warstwa= "PG_L_dzialk_pol";
	const url = 'http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=PG_L_Layers%3APG_L_dzialki&outputFormat=application%2Fjson';
	const req = new XMLHttpRequest();
	req.open('GET', url);
	req.send();
	//console.log('D_1');
	const loader = document.createElement("div");
	loader.id = "loader";
	loader.innerHTML = "<img src='../css/images/load_TMCE.gif' alt='Ładowanie danych...'>";
	document.body.appendChild(loader);
	loader.style.position = "absolute";
	loader.style.top = '50%';
	loader.style.left = '50%';
	loader.style.transform = "translate(-50%, -50%)";
	//console.log('D_2');
	// Obsługujemy odpowiedź serwera
	req.onload = () => {
		if (req.status === 200) {
			// Pobieramy dane z odpowiedzi serwera
			const dane = JSON.parse(req.responseText);
			console.log({dane});
			if (dane.type === "FeatureCollection") {
				
				const data_from_FC = dane.features.map(feature => feature.properties);
				console.log ({data_from_FC});
				table_tmp = new Tabulator("#example-table",{
					data:data_from_FC,
					layout:"fitColumns",
					rowHeight:20, //set rows to 40px height
					frozenRows:0,
					height:"100%",
					selectable:true, //make rows selectable
					pagination:"local",
					paginationSize:25,
					paginationSizeSelector:[25, 50, 75, 100],
					movableColumns:true,
					paginationCounter:"rows",
					columns:[
							{title:"ID DZIAŁKI", field:"id_dzialki",headerFilter:"input"},
							{title:"NR DZIAŁKI", field:"dzialka_nr",headerFilter:"input"},
							],
				});
				
				///----------------------------------------
				//trigger download of data.csv file
				document.getElementById("download-csv").addEventListener("click", function(){
					table_tmp.download("csv", "data.csv");
				});

				//trigger download of data.json file
				document.getElementById("download-json").addEventListener("click", function(){
					table_tmp.download("json", "data.json");
				});

				//trigger download of data.xlsx file
				document.getElementById("download-xlsx").addEventListener("click", function(){
					table_tmp.download("xlsx", "data.xlsx", {sheetName:"My Data"});
				});

				//trigger download of data.pdf file
				document.getElementById("download-pdf").addEventListener("click", function(){
					table_tmp.download("pdf", "data.pdf", {
						orientation:"portrait", //set page orientation to portrait
						title:"Example Report", //add title to report
					});
				});

				//trigger download of data.html file
				document.getElementById("download-html").addEventListener("click", function(){
					table_tmp.download("html", "data.html", {style:true});
				});
				
				///----------------------------------------
				
				//#console.log('D_3');
				loader.style.display = "none";
			} else {
				// Wyświetlamy błąd
				console.error(req.statusText);
			}
			
		} 
		else{
			return [];
		}
	};
}


//-----------------------------------------------------------------------------------------------
// funkcja do generowania tabeli z budynkami
//-----------------------------------------------------------------------------------------------
function renderBudyni() {
  warstwa= "PG_L_budynki_opisowka";
  const url = 'http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=PG_L_Layers%3APG_L_budynki_opisowka&outputFormat=application%2Fjson';
  const req = new XMLHttpRequest();
  req.open('GET', url);
  req.send();
  // Obsługujemy odpowiedź serwera
  req.onload = () => {
    if (req.status === 200) {
	  // Pobieramy dane z odpowiedzi serwera
     const dane = JSON.parse(req.responseText);
	 console.log({dane});
	 if (dane.type === "FeatureCollection") {
		const data_from_FC = dane.features.map(feature => feature.properties);
		console.log ({data_from_FC});
		table_tmp = new Tabulator("#example-table",{
			data:data_from_FC,
			layout:"fitColumns",
			rowHeight:20, //set rows to 40px height
			frozenRows:0,
			height:"100%",
			selectable:true, //make rows selectable
			pagination:"local",
			paginationSize:25,
			paginationSizeSelector:[25, 50, 75, 100],
			movableColumns:true,
			paginationCounter:"rows",
			columns:[
						{title:"RODZAJ BUDYNKU", field:"rodzaj_opis",headerFilter:"input"},
						{title:"ID BUDYNKU", field:"id_budynku",headerFilter:"input"},
						
					],
		});
		
		///----------------------------------------
				//trigger download of data.csv file
				document.getElementById("download-csv").addEventListener("click", function(){
					table_tmp.download("csv", "data.csv");
				});

				//trigger download of data.json file
				document.getElementById("download-json").addEventListener("click", function(){
					table_tmp.download("json", "data.json");
				});

				//trigger download of data.xlsx file
				document.getElementById("download-xlsx").addEventListener("click", function(){
					table_tmp.download("xlsx", "data.xlsx", {sheetName:"My Data"});
				});

				//trigger download of data.pdf file
				document.getElementById("download-pdf").addEventListener("click", function(){
					table_tmp.download("pdf", "data.pdf", {
						orientation:"portrait", //set page orientation to portrait
						title:"Example Report", //add title to report
					});
				});

				//trigger download of data.html file
				document.getElementById("download-html").addEventListener("click", function(){
					table_tmp.download("html", "data.html", {style:true});
				});
				
				///----------------------------------------
		
		console.log({table_tmp});
		} else {
		  // Wyświetlamy błąd
		  console.error(req.statusText);
		}
	} 
	else{
		return [];
	}
  };
}


//-----------------------------------------------------------------------------------------------
// funkcja do generowania tabeli z Adresami 
//-----------------------------------------------------------------------------------------------
function renderAdresy() {
  warstwa= "PG_L_adresy_id";
  const url = 'http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=PG_L_Layers%3APG_L_adresy_id&outputFormat=application%2Fjson';
  const req = new XMLHttpRequest();
  req.open('GET', url);
  req.send();
  // Obsługujemy odpowiedź serwera
  req.onload = () => {
    if (req.status === 200) {
		// Pobieramy dane z odpowiedzi serwera
		const dane = JSON.parse(req.responseText);
		
		if (dane.type === "FeatureCollection") {
			const data_from_FC = dane.features.map(feature => feature.properties);
			console.log ({data_from_FC});

			table_tmp = new Tabulator("#example-table",{
				data:data_from_FC,
				layout:"fitColumns",
				rowHeight:20, //set rows to 40px height
				frozenRows:0,
				height:"100%",
				selectable:true, //make rows selectable
				pagination:"local",
				paginationSize:25,
				paginationSizeSelector:[25, 50, 75, 100],
				movableColumns:true,
				paginationCounter:"rows",
				
				columns:[
							{title:"IDENT", field:"idENT",headerFilter:"input",visible:false},
							{title:"ULICA", field:"ulica",headerFilter:"input"},
							{title:"NUMER", field:"numer",headerFilter:"input"},
							{title:"KOD POCZTOWY", field:"kod_pocztowy",headerFilter:"input"},
							{title:"MIEJSCOWOŚĆ", field:"miejscowosc",headerFilter:"input"},
							],
			});
			
			///----------------------------------------
				//trigger download of data.csv file
				document.getElementById("download-csv").addEventListener("click", function(){
					table_tmp.download("csv", "data.csv");
				});

				//trigger download of data.json file
				document.getElementById("download-json").addEventListener("click", function(){
					table_tmp.download("json", "data.json");
				});

				//trigger download of data.xlsx file
				document.getElementById("download-xlsx").addEventListener("click", function(){
					table_tmp.download("xlsx", "data.xlsx", {sheetName:"My Data"});
				});

				//trigger download of data.pdf file
				document.getElementById("download-pdf").addEventListener("click", function(){
					table_tmp.download("pdf", "data.pdf", {
						orientation:"portrait", //set page orientation to portrait
						title:"Example Report", //add title to report
					});
				});

				//trigger download of data.html file
				document.getElementById("download-html").addEventListener("click", function(){
					table_tmp.download("html", "data.html", {style:true});
				});
				
				///----------------------------------------
			
			console.log({table_tmp});

		}
		else {
			  // Wyświetlamy błąd
			  console.error(req.statusText);
		}
	} 
	else{
		return [];
	}
  };
}


//-----------------------------------------------------------------------------------------------
// funkcja do generowania tabeli z słupami werstwy geodezyjnej 
//-----------------------------------------------------------------------------------------------
function renderSlupyGeodezja() {
  warstwa= "PG_L_slupy_geo_sz";
  
   const url = 'http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=PG_L_Layers%3APG_L_slupy_geo_sz&outputFormat=application%2Fjson';

  const req = new XMLHttpRequest();
  req.open('GET', url);
  req.send();
  // Obsługujemy odpowiedź serwera
  req.onload = () => {
    if (req.status === 200) {
	  // Pobieramy dane z odpowiedzi serwera
      const dane = JSON.parse(req.responseText);
	 
	 if (dane.type === "FeatureCollection") {
		const data_from_FC = dane.features.map(feature => feature.properties);
		console.log ({data_from_FC});
		table_tmp = new Tabulator("#example-table",{
			data:data_from_FC,
			layout:"fitColumns",
			rowHeight:20, //set rows to 40px height
			frozenRows:0,
			height:"100%",
			selectable:true, //make rows selectable
			pagination:"local",
			paginationSize:25,
			paginationSizeSelector:[25, 50, 75, 100],
			movableColumns:true,
			paginationCounter:"rows",
			columns:[
						{title:"ŹRÓDŁO", field:"zrodlo",headerFilter:"input"},
						{title:"EKSPLOATACJA", field:"eksploatac",headerFilter:"input"},
						{title:"DATA POMIARU", field:"datapomiar",headerFilter:"input"},
						{title:"RODZAJ SIECI", field:"rodzajsiec",headerFilter:"input"},
						{title:"RODZAJ SŁUPA", field:"rodzajslup",headerFilter:"input"},
						{title:"CZY Z LATARNIĄ", field:"zlatarnia",headerFilter:"input"},
						{title:"WŁAŚCICIEL", field:"nazwapelna",headerFilter:"input"},
					],
		});
		
		///----------------------------------------
				//trigger download of data.csv file
				document.getElementById("download-csv").addEventListener("click", function(){
					table_tmp.download("csv", "data.csv");
				});

				//trigger download of data.json file
				document.getElementById("download-json").addEventListener("click", function(){
					table_tmp.download("json", "data.json");
				});

				//trigger download of data.xlsx file
				document.getElementById("download-xlsx").addEventListener("click", function(){
					table_tmp.download("xlsx", "data.xlsx", {sheetName:"My Data"});
				});

				//trigger download of data.pdf file
				document.getElementById("download-pdf").addEventListener("click", function(){
					table_tmp.download("pdf", "data.pdf", {
						orientation:"portrait", //set page orientation to portrait
						title:"Example Report", //add title to report
					});
				});

				//trigger download of data.html file
				document.getElementById("download-html").addEventListener("click", function(){
					table_tmp.download("html", "data.html", {style:true});
				});
				
				///----------------------------------------
		
		console.log({table_tmp});
		} else {
		  // Wyświetlamy błąd
		  console.error(req.statusText);
		}
	} 
	else{
		return [];
	}
  };
}


//-----------------------------------------------------------------------------------------------
// funkcja do generowania tabeli z slupami warstwy Tauron
//-----------------------------------------------------------------------------------------------

function renderSlupyTauron() {
	warstwa= "PG_slup_nn";
	const url = 'http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=PG_L_Layers%3APG_slup_nn&outputFormat=application%2Fjson';  
	const req = new XMLHttpRequest();
	req.open('GET', url);
	req.send();
	// Obsługujemy odpowiedź serwera
	req.onload = () => {
		if (req.status === 200) {
		  // Pobieramy dane z odpowiedzi serwera
		 const dane = JSON.parse(req.responseText);
		 
		 if (dane.type === "FeatureCollection") {
			const data_from_FC = dane.features.map(feature => feature);
			console.log ({data_from_FC});
			table_tmp = new Tabulator("#example-table",{
				data:data_from_FC,
				layout:"fitColumns",
				rowHeight:20, //set rows to 40px height
				frozenRows:0,
				height:"100%",
				selectable:true, //make rows selectable
				pagination:"local",
				paginationSize:25,
				paginationSizeSelector:[25, 50, 75, 100],
				movableColumns:true,
				paginationCounter:"rows",
				columns:[
							{title:"NUMER SŁUPA", field:"num_slup",headerFilter:"input"},
							{title:"MATERIAŁ ŻERDZI", field:"mat_zerdz",headerFilter:"input"},
							{title:"TYP", field:"typ",headerFilter:"input"},
							{title:"RODZAJ", field:"rodz_sr_tr",headerFilter:"input"},
							{title:"REJON", field:"nazw_rej",headerFilter:"input"},
							{title:"WŁASNOŚĆ", field:"wlasnosc",headerFilter:"input"},
							{title:"FUNKCJA SŁUPA", field:"funkcja_sl",headerFilter:"input"},
							{title:"NAPIĘCIE ROBOCZE", field:"nap_rob",headerFilter:"input"},
							{title:"PRZEZNACZENIE", field:"przeznacze",headerFilter:"input"},
							{title:"RZODZAJ SŁUPA", field:"rodzaj_slu",headerFilter:"input"},
							{title:"GEOM", field:"geometry.coordinates",visible:false},
						],
			});
			
			///----------------------------------------
				//trigger download of data.csv file
				document.getElementById("download-csv").addEventListener("click", function(){
					table_tmp.download("csv", "data.csv");
				});

				//trigger download of data.json file
				document.getElementById("download-json").addEventListener("click", function(){
					table_tmp.download("json", "data.json");
				});

				//trigger download of data.xlsx file
				document.getElementById("download-xlsx").addEventListener("click", function(){
					table_tmp.download("xlsx", "data.xlsx", {sheetName:"My Data"});
				});

				//trigger download of data.pdf file
				document.getElementById("download-pdf").addEventListener("click", function(){
					table_tmp.download("pdf", "data.pdf", {
						orientation:"portrait", //set page orientation to portrait
						title:"Example Report", //add title to report
					});
				});

				//trigger download of data.html file
				document.getElementById("download-html").addEventListener("click", function(){
					table_tmp.download("html", "data.html", {style:true});
				});
				
				///----------------------------------------
			
			console.log({table_tmp});
			} else {
			  // Wyświetlamy błąd
			  console.error(req.statusText);
			}
		} 
		else{
			return [];
		}
	};
}	
//-----------------------------------------------------------------------------------------------------------------------------------------

function renderSzamba() {
	warstwa= "PG_L_szamba_adresy";
	const url = 'http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=PG_L_Layers%3APG_L_szamba_adresy&outputFormat=application%2Fjson';  
	const req = new XMLHttpRequest();
	req.open('GET', url);
	req.send();
	// Obsługujemy odpowiedź serwera
	req.onload = () => {
		if (req.status === 200) {
		  // Pobieramy dane z odpowiedzi serwera
		 const dane = JSON.parse(req.responseText);
		 
		 if (dane.type === "FeatureCollection") {
			const data_from_FC = dane.features.map(feature => feature);
			console.log ({data_from_FC});
			table_tmp = new Tabulator("#example-table",{
				data:data_from_FC,
				layout:"fitColumns",
				rowHeight:20, //set rows to 40px height
				frozenRows:0,
				height:"100%",
				selectable:true, //make rows selectable
				pagination:"local",
				paginationSize:25,
				paginationSizeSelector:[25, 50, 75, 100],
				movableColumns:true,
				paginationCounter:"rows",
				
				columns:[
							{title:"ID SZAMBA", field:"properties.id_szamba",headerFilter:"input"},
							{title:"ULICA", field:"properties.ulica",headerFilter:"input"},
							{title:"NUMER", field:"properties.numer",headerFilter:"input"},
							{title:"MIEJSCOWOSC", field:"properties.miejscowosc",headerFilter:"input"},
							{title:"KOD POCZTOWY", field:"properties.kod_pocztowy",headerFilter:"input"},
							{title:"POJEMNOŚĆ", field:"properties.pojemnosc_m3",headerFilter:"input"},
							{title:"GEOM", field:"geometry.wkb_geometry",visible:false},
						],
			});
			
			///----------------------------------------
				//trigger download of data.csv file
				document.getElementById("download-csv").addEventListener("click", function(){
					table_tmp.download("csv", "data.csv");
				});

				//trigger download of data.json file
				document.getElementById("download-json").addEventListener("click", function(){
					table_tmp.download("json", "data.json");
				});

				//trigger download of data.xlsx file
				document.getElementById("download-xlsx").addEventListener("click", function(){
					table_tmp.download("xlsx", "data.xlsx", {sheetName:"My Data"});
				});

				//trigger download of data.pdf file
				document.getElementById("download-pdf").addEventListener("click", function(){
					table_tmp.download("pdf", "data.pdf", {
						orientation:"portrait", //set page orientation to portrait
						title:"Example Report", //add title to report
					});
				});

				//trigger download of data.html file
				document.getElementById("download-html").addEventListener("click", function(){
					table_tmp.download("html", "data.html", {style:true});
				});
				
				///----------------------------------------
			
			console.log({table_tmp});
			} else {
			  // Wyświetlamy błąd
			  console.error(req.statusText);
			}
		} 
		else{
			return [];
		}
	};
}	




//-----------------------------------------------------------------------------------------------------------------------------------------------
function GestSelectetData(){
	console.log('GestSelectetData');
	console.log({table_tmp});
	var selectedRows = table_tmp.getSelectedRows();
	console.log({selectedRows});
	
	let tab_element=[];
	
	switch (warstwa){
		case  "gmina":
			break;
		case "PG_L_obreby_nazwa":
			for (obj of selectedRows) {
				console.log({obj});
				let dataObj = obj.getData();
				console.log(dataObj.TERYT);
				tab_element.push(dataObj.TERYT);
				console.log ({tab_element});
			}		
			break;
		case "PG_L_dzialk_pol":
			for (obj of selectedRows) {
				console.log({obj});
				let dataObj = obj.getData();
				console.log(dataObj.id_dzialki);
				tab_element.push(dataObj.id_dzialki);
				console.log ({tab_element});
			}		
			break;
		
		case "PG_L_budynki_opisowka":
			for (obj of selectedRows) {
				console.log({obj});
				let dataObj = obj.getData();
				console.log(dataObj.id_budynku);
				tab_element.push(dataObj.id_budynku);
				console.log ({tab_element});
			}		
			break;
		
		case "PG_L_adresy_id":
			for (obj of selectedRows) {
				console.log({obj});
				let dataObj = obj.getData();
				console.log(dataObj.ident);
				tab_element.push(dataObj.ident);
				console.log ({tab_element});
			}		
			break;
		case "PG_L_slupy_geo_sz":
			for (obj of selectedRows) {
				console.log({obj});
				let dataObj = obj.getData();
				console.log(dataObj.ogc_fid);
				tab_element.push(dataObj.ogc_fid);
				console.log ({tab_element});
			}		
			break;
		case "PG_L_slupy_geo_sz": 
			for (obj of selectedRows) {
				console.log({obj});
				let dataObj = obj.getData();
				console.log(dataObj.properties.num_slup);
				tab_element.push(dataObj.properties.num_slup);
				console.log ({tab_element});
			}
			break;
		
		case "PG_L_szamba_adresy": 
			for (obj of selectedRows) {
				console.log({obj});
				let dataObj = obj.getData();
				console.log(dataObj.properties.id_szamba);
				tab_element.push(dataObj.properties.id_szamba);
				console.log ({tab_element});
			}
			break;

		default:
			break;
		
	}
	
	console.log ({tab_element});
	
	// Utwórz cookie z danymi
	var cookie =JSON.parse(JSON.stringify(warstwa +"^"+tab_element));
	console.log ({cookie});
	document.cookie = cookie;
	console.log (document.cookie);
	// Otwórz nową stronę z danymi
	// otwarcie w tej samej zakładce 
	//window.open('map_opisowka.php',"_self");
	// otwarcie w nowej zakładce 
	window.open('map_opisowka.php');

};




//-----------------***********
// Uruchamiamy funkcję do pobierania danych
pobierzDane();

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
zegar();

setInterval(zegar,1000);

let myArray = ['a', 1, 'a', 2, '1'];
console.log(myArray);
let unique = [...new Set(myArray)];
console.log(unique);

function addLeadingZero(number) {
  return number < 10 ? "0" + number : number;
}

/*
table_tmp.on("rowSelectionChanged", function(data, rows){
  document.getElementById("select-stats").innerHTML = data.length;
});

//select row on "select all" button click
document.getElementById("select-all").addEventListener("click", function(){
    table_tmp.selectRow();
	console.log('table_tmp.selected')
	console.log(table_tmp.selected)
});

//deselect row on "deselect all" button click
document.getElementById("deselect-all").addEventListener("click", function(){
	table_tmp.deselectRow();
	console.log('table_tmp.selected')
	console.log(table_tmp.selected)
});
*/