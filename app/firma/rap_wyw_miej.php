			<?php
				require '../menu_str.php';
			?>

			<div style = "height:20px"></div>

			<div style = "margin : 20px 10px 10px 10px; width:99%;"> 
				
				<nav style = "border: 1px solid rgba(0,0,0,0.2); height:40px; align-items: center; justify-content: center;">
					<div class="select-style-rwm">
						<?php
							try {
								$pdo = new PDO("pgsql:host=__host__;dbname=__dbname__", "__user__", "__passwd__");
							} catch (PDOException $e) {
								echo "Błąd połączenia: " . $e->getMessage();
							}

							$sql = "select distinct miejscowosc from adresy a where a.teryt like '121204' order by miejscowosc;";
							$stmt = $pdo->query($sql);

							echo '<select name="selMiejscowosc" id="selMiejscowosc">';
							echo '<option value="initial">Wybierz miejscowość</option>';

							while ($row = $stmt->fetch())
							{
								echo '<option value="'.$row['miejscowosc'].'">'.$row['miejscowosc'].'</option>';
							}
							echo '<option value="Wszystkie">Wszystkie</option>';
							echo '</select>';
						?>
					</div>
					
					<div style = "width:15px"></div>

					<div class="select-style-rwm">
						<?php
						
							$sql = "select id, nazwa from firmy;";
							$stmt = $pdo->query($sql);

							echo '<select name="selFirma" id="selFirma">';
							echo '<option value="initial">Wybierz firmę</option>';

							while ($row = $stmt->fetch())
							{
								echo '<option value="'.$row['id'].'">'.$row['nazwa'].'</option>';
							}
							echo '<option value="-1">Wszystkie</option>';
							echo '</select>';
						?>
					</div>

					<div style = "width:15px"></div>

					<div class="dataOdDo">
						<label for="firma">Od:</label>
						<input type="date" id="start" name="trip-start" value="2023-01-01" min="2020-01-01" max="2099-01-01" />
						
						<label for="firma">Do:</label>
						<input type="date" id="koniec" name="trip-koniec" value="2023-12-31" min="2020-01-01" max="2099-01-01" />
					</div> 

					<div style = "width:15px"></div>

					<div class="select-style-rwm">
						<?php
						
							$sql = "select distinct rodzaj_nieczystosci from szamba order by rodzaj_nieczystosci;";
							$stmt = $pdo->query($sql);

							echo '<select name="selRodzNiecz" id="selRodzNiecz">';
							echo '<option value="initial">Wybierz rodzaj niecz.</option>';

							while ($row = $stmt->fetch())
							{
								echo '<option value="'.$row['rodzaj_nieczystosci'].'">'.$row['rodzaj_nieczystosci'].'</option>';
							}
							echo '<option value="Wszystkie">Wszystkie</option>';
							echo '</select>';
						?>
					</div>

					<div style = "width:15px"></div>
					
					<div class="select-style-rwm">
						<select name="selRelGmin" id="selRelGmin">
							<option value="initial">Realizacja Gmina</option>
							<option value="TAK">TAK</option>
							<option value="NIE">NIE</option>
							<option value="Wszystkie">Wszystkie</option>
						</select>
					</div>

					<div style = "width:15px"></div>

					<button class="raport">Generuj tabelę</button>
					<span id="select-stats"></span>
				</nav>
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
		<script type="text/javascript" src="rap_wyw_miej.js"></script> 
		<script type="text/javascript" src="https://vectorjs.org/interactive.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<script src="pop_up.js"></script>
	</body>
</html>