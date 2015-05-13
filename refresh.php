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

$accessToken = trim(mysqli_real_escape_string($MySQL_CONNECT, $json["accessToken"]));
$clientToken = trim(mysqli_real_escape_string($MySQL_CONNECT, $json["clientToken"]));

$exec = mysqli_query($MySQL_CONNECT, "SELECT *  FROM `tokens` WHERE `accessToken` = '".$accessToken."' AND `clientToken` = '".$clientToken."'");
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
	$newClientToken = GenClientToken();
	$newAccessToken = GenAccessToken();
	mysqli_query($MySQL_CONNECT, "UPDATE `tokens` SET `accessToken` = '".$newAccessToken."', `clientToken` = '".$newClientToken."' WHERE `uuid` = '".$data["uuid"]."';");
	$jsonData = Array(
		"accessToken" => $newAccessToken,
		"clientToken" => $newClientToken
	);
}

echo json_encode($jsonData);
?>