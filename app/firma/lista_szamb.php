	<?php
		require '../menu_str.php';
	?>
	
		<div style = "height:20px"></div>
	
	</body>
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
<script type="text/javascript" src="lista_szamb.js"></script> 
 <!-- <script type="text/javascript" src="tabele_back_20231031.js"></script> -->
</body>
</html>