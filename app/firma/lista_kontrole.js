
let rodzajKontroli = 0;
let id_wlascicel_szamba = 0;
let id_adr_ob_kon = 0;
let id_oso_kon = 0;


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

//-------------------------------------------------------------------------------
// Imie i nazwisko właściciela szamba 
//-------------------------------------------------------------------------------
// Inicjalizacja biblioteki jQuery UI Autocomplete
$(function() {
	// Wybór pola, dla którego ma być używane autouzupełnianie
	var input = $("#ImieNazwWlSzamba");
	console.log({input});
	// Ustawienie opcji biblioteki jQuery UI Autocomplete
	input.autocomplete({
		// Źródło danych dla autouzupełniania
		source: function(request, response) {
			// Wysłanie zapytania do serwera
			$.ajax({
					url: "ajax/ajax_ImieNazwWlSzamba.php",
					type: "POST",
					
					data: {
						ImieNazWlSzamba: request.term
					},
					success: function(data) {
						 // Przetworzenie danych z serwera
						 var daneOsoby = JSON.parse(data);
						 // Zwrócenie listy wartości do autouzupełniania
						 console.log(daneOsoby);
						 const listaDaneOsobowe = daneOsoby.map((osoba) => ({
						 	label: osoba.imie + " " + osoba.nazwisko+ " Ul." + osoba.ulica+ " " + osoba.numer+ " " + osoba.kod_pocztowy+ " " + osoba.miejscowosc,
						 	value: osoba.imie + " " + osoba.nazwisko,
							id_osoby: osoba.id_osoby,
							imie: osoba.imie,
							nazwisko:osoba.nazwisko,
						 	ulica: osoba.ulica,
						 	numer: osoba.numer,
						 	kod_pocztowy: osoba.kod_pocztowy,
						 	miejscowosc: osoba.miejscowosc
						 }));
						  // Zwrócenie listy wartości do autouzupełniania
						 response(listaDaneOsobowe);
					}
			});
		},

		// Wybór wartości z autouzupełniania
		select: function(event, ui) {
			// Ustawienie wartości pola na wybraną wartość
			// Wybranie obiektu z listy podpowiedzi
			var wybrany = ui.item;
			
			// Wybranie z obiektu imienia i nazwiska
			var imieNazwisko = wybrany.value;
			// Ustawienie wartości pola tekstowego
			input.val(imieNazwisko);

			$("#divAdresyWlaSzam").show();

			// Ustawienie wartości innych pól tekstowych
			$("#ImieAdrWlSzamba").val(wybrany.imie);
			$("#NazwiskoAdrWlSzamba").val(wybrany.nazwisko);
			$("#UlicaAdrWlSzamba").val(wybrany.ulica);
			$("#NrAdrWlSzamba").val(wybrany.numer);
			$("#KodAdrWlSzamba").val(wybrany.kod_pocztowy);
			$("#MiejscowoscAdrWlSzamba").val(wybrany.miejscowosc);

			console.log ({wybrany});
			id_wlascicel_szamba = wybrany.id_osoby;			
			console.log ({id_wlascicel_szamba});
			
			// Zapobieganie domyślnej akcji
			event.preventDefault();
		},
		// Wyświetlanie podpowiedzi w miarę wpisywania tekstu
		minLength: 0
	});
});

//-------------------------------------------------------------------------------
// Adres kontrolowanego obiektu
//-------------------------------------------------------------------------------
// Inicjalizacja biblioteki jQuery UI Autocomplete
$(function() {
	// Wybór pola, dla którego ma być używane autouzupełnianie
	var input = $("#AdresKonObie");
	console.log({input});
	// Ustawienie opcji biblioteki jQuery UI Autocomplete
	input.autocomplete({
		// Źródło danych dla autouzupełniania
		source: function(request, response) {
			// Wysłanie zapytania do serwera
			$.ajax({
					url: "ajax/ajax_pobierz_adres_szamba_po_adres.php",
					type: "POST",
					
					data: {
						CzescAdres: request.term
					},
					success: function(data) {
						 // Przetworzenie danych z serwera
						 var daneAdres = JSON.parse(data);
						 // Zwrócenie listy wartości do autouzupełniania
						 console.log(daneAdres);
						 const listaDaneAdres = daneAdres.map((adres) => ({
						 	label: adres.kod_pocztowy+ " " + adres.miejscowosc + " Ul." + adres.ulica+ " " + adres.numer,
						 	value: adres.kod_pocztowy+ " " + adres.miejscowosc + " Ul." + adres.ulica+ " " + adres.numer,
						 	ulica: adres.ulica,
						 	numer: adres.numer,
						 	kod_pocztowy: adres.kod_pocztowy,
						 	miejscowosc: adres.miejscowosc,
							id_obiektu :adres.id_obiektu
						 }));
						  // Zwrócenie listy wartości do autouzupełniania
						 response(listaDaneAdres);
					}
			});
		},

		// Wybór wartości z autouzupełniania
		select: function(event, ui) {
			// Ustawienie wartości pola na wybraną wartość
			// Wybranie obiektu z listy podpowiedzi
			var wybrany = ui.item;
			
			// Wybranie z obiektu imienia i nazwiska
			var adreObiekt = wybrany.value;
			// Ustawienie wartości pola tekstowego
			input.val(adreObiekt);

			// Ustawienie wartości innych pól tekstowych
			
			$("#KodAdrObiektu").val(wybrany.kod_pocztowy);
			$("#MiejscowoscAdrObiektu").val(wybrany.miejscowosc);
			$("#UlicaAdrObiektu").val(wybrany.ulica);
			$("#NrAdrObiektu").val(wybrany.numer);
			id_adr_ob_kon = wybrany.id_obiektu;
			// Zapobieganie domyślnej akcji
			event.preventDefault();
		},
		// Wyświetlanie podpowiedzi w miarę wpisywania tekstu
		minLength: 0
	});
});



//-------------------------------------------------------------------------------
// Miejscowość 
//-------------------------------------------------------------------------------
// Inicjalizacja biblioteki jQuery UI Autocomplete
$(function() {
	// Wybór pola, dla którego ma być używane autouzupełnianie
	var input = $("#MiejsObie");
  
	console.log({input});
	// Ustawienie opcji biblioteki jQuery UI Autocomplete
	input.autocomplete({
	  // Źródło danych dla autouzupełniania
	  source: function(request, response) {
		// Wysłanie zapytania do serwera
		$.ajax({
		  url: "ajax/ajax_Miejscowosc_kontrole.php",
		  type: "POST",
		  
		  data: {
			Miejscowosc: request.term,
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
  // Nazwa ulicy 
  //-------------------------------------------------------------------------------
  // Inicjalizacja biblioteki jQuery UI Autocomplete
  $(function() {
	// Wybór pola, dla którego ma być używane autouzupełnianie
	var input = $("#UlicObie");
	var input_miej = $("#MiejsObie").val();
	
	// Ustawienie opcji biblioteki jQuery UI Autocomplete
	input.autocomplete({
	  // Źródło danych dla autouzupełniania
	  source: function(request, response) {
		// Wysłanie zapytania do serwera
		$.ajax({
		  url: "ajax/ajax_Ulice_kontrole.php",
		  type: "POST",
		  
		  data: {
			Ulica: request.term,
			Miejscowosc: $("#MiejsObie").val(),
			
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
  // Kontrolujący 
  //-------------------------------------------------------------------------------
  // Inicjalizacja biblioteki jQuery UI Autocomplete
  $(function() {
	// Wybór pola, dla którego ma być używane autouzupełnianie
	var input = $("#OsobaKontr");
	console.log({input});
	// Ustawienie opcji biblioteki jQuery UI Autocomplete
	input.autocomplete({
	  // Źródło danych dla autouzupełniania
	  source: function(request, response) {
		// Wysłanie zapytania do serwera
		$.ajax({
		  url: "ajax/ajax_Osoby_kontrolujace.php",
		  type: "POST",
		  
		  data: {
			Osoba: request.term,
		  },
		  success: function(datain) {
			
			// Przetworzenie danych z serwera
			var osoby = JSON.parse(datain);
			var imnazw = osoby.map((imnaz) => ({
				label: imnaz.imnaz,
				value: imnaz.imnaz,
				id_oso_kon:imnaz.id_oso_kont
			}));
			
			// Zwrócenie listy wartości do autouzupełniania
			response(imnazw);
		  }
		});
	  },

	  // Wybór wartości z autouzupełniania
	  select: function(event, ui) {
		// Ustawienie wartości pola na wybraną wartość
		input.val(ui.item.value);
		id_oso_kon = ui.item.id_oso_kon;
	  },
	  
	  // Wyświetlanie podpowiedzi w miarę wpisywania tekstu
	  minLength: 0
	});
  });

//-----------------------------------------------------------------------------------------------
// obsługa checkbox do wybory rodzaju kontroli 
const checkbox = document.getElementById('uwzglednijRodzKontr');
const select = document.getElementById('selRodzKontr');

select.disabled = true;
select.style.backgroundColor = 'white';
select.style.borderRadius = "3px";
select.style.color = 'silver';

checkbox.addEventListener('change', function() {
	console.log ("przed chb");
	if (this.checked) {
		console.log ("aktywny");
		select.disabled = false;
		select.style.backgroundColor ='transparent'
		select.style.borderRadius = "3px";
		select.style.color = 'gray'

	} 
	else {
		console.log ("nie akt");
		select.disabled = true;
		select.style.backgroundColor = 'white';
		select.style.borderRadius = "3px";
		select.style.color = 'Silver'
	}
});

//-----------------------------------------------------------------------------------------------
// obsługa checkbox do wybory rodzaju kontroli 
const checkboxImNaz = document.getElementById('uwzglednijImieNazwWlSzamba');
const textImNaz = document.getElementById('ImieNazwWlSzamba');
const divAdrWlSzamba = document.getElementById('divAdrWlSzamba');

textImNaz.style.backgroundColor = 'white';
divAdrWlSzamba.style.display = "none";
textImNaz.disabled = true;

checkboxImNaz.addEventListener('change', function() {
	console.log ("przed chb");
	if (this.checked) {
		console.log ("aktywny");
		textImNaz.disabled = false;
		textImNaz.style.backgroundColor ='transparent'
		divAdrWlSzamba.style.display = "flex";

	} 
	else {
		console.log ("nie akt");
		textImNaz.disabled = true;
		textImNaz.style.backgroundColor = 'white';
		divAdrWlSzamba.style.display = "none";
	}
});

//-----------------------------------------------------------------------------------------------
// obsługa checkbox do wybory adresu obiektu 
const checkboxAdrObie = document.getElementById('uwzglednijAdresKonObie');
const textAdrObie = document.getElementById('AdresKonObie');
const divAdrObiektu = document.getElementById('divAdrObiektu');

const KodAdrObiektu = document.getElementById('KodAdrObiektu');
const MiejscowoscAdrObiektu = document.getElementById('MiejscowoscAdrObiektu');
const UlicaAdrObiektu = document.getElementById('UlicaAdrObiektu');
const NrAdrObiektu = document.getElementById('NrAdrObiektu');


textAdrObie.style.backgroundColor = 'white';
divAdrObiektu.style.display = "none";
textAdrObie.disabled = true;

checkboxAdrObie.addEventListener('change', function() {
	console.log ("przed chb");
	if (this.checked) {
		console.log ("aktywny");
		textAdrObie.disabled = false;
		textAdrObie.style.backgroundColor ='transparent'
		divAdrObiektu.style.display = "flex";
		ulicaMiejCheckbox.checked = false;
		UlicObie.value="";
		MiejsObie.value="";
		UlicObie.disabled = true;
		UlicObie.style.backgroundColor ='white';
		MiejsObie.disabled = true;
		MiejsObie.style.backgroundColor ='white';

	} 
	else {
		console.log ("nie akt");
		textAdrObie.value="";
		textAdrObie.disabled = true;
		textAdrObie.style.backgroundColor = 'white';
		
		KodAdrObiektu.value="";
		MiejscowoscAdrObiektu.value="";
		UlicaAdrObiektu.value="";
		NrAdrObiektu.value="";

		divAdrObiektu.style.display = "none";
	}
});
//-----------------------------------------------------------------------------------------------
// obsługa checkbox filtrowania po miejscowość - ulica
const uwzgledniUlicaMiej = document.getElementById('uwzgledniUlicaMiej');
const UlicObie = document.getElementById('UlicObie');
const MiejsObie = document.getElementById('MiejsObie');
const AdresKonObie = document.getElementById('AdresKonObie');

UlicObie.disabled = true;
UlicObie.style.backgroundColor ='white'
MiejsObie.disabled = true;
MiejsObie.style.backgroundColor ='white'

uwzgledniUlicaMiej.addEventListener('change', function() {
	console.log ("przed chb");
	if (this.checked) {
		console.log ("aktywny");
		UlicObie.disabled = false;
		UlicObie.style.backgroundColor ='transparent'
		MiejsObie.disabled = false;
		MiejsObie.style.backgroundColor ='transparent'
		AdresKonObie.value="";
		KodAdrObiektu.value="";
		MiejscowoscAdrObiektu.value="";
		UlicaAdrObiektu.value="";
		NrAdrObiektu.value="";

	} 
	else {
		console.log ("nie akt");
		UlicObie.disabled = true;
		UlicObie.style.backgroundColor ='white';
		MiejsObie.disabled = true;
		MiejsObie.style.backgroundColor ='white';
		UlicObie.value="";
		MiejsObie.value="";
	}
});

//-----------------------------------------------------------------------------------------------
// obsługa checkbox z datami 
const checkDataOdDo = document.getElementById('uwzglednijDataOdDo');

const txtDataOd = document.getElementById('txtDataOd');
const txtDataDo = document.getElementById('txtDataDo');
const dataOd = document.getElementById('dataOd');
const dataDo = document.getElementById('dataDo');

txtDataOd.style.backgroundColor = 'white';
txtDataOd.style.color = "silver"
dataOd.style.backgroundColor = 'white';
dataOd.disabled = true;
dataOd.style.color = 'silver';

txtDataDo.style.backgroundColor = 'white';
txtDataDo.style.color = "silver"
dataDo.style.backgroundColor = 'white';
dataDo.disabled = true;
dataDo.style.color = 'silver';

checkDataOdDo.addEventListener('change', function() {
	console.log ("przed chb");
	if (this.checked) {
		console.log ("aktywny");
		
		txtDataOd.style.backgroundColor = 'transparent';
		txtDataOd.style.color = "gray"
		dataOd.style.backgroundColor = 'transparent';
		dataOd.disabled = false;
		dataOd.style.color = 'gray';

		txtDataDo.style.backgroundColor = 'transparent';
		txtDataDo.style.color = "gray"
		dataDo.style.backgroundColor = 'transparent';
		dataDo.disabled = false;
		dataDo.style.color = 'gray';

	} 
	else {
		console.log ("nie akt");
		txtDataOd.style.backgroundColor = 'white';
		txtDataOd.style.color = "silver"
		dataOd.style.backgroundColor = 'white';
		dataOd.disabled = true;
		dataOd.style.color = 'silver';

		txtDataDo.style.backgroundColor = 'white';
		txtDataDo.style.color = "silver"
		dataDo.style.backgroundColor = 'white';
		dataDo.disabled = true;
		dataDo.style.color = 'silver';

	}
});


//-----------------------------------------------------------------------------------------------
// obsługa checkbox osoby kontrolujace 
const uwzgledniOsobaKontr = document.getElementById('uwzgledniOsobaKontr');
const OsobaKontr = document.getElementById('OsobaKontr');

OsobaKontr.style.backgroundColor = 'white';
OsobaKontr.disabled = true;

uwzgledniOsobaKontr.addEventListener('change', function() {
	console.log ("przed chb");
	if (this.checked) {
		console.log ("aktywny");
		OsobaKontr.disabled = false;
		OsobaKontr.style.backgroundColor ='transparent'
		
	} 
	else {
		console.log ("nie akt");
		OsobaKontr.value="";
		OsobaKontr.disabled = true;
		OsobaKontr.style.backgroundColor = 'white';
	}
});

//-----------------------------------------------------------------------------------------------
// Obsluga przycisku do generowania list z raportów kontroli 
//-----------------------------------------------------------------------------------------------

document.querySelector('.gen_rap_kontrole').addEventListener('click', function() {
	let maska_szukania = '';

	// Sprawdzenie które chceckboxy sa zaznaczone 
	const selectElement = document.getElementById('selRodzKontr');
	console.log({selectElement});
	console.log('selectElement.value :'+selectElement.value);
	let rodzajKontroli = selectElement.value;


	if (uwzglednijRodzKontr.checked) {
		//maska_szukania |= 1;
		maska_szukania=maska_szukania+'a';
		console.log('Rodzja kontroli - zaznaczony');
		if (selectElement.value==="initial"){
			customAlert('Proszę wybrać rodzaj kontroli.');		
		}else if (selectElement.value==="Wszystkie"){
			rodzajKontroli = '%';
		
		console.log('Rodzaj kontroli - zaznaczony');}
		console.log({maska_szukania});
	} 

	if (uwzglednijImieNazwWlSzamba.checked) {
		//maska_szukania |= 10;
		maska_szukania=maska_szukania+'b';
		console.log('ImNaz własciciela - zaznaczony');
		console.log({maska_szukania});
	} 

	if (uwzglednijAdresKonObie.checked) {
		//maska_szukania |= 100;
		maska_szukania=maska_szukania+'c';
		console.log('Adres Obiektu - zaznaczony');
		console.log({maska_szukania});
	} 

	if (uwzgledniUlicaMiej.checked) {
		//maska_szukania |= 1000;
		maska_szukania=maska_szukania+'d';
		console.log('Miejscowosc/ulica - zaznaczony');
		console.log({maska_szukania});
	}
	if (uwzglednijDataOdDo.checked) {
		//maska_szukania |= 10000;
		maska_szukania=maska_szukania+'e';
		console.log('Data od - do - zaznaczony');
		console.log({maska_szukania});
	}
	if (uwzgledniOsobaKontr.checked) {
		//maska_szukania |= 100000;
		maska_szukania=maska_szukania+'f';
		console.log('Ososba kontrolująca - zaznaczony');
		console.log({maska_szukania});
	}
	

	const miejscowoscInput = document.querySelector('#MiejsObie');
	const miejscowosc = miejscowoscInput.value;

	const ulicaInput = document.querySelector('#UlicObie');
	const ulica = ulicaInput.value;

	const dataOdInput = document.querySelector('#dataOd');
	const dataOd = dataOdInput.value;
	
	const dataDoInput = document.querySelector('#dataDo');
	const dataDo = dataDoInput.value;


//---------------

//define row context menu contents
var rowMenu = [
    {
        label:"<i class='fas fa-eye'></i> Podgląd",
        action:function(e, row){
            //window.open('kontrole_view.php',"_self");
			console.log ({row});
			let dataObj = row.getData();
			console.log ({dataObj});
			console.log (dataObj.id_kon);
			//sessionStorage.setItem('SS_id_kontroli', dataObj.id_kon);
			//localStorage.setItem('SS_id_kontroli', dataObj.id_kon);
			// Otwórz nowe okno po pomyślnym przekazaniu danych
			//let wys_rapo_kon = window.open('kontrole_view.php',"_self");

			 // Zbuduj adres URL z parametrem id_kon
			 let url = 'kontrole_view.php?id_kon=' + dataObj.id_kon;

			 // Otwórz nowe okno z podanym adresem URL
			 let wys_rapo_kon = window.open(url, '_self');
			
        }
    },
    {
        label:"<i class='fas fa-trash'></i> Usuń",
        action:function(e, row){
			row.delete();
        }
    }
]


//---------------
	
	console.log({id_oso_kon});

	// Wysyłamy wartość do serwera
	$.ajax({
		url: "ajax/ajax_lista_kontrole.php",
		type: "POST",
		data: {
			maska_szukania:maska_szukania,
			rodzajKontroli:rodzajKontroli, // 1
			id_wlascicel_szamba:id_wlascicel_szamba,  //10
			id_adr_ob_kon:id_adr_ob_kon, //100
			miejscowosc:miejscowosc, //1 000
			ulica:ulica, //	1 000
			dataOd:dataOd, //10 000
			dataDo:dataDo, //10 000
			id_oso_kon:id_oso_kon, //100 000
		},
		success: function(dane) {
		  // Jeśli wartość znajduje się w tabeli
		  var dane_osob = JSON.parse(dane);
		  //o.imie , o.nazwisko, a.ulica , a.numer , a.kod_pocztowy , a.miejscowosc
		  	dane_osob = new Tabulator("#example-table",{
				rowContextMenu: rowMenu, //add context menu to rows
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
					
						{title:"Id_kontroli", field:"id_kon",visible: false},
						{title:"Rodzaj kontroli", field:"nazwa_kontroli",headerFilter:"input"},
						{title:"Data kontroli", field:"data_kon",headerFilter:"input"},
						{title:"Właściciel", field:"wasciciel",headerFilter:"input"},
						{title:"Adres właściciela", field:"adres_wlasciciel",headerFilter:"input"},
						{title:"Adres obiektu", field:"adres_obiektu",headerFilter:"input"},
						{title:"Osoby kontrolujące", field:"osoby_kont",headerFilter:"input"},
						{title:"Dokumenty", field:"dokumenty",headerFilter:"input" },
						{title:"Zalecenia pokontrolne", field:"zalecenia_pokontrolne",headerFilter:"input" },
						{title:"Uwagi", field:"uwagi",headerFilter:"input" },
					],
				rowFormatter:function(row){
					var data_tmp = row.getData(); //pobierz dane z wiersza
				},
				
		});
		},
		error: function(error) {
		  callback(-1); // Błąd
		}
	});
});

//------------------------------------------------------------------------------------------
// Ustawienia daty poczaątku wyszukiwania na początek aktualnego roku
//------------------------------------------------------------------------------------------
const startDateInput = document.getElementById('dataOd');

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
const endDateInput = document.getElementById('dataDo');

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
 
// Zablokowanie możliwości jednoiczesnego zaznaczenia checkboxów do szukania po pełnym adresie obierktu w szukania po miejscowości i ulicy 
// Pobierz elementy checkboxów

const adresKonObieCheckbox = document.getElementById('uwzglednijAdresKonObie');
const ulicaMiejCheckbox = document.getElementById('uwzgledniUlicaMiej');

/*adresKonObieCheckbox.addEventListener('change', () => {
	if (adresKonObieCheckbox.checked) {
	  ulicaMiejCheckbox.checked = false;
	}
  });
  */
// Dodaj zdarzenie zmiany stanu dla każdego checkboxa
ulicaMiejCheckbox.addEventListener('change', () => {
  if (ulicaMiejCheckbox.checked) {
    adresKonObieCheckbox.checked = false;
	textAdrObie.disabled = true;
	textAdrObie.style.backgroundColor = 'white';
	divAdrObiektu.style.display = "none";

  }
});

/*
console.log ("przed chb");
	if (this.checked) {
		console.log ("aktywny");
		textAdrObie.disabled = false;
		textAdrObie.style.backgroundColor ='transparent'
		divAdrObiektu.style.display = "flex";

	} 
	else {
		console.log ("nie akt");
		textAdrObie.disabled = true;
		textAdrObie.style.backgroundColor = 'white';
		divAdrObiektu.style.display = "none";
	}
*/

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

