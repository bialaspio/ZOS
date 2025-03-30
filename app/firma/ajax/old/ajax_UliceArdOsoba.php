<?php
// Łączymy się z bazą __user__QL
/*try {
  $db = new PDO("pgsql:host=__host__;dbname=__dbname__", "__user__", "__passwd__");
} catch (PDOException $e) {
  echo "Błąd połączenia: " . $e->getMessage();
}
*/
// Pobieramy wartość z formularza

$ulica_adresu_w_szamba = $_POST["UlicaAdrWlSzamba"];
$miejscowosc_w_szamba = $_POST["MiejscowoscAdrWlSzamba"];
$kod_w_szamba = $_POST["KodAdrWlSzamba"];


$host = '__host__';
$dbname = '__dbname__';
$user_db = '__user__';
$password_db = '__passwd__';
// Połącz z bazą danych
$dsn = "pgsql:host=$host;dbname=$dbname";

$baza_danych = new PDO($dsn, $user_db, $password_db);

// Wykonujemy zapytanie SQL
if ($kod_w_szamba & $miejscowosc_w_szamba){
	$zapytanie = $baza_danych->prepare("SELECT distinct ulica FROM adresy WHERE ulica like :part and kod_pocztowy=:kod  and miejscowosc=:miej order by ulica");
	$ulica_adresu_w_szamba = $ulica_adresu_w_szamba.'%';
	$zapytanie->bindParam(':part', $ulica_adresu_w_szamba);
	$zapytanie->bindParam(':miej', $miejscowosc_w_szamba);
	$zapytanie->bindParam(':kod', $kod_w_szamba);
	
}
else{
	$zapytanie = $baza_danych->prepare("SELECT distinct ulica FROM adresy WHERE ulica like :part order by ulica");
	$ulica_adresu_w_szamba = $ulica_adresu_w_szamba.'%';
	$zapytanie->bindParam(':part', $ulica_adresu_w_szamba);
}

$zapytanie->execute();
$przewidywane_ulice = $zapytanie->fetchAll(PDO::FETCH_ASSOC);

if ($przewidywane_ulice){
	$json = json_encode($przewidywane_ulice);
	echo $json;
}
else{
	// Tworzymy tablicę z danymi
	$data = array(
	);
	// Kodowanie danych do formatu JSON
	$jsonData = json_encode($data);

	// Wyświetlanie wyniku
	echo $jsonData;
}
?>

