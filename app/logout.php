<?php
// Rozpocznij sesję
// Jeśli używasz sesion_name("cośtam"), nie zapomnij o tym teraz!
session_start();
// Usuń wszystkie zmienne sesyjne
$_SESSION = array();

// Jeśli pożądane jest zabicie sesji, usuń także ciasteczko sesyjne.
// Uwaga: to usunie sesję, nie tylko dane sesji
if (isset($_COOKIE[session_name()])) { 
   setcookie(session_name(), '', time()-42000, '/'); 
}
session_destroy();

//header('Location: http://192.168.157.177/geoserwer');
//header('Location: http://192.168.0.94/geoserwer');
header('Location: /PB');
?>