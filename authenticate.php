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

$username = trim($json["username"]);
$password = HashPassword(trim($json["password"]));

if (isset($json["clientToken"]) && !empty($json["clientToken"])) {
	$clientToken = trim($json["clientToken"]);
}

$exec = $_PDO->prepare( 'SELECT *  FROM users WHERE username = :username ' );
$exec->execute( array( 'username' => $username ) );
$data = $exec->fetch(PDO::FETCH_ASSOC);
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
	if ($data["password"] == $password) {
		if (!isset($clientToken)) {
			$clientToken = GenClientToken();
		}
		$accessToken = GenAccessToken();
		$exec = $_PDO->prepare( 'INSERT INTO tokens (accessToken, clientToken, uuid, username) VALUES (:accessToken, :clientToken, :uuid, :username);' );
		$exec = $exec->execute( array( 'accessToken' => $accessToken ,'clientToken' => $clientToken,'uuid'=>$data["uuid"],'username'=>$username) );
		$jsonData = Array(
			"accessToken" => $accessToken,
			"clientToken" => $clientToken,
			"selectedProfile" => Array(
				"id" => $data["uuid"],
				"name" => $data["username"]
			),
			"availableProfiles" => Array(
				Array(
					"id" => $data["uuid"],
					"name" => $data["username"]
				)
			)
		);
	} else {
		$jsonData = Array(
			"error" => "ForbiddenOperationException",
			"errorMessage" => "Invalid credentials. Invalid username or password."
		);
	}
}

echo json_encode($jsonData);
?>
