<?PHP
include "config.php";

header('Content-Type: application/json');

$jsonData = Array(
	"Status" => "OK",
	"Runtime-Mode" => "productionMode",
	"Application-Author" => "NathaanGaming",
	"Application-Description" => "Remake of Mojang Authentication Server (Yggdrasil) for LaunchMyCraft.",
	"Specification-Version" => "0.2",
	"Application-Name" => "launchmycraft.auth.server",
	"Implementation-Version" => "0.2_beta",
	"Application-Owner" => "NathaanGaming"
);

echo json_encode($jsonData);
?>