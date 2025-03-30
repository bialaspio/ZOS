<?php
// Łączymy się z bazą __user__QL
/*try {
  $db = new PDO("pgsql:host=__host__;dbname=__dbname__", "__user__", "__passwd__");
} catch (PDOException $e) {
  echo "Błąd połączenia: " . $e->getMessage();
}
*/
// Pobieramy wartość z formularza

$partNazwisko = $_POST["NazwWlSzamba"];

$host = '__host__';
$dbname = '__dbname__';
$user_db = '__user__';
$password_db = '__passwd__';
// Połącz z bazą danych
$dsn = "pgsql:host=$host;dbname=$dbname";

$baza_danych = new PDO($dsn, $user_db, $password_db);

// Pobieramy wartość z formularza

// Wykonujemy zapytanie SQL
$zapytanie = $baza_danych->prepare("select distinct nazwisko from osoba o WHERE nazwisko like :naz");
$partNazwisko = $partNazwisko.'%';
$zapytanie->bindParam(':naz', $partNazwisko);
$zapytanie->execute();
$przewidywane_ulice = $zapytanie->fetchAll(PDO::FETCH_ASSOC);
// Sprawdzamy, czy wartość znajduje się w tabeli

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

