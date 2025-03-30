<?php
// Łączymy się z bazą __user__QL
$nip = $_POST["nip"];

$host = '__host__';
$dbname = '__dbname__';
$user_db = '__user__';
$password_db = '__passwd__';
// Połącz z bazą danych
$dsn = "pgsql:host=$host;dbname=$dbname";

$dbh = new PDO($dsn, $user_db, $password_db);

// Sprawdź, czy użytkownik istnieje


$stmt = $dbh->prepare("select a.ulica, a.numer, a.kod_pocztowy, a.miejscowosc  from adresy a where ogc_fid in ( select id_adres from firmy where nip = :nip)");

$stmt->bindParam(':nip', $nip);
$stmt->execute();
$dane_z_zap = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Sprawdzamy, czy wartość znajduje się w tabeli

if ($dane_z_zap){
	$json = json_encode($dane_z_zap);
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