<?PHP
include "config.php";

header('Content-Type: application/json');

$jsonData = Array(
	"error" => "Not Found",
	"errorMessage" => "The server has not found anything matching the request URI"
);

echo json_encode($jsonData);
?>