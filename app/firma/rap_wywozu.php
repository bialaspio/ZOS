			<?php
				require '../menu_str.php';
			?>

			<div style = "height:20px"></div>
			

			<?php
				//print_r($_COOKIE['id_szamba']);
				$appName = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				$conn_string = "host=__host__ port=5432 dbname=__dbname__ user=__user__ password=__passwd__";
				
				//simple check
				$conn = pg_connect($conn_string);
				
				$query = "select id, nazwa from firmy;";
				
				$result = pg_query($conn,$query);
				
				$id_nazwa_firm = [];
				$idnazwa_firm =[];
				$licz=0;
				while ($row = pg_fetch_row($result)) {
					$id_nazwa_firm[0] = $row[0];
					$id_nazwa_firm[1]= $row[1];
					$idnazwa_firm[$licz] =$id_nazwa_firm;
					$licz ++;
				}
				pg_free_result($result);
				pg_close ();
			?>
		
			<div style = "margin : 20px 10px 10px 10px; width:99%;"> 
				
				<nav style = "border: 1px solid rgba(0,0,0,0.2); height:40px; align-items: center; justify-content: center;">
					<div class="select">
						<label for="firma">Firma:</label>
						<select name="firma" id="wybierz-firme" >
						<option value="initial">Wybierz firmę</option>
						<option value="*">'Wszystie'</option>
						<?php
							foreach ($idnazwa_firm as $idnazwa_firma){
								echo '<option value="'.$idnazwa_firma[0].'">'.$idnazwa_firma[1].'</option>";';
							}
						?>
						</select>
					</div> 
				
					<div class="dataOdDo">
						<label for="firma">Od:</label>
						<input type="date" id="start" name="trip-start" value="2023-01-01" min="2020-01-01" max="2099-01-01" />
						<label for="firma">Do:</label>
						<input type="date" id="koniec" name="trip-koniec" value="2023-12-31" min="2020-01-01" max="2099-01-01" />
					</div> 

					<div class="select">
					  <label for="UmnowySzamba">Umowy/wywozy</label>
					  <select name="UmnowySzamba" id="wybierzUmnowySzamba" >
						<option value="initial">Umowy/wywozy</option>
							<option value="umowy">Umowy</option>
							<option value="szamba">Wywozy</option>
					  </select>
					</div> 

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
		<script type="text/javascript" src="rap_wywozu.js"></script> 
		<script type="text/javascript" src="https://vectorjs.org/interactive.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<script src="pop_up.js"></script>
	</body>
</html>