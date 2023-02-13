
<?php

header("Content-Type: application/json");
header("Acess-Control-Allow-Origin: *");
header("Acess-Control-Allow-Methods: DELETE");

//We need firstname, lastname, and the login and password
$inData = getRequestInfo();

$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331"); 	
if( $conn->connect_error )
{
    returnWithError( $conn->connect_error );
}
else
{
    $neededFieldNames = ["userid","contactid"];

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

    $userId = $inData["userid"];
    $contactId = $inData["contactid"];
    
    $stmt = $conn->prepare('DELETE FROM Contacts WHERE UserID = ? AND ID = ?');
    $stmt->bind_param('ii', $userId,$contactId); // 's' specifies the variable type => 'string'
    $stmt->execute();
    $deleteResult = $stmt->get_result();

    
    //$deleteResult `= mysqli_query($conn,$deleteStatement);
    $affected = $conn->affected_rows;

    if($affected == 0)
    {
        http_response_code(404);
        $msg = array();
        $msg["success"] = false;
        echo json_encode($msg);
        return;
    }

    $msg = [];
    $msg["success"] = true;
    echo json_encode($msg);
    
    mysqli_close($conn);
}

function getRequestInfo()
{
    return json_decode(file_get_contents('php://input'), true);
}

function sendResultInfoAsJson( $obj )
{
    echo json_encode($obj);
}

function response($id, $firstName, $lastName, $email, $phone) : array
{
    $response['id'] = $id;
    //$response['DateCreated'] = $dateCreated;
    //$response['DateLastLoggedIn'] = $dateLastLoggedIn;
    $response['firstname'] = $firstName;
    $response['lastname'] = $lastName;
    $response['email'] = $email;
    $response['phone'] = $phone;

    //$json_response = json_encode($response);
    //echo $json_response;
    return $response;
}

function returnWithError( $err )
{
    $retValue = '{"error":"' . $err . '"}';
    sendResultInfoAsJson( $retValue );
}


?>
