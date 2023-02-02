
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

    $queryStatement = empty($contactName) ? "SELECT * FROM Contacts WHERE UserID = $userId LIMIT $skipped, $perPage" :
    "SELECT * FROM Contacts WHERE UserID = $userId AND (concat_ws(FirstName,'',LastName) LIKE \"$contactName%\" OR concat_ws(LastName,'',FirstName) LIKE \"$contactName%\") LIMIT $skipped, $perPage";
    //SELECT * FROM Users WHERE concat_ws(FirstName,"",LastName) LIKE "%Jo%" LIMIT 2, 2
    $result = mysqli_query($conn, $queryStatement);

    if (mysqli_num_rows($result) == 0) {
        http_response_code(204);
        return;
    }
    
    $countStatement = empty($contactName) ? "SELECT COUNT(*) as num FROM Contacts WHERE UserID = $userId LIMIT $skipped, $perPage" :
    "SELECT COUNT(*) as num FROM Contacts WHERE UserID = $userId AND (concat_ws(FirstName,'',LastName) LIKE \"$contactName%\" OR concat_ws(LastName,'',FirstName) LIKE \"$contactName%\") LIMIT $skipped, $perPage";
    $countResult = mysqli_query($conn,$countStatement);

    $totalCount = mysqli_fetch_assoc($countResult)["num"];
    $dataArray = array();

    //Contact Count needs to be thought about for a bit. It should hold how many contacts there should have been without the limit.
    $dataArray["contactcount"] = $totalCount;
    
    $contactArray = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['ID'];
        //$dateCreated = $row['DateCreated'];
        //$dateLastLoggedIn = $row['DateLastLoggedIn']
        $firstName = $row['FirstName'];
        $lastName = $row['LastName'];
        $email = $row['email'];
        $phone = $row['phone'];

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
