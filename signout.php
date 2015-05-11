<?PHP
include "config.php";

header('Content-Type: application/json');

$json = file_get_contents("php://input");
if (!$json) {
	$jsonData = Array(
		"error" => "Method Not Allowed",
		"errorMessage" => "The method specified in the request is not allowed for the resource identified by the request URI"
	);
	echo json_encode($jsonData);
	die();
}

$json = json_decode($json, true);
$exec = mysqli_query($MySQL_CONNECT, "SELECT *  FROM `users` WHERE `username` = '".$json["username"]."'");
$data = @mysqli_fetch_array($exec);
if (!isset($json["username"])) {
	$jsonData = Array(
		"error" => "IllegalArgumentException",
		"errorMessage" => "credentials can not be null."
	);
} elseif (empty($json["username"])) {
	$jsonData = Array(
		"error" => "IllegalArgumentException",
		"errorMessage" => "credentials can not be null."
	);
} elseif (!isset($json["password"])) {
	$jsonData = Array(
		"error" => "IllegalArgumentException",
		"errorMessage" => "credentials can not be null."
	);
} elseif (empty($json["password"])) {
	$jsonData = Array(
		"error" => "IllegalArgumentException",
		"errorMessage" => "credentials can not be null."
	);
} elseif ($exec == false) {
	$jsonData = Array(
		"error" => "ForbiddenOperationException",
		"errorMessage" => "Invalid credentials. Invalid username or password."
	);
} elseif (count($data) == 0) {
	$jsonData = Array(
		"error" => "ForbiddenOperationException",
		"errorMessage" => "Invalid credentials. Invalid username or password."
	);
} else {
	if ($data["password"] == HashPassword($json["password"])) {
		$clientToken = GenClientToken();
		$accessToken = GenAccessToken();
		mysqli_query($MySQL_CONNECT, "UPDATE `users` SET `clientToken` = '".$clientToken."', `accessToken` = '".$accessToken."' WHERE `uuid`= '".$data["uuid"]."' LIMIT 1;");
		die();
	} else {
		$jsonData = Array(
			"error" => "ForbiddenOperationException",
			"errorMessage" => "Invalid credentials. Invalid username or password."
		);
	}

}

echo json_encode($jsonData);
?>