
<?php

header("Content-Type: application/json");
header("Acess-Control-Allow-Origin: *");
header("Acess-Control-Allow-Methods: POST");

//We need firstname, lastname, and the login and password
$inData = getRequestInfo();

$id = 0;
$firstName = "";
$lastName = "";

$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331"); 	
if( $conn->connect_error )
{
    returnWithError( $conn->connect_error );
}
else
{
    $query = "INSERT INTO COP4331.Users(ID, firstName, lastName, Login, Password) VALUES (\"".$inData["firstName"]."\",\"".$inData["lastName"]."\",\"".$inData["login"]."\",\"".$inData["password"]."\")";
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
