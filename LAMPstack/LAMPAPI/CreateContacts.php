
<?php

	header("Content-Type: application/json");
	header("Acess-Control-Allow-Origin: *");
	header("Acess-Control-Allow-Methods: POST");

	$inData = getRequestInfo();

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		/*$stmt = $conn->prepare("INSERT into Contacts (UserId,FirstName, LastName, Phone, Email) VALUES(?,?,?,?,?)");
		$stmt->bind_param("ss", $userId, $firstname, $lastname, $phone, $email);
		$stmt->execute();
		$result = $stmt->get_result();
    	echo json_encode($result);
		$stmt->close();
		$conn->close();*/


		$query = "INSERT INTO COP4331.Contacts(FirstName, LastName, Email, Phone, UserId)
		 VALUES (\"".$inData["firstname"]."\",\"".$inData["lastname"]."\",\"".$inData["email"]."\",\"".$inData["phone"]."\",\"".$inData["userid"]."\")";
		try
		{
			$stmt = $conn->prepare("INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserId) VALUES (?,?,?,?,?) ");
			$stmt->bind_param("sssss" , $inData["firstname"], $inData["lastname"], $inData["email"], $inData["phone"], $inData["userid"]);
			$stmt->execute();
			$msg["error"] = "";
			http_response_code(200);
			sendResultInfoAsJson($msg);
		}
		catch(exception)
		{
			$msg["error"] = "unsuccessful";
			http_response_code(409);
			sendResultInfoAsJson($msg);
		}
		
		$conn->close();

		//returnWithError("");
	}

	function getRequestInfo()
{
    return json_decode(file_get_contents('php://input'), true);
}

function sendResultInfoAsJson( $obj )
{
    echo json_encode($obj);
}
	
?>