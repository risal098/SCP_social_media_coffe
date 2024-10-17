<?php

// header('Access-Control-Allow-Origin: *');
// header("Access-Control-Allow-Credentials: true");
// header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
// header('Access-Control-Max-Age: 1000');
// header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Set the CORS headers
    header('Access-Control-Allow-Origin: *'); // Replace * with the allowed origin(s) if needed
    // header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE'); // Specify the allowed HTTP methods
    // header('Access-Control-Allow-Headers: Accept, X-Requested-With, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization, access-control-allow-origin'); // Specify the allowed headers
    header('Access-Control-Allow-Methods: *'); // Specify the allowed HTTP methods
    header('Access-Control-Allow-Headers: *'); // Specify the allowed headers
 
    // End the script execution for the preflight request
    exit;
}
//include 'plask.php'; //render_html,writeErrorLog,writeNormalLog
include "functionality.php";
$servername = "<default.db>"; 
$usernamedb = "<default.db>"; 
$passworddb = "<default.db>"; 
$dbname = "<default.db>"; 

/////////////////////////////////////////////////////////////
if ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['REQUEST_URI'] == "/testing") {
    echo "do me";
}
/////////////////////////////////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'GET' and $_SERVER['REQUEST_URI'] == "/testinger") {
    $body = (file_get_contents('php://input'));
    if ($body == null) {
        echo "sapiul";
    }

}
/////////////////////////////////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'GET' and $_SERVER['REQUEST_URI'] == "/teslogin") {

    echo render_html("tesLogin.html");
}
/////////////////////////////////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'GET' and $_SERVER['REQUEST_URI'] == "/") {
    try {
        http_response_code(200);
        echo render_html("error.html");

    } catch (Error $e) {
        writeErrorLog($e);
        http_response_code(500);
        echo "unknown error";

    }
}
/////////////////////////////////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['REQUEST_URI'] == "/api/login") {
    $conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
    try {
        $body = (file_get_contents('php://input'));
        $data = json_decode($body, true);
        $username = $data['username'];
        $password = $data['password'];

        $sql = "SELECT * FROM auth WHERE username='$username' AND password='$password' LIMIT 1";

        $result = $conn->query($sql);
        if (mysqli_num_rows($result) >= 1) {
            $row = $result->fetch_assoc();
            $userId = $row['userId'];
            header('Access-Control-Allow-Origin: *');
            http_response_code(200);
            echo returnBasicAkunDataModel($userId);
        } else {
            header('Access-Control-Allow-Origin: *');
            http_response_code(401);
            echo "username or password is wrong";
        }
    } catch (Error $e) {
        writeErrorLog($e);
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
        echo "unknown error";
    }
    $conn->close();
}
/////////////////////////////////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['REQUEST_URI'] == "/api/register") {
    $conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
    try {
        $body = (file_get_contents('php://input'));
        $data = json_decode($body, true);
        $email = $data['email'];
        $username = $data['username'];
        $password = $data['password'];
        $sql = "SELECT * FROM akun WHERE username='$username' OR email='$email' LIMIT 1";
        $result = $conn->query($sql);
        if (mysqli_num_rows($result) >= 1) {
            header('Access-Control-Allow-Origin: *');
            http_response_code(401);
            echo "username or email is already taken";
        } else {
            //registar
            $userId = insertFirstAkun($username, $email);
            insertFirstAuth($userId, $username, $email, $password);
            insertFirstAkunData($userId);
            header('Access-Control-Allow-Origin: *');
            http_response_code(200);
            echo returnBasicAkunDataModel($userId);
        }
    } catch (Error $e) {
        writeErrorLog($e);
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
        echo "unknown error";

    }
    $conn->close();
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' and $_SERVER['REQUEST_URI'] == "/api/getBasicPost") {
    try {

        header('Access-Control-Allow-Origin: *');
        http_response_code(200);
        echo givePost();
    } catch (Error $e) {
        writeErrorLog($e);
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
        echo render_html("error.html");

    }
}
/////////////////////////////////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['REQUEST_URI'] == "/api/getBasicUserPost") {
    try {
        $body = (file_get_contents('php://input'));
        $data = json_decode($body, true);
        $userId = $data['userId'];
        header('Access-Control-Allow-Origin: *');
        http_response_code(200);
        echo givePostUser($userId);
    } catch (Error $e) {
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
        writeErrorLog($e);
        echo render_html("error.html");

    }
}
/////////////////////////////////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['REQUEST_URI'] == "/api/insertNewBasicPost") {
    try {
        $body = (file_get_contents('php://input'));
        $data = json_decode($body, true);
        $userId = $data['userId'];

        $content = $data['content'];
        $likes = $data['likes'];
        $date = getStringCurretDate();

        insertNewBasicPost(
            $userId,
            $content,
            $likes,
            $date
        );
        header('Access-Control-Allow-Origin: *');
        http_response_code(200);
        echo "post inserted";
    } catch (Error $e) {
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
        writeErrorLog($e);
        echo render_html("error.html");

    }
}
/////////////////////////////////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['REQUEST_URI'] == "/api/insertFriendRequest") {
    try {
        $body = (file_get_contents('php://input'));
        $data = json_decode($body, true);

        $userId1 = $data["userIdSource"];
        $userId2 = $data["userIdTarget"];
        insertNewFriendRequest($userId1, $userId2);
        header('Access-Control-Allow-Origin: *');
        http_response_code(200);
        echo "request sended";
    } catch (Error $e) {
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
        writeErrorLog($e);
        echo render_html("error.html");

    }
}
///////////////////////////////////////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['REQUEST_URI'] == "/api/getUserFriendship") {
    try {
        $body = (file_get_contents('php://input'));
        $data = json_decode($body, true);
        $userId = $data['userId'];
        header('Access-Control-Allow-Origin: *');
        http_response_code(200);
        echo getUserFriendship($userId);
    } catch (Error $e) {
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
        writeErrorLog($e);
        echo render_html("error.html");

    }
} ///////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['REQUEST_URI'] == "/api/acceptFriendRequest") {
    try {
        $body = (file_get_contents('php://input'));
        $data = json_decode($body, true);
        $userId1 = $data["userIdSource"];
        $userId2 = $data["userIdTarget"];

        acceptFriendRequest($userId1, $userId2);
        header('Access-Control-Allow-Origin: *');
        http_response_code(200);

        echo "request accepted";

    } catch (Error $e) {
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
        writeErrorLog($e);
        echo render_html("error.html");

    }
} ///////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['REQUEST_URI'] == "/api/removeFriendship") {
    try {
        $body = (file_get_contents('php://input'));
        $data = json_decode($body, true);
        $userId1 = $data["userId1"];
        $userId2 = $data["userId2"];
        deleteFriend($userId1, $userId2);
        header('Access-Control-Allow-Origin: *');
        http_response_code(200);

        echo "request accepted";

    } catch (Error $e) {
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
        writeErrorLog($e);
        echo render_html("error.html");

    }
} ///////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['REQUEST_URI'] == "/api/getAnotherUserBasicData") {
    try {
        $body = (file_get_contents('php://input'));
        $data = json_decode($body, true);
        $userId1 = $data["userIdSource"];
        $userId2 = $data["userIdTarget"];
        $basicData = returnBasicAnotherAkunDataModel($userId1, $userId2);
        if ($basicData == null) {
            writeNormalLog("tis");
            header('Access-Control-Allow-Origin: *');
            http_response_code(404);
            echo "not found";
        } else {
            header('Access-Control-Allow-Origin: *');
            http_response_code(200);
            writeNormalLog("tes");
            echo $basicData;
        }

    } catch (Error $e) {
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
        writeErrorLog($e);
        echo render_html("error.html");

    }
} ///////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['REQUEST_URI'] == "/api/getUserIdByUsername") {
    try {
        $body = (file_get_contents('php://input'));
        $data = json_decode($body, true);
        $username = $data["username"];
        $basicData = getUserIdWithUsername($username);
        if ($basicData == null) {
            writeNormalLog("tis");
            header('Access-Control-Allow-Origin: *');
            http_response_code(404);
            echo "not found";
        } else {
            header('Access-Control-Allow-Origin: *');
            http_response_code(200);
            writeNormalLog("tes");
            echo $basicData;
        }

    } catch (Error $e) {
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
        writeErrorLog($e);
        echo render_html("error.html");

    }
} ///////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['REQUEST_URI'] == "/api/insertNotification") {
    try {
        $body = (file_get_contents('php://input'));
        $data = json_decode($body, true);
        $type = $data["type"];
        $userId = $data["userId"];
        $content = $data["content"];
        $userIdSource = $data["userIdSource"];
        insertNotification($type, $userId, $content, $userIdSource);
        header('Access-Control-Allow-Origin: *');
        http_response_code(200);
        echo "notification inserted";

    } catch (Error $e) {
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
        writeErrorLog($e);
        echo render_html("error.html");

    }
} ///////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['REQUEST_URI'] == "/api/readNotification") {
    try {
        $body = (file_get_contents('php://input'));
        $data = json_decode($body, true);
        $userId = $data["userId"];
        $jsonData = readNotification($userId);
        if ($jsonData == null) {
            header('Access-Control-Allow-Origin: *');
            http_response_code(404);
            echo "no new notif";
        } else {
            header('Access-Control-Allow-Origin: *');
            http_response_code(200);
            echo $jsonData;
        }

    } catch (Error $e) {
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
        writeErrorLog($e);
        echo render_html("error.html");

    }
} ///////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['REQUEST_URI'] == "/api/updateUserAllData") {
    try {
        $body = (file_get_contents('php://input'));
        $data = json_decode($body, true);

        $userId = $data["userId"];
        $email = $data["email"];
        $username = $data["username"];
        $bio = $data["bio"];
        $ttl = $data["ttl"];
        $alamat = $data["alamat"];
        updateBasicAkunData($userId, $bio, $ttl, $alamat);
        updateBasicAuthData($userId, $email, $username);
        header('Access-Control-Allow-Origin: *');
        http_response_code(200);
        echo "ok";
    } catch (Error $e) {
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
        writeErrorLog($e);
        echo render_html("error.html");

    }
} ///////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'GET' and $_SERVER['REQUEST_URI'] == "/api/getAllUser") {
    try {

        header('Access-Control-Allow-Origin: *');
        http_response_code(200);
        echo getAllUser();
    } catch (Error $e) {
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
        writeErrorLog($e);
        echo render_html("error.html");

    }
} ///////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['REQUEST_URI'] == "/api/getCountNewNotif") {
    try {


        $body = (file_get_contents('php://input'));
        $data = json_decode($body, true);

        $userId = $data["userId"];
        $data = countNewNotification($userId);
        if ($data == null) {
            header('Access-Control-Allow-Origin: *');
            http_response_code(404);
            echo json_encode(array("countNew"=>0));
        } else {
            header('Access-Control-Allow-Origin: *');
            http_response_code(200);
            echo $data;
        }
    } catch (Error $e) {
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
        writeErrorLog($e);
        echo render_html("error.html");

    }
} ///////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] == 'GET' and $_SERVER['REQUEST_URI'] == "/secret/dashboard") {
    echo render_html("dashboard.html");
} ///////////////////////////////////
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_FILES['file']) and $_SERVER['REQUEST_URI'] == "/api/uploadProfile") {

    $userId = isset($_POST["userId"]) ? $_POST["userId"] : "";
    $data = json_decode($userId, true);
    $userId = $data["userId"];
    $fileType = $_FILES['file']["type"];

    if ($fileType == "image/jpeg") {
        $fileType = "jpg";
        $uploadsDirectory = './userProfileImage/';
        $uploadedFile = $uploadsDirectory . $userId . "." . $fileType;
        if (file_exists($uploadedFile)) {
            unlink($uploadedFile);
        }
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadedFile)) {
            header('Access-Control-Allow-Origin: *');
            http_response_code(200);
            echo 'File uploaded successfully .' . $uploadedFile;
        } else {
            header('Access-Control-Allow-Origin: *');
            http_response_code(500);
            echo 'Error uploading file.';
        }
    } else {
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
        echo 'must be in jpg format';
    }
} ///////////////////////////////////
  elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['REQUEST_URI'] == "/api/searchUsername") {
      try {
          $body = (file_get_contents('php://input'));
          $data = json_decode($body, true);

          $username = $data["username"];
          $data=getSearchUser($username);
          if ($data == null) {header('Access-Control-Allow-Origin: *');
                             http_response_code(404);echo "not found";}
          else{
          header('Access-Control-Allow-Origin: *');
          http_response_code(200);
          echo $data;}
      } catch (Error $e) {
          header('Access-Control-Allow-Origin: *');
          http_response_code(500);
          writeErrorLog($e);
          echo render_html("error.html");

      }
  } ///////////////////////////////////
  elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['REQUEST_URI'] == "/api/deleteUser") {
      try {
          $body = (file_get_contents('php://input'));
          $data = json_decode($body, true);
          $userId = $data['userId'];
          deleteUser($userId);
          header('Access-Control-Allow-Origin: *');
          http_response_code(200);
          echo "user deleted";
      } catch (Error $e) {
          header('Access-Control-Allow-Origin: *');
          http_response_code(500);
          writeErrorLog($e);
          echo render_html("error.html");

      }
  } ///////////////////////////////////
else {
    header('Access-Control-Allow-Origin: *');
    http_response_code(404);
    echo render_html("404.html");
}



