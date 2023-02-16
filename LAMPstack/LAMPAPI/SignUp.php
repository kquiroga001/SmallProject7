
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
    $neededFieldNames = ["firstname","lastname","login","password"];

    foreach($neededFieldNames as $fieldName)
    {
        if(!isset($inData[$fieldName]))
        {
            http_response_code(400);
            $data = [];
            $data["error"] = "Body is missing $fieldName";
            echo json_encode($data);
            return;
        }
    }

    /*ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);*/

    //$stmt = $conn->prepare("SELECT ID,firstName,lastName FROM Users WHERE Login=? AND Password =?");
    try
    {
        $stmt = $conn->prepare(("INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (?,?,?,?)"));
        $stmt->bind_param("ssss", $inData["firstname"],$inData["lastname"],$inData["login"], $inData["password"]);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $msg["error"] = "";
        http_response_code(200);
        sendResultInfoAsJson($msg);
        //echo json_encode($result);
        $stmt->close();
        $conn->close(); 
    }
    catch(Exception $ex)
    {
        $msg["error"] = "unsuccessful";
        sendResultInfoAsJson($msg);
        http_response_code(409);
    }
}

function getRequestInfo()
{
    return json_decode(file_get_contents('php://input'), true);
}
function sendResultInfoAsJson( $obj )
{
    echo json_encode($obj);
}

function returnWithError( $err )
{
    $retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
    sendResultInfoAsJson( $retValue );
}

function returnWithInfo( $firstName, $lastName, $id )
{
    $retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
    sendResultInfoAsJson( $retValue );
}

?>
