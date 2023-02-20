
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
    
    $neededFieldNames = ["contactid", "userid"];

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

    try
    {
        $stmt = $conn->prepare("SELECT  FirstName FROM Contacts WHERE ID = ? AND UserID = ?");
        $stmt->bind_param("ii", $inData["contactid"], $inData["userid"]);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) 
        {
            http_response_code(404);
            $msg["error"] = "No records found";
            echo json_encode($msg);
            $stmt->close();
            $conn->close();
            return;
        }
    }
    catch(exception)
    {
        $msg["error"] = "unsuccessful";
        http_response_code(409);
        sendResultInfoAsJson($msg);
        $stmt->close();
        $conn->close();
    }

    if ($inData["lastname"] != "")
    {
        try
        {
            $stmt = $conn->prepare("UPDATE Contacts SET LastName  = ? WHERE ID = ? AND UserID = ?");
            $stmt->bind_param("sii", $inData["lastname"], $inData["contactid"], $inData["userid"]);
            $stmt->execute();
        }
        catch(exception)
        {
            $msg["error"] = "unsuccessful";
            http_response_code(409);
            sendResultInfoAsJson($msg);
            $stmt->close();
            $conn->close();
        }
    }
    if ($inData["firstname"] != "")
    {
        try
        {
            $stmt = $conn->prepare("UPDATE Contacts SET FirstName  = ? WHERE ID = ? AND UserID = ?");
            $stmt->bind_param("sii", $inData["firstname"], $inData["contactid"], $inData["userid"]);
            $stmt->execute();
            
        }
        catch(exception)
        {
            $msg["error"] = "unsuccessful";
            http_response_code(409);
            sendResultInfoAsJson($msg);
            $stmt->close();
            $conn->close();
        }

        //$stmt->close();
        //$conn->close();
    }
    if ($inData["email"] != "")
    {
        try
        {
            $stmt = $conn->prepare("UPDATE Contacts SET Email  = ? WHERE ID = ? AND UserID = ?");
            $stmt->bind_param("sii", $inData["email"], $inData["contactid"], $inData["userid"]);
            $stmt->execute();
        }
        catch(exception)
        {
            $msg["error"] = "unsuccessful";
            http_response_code(409);
            sendResultInfoAsJson($msg);
            $stmt->close();
            $conn->close();
        }

        //$stmt->close();
        //$conn->close();
    }
    if ($inData["phone"] != "")
    {
        try
        {
            $stmt = $conn->prepare("UPDATE Contacts SET Phone  = ? WHERE ID = ? AND UserID = ?");
            $stmt->bind_param("sii", $inData["phone"], $inData["contactid"], $inData["userid"]);
            $stmt->execute();
            
        }
        catch(exception)
        {
            $msg["error"] = "unsuccessful";
            http_response_code(409);
            sendResultInfoAsJson($msg);
            $stmt->close();
            $conn->close();
        }

    }

    $msg["error"] = "";
    http_response_code(200);
   sendResultInfoAsJson($msg);
    $stmt->close();
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
