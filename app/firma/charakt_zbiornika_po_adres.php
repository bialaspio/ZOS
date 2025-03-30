<?php
require '../menu_str.php';
?>

<div style="height:20px"></div>

<div style="margin: auto; width:50%; align-items: center; justify-content: center;"> 
    <nav style="height:100px; display:block">
        <div style="height:20px"></div>
        <div style="display:inline-block; width: 60%; margin: auto;">
            <div class="divPoleNaGorDol">
                <h4 style="font-size: 22px; color:#154c79; text-align: center; margin-bottom: 10px;">Adres nieruchomości</h4>
            </div>
            <div class="divPoleNaGorDol">
                <div class="divAdrSzambo">
                    <div style="display: grid; width: 15%; margin-left: 5px;">
                        <label class="labDodUmowa">Kod:</label>
                        <input id="KodAdrSzamba" name="KodAdrSzamba" class="KodAdrSzamba textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="00-000">
                    </div>
                    <div class="divOdstep"></div>
                    <div style="display: grid; width: 30%;">
                        <label class="labDodUmowa">Miejscowość:</label>
                        <input id="MiejscowoscAdrSzamba" name="MiejscowoscAdrSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Miejscowość">
                    </div>
                    <div style="display: grid; width: 30%;">
                        <label class="labDodUmowa">Ulica:</label>
                        <input id="UlicaAdrSzamba" name="UlicaAdrSzamba" class="textDodUmoweSzamba form-control basicAutoComplete" type="text" autocomplete="off" placeholder="Nazwa ulicy">
                    </div>
                    <div class="divOdstep"></div>
                    <div style="display: grid; width: 15%;">
                        <label class="labDodUmowa">Numer:</label>
                        <input type="text" id="NrAdrSzamba" name="NrAdrSzamba" class="textDodUmoweSzamba" placeholder="Wprowadź numer">
                    </div>
                    <div class="divOdstep"></div>
                </div>
            </div>
        </div>
    </nav>
</div>

<div style="height:20px"></div>

<div style="margin: 20px 70px 10px 70px;"> 
    <nav id="navimnazadr" style="border: 1px solid rgba(0,0,0,0.2); height:auto; align-items: center;">
        <h4 id="imnazadr" style="font-size: 18px; color:#154c79; text-align: center; justify-content: center; margin-top: 10px; margin-bottom: 10px;">Dane opisowe szamba</h4>
    </nav>
</div>

<div id="tab1">
    <div>
        <div style="height:30px"></div>
        <nav style="border: 1px solid rgba(0,0,0,0.2); height:30px; width:600px; align-items: center; margin-left:auto; margin-right:auto">
            <?php
            $formats = ['csv', 'json', 'xlsx', 'pdf', 'html'];
            foreach ($formats as $format) {
                echo "<div style='background-color: dodgerblue; height:100%;'>
                        <button type='button' class='button' id='download-$format' style='width:118px'>Download " . strtoupper($format) . "</button>
                      </div>";
            }
            ?>
        </nav>
    </div>
    <div id="hist_wyw-table" style="margin-top:10px"></div>
    <div style="height:20px"></div>
    <div style="width:99%; justify-content: space-around;"> 
        <nav style="margin:auto; border: 1px solid rgba(0,0,0,0.2); height:40px; width:80%; align-items: center; display: flex;">
            <label id="data_ost_wyw" style="font-size: 22px; color:#154c79;">Data ostatniego wywozu:</label>
            <label id="ilosc_dni" style="font-size: 22px;">Ilość dni od wywozu:</label>
            <label id="srednia_oproznien" style="font-size: 22px; color:#154c79;">Średnia opróżnień co:</label>
        </nav>
    </div>
</div>

<div id="tab2" style="display:none">
    <div>
        <div style="height:15px"></div>
        <nav style="border: 1px solid rgba(0,0,0,0.2); height:30px; width:600px; align-items: center; margin-left:auto; margin-right:auto">
            <?php
            foreach ($formats as $format) {
                echo "<div style='background-color: dodgerblue; height:100%;'>
                        <button type='button' class='button' id='download-$format-2' style='width:118px'>Download " . strtoupper($format) . "</button>
                      </div>";
            }
            ?>
        </nav>
    </div>
    <div id="hist_wyw-table2" style="margin-top:10px"></div>
    <div style="height:20px"></div>
    <div style="width:99%; justify-content: space-around;"> 
        <nav style="margin:auto; border: 1px solid rgba(0,0,0,0.2); height:40px; width:80%; align-items: center; display: flex;">
            <label id="data_ost_wyw2" style="font-size: 22px; color:#154c79;">Data ostatniego wywozu:</label>
            <label id="ilosc_dni2" style="font-size: 22px;">Ilość dni od wywozu:</label>
            <label id="srednia_oproznien2" style="font-size: 22px; color:#154c79;">Średnia opróżnień co:</label>
        </nav>
    </div>
</div>

<div style="height:30px"></div>

<div style="margin: 20px 70px 10px 70px;"> 
    <nav style="border: 1px solid rgba(0,0,0,0.2); height:40px; align-items: center; justify-content: center;">
        <h4 style="font-size: 22px; color:#154c79; text-align: center;">Lista zamieszkałych osób</h4>
    </nav>
</div>

<div>
    <nav style="border: 1px solid rgba(0,0,0,0.2); height:30px; width:600px; align-items: center; margin-left:auto; margin-right:auto">
        <?php
        foreach ($formats as $format) {
            echo "<div style='background-color: dodgerblue; height:100%;'>
                    <button type='button' class='button' id='download-$format-list-osob' style='width:118px'>Download " . strtoupper($format) . "</button>
                  </div>";
        }
        ?>
    </nav>
</div>

<div id="lista_osob-table" style="margin-top:10px"></div>

<script type="text/javascript" src="https://unpkg.com/tabulator-tables/dist/js/tabulator.min.js"></script>
<script type="text/javascript" src="https://vectorjs.org/interactive.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.20/jspdf.plugin.autotable.min.js"></script>
<script type="text/javascript" src="https://oss.sheetjs.com/sheetjs/xlsx.full.min.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"/>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript" src="charakt_zbiornika_po_adres.js"></script>
</body>
</html>