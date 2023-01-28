
<?php
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
    $query = "INSERT INTO COP4331.Users(FirstName, LastName, Login, Password) VALUES (\"".$inData["firstname"]."\",\"".$inData["lastname"]."\",\"".$inData["login"]."\",\"".$inData["password"]."\")";
    $msg = [];
    try{
        $msg["successful"] = true;
        $result = mysqli_query($conn,$query);
        sendResultInfoAsJson( $msg );
    }
    catch(exception)
    {
        $msg["successful"] = false;
        sendResultInfoAsJson( $msg );
    }
    
    $conn->close();
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
?>
