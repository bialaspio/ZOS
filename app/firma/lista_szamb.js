var table_tmp;

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

ListaSzamb_wszystkie_dane();

//-----------------------------------------------------------------------------------------------
//  Funkcja do generowania tabeli z Adresami 
//-----------------------------------------------------------------------------------------------
function ListaSzamb_wszystkie_dane() {
	$(document).ready(function() {
		// Pobieramy dane z bazy PostgreSQL
		$.ajax({
			url: "ajax/ajax_pobierz_szamba.php",
			type: "GET",
			dataType: "json",
		})
		.done(function(dane) {
			// Wyświetlamy dane w przeglądarce
			console.log({dane});
			table_tmp = new Tabulator("#example-table",{
				data:dane,
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
							{title:"Właściciel", field:"im_naz",headerFilter:"input"},
							{title:"Kod", field:"kod_pocztowy",headerFilter:"input"},
							{title:"Miejscowość", field:"miejscowosc",headerFilter:"input"},
							{title:"Ulica", field:"ulica",headerFilter:"input"},
							{title:"Numer", field:"numer",headerFilter:"input"},
							{title:"Pojemność w m3", field:"pojemnosc_m3",headerFilter:"input"},
							{title:"Rodzaj nieczystości", field:"rodzaj_nieczystosci",headerFilter:"input"},
							{title:"Firma", field:"fir_nip",headerFilter:"input"},
							{title:"Id szamba", field:"id_szamba", visible: false },
						],
			});

/*
STRING_AGG (o.imie ||' '|| o.nazwisko,',') as im_naz,
a.kod_pocztowy, a.miejscowosc, a.ulica, a.numer ,
s.pojemnosc_m3, s.rodzaj_nieczystosci,
STRING_AGG (f.nazwa  || ' - NIP: '||f.nip,',') as fir_nip
*/
//*****************

			//----------------------------------------
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
			//-----------------------------------------------------

//*****************


		})
		.fail(function() {
			console.log("Błąd podczas pobierania danych z bazy");
		});
	});
 };


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