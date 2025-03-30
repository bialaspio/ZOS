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

//-----------------------------------------------------------------------------------------------
//  Funkcja do kontroi czy wszystkie pola zostaly wypełnione 
//-----------------------------------------------------------------------------------------------
document.getElementById('KodAdrSzamba').addEventListener('input', function() {
	checkFields();
});

document.getElementById('MiejscowoscAdrSzamba').addEventListener('input', function() {
	checkFields();
});

document.getElementById('UlicaAdrSzamba').addEventListener('input', function() {
	checkFields();
});

document.getElementById('NrAdrSzamba').addEventListener('input', function() {
	checkFields();
});

function checkFields() {
    var kod = document.getElementById('KodAdrSzamba').value;
    var miejscowosc = document.getElementById('MiejscowoscAdrSzamba').value;
    var ulica = document.getElementById('UlicaAdrSzamba').value;
    var numer = document.getElementById('NrAdrSzamba').value;

    if(kod && miejscowosc && ulica && numer) {
        // Wszystkie pola są wypełnione, wykonaj swoją funkcję
        console.log('Wszystkie pola są wypełnione!');
		pobierzDaneSzamba(kod, miejscowosc, ulica, numer);
		listaOsobDoSzamba(kod, miejscowosc, ulica, numer);
    }
	else {
		console.log('Jeszcze nie wszystko ');
	}
}

//-----------------------------------------------------------------------------------------------
//  Funkcja do pobrania danych szamba 
//-----------------------------------------------------------------------------------------------

// Funkcja do pobierania danych z serwera
function pobierzDaneSzamba(kod, miejscowosc, ulica, numer) {
	console.log("pobierzDaneSzamba");
	$.ajax({
		url: "ajax/ajax_pobierz_dane_szamba.php",
		type: "POST",
		data: {
			KodAdrSzamba: kod.replace(/\u00A0/g, ' '),
			MiejscowoscAdrSzamba: miejscowosc.replace(/\u00A0/g, ' '),
			UlicaAdrSzamba: ulica.replace(/\u00A0/g, ' '),
			NrAdrSzamba: numer.replace(/\u00A0/g, ' '),
		},
		success: function (dane) {
			var dane_szamba = JSON.parse(dane);
			var divDane = document.getElementById("dane_szamba");
			console.log({ dane_szamba });
			// Wyczyść poprzednie dane
			divDane.innerHTML = "";
			dane_szamba.forEach(function (szambo) {
				var label = document.createElement("label");
				var div = document.createElement("div");
				//div.style.border = "1px solid rgba(0,100,150,0.2)";
				div.style.borderTop = "1px solid rgba(0,100,150,0.2)";
				//div.style.textAlignLast ="center";
				div.style.height = "2em";
				div.style.display = "flex";
				div.style.justifyContent = "center";
				div.style.alignItems = "center";

				label.innerHTML =
					"<span style='color:#154c79;'>Adres: </span>" +
					szambo.kod_pocztowy +
					" " +
					szambo.miejscowosc +
					" Ul. " +
					szambo.ulica +
					" " +
					szambo.numer +
					"<span style='color:#154c79;'> Pojemność: </span>" +
					szambo.pojemnosc_m3 +
					" m3, <span style='color:#154c79;'>Rodzaj nieczystości: </span>" +
					szambo.rodzaj_nieczystosci +
					"<span style='color:#154c79;'>, Wywóz: </span>" +
					szambo.fnaznip +
					"</span>";
				label.style.fontSize = "14px";
				label.style.fontWeight = "bold";
				label.style.textAlign = "center";
				
				div.appendChild(label);
				divDane.appendChild(div);
			});
		},
		error: function (error) {
			console.log("Błąd: ", error); // Błąd
		},
	});
}



//-----------------------------------------------------------------------------------------------
//  Funkcja do generowania tabeli z Adresami 
//-----------------------------------------------------------------------------------------------
function listaOsobDoSzamba(kod, miejscowosc, ulica, numer) {
// Wysyłamy wartość do serwera
$.ajax({
    url: "ajax/ajax_pobierz_osoby_do_szambo.php",
    type: "POST",
    data: {
      KodAdrWlSzamba: kod.replace(/\u00A0/g, ' '),
      MiejscowoscAdrWlSzamba: miejscowosc.replace(/\u00A0/g, ' '),
      UlicaAdrWlSzamba: ulica.replace(/\u00A0/g, ' '),
      NrAdrWlSzamba: numer.replace(/\u00A0/g, ' '),
    },
    success: function(dane) {
      // Jeśli wartość znajduje się w tabeli
      var dane_osob = JSON.parse(dane);
	  //o.imie , o.nazwisko, a.ulica , a.numer , a.kod_pocztowy , a.miejscowosc
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
					{title:"Imie", field:"imie",headerFilter:"input"},
					{title:"Nazwisko", field:"nazwisko",headerFilter:"input"},
					{title:"Ulica", field:"ulica",headerFilter:"input"},
					{title:"Numer", field:"numer",headerFilter:"input"},
					{title:"Kod pocztowy", field:"kod_pocztowy",headerFilter:"input"},
					{title:"Wlasciciel", field:"wlasciciel",visible: false, },
				],
				rowFormatter:function(row){
					var data_tmp = row.getData(); //pobierz dane z wiersza
			
					if(data_tmp.wlasciciel == true){ //sprawdź, czy pole 'właściciel' ma wartość true
						row.getElement().style.backgroundColor = "#A6A6DF"; //zmień kolor tła wiersza
					}
				},
	});

    },
    error: function(error) {
      callback(-1); // Błąd
    }
  });
}

//****************************************************************************************************************************************************
//-------------------------------------------------------------------------------
// Kod pocztowy szamba 
//-------------------------------------------------------------------------------
// Inicjalizacja biblioteki jQuery UI Autocomplete
$(function() {
	// Wybór pola, dla którego ma być używane autouzupełnianie
	var input = $("#KodAdrSzamba");
	var miejscowosc = $("#MiejscowoscAdrSzamba").val;
	console.log({input});
	// Ustawienie opcji biblioteki jQuery UI Autocomplete
	input.autocomplete({
	  // Źródło danych dla autouzupełniania
	  source: function(request, response) {
		// Wysłanie zapytania do serwera
		$.ajax({
		  url: "ajax/ajax_KodPocz.php",
		  type: "POST",
		  
		  data: {
			KodPocz: request.term,
			Miejscowosc: $("#MiejscowoscAdrSzamba").val()
		  },
		  success: function(data) {
			// Przetworzenie danych z serwera
			var Kody = JSON.parse(data);
			const listaKodow = Kody.map((kod_pocztowy) => kod_pocztowy.kod_pocztowy);
			// Zwrócenie listy wartości do autouzupełniania
			response(listaKodow);
		  }
		});
	  },
	  // Wybór wartości z autouzupełniania
	  select: function(event, ui) {
		// Ustawienie wartości pola na wybraną wartość
		input.val(ui.item.value);
		console.log(ui.item.value);
	  },
	  // Wyświetlanie podpowiedzi w miarę wpisywania tekstu
	  minLength: 0
	});
  });
    
  //-------------------------------------------------------------------------------
  // Miejscowość szamba 
  //-------------------------------------------------------------------------------
  // Inicjalizacja biblioteki jQuery UI Autocomplete
  $(function() {
	// Wybór pola, dla którego ma być używane autouzupełnianie
	var input = $("#MiejscowoscAdrSzamba");
	var input_kod = $("#KodAdrSzamba").val();
  	console.log({input_kod});
	// Ustawienie opcji biblioteki jQuery UI Autocomplete
	input.autocomplete({
	  // Źródło danych dla autouzupełniania
	  source: function(request, response) {
		// Wysłanie zapytania do serwera
		$.ajax({
		  url: "ajax/ajax_Miejscowosc.php",
		  type: "POST",
		  data: {
			Miejscowosc: request.term,
			Kod:$("#KodAdrSzamba").val()
		  },
		  success: function(data) {
			// Przetworzenie danych z serwera
			var Miejscowosci = JSON.parse(data);
			const listaMiejscowosci = Miejscowosci.map((miejscowosc) => miejscowosc.miejscowosc);
			// Zwrócenie listy wartości do autouzupełniania
			response(listaMiejscowosci);
		  }
		});
	  },
  
	  // Wybór wartości z autouzupełniania
	  select: function(event, ui) {
		// Ustawienie wartości pola na wybraną wartość
		input.val(ui.item.value);
		console.log(ui.item.value);
	  },
	  
	  // Wyświetlanie podpowiedzi w miarę wpisywania tekstu
	  minLength: 0
	});
  });
  
  
  //-------------------------------------------------------------------------------
  // Nazwa ulicy dla szamba 
  //-------------------------------------------------------------------------------
  // Inicjalizacja biblioteki jQuery UI Autocomplete
  $(function() {
	// Wybór pola, dla którego ma być używane autouzupełnianie
	var input = $("#UlicaAdrSzamba");
	var input_miej = $("#MiejscowoscAdrSzamba").val();
	var input_kod = $("#KodAdrSzamba").val();
	console.log({input});
	
	// Ustawienie opcji biblioteki jQuery UI Autocomplete
	input.autocomplete({
	  // Źródło danych dla autouzupełniania
	  source: function(request, response) {
		// Wysłanie zapytania do serwera
		$.ajax({
		  url: "ajax/ajax_Ulice.php",
		  type: "POST",
		  
		  data: {
			Ulica: request.term,
			Miejscowosc: $("#MiejscowoscAdrSzamba").val(),
			Kod:$("#KodAdrSzamba").val()
		  },
		  success: function(data) {
			// Przetworzenie danych z serwera
			var ulice = JSON.parse(data);
			const nazwyUlic = ulice.map((ulica) => ulica.ulica);
			// Zwrócenie listy wartości do autouzupełniania
			response(nazwyUlic);
		  }
		});
	  },
  
	  // Wybór wartości z autouzupełniania
	  select: function(event, ui) {
		// Ustawienie wartości pola na wybraną wartość
		input.val(ui.item.value);
		console.log(ui.item.value);
	  },
	  
	  // Wyświetlanie podpowiedzi w miarę wpisywania tekstu
	  minLength: 0
	});
  });
  
  
  //-------------------------------------------------------------------------------
  // Numer dla szamba 
  //-------------------------------------------------------------------------------
  // Inicjalizacja biblioteki jQuery UI Autocomplete
  $(function() {
	// Wybór pola, dla którego ma być używane autouzupełnianie
	var Numer = $("#NrAdrSzamba")
	var Ulica = $("#UlicaAdrSzamba").val();
	var Miejscowosc = $("#MiejscowoscAdrSzamba").val();
	var Kod = $("#KodAdrSzamba").val();
	console.log({Numer});
	
	// Ustawienie opcji biblioteki jQuery UI Autocomplete
	Numer.autocomplete({
	  // Źródło danych dla autouzupełniania
	  source: function(request, response) {
		// Wysłanie zapytania do serwera
		$.ajax({
		  url: "ajax/ajax_Numer.php",
		  type: "POST",
		  
		  data: {
			Numer: request.term,
			Ulica: $("#UlicaAdrSzamba").val(),
			Miejscowosc: $("#MiejscowoscAdrSzamba").val(),
			Kod:$("#KodAdrSzamba").val()
		  },
		  success: function(data) {
			// Przetworzenie danych z serwera
			var numery_parse = JSON.parse(data);
			const numery = numery_parse.map((numer) => numer.numer);
			// Zwrócenie listy wartości do autouzupełniania
			response(numery);
		  }
		});
	  },
  
	  // Wybór wartości z autouzupełniania
	  select: function(event, ui) {
		// Ustawienie wartości pola na wybraną wartość
		Numer.val(ui.item.value);
		console.log(ui.item.value);
	  },
	  
	  // Wyświetlanie podpowiedzi w miarę wpisywania tekstu
	  minLength: 0
	});
  });
  
  


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