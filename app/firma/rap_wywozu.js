let buttonRaportEl=document.querySelector(".raport");
buttonRaportEl.addEventListener("click",generujRaport);

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
	let selectedEl=document.getElementById("wybierzUmnowySzamba").value;
	console.log(selectedEl);
	
	if(selectedEl=='umowy'){
		renderUmowy(); 
	}
	else if(selectedEl=='szamba'){
		renderWywozy();
	}
	else if(selectedEl=='initial'){
		customAlert("Wybierz najpierw warstwę, to wygeneruję Ci tabelę z danymi :)")
	}
};

//-----------------------------------------------------------------------------------------------
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
//-----------------------------------------------------------------------------------------------

function renderUmowy(){
	let id_firmy  = document.getElementById("wybierz-firme").value;
	let start  = document.getElementById("start").value;
	let koniec  = document.getElementById("koniec").value;
	
	console.log ({id_firmy});
	console.log ({start});
	console.log ({koniec});
	
	
	if (id_firmy =='initial'){
		 customAlert("Wybierz najpierw nazwę firmy !!!")
	}
	else {
		 $(document).ready(function() {
			var id = id_firmy;
			console.log ({id});
			// Pobieramy dane z bazy PostgreSQL
			$.ajax({
				url: "ajax/ajax_umowy.php",
				type: "GET",
				data: {id:id, start:start, koniec:koniec},
				dataType: "json",
			})
			.done(function(dane) {
				// Wyświetlamy dane w przeglądarce
				console.log({dane});
					
				//-----------------------------------------------------
				console.log('-----------------');
				console.log(dane.type);
				console.log('-----------------');
			

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
								{title:"Nazwa", field:"nazwa",headerFilter:"input"},
								{title:"Nip", field:"nip",headerFilter:"input"},
								{title:"Umowa od", field:"umowa_od",headerFilter:"input"},
								{title:"Umowa do", field:"umowa_do",headerFilter:"input"},
								{title:"Adres", field:"adres",headerFilter:"input"},
								{title:"Pojemność w m3", field:"pojemnosc_m3",headerFilter:"input"},

							],
				});

				console.log({table_tmp});
		
			//-----------------------------------------------------
				
				
			})
			.fail(function() {
				console.log("Błąd podczas pobierania danych z bazy");
			});
		});
	 }
}

//-----------------------------------------------------------------------------------------------
function renderWywozy(){
	let id_firmy  = document.getElementById("wybierz-firme").value;
	let start  = document.getElementById("start").value;
	let koniec  = document.getElementById("koniec").value;
	
	console.log ({id_firmy});
	console.log ({start});
	console.log ({koniec});
	
	
	if (id_firmy =='initial'){
		 customAlert("Wybierz najpierw nazwę firmy !!!")
	}
	else {
		 $(document).ready(function() {
			var id = id_firmy;
			console.log ({id});
			// Pobieramy dane z bazy PostgreSQL
			$.ajax({
				url: "ajax/ajax_wywozy.php",
				type: "GET",
				data: {id:id, start:start, koniec:koniec},
				dataType: "json",
			})
			.done(function(dane) {
				// Wyświetlamy dane w przeglądarce
				console.log({dane});
					
				//-----------------------------------------------------
				console.log('-----------------');
				console.log(dane.type);
				console.log('-----------------');
			

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
								{title:"Nr zlecenia", field:"nr_zlecenia",headerFilter:"input"},
								{title:"Nazwa firmy", field:"nazwa",headerFilter:"input"},
								{title:"NIP", field:"nip",headerFilter:"input"},
								{title:"Szambo adres", field:"adres",headerFilter:"input"},
								{title:"Data wywozu", field:"data_wywozu",headerFilter:"input"},
								{title:"Ilość w m3", field:"ilosc_sciekow",headerFilter:"input",bottomCalc:"sum"},
								{title:"Oczyszczalnia", field:"nazwa_oczysz",headerFilter:"input"},
							],
				});

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
			
				
			})
			.fail(function() {
				console.log("Błąd podczas pobierania danych z bazy");
			});
		});
	 }
}

//------------------------------------------------------------------------------------------
// Ustawienia daty poczaątku wyszukiwania na początek aktualnego roku
//------------------------------------------------------------------------------------------
const startDateInput = document.getElementById('start');

// Pobierz aktualny rok
const currentYear = new Date().getFullYear();

// Ustaw domyślną datę na początek bieżącego roku
startDateInput.value = `${currentYear}-01-01`;

// Dodaj funkcję obsługi zdarzenia `change`, aby zaktualizować minimalną datę
startDateInput.addEventListener('change', () => {
  const selectedDate = new Date(startDateInput.value);
  const minDate = new Date(selectedDate.getFullYear(), 0, 1);
  startDateInput.min = minDate.toISOString().split('T')[0];
});


//------------------------------------------------------------------------------------------
// Ustawienia daty poczaątku wyszukiwania na początek aktualnego roku
//------------------------------------------------------------------------------------------
const endDateInput = document.getElementById('koniec');

// Pobierz aktualną datę
const today = new Date();

// Ustaw domyślną datę na bieżący dzień
endDateInput.value = today.toISOString().split('T')[0];

// Dodaj funkcję obsługi zdarzenia `change`, aby zaktualizować minimalną datę
endDateInput.addEventListener('change', () => {
  const selectedDate = new Date(endDateInput.value);
  const minDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate() + 1);
  endDateInput.min = minDate.toISOString().split('T')[0];
});
  

  //------------------------------------------------------------------------------------------
//zegar
//------------------------------------------------------------------------------------------
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