<?PHP
include "config.php";

if(isset($_POST["submit"])) {
	if (isset($_POST["login"]) && !empty($_POST["login"]) && isset($_POST["mdp"]) && !empty($_POST["mdp"])) {
		$login = trim($_POST["login"]);
		$mdp = trim($_POST["mdp"]);
		$uuid = GenUUID();
		
			$exec = $_PDO->prepare( "SELECT *  FROM `users` WHERE `username` = :username;");
			$exec = $exec->execute( array( 'username' => $login) );
			$data = $exec->fetch(PDO::FETCH_ASSOC);
		if ($exec == false) {
			die("Une erreur est survenue. Veuillez réssayer.");
		} elseif (count($data) > 0) {
			die("Ce pseudo est déjà utilisé !");
		}
		$usernameCorrect = preg_match("/^([A-Za-z0-9_]+)$/", $login);
		if ($usernameCorrect == 0) {
			die("Ce pseudo est incorrect");
		}
			$result = $_PDO->prepare( "INSERT INTO `users` (`uuid`, `username`, `password`) VALUES (:uuid, :username, :password);");
			$result->execute( array( 'uuid' => $uuid,'username' => $login,'password' => HashPassword($mdp)) );

		if ($result == false) {
			die("Une erreur est survenue.");
		}
		die("Votre compte a été crée avec succès !");
	} else {
		die("Une erreur est survenue. Veuillez réssayer.");
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Inscription</title>
<style type="text/css">
body {
font-family:Tahoma, Arial, Serif; /* polices du texte */
font-size:14px; /* taille du texte */
}
h1 {
font-size:1.4em; /* taille du titre */
}
label
{
font-size:1.2em; /* taille du texte pour les "label" */
display:block; /* on affiche les "label" en tant que block et non pas inline */ 
width:150px; /* on leur met une taille pour aligner nos zones de texte */
float:left; /* flottant à gauche */
}
</style>
</head>
<body>

<h1>Création d'un compte :</h1>
<p>
<form action="register.php" method="post">
<p>
<input type="hidden" name="etape" value="1" />

<label for="login">Pseudo :</label>
<input type="text" name="login" maxlength="40" /><br />

<label for="mdp">Mot de passe :</label>
<input type="password" name="mdp" maxlength="40" /><br /><br />

<label for="submit">&nbsp;</label>
<input type="submit" name="submit" value="Envoyer" />
</p>
</form>
</p>

</body>
</html>
