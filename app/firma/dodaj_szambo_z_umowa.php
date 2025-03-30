	<?php
		require '../menu_str.php';
	?>
	
	<div class = "divDodSza">
	<form id="formDodSza" method="post"  style = "display: block;">
		<div class = "divDodSzaOsoba">
			<label class = "labNaglowki"> Właścicel szamba </label>
			<div class = "divDodSzaImNazOsoba">
				<label class = "labDodUmowa">Imie.</label>
				<input id="Imie" name="Imie" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Imie" >
			</div>
			<div class = "divDodSzaImNazOsoba" >
				<label class = "labDodUmowa">Nazwisko.</label>
				<input id="Nazwisko" name="Nazwisko" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Nazwisko" >
			</div>

			<div class = "divDodSzaAdrOsoba">
				<div style = "display: grid; width: 28%;">
					<label class = "labDodUmowa">Kod:</label>
					<input id="KodAdrOsoba" name="KodAdrOsoba" class="KodAdrOsoba textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="00-000" >
				</div>
				<div class ="divOdstep"> </div>
				<div style = "display: grid; width: 70%;">
					<label class = "labDodUmowa">Miejscowość: </label>
					<input id="MiejscowoscAdrOsoba" name="MiejscowoscAdrOsoba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Miejscowość" >
				</div>
			</div>

			<div class = "divDodSzaAdrOsoba">
				<div style = "display: grid; width: 68%;">
					<label class = "labDodUmowa">Ulica:</label>
					<input id="UlicaAdrOsoba" name="UlicaAdrOsoba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Nazwa ulicy." >
					
				</div>
				<div class ="divOdstep"> </div>
				<div style = "display: grid; width: 30%;">
					<label class = "labDodUmowa">Numer: </label>
					<input type="text" id="NrAdrOsoba" name="NrAdrOsoba" class = "textDodUmoweSzamba" placeholder="Wprowadz numer" >
				</div>
			</div>

		
			<label class = "labNaglowki"> Dane szamba </label>
			<div class = "divDodSzaAdrOsoba">
				<div style = "display: grid; width: 28%;">
					<label class = "labDodUmowa">Kod:</label>
					<input id="KodAdrSzamba" name="KodAdrSzamba" class="KodAdrSzamba textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="00-000" >
				</div>
				<div class ="divOdstep"> </div>
				<div style = "display: grid; width: 70%;">
					<label class = "labDodUmowa">Miejscowość: </label>
					<input id="MiejscowoscAdrSzamba" name="MiejscowoscAdrSzamba" class="MiejscowoscAdrSzamba textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Miejscowość" >
				</div>
			</div>

			<div class = "divDodSzaAdrOsoba">
				<div style = "display: grid; width: 68%;">
					<label class = "labDodUmowa">Ulica:</label>
					<input id="UlicaAdrSzamba" name="UlicaAdrSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Nazwa ulicy." >
					
				</div>
				<div class ="divOdstep"> </div>
				<div style = "display: grid; width: 30%;">
					<label class = "labDodUmowa">Numer: </label>
					<input type="text" id="NrAdrSzamba" name="NrAdrSzamba" class = "textDodUmoweSzamba" placeholder="Wprowadz numer" >
				</div>
			</div>

			
			<div class = "divDodSzaAdrOsoba">
				<div style = "display: grid; width: 50%;" >
					<label class = "labDodUmowa">Pojemność zbiornika w m3:</label>
					<input id="Pojemnosc" name="Pojemnosc" class="textDodUmoweSzamba form-control basicAutoComplete" style = "margin-bottom: 5px;"; type="text" autocomplete="off" placeholder="m3">
				</div>
				<div  style = "display: grid; width: 50%;">
					<label class = "labDodUmowa" style="margin-bottom: 0px;">Rodzaj nieczystosci:</label>
					<div class="select-style">
						<select name="RodzajNieczystosci" id="RodzajNieczystosci">
							<option value="initial">Wybierz rodzaj</option>                          
							<option value="bytowe">przemysłowe </option>
							<option value="bytowe">bytowe </option>
						</select> 
					</div>	
				</div>
			</div>
		</div>
	</form>
	
	<div id="mapaSzamba" style="width: 100%; height: 400px;"></div>

	<div style = "width:100%; height: 60px; display:flex"> 
		<div style = "width:82%;"></div>
			<div style = "width:18%; text-align: left;"> 
				<input type="submit" value="Zapisz" id = "bt_ZapiszSzambo" class ="bt_ZapiszSzambo butZapisz" style = "width:70%; margin-top:15px;">
			</div>
		</div>
	</div>
		
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"/>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link rel="stylesheet" href="/resources/demos/style.css" />

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/proj4@latest/dist/proj4.min.js"></script>

<script src="pop_up.js"></script>
<script src="dodaj_szambo_z_umowa.js"></script>
</body>