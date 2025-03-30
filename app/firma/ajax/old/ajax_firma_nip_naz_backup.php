<?php
// Łączymy się z bazą __user__QL
$nazwa_nip = $_POST["nazwa_nip"];


$host = '__host__';
$dbname = '__dbname__';
$user_db = '__user__';
$password_db = '__passwd__';
// Połącz z bazą danych
$dsn = "pgsql:host=$host;dbname=$dbname";

$dbh = new PDO($dsn, $user_db, $password_db);

// Sprawdź, czy użytkownik istnieje


$stmt = $dbh->prepare("SELECT nazwa||'  NIP:'||nip as nipnazwa FROM FIRMY_ADRESY FA WHERE NIP::VARCHAR LIKE :nazwa_nip or nazwa LIKE :nazwa_nip");
$nazwa_nip=$nazwa_nip.'%';
$stmt->bindParam(':nazwa_nip', $nazwa_nip);
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