
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

    if ($inData["field"] == "LastName")
    {
        try
        {
            $stmt = $conn->prepare("UPDATE Contacts SET LastName  = ? WHERE ID = ?");
            $stmt->bind_param("ss", $inData["string"], $inData["contactid"]);
            $msg["error"] = "";
            $stmt->execute();
            http_response_code(200);
            sendResultInfoAsJson($msg);
        }
        catch(exception)
        {
            $msg["error"] = "unsuccessful";
            http_response_code(409);
            sendResultInfoAsJson($msg);
        }

        $stmt->close();
        $conn->close();
    }
    elseif ($inData["field"] == "FirstName")
    {
        try
        {
            $stmt = $conn->prepare("UPDATE Contacts SET FirstName  = ? WHERE ID = ?");
            $stmt->bind_param("ss", $inData["string"], $inData["contactid"]);
            $msg["error"] = "";
            $stmt->execute();
            http_response_code(200);
            sendResultInfoAsJson($msg);
        }
        catch(exception)
        {
            $msg["error"] = "unsuccessful";
            http_response_code(409);
            sendResultInfoAsJson($msg);
        }

        $stmt->close();
        $conn->close();
    }
    elseif ($inData["field"] == "Email")
    {
        try
        {
            $stmt = $conn->prepare("UPDATE Contacts SET Email  = ? WHERE ID = ?");
            $stmt->bind_param("ss", $inData["string"], $inData["contactid"]);
            $msg["error"] = "";
            $stmt->execute();
            http_response_code(200);
            sendResultInfoAsJson($msg);
        }
        catch(exception)
        {
            $msg["error"] = "unsuccessful";
            http_response_code(409);
            sendResultInfoAsJson($msg);
        }

        $stmt->close();
        $conn->close();
    }
    elseif ($inData["field"] == "Phone")
    {
        try
        {
            $stmt = $conn->prepare("UPDATE Contacts SET Phone  = ? WHERE ID = ?");
            $stmt->bind_param("ss", $inData["string"], $inData["contactid"]);
            $msg["error"] = "";
            $stmt->execute();
            http_response_code(200);
            sendResultInfoAsJson($msg);
        }
        catch(exception)
        {
            $msg["error"] = "unsuccessful";
            http_response_code(409);
            sendResultInfoAsJson($msg);
        }

        $stmt->close();
        $conn->close();
    }
    else
    {
        $msg["error"] = "unsuccessful";
        http_response_code(409);
        sendResultInfoAsJson($msg);
        $stmt->close();
        $conn->close();
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

?>