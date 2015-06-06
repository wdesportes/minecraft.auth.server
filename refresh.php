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

$accessToken = trim($json["accessToken"]);
$clientToken = trim($json["clientToken"]);

$exec = $_PDO->prepare( "SELECT *  FROM `tokens` WHERE `accessToken` = :accessToken AND `clientToken` = :clientToken");
$exec = $exec->execute( array( 'accessToken' => $accessToken ,'clientToken' => $clientToken ) );
$data = $exec->fetch(PDO::FETCH_ASSOC);

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
	$exec = $_PDO->prepare( "UPDATE `tokens` SET `accessToken` = :accessToken, `clientToken` = :clientToken  WHERE `uuid` = :uuid;");
	$exec = $exec->execute( array( 'accessToken' => $accessToken ,'clientToken' => $clientToken ,'uuid' => $data["uuid"]) );
	$data = $exec->fetch(PDO::FETCH_ASSOC);
	$jsonData = Array(
		"accessToken" => $newAccessToken,
		"clientToken" => $newClientToken
	);
}

echo json_encode($jsonData);
?>
