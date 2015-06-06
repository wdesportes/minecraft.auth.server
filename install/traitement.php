<?PHP
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Installation automatisée : 2nde étape</title>
<style type="text/css">
body {
font-family:Tahoma, Arial, Serif;
font-size:14px;
}
.note {
font-size:1.1em;
font-style:italic;
}
.ok {
color:green;
font-weight:bold;
}
.echec {
color:red;
font-weight:bold;
}
</style>
</head>
<body>
<p>
<?php
if(isset($_POST['etape']) AND $_POST['etape'] == 1) { // si nous venons du formulaire alors

// on crée des constantes dont on se servira plus tard
define('RETOUR', '<br /><br /><input type="button" value="Retour" onclick="history.back()">');
define('OK', '<span class="ok">OK</span><br />');
define('ECHEC', '<span class="echec">ECHEC</span>');

$fichier = '../config.php';
if(file_exists($fichier) AND filesize($fichier ) > 0) { // si le fichier existe et qu'il n'est pas vide alors
exit('Fichier de configuration déjà existant. Installation interrompue.'. RETOUR);
}	

// on crée nos variables, et au passage on retire les éventuels espaces	
$hote = trim($_POST['hote']);
$type = trim($_POST['type']);
$port = trim($_POST['port']);
$login = trim($_POST['login']);
$mdp = trim($_POST['mdp']);
$base = trim($_POST['base']);
$key = trim($_POST['key']);

// on vérifie la connectivité avec le serveur avant d'aller plus loin
try {
	
    $_PDO = new PDO(
	$_CONFIG['bdd']['type'].':dbname='.$_CONFIG['bdd']['table'].';port='.$_CONFIG['bdd']['port'].';host='.$_CONFIG['bdd']['host'],
	$_CONFIG['bdd']['username'],
	$_CONFIG['bdd']['password']
	);
	$bdd->exec('SET NAMES utf8');
} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
    exit();
}

// le texte que l'on va mettre dans le config.php
$texte = '<?PHP
$privateKey = "'.$key.'";

$_CONFIG['bdd']['type']     = "'.$type.'";
$_CONFIG['bdd']['host']     = "'.$hote.'";
$_CONFIG['bdd']['port']     = '.$port.';
$_CONFIG['bdd']['username'] = "'.$login.'";
$_CONFIG['bdd']['password'] = "'.$mdp.'";
$_CONFIG['bdd']['table']    = "'.$base.'";



try {
	
    $_PDO = new PDO(
	$_CONFIG['bdd']['type'].':dbname='.$_CONFIG['bdd']['table'].';port='.$_CONFIG['bdd']['port'].';host='.$_CONFIG['bdd']['host'],
	$_CONFIG['bdd']['username'],
	$_CONFIG['bdd']['password']
	);
	$bdd->exec('SET NAMES utf8');
} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
}

function HashPassword($password) {
	return md5(sha1($privateKey) . sha1($password));
}

function GenUUID() {
	return md5(sha1($privateKey) . sha1(time()) . sha1($privateKey));
}

function GenClientToken() {
	return md5(sha1($privateKey) . sha1(time()));
}

function GenAccessToken() {
	return md5(sha1(time()) . sha1($privateKey));
}
?>';

// on vérifie s'il est possible d'ouvrir le fichier
if(!$ouvrir = fopen($fichier, 'w')) {
exit('Impossible d\'ouvrir le fichier : <strong>'. $fichier .'</strong>.'. RETOUR);
}

// s'il est possible d'écrire dans le fichier alors on ne se gêne pas
if(fwrite($ouvrir, $texte) == FALSE) {
exit('Impossible d\'écrire dans le fichier : <strong>'. $fichier .'</strong>.'. RETOUR);
}

// tout s'est bien passé
echo 'Fichier de configuration : '. OK;
fclose($ouvrir); // on ferme le fichier
	

$requetes = ''; // on crée une variable vide car on va s'en servir après
 
$sql = file('./base.sql'); // on charge le fichier SQL qui contient des requêtes
foreach($sql as $lecture) { // on le lit
	if(substr(trim($lecture), 0, 2) != '--') { // suppression des commentaires et des espaces
	$requetes .= $lecture; // nous avons nos requêtes dans la variable
	}
}
 
$reqs = split(';', $requetes); // on sépare les requêtes
foreach($reqs as $req){	// et on les exécute
	if(!mysql_query($req) AND trim($req) != '') { // si la requête fonctionne bien et qu'elle n'est pas vide
		exit('ERREUR : '. $req); // message d'erreur
	}
}
echo 'Installation : '. OK;	
echo '<br /><span class="note">Note : si le site est en ligne, veuillez supprimer le répertoire <strong>/install</strong> du ftp.</span>';

} // si on passe sur ce fichier sans être passé par la première étape alors on redirige
else
exit('Vous devez d\'abord être passé par <a href="./index.php">le formulaire</a>.');	
?>
</p>
</body>
</html>
