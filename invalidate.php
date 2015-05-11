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
$exec = mysqli_query($MySQL_CONNECT, "SELECT *  FROM `users` WHERE `clientToken` = '".$json["clientToken"]."' AND `accessToken` = '".$json["accessToken"]."'");
$data = @mysqli_fetch_array($exec);
if ($exec == false) {
	$jsonData = Array(
		"error" => "ForbiddenOperationException",
		"errorMessage" => "Invalid token."
	);
} elseif (count($data) == 0) {
	$jsonData = Array(
		"error" => "ForbiddenOperationException",
		"errorMessage" => "Invalid token."
	);
} else {
	if (isset($json["clientToken"]) && !empty($json["clientToken"])) {
	$clientToken = GenClientToken();
	$accessToken = GenAccessToken();
	mysqli_query($MySQL_CONNECT, "UPDATE `users` SET `clientToken` = '".$clientToken."', `accessToken` = '".$accessToken."' WHERE `uuid`= '".$data["uuid"]."' LIMIT 1;");
	die();
}

echo json_encode($jsonData);
?>