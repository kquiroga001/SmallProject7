
<?php

header("Content-Type: application/json");
header("Acess-Control-Allow-Origin: *");
header("Acess-Control-Allow-Methods: GET");

//We need firstname, lastname, and the login and password
$inData = getRequestInfo();

$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331"); 	
if( $conn->connect_error )
{
    returnWithError( $conn->connect_error );
}
else
{
    $neededFieldNames = ["userid","name"];

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
    $contactName = $inData["name"];
    $contactName = str_replace(' ', '', $contactName);
    $page = isset($inData["page"]) ? $inData["page"] : 1;
    $perPage = (isset($inData["perpage"])) ? $inData["perpage"] : 10;
    $skipped = $perPage * ($page-1);

    $countStatement = null;

    error_reporting(E_ALL);
    ini_set('display_errors', 'On');

    if(empty($contactName))
    {
        $countStatement = $conn->prepare("SELECT COUNT(*) as num FROM Contacts WHERE UserID = ?");
		$countStatement->bind_param("iii", $userId);
		$countStatement->execute();    
    }
    else
    {
        // AND (concat_ws(FirstName,'',LastName) LIKE \"?%\" OR concat_ws(LastName,'',FirstName) LIKE \"?%\")
        $countStatement = $conn->prepare("SELECT COUNT(*) as num FROM Contacts WHERE UserID = ? AND (concat_ws(FirstName,'',LastName) LIKE CONCAT(?,'%') OR concat_ws(LastName,'',FirstName) LIKE CONCAT('%',?))");       
        $countStatement->bind_param("iss", $userId, $contactName,$contactName);
        $countStatement->execute();
    }

    $countResult = $countStatement->get_result();
    $totalCount = mysqli_fetch_assoc($countResult)["num"];

    if ($totalCount == 0) {
        http_response_code(404);
        echo json_encode(array("contactcount"=>$totalCount));
        return;
    }

    $contactResult = null;
    
    if(empty($contactName))
    {
        $stmt = $conn->prepare("SELECT * FROM Contacts WHERE UserID = ? LIMIT ?,?");
        //$succesChek = $stmt->bind_param("iii", $userId,$skipped,$perPage);
        $stmt->bind_param("iii",$userId,$skipped,$perPage);
        $stmt->execute();
        $contactResult = $stmt->get_result();
    }
    else
    {
        $stmt = $conn->prepare("SELECT * FROM Contacts WHERE UserID = ? AND (concat_ws(FirstName,'',LastName) LIKE CONCAT(?,'%') OR concat_ws(LastName,'',FirstName) LIKE CONCAT('%',?)) LIMIT ?, ?");
		$stmt->bind_param("issii", $userId,$contactName,$contactName, $skipped,$perPage);
		$stmt->execute();
        $contactResult = $stmt->get_result();
    }

    $dataArray["contactcount"] = $totalCount;
    
    $contactArray = [];
    while ($row = mysqli_fetch_assoc($contactResult)) {
        $id = $row['ID'];
        //$dateCreated = $row['DateCreated'];
        //$dateLastLoggedIn = $row['DateLastLoggedIn']
        $firstName = $row['FirstName'];
        $lastName = $row['LastName'];
        $email = $row['Email'];
        $phone = $row['Phone'];

        $data = response($id, $firstName, $lastName, $email,$phone);
        array_push($contactArray, $data);
    }

    $dataArray["contacts"] = $contactArray;
    echo json_encode($dataArray);

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
