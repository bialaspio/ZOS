
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

// Pobierz konkretne ciasteczko po nazwie
function pobierzCiasteczko(nazwa) {
  var ciasteczka = document.cookie.split("; ");
  for (var i = 0; i < ciasteczka.length; i++) {
    var para = ciasteczka[i].split("=");
    if (para[0] === nazwa) {
      return para[1];
    }
  }
  return null;
}


RenderHistWyw();

//-----------------------------------------------------------------------------------------------
// funkcja do generowania tabeli z Adresami 
//-----------------------------------------------------------------------------------------------
function RenderHistWyw() {
	var id_szamba = pobierzCiasteczko("id_szamba");
	//const url = 'http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=PG_L_Layers%3APG_adresy_id&outputFormat=application%2Fjson';
	const url = 'http://geoportal.tmce.pl:8080/geoserver/PG_L_Layers/ows?service=WFS&version=1.0.0&request=GetFeature&CQL_FILTER=id_szamba='+id_szamba+'&typeName=PG_L_Layers%3APG_L_hist_wywozu&outputFormat=application%2Fjson';
	
	console.log({id_szamba});
	console.log({url});
	
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
//				console.log ({data_from_FC});
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
					
					
					//id_prot, nr_prot_data, nr_prot_nr, firma_id, id_szamba, platnosc, ilosc_sciekow, data_wywozu, id_oczyszcz
					columns:[
								{title:"Nr protokołu", field:"nr_prot",headerFilter:"input"},
								{title:"Firma", field:"firma",headerFilter:"input"},
								{title:"Adres ", field:"adres",headerFilter:"input"},
								{title:"Platnosc", field:"platnosc",headerFilter:"input"},
								{title:"Data wywozu", field:"data_wywozu",headerFilter:"input"},
								{title:"Ilość ścieków", field:"ilosc_sciekow",headerFilter:"input"},
								{title:"Oczyszczalnia", field:"nazwa_oczysz",headerFilter:"input"},
								],
				});
				
				
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

function addLeadingZero(number) {
  return number < 10 ? "0" + number : number;
}
