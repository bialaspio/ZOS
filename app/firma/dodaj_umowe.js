// --- Helper Functions ---

/**
 * Adds a leading zero to numbers less than 10.
 * @param {number} number - The number to format.
 * @returns {string} The formatted number.
 */
function addLeadingZero(number) {
	return number < 10 ? "0" + number : number;
  }
  
  /**
   * Initializes a jQuery UI Autocomplete field.
   * @param {string} inputSelector - The CSS selector for the input field.
   * @param {string} url - The URL for the AJAX request.
   * @param {function} requestDataCallback - A function to format the request data.
   * @param {function} responseDataCallback - A function to format the response data.
   */
  function initializeAutocomplete(inputSelector, url, requestDataCallback, responseDataCallback) {
	$(inputSelector).autocomplete({
	  source: function (request, response) {
		$.ajax({
		  url: url,
		  type: "POST",
		  data: requestDataCallback(request),
		  success: function (data) {
			const parsedData = JSON.parse(data);
			response(responseDataCallback(parsedData));
		  },
		  error: function (error) {
			console.error("Autocomplete error:", error);
		  },
		});
	  },
	  select: function (event, ui) {
		$(inputSelector).val(ui.item.value);
	  },
	  minLength: 0,
	});
  }
  
  /**
   * Creates a custom dialog box.
   * @param {string} message - The message to display in the dialog.
   * @param {function} yesCallback - The callback function for the "Yes" button.
   * @param {function} noCallback - The callback function for the "No" button.
   */
  function createDialog(message, yesCallback, noCallback) {
	const dialog = document.createElement("div");
	styleDialog(dialog);
  
	const messageElement = document.createElement("p");
	messageElement.innerText = message;
	messageElement.style.fontWeight = "bold";
	messageElement.style.color = "#424949";
	dialog.appendChild(messageElement);
  
	const buttonYes = document.createElement("button");
	buttonYes.innerText = "Tak";
	buttonYes.onclick = () => {
	  yesCallback();
	  dialog.remove();
	};
	styleButton(buttonYes);
	dialog.appendChild(buttonYes);
  
	const buttonNo = document.createElement("button");
	buttonNo.innerText = "Nie";
	buttonNo.onclick = () => {
	  noCallback();
	  dialog.remove();
	};
	styleButton(buttonNo);
	dialog.appendChild(buttonNo);
  
	document.body.appendChild(dialog);
  }
  
  /**
   * Generic AJAX function to handle server requests.
   * @param {string} url - The URL for the AJAX request.
   * @param {object} data - The data to send to the server.
   * @returns {Promise<Array|number>} - A promise that resolves with the data or 0 if no data is found.
   */
  async function fetchData(url, data) {
	return new Promise((resolve, reject) => {
	  $.ajax({
		url: url,
		type: "POST",
		data: data,
		success: function (response) {
		  try {
			const parsedData = JSON.parse(response);
			const ids = parsedData.map((item) => item.id || item.id_osoby || item.id_szamba || item.ogc_fid);
			resolve(ids.length > 0 ? ids : 0);
		  } catch (error) {
			reject(error);
		  }
		},
		error: function (error) {
		  reject(error);
		},
	  });
	});
  }
  
  // --- Autocomplete Initialization ---
  
  // Initialize Autocomplete for all address fields
  initializeAutocomplete(
	"#KodAdrSzamba",
	"ajax/ajax_KodPocz.php",
	(request) => ({ KodPocz: request.term, Miejscowosc: $("#MiejscowoscAdrSzamba").val() }),
	(data) => data.map((item) => item.kod_pocztowy)
  );
  
  initializeAutocomplete(
	"#MiejscowoscAdrSzamba",
	"ajax/ajax_Miejscowosc.php",
	(request) => ({ Miejscowosc: request.term, Kod: $("#KodAdrSzamba").val() }),
	(data) => data.map((item) => item.miejscowosc)
  );
  
  initializeAutocomplete(
	"#UlicaAdrSzamba",
	"ajax/ajax_Ulice.php",
	(request) => ({
	  Ulica: request.term,
	  Miejscowosc: $("#MiejscowoscAdrSzamba").val(),
	  Kod: $("#KodAdrSzamba").val(),
	}),
	(data) => data.map((item) => item.ulica)
  );
  
  initializeAutocomplete(
	"#NrAdrSzamba",
	"ajax/ajax_Numer.php",
	(request) => ({
	  Numer: request.term,
	  Ulica: $("#UlicaAdrSzamba").val(),
	  Miejscowosc: $("#MiejscowoscAdrSzamba").val(),
	  Kod: $("#KodAdrSzamba").val(),
	}),
	(data) => data.map((item) => item.numer)
  );
  
  // Initialize Autocomplete for owner name
  initializeAutocomplete(
	"#ImieNazwWlSzamba",
	"ajax/ajax_ImieNazwWlSzamba.php",
	(request) => ({ ImieNazWlSzamba: request.term }),
	(data) =>
	  data.map((osoba) => ({
		label: `${osoba.imie} ${osoba.nazwisko} Ul. ${osoba.ulica} ${osoba.numer} ${osoba.kod_pocztowy} ${osoba.miejscowosc}`,
		value: `${osoba.imie} ${osoba.nazwisko}`,
		ulica: osoba.ulica,
		numer: osoba.numer,
		kod_pocztowy: osoba.kod_pocztowy,
		miejscowosc: osoba.miejscowosc,
	  }))
  );
  
  // Initialize Autocomplete for company name/NIP
  initializeAutocomplete(
	"#NazwaNipFirma",
	"ajax/ajax_firma_nip_naz.php",
	(request) => ({ nazwa_nip: request.term }),
	(data) =>
	  data.map((firma) => ({
		label: `${firma.nazwa} NIP:${firma.nip}`,
		value: `${firma.nazwa} NIP:${firma.nip}`,
		ulica: firma.ulica,
		numer: firma.numer,
		kod_pocztowy: firma.kod_pocztowy,
		miejscowosc: firma.miejscowosc,
	  }))
  );
  
  // --- Event Listeners ---
  
  // Update address fields when owner name is selected
  $("#ImieNazwWlSzamba").on("autocompleteselect", function (event, ui) {
	$("#UlicaAdrWlSzamba").val(ui.item.ulica);
	$("#NrAdrWlSzamba").val(ui.item.numer);
	$("#KodAdrWlSzamba").val(ui.item.kod_pocztowy);
	$("#MiejscowoscAdrWlSzamba").val(ui.item.miejscowosc);
	event.preventDefault();
  });
  
  // Update company address fields when company name/NIP is selected
  $("#NazwaNipFirma").on("autocompleteselect", function (event, ui) {
	$("#UlicaFirma").val(ui.item.ulica);
	$("#NrFirma").val(ui.item.numer);
	$("#KodFirma").val(ui.item.kod_pocztowy);
	$("#MiejscowoscFirma").val(ui.item.miejscowosc);
	event.preventDefault();
  });
  
  // Add company button
  $(".bt_DodajFirme").on("click", () => window.open("dodaj_firme.php"));
  
  // Add person button
  $(".bt_DodajOsobe").on("click", () => window.open("dodaj_osobe.php"));
  
  // --- Main Logic ---
  
  /**
   * Adds a new contract.
   */
  async function addContract() {
	const isDifferentAddress = $("#checkboxInnyAdrSzamb").prop("checked");
  
	const ownerData = {
	  ImieNazwWlSzamba: $("#ImieNazwWlSzamba").val(),
	  KodAdrWlSzamba: $("#KodAdrWlSzamba").val(),
	  MiejscowoscAdrWlSzamba: $("#MiejscowoscAdrWlSzamba").val(),
	  UlicaAdrWlSzamba: $("#UlicaAdrWlSzamba").val(),
	  NrAdrWlSzamba: $("#NrAdrWlSzamba").val(),
	};
  
	const sewageData = isDifferentAddress
	  ? {
		  KodAdrSzamba: $("#KodAdrSzamba").val(),
		  MiejscowoscAdrSzamba: $("#MiejscowoscAdrSzamba").val(),
		  UlicaAdrSzamba: $("#UlicaAdrSzamba").val(),
		  NrAdrSzamba: $("#NrAdrSzamba").val(),
		}
	  : ownerData;
  
	const companyData = {
	  NazwaNipFirma: $("#NazwaNipFirma").val(),
	};
  
	// Validate different address fields if checked
	if (isDifferentAddress) {
	  if (!sewageData.KodAdrSzamba.trim()) return customAlert("Pole Kod w sekcji Inny adres szamba nie może być puste");
	  if (!sewageData.MiejscowoscAdrSzamba.trim()) return customAlert("Pole Miejscowość w sekcji Inny adres szamba nie może być puste");
	  if (!sewageData.UlicaAdrSzamba.trim()) return customAlert("Pole Ulica w sekcji Inny adres szamba nie może być puste");
	  if (!sewageData.NrAdrSzamba.trim()) return customAlert("Pole Numer w sekcji Inny adres szamba nie może być puste");
	}
  
	try {
	  const ownerId = await fetchData("ajax/ajax_pobierz_id_osoba.php", ownerData);
	  if (ownerId === 0) return createDialog("Brak w bazie podanych danych osobowych. \nW celu dodania umowy należy najpierw dodać dane osobowe.\nCzy chcesz teraz dodać osobę do bazy?", () => window.open("dodaj_osobe.php"), () => {});
  
	  const addressPointId = isDifferentAddress ? await fetchData("ajax/ajax_pobierz_id_adresu.php", sewageData) : 1;
	  if (addressPointId === 0) return customAlert("W bazie nie ma podengo punktu adresowego, dodanie szamba nie jest możliwe");
  
	  const sewageId = isDifferentAddress ? await fetchData("ajax/ajax_pobierz_id_szamba.php", sewageData) : await fetchData("ajax/ajax_pobierz_id_szamba.php", ownerData);
	  if (sewageId === 0) return createDialog("W bazie brak danych dla szamba w tej lokalizacji.\nCzy dodać teraz to szambo do bazy?", () => dodanie_szamba_PHP(ownerData, sewageData), () => {});
  
	  const companyId = await fetchData("ajax/ajax_pobierz_id_firmy.php", companyData);
	  if (companyId === 0) return createDialog("W bazie brak danych Firmy.\nCzy chcesz teraz dodać firmę do bazy?", () => window.open("dodaj_firme.php"), () => {});
  
	  await addContractToDatabase(ownerId, sewageId, companyId);
	} catch (error) {
	  console.error("Error adding contract:", error);
	  customAlert("Wystąpił błąd podczas dodawania umowy.");
	}
  }
  
  /**
   * Adds a contract to the database.
   * @param {number} ownerId - The ID of the owner.
   * @param {number} sewageId - The ID of the sewage.
   * @param {number} companyId - The ID of the company.
   */
  async function addContractToDatabase(ownerId, sewageId, companyId) {
	const contractData = {
	  id_osoba: ownerId[0],
	  id_szambo: sewageId[0],
	  id_firma: companyId[0],
	  umowa_od: $("#start").val(),
	  umowa_do: $("#end").val(),
	};
  
	$.ajax({
	  url: "ajax/ajax_dodaj_umowe.php",
	  type: "POST",
	  data: contractData,
	  success: function (response) {
		const data = response.replace(/\r?\n/g, "");
		if (data === "Error" || data === "false") {
		  customAlert("Nie udało się danych dodać do bazy.");
		} else if (data === "true") {
		  customAlert("Dane dodane do bazy.");
		  const addedContract = window.open("rap_wywozu.php");
		  addedContract.onload = function () {
			const select = addedContract.document.getElementById("wybierz-firme");
			for (let i = 0; i < select.options.length; i++) {
			  if (select.options[i].value == contractData.id_firma) {
				select.options[i].selected = true;
				break;
			  }
			}
		  };
		} else {
		  customAlert("Błąd podczas wykonywania polecenia.");
		}
	  },
	  error: function (error) {
		console.error("Error adding contract to database:", error);
		customAlert("Wystąpił błąd podczas dodawania umowy.");
	  },
	});
  }
  
  /**
   * Opens a new window to add a sewage system.
   * @param {object} ownerData - The owner data.
   * @param {object} sewageData - The sewage data.
   */
  async function dodanie_szamba_PHP(ownerData, sewageData) {
	const { ImieNazwWlSzamba, KodAdrWlSzamba, MiejscowoscAdrWlSzamba, UlicaAdrWlSzamba, NrAdrWlSzamba } = ownerData;
	const { KodAdrSzamba, MiejscowoscAdrSzamba, UlicaAdrSzamba, NrAdrSzamba } = sewageData;
  
	sessionStorage.setItem("SS_ulica_szambo", UlicaAdrSzamba);
	sessionStorage.setItem("SS_numer_szambo", NrAdrSzamba);
	sessionStorage.setItem("SS_miejscowosc_szambo", MiejscowoscAdrSzamba);
	sessionStorage.setItem("SS_kod_szambo", KodAdrSzamba);
  
	const addSewageWindow = window.open("dodaj_szambo_z_umowa.php");
	addSewageWindow.onload = function () {
	  const [Imie, Nazwisko] = ImieNazwWlSzamba.split(" ");
  
	  addSewageWindow.document.getElementById("Imie").value = Imie;
	  addSewageWindow.document.getElementById("Nazwisko").value = Nazwisko;
  
	  addSewageWindow.document.getElementById("UlicaAdrOsoba").value = UlicaAdrWlSzamba;
	  addSewageWindow.document.getElementById("NrAdrOsoba").value = NrAdrWlSzamba;
	  addSewageWindow.document.getElementById("MiejscowoscAdrOsoba").value = MiejscowoscAdrWlSzamba;
	  addSewageWindow.document.getElementById("KodAdrOsoba").value = KodAdrWlSzamba;
  
	  addSewageWindow.document.getElementById("UlicaAdrSzamba").value = UlicaAdrSzamba;
	  addSewageWindow.document.getElementById("NrAdrSzamba").value = NrAdrSzamba;
	  addSewageWindow.document.getElementById("MiejscowoscAdrSzamba").value = MiejscowoscAdrSzamba;
	  addSewageWindow.document.getElementById("KodAdrSzamba").value = KodAdrSzamba;
	};
  }
  
  // --- Event Listeners ---
  $(".bt_Zapisz").on("click", addContract);
  
  // --- Clock ---
  const zegarContent = document.querySelector(".czas");
  function zegar() {
	const d = new Date();
	const day = addLeadingZero(d.getDate());
	const month = addLeadingZero(d.getMonth() + 1);
	const year = d.getFullYear();
	const hour = addLeadingZero(d.getHours());
	const minutes = addLeadingZero(d.getMinutes());
	const seconds = addLeadingZero(d.getSeconds());
	zegarContent.innerHTML = `${year}.${month}.${day} ${hour}:${minutes}:${seconds}`;
  }
  zegar();
  setInterval(zegar, 1000);
  