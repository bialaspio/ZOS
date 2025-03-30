	<?php
		require '../menu_str.php';
	?>
	
		<div style = "height:20px"></div>

		<div class = "dodaj_umowe">
			<label class = "labDaneFirmy"> Dodanie umowy. </label >
			<form id="formDodUmowa" method="post"  style = "display: flex;">
				<div class = "divP1InForm">
					<label class = "labDaneOsobFirma">Dane zawierającego umowę. </label >
					<div style = "display: flex; width: 90%;">
						<div style = "width: 73%">
							<div class = "divWlSzamba">
								<label class = "labDodUmowa">Imie i Nazwisko.</label>
								<input id="ImieNazwWlSzamba" name="ImieNazwWlSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Imie i Nazwisko">
							</div>
						</div>
						<div style = "width: 2%;"> </div>
						<div style = "width: 24%;">
							<label class = "labDodUmowa" style = "display:flex;">&nbsp&nbsp</label>
							<input type="submit" value="Dodaj osobę" id = "bt_DodajOsobe" class ="bt_DodajOsobe butZapisz" style = "width:98%;" >	
						</div>
					</div>
					<div class = "divAdrWlSzamba">
						<div style = "display: grid; width: 28%;">
							<label class = "labDodUmowa">Kod:</label>
							<input id="KodAdrWlSzamba" name="KodAdrWlSzamba" class="KodAdrWlSzamba textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="00-000" readonly>
						</div>
						<div class ="divOdstep"> </div>
						<div <div style = "display: grid; width: 70%;">
							<label class = "labDodUmowa">Miejscowość: </label>
							<input id="MiejscowoscAdrWlSzamba" name="MiejscowoscAdrWlSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Miejscowość" readonly>
						</div>
					</div>

					<div class = "divAdrWlSzamba">
						<div style = "display: grid; width: 68%;">
							<label class = "labDodUmowa">Ulica:</label>
							<input id="UlicaAdrWlSzamba" name="UlicaAdrWlSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Nazwa ulicy." readonly>
							
						</div>
						<div class ="divOdstep"> </div>
						<div <div style = "display: grid; width: 30%;">
							<label class = "labDodUmowa">Numer: </label>
							<input type="text" id="NrAdrWlSzamba" name="NrAdrWlSzamba" class = "textDodUmoweSzamba" placeholder="Wprowadz numer" readonly>
						</div>
					</div>
					
					<div class = "divchbInnyAdrSzamba">
						<input type="checkbox" name="chkInnyAdrSzamb" id="chkInnyAdrSzamb" value="chkInnyAdrSzamb">
						<div class ="divchbInnyAdrSzamb"> </div>
						<label class = "labInnyAdrSzamb">Inny adres szamba</label>
					</div>
					
					<div class = "divInnyAdrSzamba" id = "divInnyAdrSzamba">
						<!--<input type="textarea" id="InnyAdrKor" class = "textnazInnyAdrKor" style="display: none; " value ="<?php echo $innyadrkor?>">-->
						
						<div class = "divAdrSzamba">
							<div style = "display: grid; width: 28%;">
								<label class = "labDodUmowa">Kod:</label>
								<input id="KodAdrSzamba" name="KodAdrSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="00-000">
							</div>
							<div class ="divOdstep"> </div>
							<div style = "display: grid; width: 70%;">
								<label class = "labDodUmowa">Miejscowość: </label>
								<input id="MiejscowoscAdrSzamba" name="MiejscowoscAdrSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Miejscowość">
							</div>
						</div>
						
						<div class = "divAdrSzamba">
							<div style = "display: grid; width: 68%;">
								<label class = "labDodUmowa">Ulica:</label>
								<input id="UlicaAdrSzamba" name="UlicaAdrSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Ulica">
							</div>
							<div class ="divOdstep"> </div>
							<div <div style = "display: grid; width: 30%;">
								<label class = "labDodUmowa">Numer: </label>
								<input type="text" id="NrAdrSzamba" name="NrAdrSzamba" class = "textDodUmoweSzamba" placeholder="Wprowadz numer">
							</div>
						</div>
					</div>
						
					<script>
						const checkboxInnyAdrSzamb = document.getElementById("chkInnyAdrSzamb");
						const divInnyAdrSzamba = document.getElementById("divInnyAdrSzamba");

							checkboxInnyAdrSzamb.addEventListener("change", function() {
							divInnyAdrSzamba.style.display = checkboxInnyAdrSzamb.checked ? "block" : "none";
						});
					</script>
				</div>	
			
				<div class = "divP2InForm">
					<div class = "padingCzasUmowy" name="padingCzasUmowy" id="padingCzasUmowy" ></div> 
						<div class = "divFirmaUmowa">
							<div class = "divAdrSzamba" id = "divAdrSzamba">
								<div class = "czasUmowy" name="czasUmowy" id="czasUmowy">
									<div>
										<label class = "labDodUmowa">Początek umowy:</label>
										<input type="date" id="start" name="trip-start" value="2023-01-01" class = "textDodUmoweSzamba" />
									</div>
									<div style = "width:46%"> </div>
									<div>
										<label class = "labDodUmowa">Koniec umowy:</label>
										<input type="date" id="end" name="trip-end" value="2025-01-01"  class = "textDodUmoweSzamba" />
									</div>
								</div>
							</div>
							<div style = "display: flex; width: 90%;">
								<div style = "width: 68%">
									<label class = "labDodUmowa">Firma </label>
									<input id="NazwaNipFirma" name="NazwaNipFirma" class="NazwaNipFirma textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Nazwa firmy lub NIP" >
								</div>
								<div style = "width: 2%;"> </div>
								<div style = "width: 30%;">
									<label class = "labDodUmowa" style = "display:flex;">&nbsp&nbsp</label>
									<input type="submit" value="Dodaj firmę" id = "bt_DodajFirme" class ="bt_DodajFirme butZapisz" style = "width:98%;" >	
								</div>
							</div>

							<div class = "divInnyAdrFirma" id = "divInnyAdrFirma">
								<div class = "divAdrFirma ">
									<div style = "display: grid; width: 68%;">
										<label class = "labDodUmowa">Ulica:</label>
										<input id="UlicaFirma" name="UlicaFirma" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Ulica" readonly>
									</div>
									<div class ="divOdstep"> </div>
									<div <div style = "display: grid; width: 30%;">
										<label class = "labDodUmowa">Numer: </label>
										<input type="text" id="NrFirma" name="NrFirma" class = "textDodUmoweSzamba" placeholder="Numer" readonly>
									</div>
								</div>
								
								<div class = "divAdrFirma ">
									<div style = "display: grid; width: 28%;">
										<label class = "labDodUmowa">Kod:</label>
										<input id="KodFirma" name="KodFirma" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="00-000" readonly>
									</div>
									<div class ="divOdstep"> </div>
									<div <div style = "display: grid; width: 70%;">
										<label class = "labDodUmowa">Miejscowość: </label>
										<input id="MiejscowoscFirma" name="MiejscowoscFirma" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Miejscowość" readonly>
									</div>
								</div>
							</div>
					</div>
				</div>
			</form>
			<div style = "width:100%; display:flex"> 
				<div style = "width:82%;">
				</div>
				<div style = "width:18%; text-align: left;"> 
					<input type="submit" value="Zapisz" id = "bt_Zapisz" class ="bt_Zapisz butZapisz" style = "width:70%;">
				</div>
			</div>
		</div>
	

<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"/>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link rel="stylesheet" href="/resources/demos/style.css" />

<script src="pop_up.js"></script>
<script src="dodaj_umowe.js"></script>
</body>