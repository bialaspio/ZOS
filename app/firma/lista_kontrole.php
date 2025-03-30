				<?php
					require '../menu_str.php';
				?>

				<div style = "height:20px"></div>
				<div style = "margin : 20px 10px 10px 10px; width:99%;"> 
				<nav style="border: 1px solid rgba(0,0,0,0.2); height:55px; align-items: center; justify-content: center;">
					<div class="checkbox-style-rwm">
						<input type="checkbox" id="uwzglednijRodzKontr" name="uwzglednijRodzKontr" >
					</div>

					<div style="width:5px"></div>				

					<div class="select-style-rwm">
						<?php
							try {
								$pdo = new PDO("pgsql:host=__host__;dbname=__dbname__", "__user__", "__passwd__");
							} catch (PDOException $e) {
								echo "Błąd połączenia: " . $e->getMessage();
							}

							$sql = "SELECT id, nazwa_kontroli FROM public.rodzaj_kontroli;";
							$stmt = $pdo->query($sql);

							echo '<select name="selRodzKontr" id="selRodzKontr">';
							echo '<option value="initial">Wybierz typ kontroli</option>';

							while ($row = $stmt->fetch()) {
								echo '<option value="' . $row['id'] . '">' . $row['nazwa_kontroli'] . '</option>';
							}

							echo '<option value="Wszystkie">Wszystkie</option>';
							echo '</select>';
						?>
					</div>

					<div style="width:15px"></div>

					<div class="checkbox-style-rwm">
						<input type="checkbox" id="uwzglednijImieNazwWlSzamba" name="uwzglednijImieNazwWlSzamba" >
					</div>

					<div style="width:5px"></div>

					<div class="div_wlasciciel">
						<input id="ImieNazwWlSzamba" name="ImieNazwWlSzamba" class="ImieNazwWlSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Imię i nazwisko">
					</div>

					<div style="width:15px"></div>

					<div class="checkbox-style-rwm">
						<input type="checkbox" id="uwzglednijAdresKonObie" name="uwzglednijAdresKonObie" >
					</div>

					<div style="width:5px"></div>

					<div class="div_AdresKonObie">
						<input id="AdresKonObie" name="AdresKonObie" class="AdresKonObie form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Adres obiektu">
					</div>

					<div style="width:15px"></div>

					<div class="checkbox-style-rwm">
						<input type="checkbox" id="uwzgledniUlicaMiej" name="uwzgledniUlicaMiej">
					</div>

					<div style="width:5px"></div>

					<div class="dataOdDo" style ="display: grid;">
						<div display ="style: flex">
							<input id="MiejsObie" name="MiejsObie" class="MiejsObie form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Miejscowość">
						</div>
						<div style = "height: 5px"></div>
						<div display ="style: flex">
							<input id="UlicObie" name="UlicObie" class="UlicObie form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Ulica">
						</div>
					</div>

					<div style="width:15px"></div>


					<div class="checkbox-style-rwm">
						<input type="checkbox" id="uwzglednijDataOdDo" name="uwzglednijDataOdDo" >
					</div>

					<div style="width:5px"></div>

					<div class="dataOdDo" style ="display: grid;">
						<div display ="style: flex">
							<label id = "txtDataOd">Od: </label>
							<input type="date" id="dataOd" name="trip-start" value="2023-01-01" min="2020-01-01" max="2099-01-01" />
						</div>
						<div style = "height: 5px"></div>
						<div display ="style: flex">
							<label id="txtDataDo">Do: </label>
							<input type="date" id="dataDo" name="trip-koniec" value="2023-12-31" min="2020-01-01" max="2099-01-01" />
						</div>
					</div>

					<div style="width:15px"></div>

					<div class="checkbox-style-rwm">
						<input type="checkbox" id="uwzgledniOsobaKontr" name="uwzgledniOsobaKontr" >
					</div>

					<div style="width:5px"></div>

					<div class="div_OsobaKontr">
						<input id="OsobaKontr" name="OsobaKontr" class="OsobaKontr form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Osoby kontrolujace">
					</div>

					<div style="width:15px"></div>

					<button id = "gen_rap_kontrole" class="gen_rap_kontrole raport">Generuj tabelę</button>

			</nav>


				<div id = "divAdresyWlaSzam" style = "border: 1px solid rgba(0,0,0,0.2); border-top: none; height:auto; align-items: center; justify-content: center; display:block">
					
					<div style="height: 7px;  "></div>
							
					<div class = "divAdrWlSzamba" id = "divAdrWlSzamba">
						
						<div style = "display: flex; width: 11%;">
							<label class = "labDivDaneWl" style = "color: #154c79; font-size: 16px; font-weight: bold;">Dane właściciela:</label>
						</div>
						
						<div style = "display: flex; width: 10%;">
							<label class = "labDivDaneWl">Imię:</label>
							<input id="ImieAdrWlSzamba" name="ImieAdrWlSzamba" class="ImieAdrWlSzamba textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Imie" readonly>
						</div>
						
						<div class ="divOdstep"> </div>
						
						<div style = "display: flex; width: 20%;">
							<label class = "labDivDaneWl">Nazwisko: </label>
							<input id="NazwiskoAdrWlSzamba" name="NazwiskoAdrWlSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Nazwisko" readonly>
						</div>
						
						<div class ="divOdstep"> </div>

						<div style = "display: flex; width: 10%;">
							<label class = "labDivDaneWl">Kod:</label>
							<input id="KodAdrWlSzamba" name="KodAdrWlSzamba" class="KodAdrWlSzamba textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Kod" readonly>
						</div>
						
						<div class ="divOdstep"> </div>
						
						<div style = "display: flex; width: 20%;">
							<label class = "labDivDaneWl">Miejscowość: </label>
							<input id="MiejscowoscAdrWlSzamba" name="MiejscowoscAdrWlSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Miejscowość" readonly>
						</div>
						
						<div class ="divOdstep"> </div>

						<div style = "display: flex; width: 20%;">
							<label class = "labDivDaneWl">Ulica:</label>
							<input id="UlicaAdrWlSzamba" name="UlicaAdrWlSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Ulica." readonly>
						</div>

						<div class ="divOdstep"> </div>
						
						<div style = "display: flex; width: 10%;">
							<label class = "labDivDaneWl">Numer: </label>
							<input type="text" id="NrAdrWlSzamba" name="NrAdrWlSzamba" class = "textDodUmoweSzamba" placeholder="Numer" readonly>
						</div>

					</div>
					
					<div style="height: 10px;  "></div>
							
					<div class = "divAdrObiektu" id = "divAdrObiektu">
						
						<div style = "display: flex; width: 11%;">
							<label class = "labDivDaneObiektu" style = "color: #154c79; font-size: 16px; font-weight: bold;">Adres obiektu:</label>
						</div>
						
						<div class ="divOdstep"> </div>

						<div style = "display: flex; width: 10%;">
							<label class = "labDivDaneObiektu">Kod:</label>
							<input id="KodAdrObiektu" name="KodAdrObiektu" class="KodAdrObiektu textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Kod" readonly>
						</div>
						
						<div class ="divOdstep"> </div>
						
						<div style = "display: flex; width: 20%;">
							<label class = "labDivDaneObiektu">Miejscowość: </label>
							<input id="MiejscowoscAdrObiektu" name="MiejscowoscAdrObiektu" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Miejscowość" readonly>
						</div>
						
						<div class ="divOdstep"> </div>

						<div style = "display: flex; width: 20%;">
							<label class = "labDivDaneObiektu">Ulica:</label>
							<input id="UlicaAdrObiektu" name="UlicaAdrObiektu" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Ulica." readonly>
						</div>

						<div class ="divOdstep"> </div>
						
						<div style = "display: flex; width: 10%;">
							<label class = "labDivDaneObiektu">Numer: </label>
							<input type="text" id="NrAdrObiektu" name="NrAdrObiektu" class = "textDodUmoweSzamba" placeholder="Numer" readonly>
						</div>

					</div>
					<div style="height: 7px;  "></div>
				</div>
			</div>
		</div>  

		<div>
			<nav style = "border: 1px solid rgba(0,0,0,0.2); height:30px; width:600px;  align-items: center;margin-left:auto; margin-right:auto">
				<div style = "background-color: dodgerblue;height:100%">
					<button type="button" class="button" id="download-csv" style="width:118px">Download CSV</button>
				</div>
				<div style = "background-color: dodgerblue;height:100%">
					<button type="button" class="button" id="download-json" style="width:118px">Download JSON</button>
				</div>
				<div style = "background-color: dodgerblue;height:100%">
					<button type="button" class="button"id="download-xlsx" style="width:118px">Download XLSX</button>
				</div>
				<div style = "background-color: dodgerblue;height:100%">
					<button type="button" class="button" id="download-pdf" style="width:118px">Download PDF</button>
				</div>
				<div style = "background-color: dodgerblue;height:100%">
					<button type="button" class="button" id="download-html" style="width:118px">Download HTML</button>
				</div>
			</nav>
		</div>

		<div id="example-table" style ="margin-top:10px"></div>
		<div id="myJSON" ></div>

		<script type="text/javascript" src="https://unpkg.com/tabulator-tables/dist/js/tabulator.min.js"></script>
		
		<script type="text/javascript" src="https://vectorjs.org/interactive.js"></script>
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"/>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script> 
		
		<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
		<link rel="stylesheet" href="/resources/demos/style.css" />
		<script type="text/javascript" src="lista_kontrole.js"></script> 
		<script src="pop_up.js"></script>
	</body>
</html>