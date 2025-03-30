var zlecen_nr = sessionStorage.getItem('zlecen_nr');
var id_firmy = sessionStorage.getItem('id_firmy');
var id_osoba = sessionStorage.getItem('id_osoba');
var id_szamba = sessionStorage.getItem('id_szamba');

var PojSzamba = sessionStorage.getItem('PojSzamba');
var data_wyw = sessionStorage.getItem('data_wyw');
var id_oczyszcz = sessionStorage.getItem('id_oczyszcz');




document.getElementById('pojzbior').innerText = PojSzamba;
document.getElementById('datawywozu').innerText = data_wyw;
document.getElementById('nr_zlecenia').innerText = 'Zlecenie nr. '+zlecen_nr;

let dataZeZlecenie = zlecen_nr.split('/')[0];

document.getElementById('labKrakData').innerText = 'Kraków. '+dataZeZlecenie;

pobierz_dane_firmy(id_firmy);
pobierz_dane_osoby(id_osoba) ;
pobierz_adres_szamba(id_szamba) ;
pobierz_oczyszczalni(id_oczyszcz);

//----------------------------------------------------------------------------------------------------------------
// Zaczyatanie rodzaju płatności przy łądowaniu strony 
//----------------------------------------------------------------------------------------------------------------
window.onload = function() {
    var rodzajplatonsci = sessionStorage.getItem('selectedOptionPlatnosc');
    var chkWywozGmina = sessionStorage.getItem('chkWywozGmina');
    console.log({chkWywozGmina});

    if (rodzajplatonsci === 'Gotowka') {
        document.getElementById('labGotowka').style.textDecoration = 'line-through';
    } else if (rodzajplatonsci === 'Przelew') {
        document.getElementById('labPrzelew').style.textDecoration = 'line-through';
    }

    if (chkWywozGmina === "true"){
        
        document.getElementById('labRealGminNIE').style.textDecoration = 'line-through';
    }
    else 
    {
        document.getElementById('labRealGminTAK').style.textDecoration = 'line-through';
    }

}


//----------------------------------------------------------------------------------------------------------------
// Pobranie danych fiirmy 
//----------------------------------------------------------------------------------------------------------------
function pobierz_dane_firmy(IdFirma) {
	// Wysyłamy wartość do serwera
	$.ajax({
		url: "ajax/ajax_pobierz_dane_firmy.php",
		type: "POST",
		data: {
			IdFirma : IdFirma,
		},
		success: function(data) {
			console.log ({data});
            // Jeśli wartość znajduje się w tabeli
			var dane_firmy = JSON.parse(data);
                
            if (dane_firmy.length > 0){
				document.getElementById('labNazwaFirmy').innerText = dane_firmy[0].nazwa;
                document.getElementById('labNipFirmy').innerText = dane_firmy[0].nip;
                document.getElementById('labAdresFirmy').innerText = dane_firmy[0].adres;
			}
		},
		error: function(error) {
			console.error(error);
		}
	});
}

//----------------------------------------------------------------------------------------------------------------
// Pobranie danych osoby 
//----------------------------------------------------------------------------------------------------------------
function pobierz_dane_osoby(id_osoba) {
	// Wysyłamy wartość do serwera
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "ajax/ajax_pobierz_dane_osoby.php",
            type: "POST",
            data: {
                IdOsoba : id_osoba,
            },
            success: function(data) {
                console.log ({data});
                // Jeśli wartość znajduje się w tabeli
                var dane_osoby = JSON.parse(data);
                    
                if (dane_osoby.length > 0){
                    document.getElementById('labImie').innerText = dane_osoby[0].imie;
                    document.getElementById('labNazwisko').innerText = dane_osoby[0].nazwisko;
                }
            },
            error: function(error) {
                console.error(error);
            }
        });
    });
}


 
//----------------------------------------------------------------------------------------------------------------
// Pobranie adresu szamba
//----------------------------------------------------------------------------------------------------------------
function pobierz_adres_szamba(id_szamba) {
	// Wysyłamy wartość do serwera
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "ajax/ajax_pobierz_adres_szamba.php",
            type: "POST",
            data: {
                IdSzamba : id_szamba,
            },
            success: function(data) {
                console.log ({data});
                // Jeśli wartość znajduje się w tabeli
                var dane_adresSzamba = JSON.parse(data);
                    
                if (dane_adresSzamba.length > 0){
                    document.getElementById('labUlicaNr').innerText = dane_adresSzamba[0].adres;
                    document.getElementById('labKodMiejscowosc').innerText = dane_adresSzamba[0].kod_miejscowosc;
                    document.getElementById('labRodzajNieczystosci').innerText = dane_adresSzamba[0].rodzaj_nieczystosci;
                    
                }
            },
            error: function(error) {
                console.error(error);
            }
        });
    });
}


//----------------------------------------------------------------------------------------------------------------
// Pobranie danych oczyszczalni
//----------------------------------------------------------------------------------------------------------------
function pobierz_oczyszczalni(id_oczyszczalni) {
	// Wysyłamy wartość do serwera
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "ajax/ajax_pobierz_dane_oczyszczalni.php",
            type: "POST",
            data: {
                IdOczyszczalni : id_oczyszczalni,
            },
            success: function(data) {
                console.log ({data});
                // Jeśli wartość znajduje się w tabeli
                var dane_oczyszczalni = JSON.parse(data);
                    
                if (dane_oczyszczalni.length > 0){
                    document.getElementById('labOczysz').innerText = dane_oczyszczalni[0].nazwa_oczysz;
                }
            },
            error: function(error) {
                console.error(error);
            }
        });
    });
}

//----------------------------------------------------------------------------------------------------------------
// Wydrukowanie strony 
//----------------------------------------------------------------------------------------------------------------
document.getElementById("bt_Drukuj").addEventListener("click", function() {
    console.log("Drukuj")
    window.print();
    console.log("Zamknij")
    window.close();
  });

  //----------------------------------------------------------------------------------------------------------------
// Zamknięcie strony 
//----------------------------------------------------------------------------------------------------------------
document.getElementById("bt_Zamknij").addEventListener("click", function() {
    console.log("Zamknij")
    window.close();
  });