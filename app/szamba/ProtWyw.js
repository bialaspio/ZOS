// Pobierz dane z cookie
var cookie = document.cookie;
console.log({cookie});
const parts = cookie.split(`;`);

const id_szamba = parts[0];
console.log({id_szamba});

