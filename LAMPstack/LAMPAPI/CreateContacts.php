
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


		$query = "INSERT INTO COP4331.Contacts(FirstName, LastName, Email, Phone, UserId) VALUES (\"".$inData["firstname"]."\",\"".$inData["lastname"]."\",\"".$inData["email"]."\",\"".$inData["phone"]."\",\"".$inData["userid"]."\")";
		$msg = [];
		try{
			$msg["successful"] = true;
			$result = mysqli_query($conn,$query); 
			http_response_code(200);
			sendResultInfoAsJson($msg);
		}
		catch(exception)
		{
			$msg["successful"] = false;
			http_response_code(409);
			sendResultInfoAsJson($msg);
		}
		
		$conn->close();

		returnWithError("");
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>