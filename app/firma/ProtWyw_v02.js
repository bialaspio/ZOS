//-------------------------------------------------------------------------------------------
// Obswługa przyciksku do Zapisu umowy 
//-------------------------------------------------------------------------------------------
  document.querySelector('.bt_ZapiszProtWyw').addEventListener('click', async function(e) {
  e.preventDefault();
  var data_cal_l =document.getElementById("data_cal_l").textContent;
  var zlecen_nr = $('#zlecen_nr').val();

  console.log ({data_cal_l});
  console.log ({zlecen_nr});

  let Imie = $('#Imie').val();
  let Nazwisko = $('#Nazwisko').val();
  let KodAdrWlSzamba = $('#KodAdrWlSzamba').val();
  let MiejscowoscAdrWlSzamba = $('#MiejscowoscAdrWlSzamba').val();
  let UlicaAdrWlSzamba = $('#UlicaAdrWlSzamba').val();
  let NrAdrWlSzamba = $('#NrAdrWlSzamba').val();
  
  let KodAdrSzamba = $('#KodAdrSzamba').val();
  let MiejscowoscAdrSzamba = $('#MiejscowoscAdrSzamba').val();
  let UlicaAdrSzamba = $('#UlicaAdrSzamba').val();
  let NrAdrSzamba = $('#NrAdrSzamba').val();
  let PojSzamba = $('#PojSzamba').val();
  
  let RodzajNieczystosci = $('#RodzajNieczystosci').val();
  let data_wyw = document.getElementById('data_wyw').value; 
 
  let NazwaOczyszczalni = $('#selOczyszczalnia').val();
  
  let chkWywozGmina = document.getElementById("chkWywozGmina").checked;
  
  let nazFirmy = $('#nazFirmy').val();
  let nazNIP = $('#nazNIP').val();
  let nazKod = $('#nazKod').val();
  let nazMiejs = $('#nazMiejs').val();
  let nazUlic = $('#nazUlic').val();
  let nazNr = $('#nazNr').val();
  
  let selectElement = document.getElementById('selOczyszczalnia');
  let selectedOption = selectElement.options[selectElement.selectedIndex].value;

  let selectPlatnosc = document.getElementById('selPlatnosc');
  let selectedOptionPlatnosc = selectPlatnosc.options[selectPlatnosc.selectedIndex].value;

  let dzisiaj = new Date();
  let data_wyw_por = new Date(document.getElementById('data_wyw').value);

  dzisiaj.setHours(0, 0, 0, 0); // Ustawiamy czas dzisiejszej daty na 00:00:00
  
  if (selectedOption === 'initial' ){
    customAlert('Należy wybrać oczyszczalnie.');
  }
  else if (selectedOptionPlatnosc === 'initial' ){
    customAlert('Należy wybrać rodzaj płatności.');
  }
  else if (data_wyw_por <= dzisiaj) {
    customAlert("Data nie może być wcześniejsza od aktualnej.");
} 
  else 
  {
    let id_osoba = await pobierz_id_osoba(Imie, Nazwisko, KodAdrWlSzamba, MiejscowoscAdrWlSzamba, UlicaAdrWlSzamba, NrAdrWlSzamba);
	//let id_szamba = await pobierz_id_szamba(KodAdrSzamba, MiejscowoscAdrSzamba, UlicaAdrSzamba, NrAdrSzamba);

	const cookiesString = document.cookie;
	const cookies = {};

	for (const cookie of cookiesString.split(';')) {
  		const [name, value] = cookie.trim().split('=');
  		cookies[name] = value;
	}

	console.log('----------------------------------');
	console.log(cookies.id_szamba);
	console.log('----------------------------------');
	let id_szamba =cookies.id_szamba;

    let id_firmy = await pobierz_id_firmy(nazNIP); 
    let id_oczyszcz = await pobierz_id_oczyszczalni (NazwaOczyszczalni); 

	console.log ({id_oczyszcz}); 
    
    if (id_osoba > 0){
      if (id_szamba > 0){
        if (id_firmy > 0){
         // await zapisz_protokol(zlecen_nr, id_firmy, id_szamba, platnosc, ilosc_sciekow, data_wywozu, id_oczyszcz, realizacja_gmina); 
          await zapisz_protokol(e,zlecen_nr, id_firmy,  id_szamba, id_osoba, selectedOptionPlatnosc,PojSzamba,data_wyw, id_oczyszcz, chkWywozGmina);
		  						
        }
        else {
          customAlert('Nie udało się pobrać identyfikatora firmy.');  
        }
      }
      else {
        customAlert('Nie udało się pobrać identyfikatora szamba.');  
      }
    }
    else {
      customAlert('Nie udało się pobrać identyfikatora osoby.');
    }
  }
});


//---------------------------------------------------------------------------------------------------
//
//---------------------------------------------------------------------------------------------------

async function pobierz_id_osoba(Imie, Nazwisko, KodAdrWlSzamba, MiejscowoscAdrWlSzamba, UlicaAdrWlSzamba, NrAdrWlSzamba) {
  
	// Wysyłamy wartość do serwera
	return new Promise((resolve, reject) => {
		$.ajax({
			url: "ajax/ajax_pobierz_id_osoba_imie_i_naz.php",
			type: "POST",
			data: {
				Imie : Imie.replace(/\u00A0/g, ' '),
        		Nazwisko : Nazwisko.replace(/\u00A0/g, ' '),
				KodAdrOsoba : KodAdrWlSzamba.replace(/\u00A0/g, ' '),
				MiejscowoscAdrOsoba : MiejscowoscAdrWlSzamba.replace(/\u00A0/g, ' '),
				UlicaAdrOsoba : UlicaAdrWlSzamba.replace(/\u00A0/g, ' '),
				NrAdrOsoba : NrAdrWlSzamba.replace(/\u00A0/g, ' '),
			},
			success: function(data) {
				// Jeśli wartość znajduje się w tabeli
				var dane_osoba = JSON.parse(data);
				const id_osoba = dane_osoba.map((id_osoby) => id_osoby.id_osoby);	
				
				if (id_osoba.length > 0){
					resolve(id_osoba);
				}
				else {
					resolve(0);
				}
			},
			error: function(error) {
				reject(error);
			}
		});
	});
}

//---------------------------------------------------------------------------------------------------
//
//---------------------------------------------------------------------------------------------------

async function pobierz_id_szamba(KodAdrSzamba, MiejscowoscAdrSzamba, UlicaAdrSzamba, NrAdrSzamba) {

	// Wysyłamy wartość do serwera
	return new Promise((resolve, reject) => {
		$.ajax({
			url: "ajax/ajax_pobierz_id_szamba.php",
			type: "POST",
			data: {
				KodAdrWlSzamba : KodAdrSzamba.replace(/\u00A0/g, ' '),
				MiejscowoscAdrWlSzamba : MiejscowoscAdrSzamba.replace(/\u00A0/g, ' '),
				UlicaAdrWlSzamba : UlicaAdrSzamba.replace(/\u00A0/g, ' '),
				NrAdrWlSzamba : NrAdrSzamba.replace(/\u00A0/g, ' '),
			},
			success: function(data) {
				// Jeśli wartość znajduje się w tabeli
				var dane_szamba = JSON.parse(data);
				const id_szamba = dane_szamba.map((id_szamba) => id_szamba.id_szamba);	
				
				if (id_szamba.length > 0){
					resolve(id_szamba);
				}
				else {
					resolve(0);
				}
			},
			error: function(error) {
				reject(error);
			}
		});
	});
}

//---------------------------------------------------------------------------------------------------
//
//---------------------------------------------------------------------------------------------------

async function pobierz_id_firmy(NipFirma) {

	// Wysyłamy wartość do serwera
	return new Promise((resolve, reject) => {
		$.ajax({
			url: "ajax/ajax_pobierz_id_firmy_po_nip.php",
			type: "POST",
			data: {
				NipFirma : NipFirma,
			},
			success: function(data) {
				// Jeśli wartość znajduje się w tabeli
				var dane_firmy = JSON.parse(data);
				const id_firmy = dane_firmy.map((id) => id.id);	
				
				if (id_firmy.length > 0){
					resolve(id_firmy);
				}
				else {
					resolve(0);
				}
			},
			error: function(error) {
				reject(error);
			}
		});
	});
}

//---------------------------------------------------------------------------------------------------
//
//---------------------------------------------------------------------------------------------------

async function pobierz_id_oczyszczalni (NazwaOczyszczalni) {
	// Wysyłamy wartość do serwera
	return new Promise((resolve, reject) => {
		$.ajax({
			url: "ajax/ajax_pobierz_id_oczyszczalni.php",
			type: "POST",
			data: {
				NazwaOczyszczalni : NazwaOczyszczalni,
			},
			success: function(data) {
				// Jeśli wartość znajduje się w tabeli
				var dane_Oczyszczalni = JSON.parse(data);
				const id_Oczyszczalni = dane_Oczyszczalni.map((id_oczysz) => id_oczysz.id_oczysz);	
				
				if (id_Oczyszczalni.length > 0){
					resolve(id_Oczyszczalni);
				}
				else {
					resolve(0);
				}
			},
			error: function(error) {
				reject(error);
			}
		});
	});
}


//---------------------------------------------------------------------------------------------------
//
//---------------------------------------------------------------------------------------------------
async function zapisz_protokol(e,zlecen_nr, id_firmy, id_szambo, id_osoba, platnosc, PojSzamba, data_wyw, id_oczyszcz, realizacja_gmina){
	try {
    //console.clear();
    console.log({id_osoba});
	
    let podzielonyStr = zlecen_nr.split("/");
    let data = podzielonyStr[0]
    let nr_prot =  podzielonyStr[1]
    
	let id_firma = id_firmy[0];
    let id_szamba = id_szambo;
	let id_oczyszcz_1 = id_oczyszcz[0];

	console.log({id_oczyszcz_1});
    
    
		$.ajax({
			url: "ajax/ajax_dodaj_prot_wyw.php",
			type: "POST",
			data: {
				data:data,
				nr_prot:nr_prot,
				id_firma:id_firma,
				id_szamba:id_szamba,
				id_osoba:id_osoba,
				platnosc:platnosc,
				PojSzamba:PojSzamba,
				data_wyw:data_wyw,
				id_ocz:id_oczyszcz_1,
				realizacja_gmina:realizacja_gmina,
			},
			success: function (dane) {
		
				// Jeśli wartość znajduje się w tabeli
				if (dane.replace(/\r?\n/g, "") == "Error") {
					// Wyświetlamy komunikat o błędzie
					customAlert("Nie udało się danych dodać do bazy.");
				}
				else if (dane.replace(/\r?\n/g, "") == "false") {
					// Wyświetlamy komunikat o błędzie
					customAlert("Nie udało się danych dodać do bazy.");
				}
				else if ( dane.replace(/\r?\n/g, "") == 'true'){
					
					//customAlert("Dane dodane do bazy.");
					//e.preventDefault();
					openNewPage(zlecen_nr, id_firma, id_szamba, id_osoba,platnosc, PojSzamba, data_wyw, id_oczyszcz_1, realizacja_gmina);
					
				}
				else {
					customAlert("Bład podczas wykonywania polecnia .");
				}
			}
		});
	}
	catch (error) {
	  // Wyświetli komunikat o błędzie
	  customAlert(error.responseText);
	}
} 


//-------------------------------------------------------------------------------------------------------------------
//Funkcja otwierająca nowa strone i przekazująca jej w sesji parametry 
//-------------------------------------------------------------------------------------------------------------------

function openNewPage(zlecen_nr, id_firmy, id_szamba,id_osoba, selectedOptionPlatnosc, PojSzamba, data_wyw, id_oczyszcz, chkWywozGmina) {
    // Zapisanie wartości zmiennych do localStorage
    sessionStorage.setItem('zlecen_nr', zlecen_nr);
    sessionStorage.setItem('id_firmy', id_firmy);
    sessionStorage.setItem('id_szamba', id_szamba);
	sessionStorage.setItem('id_osoba', id_osoba);
    sessionStorage.setItem('selectedOptionPlatnosc', selectedOptionPlatnosc);
    sessionStorage.setItem('PojSzamba', PojSzamba);
    sessionStorage.setItem('data_wyw', data_wyw);
    sessionStorage.setItem('id_oczyszcz', id_oczyszcz);
    sessionStorage.setItem('chkWywozGmina', chkWywozGmina);

    // Otwarcie nowego okna
    window.open("prot_wydruk_po_wyp.php", "_blank");
}

//-------------------------------------------------------------------------------------------------------------------
//wstawienie aktualniej daty do pola kalenarza 
//-------------------------------------------------------------------------------------------------------------------

window.onload = function() {
  var dzisiaj = new Date();
  var dzien = dzisiaj.getDate();
  var miesiac = dzisiaj.getMonth() + 1; // Miesiące są numerowane od 0
  var rok = dzisiaj.getFullYear();

  if(dzien < 10) dzien = '0' + dzien;
  if(miesiac < 10) miesiac = '0' + miesiac;

  dzisiaj = rok + '-' + miesiac + '-' + dzien;

  document.getElementById('data_wyw').value = dzisiaj;
}

//-------------------------------------------------------------------------------------------------------------------
//zegar
//-------------------------------------------------------------------------------------------------------------------
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
//-------------------------------------------------------------------------------------------------------------------

