	<?php
		require '../menu_str.php';
	?>
	
		<div style = "height:20px"></div>
	

<div style = "margin : auto; width:50%; align-items: center; justify-content: center; "> 
	<nav style = " height:100px; dispaly:block">
	
	<div style = "height:20px"></div>

		<div style = "display:inline-block; width: 60%; margin: auto;" >
           
            <div class = "divPoleNaGorDol" >
					<h4  style = "font-size: 22px;	color:#154c79;  text-align: center; margin-bottom: 10px;">Adres nieruchomości.</h4>
            </div>
            
            <div class = "divPoleNaGorDol" >
				<div class = "divAdrSzambo">
				
					<div style = "display: grid; width: 15%;  margin-left: 5px ">
						<label class = "labDodUmowa">Kod:</label>
						<input id="KodAdrSzamba" name="KodAdrSzamba" class="KodAdrSzamba textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="00-000" >
					</div>
					<div class ="divOdstep"> </div>
					<div style = "display: grid; width: 30%;">
						<label class = "labDodUmowa">Miejscowość: </label>
						<input id="MiejscowoscAdrSzamba" name="MiejscowoscAdrSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Miejscowość" >
					</div>

					<div style = "display: grid; width: 30%;">
						<label class = "labDodUmowa">Ulica:</label>
						<input id="UlicaAdrSzamba" name="UlicaAdrSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Nazwa ulicy." >
					</div>
					<div class ="divOdstep"> </div>
					<div style = "display: grid; width: 15%;">
						<label class = "labDodUmowa">Numer: </label>
						<input type="text" id="NrAdrSzamba" name="NrAdrSzamba" class = "textDodUmoweSzamba" placeholder="Wprowadz numer" 	>
					</div>
					<div class ="divOdstep"> </div>
				
				</div>
            </div>

        </div>
		
	</nav>
</div>

<div style = "	border: 2px solid rgba(0,100,150,0.2); margin : auto; width:60%; align-items: center; justify-content: center; "> 
	<h4  style = "font-size: 22px;	color:#154c79;  text-align: center; margin-bottom: 10px;margin-top:10px;">Dane szamba</h4>
	<div id="dane_szamba"> </div>

</div>

<div style = "margin : auto; width:50%; align-items: center; justify-content: center; "> 
	<h4  style = "font-size: 22px;	color:#154c79;  text-align: center; margin-bottom: 10px;margin-top:10px;">Lista osób zamieszujących pod adresem</h4>
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


<script type="text/javascript" src="https://unpkg.com/tabulator-tables/dist/js/tabulator.min.js"></script>
<script type="text/javascript" src="https://vectorjs.org/interactive.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.20/jspdf.plugin.autotable.min.js"></script>
<script type="text/javascript" src="https://oss.sheetjs.com/sheetjs/xlsx.full.min.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"/>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript" src="lista_osob_adres.js"></script> 
 <!-- <script type="text/javascript" src="tabele_back_20231031.js"></script> -->
</body>
</html>