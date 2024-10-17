<?php
$servername = "<default.db>"; 
$usernamedb = "<default.db>"; 
$passworddb = "<default.db>"; 
$dbname = "<default.db>"; 
function writeErrorLog(Error  $e){
  $message=$e->getMessage();
  $myfile = fopen("errorLog.txt", "a") ;
  fwrite($myfile, "$message\n");
  fclose($myfile);
}
function writeNormalLog(string $msg){
  $message="$msg\n";
  $myfile = fopen("debugLog.txt", "a") ;
  fwrite($myfile, $message);
  fclose($myfile);
}
function render_html(string $html){
  $myfile = fopen("templates/$html", "r") ;
  $file=fread($myfile,filesize("templates/$html"));
  fclose($myfile);
  return $file;
}
function insertFirstAkun($username,$email){ //return userId: int

global $servername;
global $usernamedb;
global $passworddb;
global $dbname;

$conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
$sql="INSERT INTO akun ( email,username) VALUES('$email','$username') ";
$conn->query($sql);
$sql="SELECT * FROM akun WHERE username='$username' AND email='$email' LIMIT 1";
$result=$conn->query($sql);
$row=$result->fetch_assoc();
$userId=$row['userId'];
$conn->close();
return $userId;
}
function insertFirstAuth($userId,$username,$email,$password){

global $servername;
global $usernamedb;
global $passworddb;
global $dbname;

$conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
$sql="INSERT INTO auth (userId, email,username,password) VALUES($userId,'$email','$username','$password')";
$conn->query($sql);
$conn->close();

}
function insertFirstAkunData($userId){

global $servername;
global $usernamedb;
global $passworddb;
global $dbname;

$conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
$sql="INSERT INTO akunData (userId, bio,ttl,alamat) VALUES($userId,'not set','not set','not set')";
$conn->query($sql);
$conn->close();

}

function returnBasicAkunDataModel($userId){ //return json model
global $servername;
global $usernamedb;
global $passworddb;
global $dbname;

$conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
$sql="SELECT akun.userId,akun.email,akun.username,akunData.bio,akunData.ttl,akunData.alamat FROM akun INNER JOIN akunData ON akun.userId=akunData.userId WHERE akun.userId=$userId LIMIT 1"; 
$result=$conn->query($sql);
$row=$result->fetch_assoc();
$conn->close();
if($row==null){return null;}
return json_encode($row);

}
function givePost(){
global $servername;
global $usernamedb;
global $passworddb;
global $dbname;

$conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
$result=$conn->query( "SELECT akun.userId,akun.username,post.content,post.date,post.likes,post.postId 
FROM akun
INNER JOIN akunData
ON akun.userId = akunData.userId
INNER JOIN post
ON akunData.userId = post.userId ");

$conn->close();
return json_encode(mysqli_fetch_all ($result, MYSQLI_ASSOC));
}

function givePostUser($userId){
global $servername;
global $usernamedb;
global $passworddb;
global $dbname;

$conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
$result=$conn->query( "SELECT akun.userId,akun.username,post.content,post.date,post.likes,post.postId 
FROM akun
INNER JOIN akunData
ON akun.userId = akunData.userId
INNER JOIN post
ON akunData.userId = post.userId WHERE post.userId=$userId");

$conn->close();
return json_encode(mysqli_fetch_all ($result, MYSQLI_ASSOC));
}

function getStringCurretDate(){
  date_default_timezone_set("Asia/Jakarta");
  $result=date("Y-m-d");
  return  $result;
  
}
function getUnixCurretDate(){
    date_default_timezone_set("Asia/Jakarta");
  $result=date("Y-m-d");
  $result=strtotime($result);
  return   $result;

}
function stringDateToUnix($date){
$result= strtotime($date);
  return $result; 
}
function insertNewBasicPost(
  $userId,
  
  $content,
  $likes,
  $date
)
{
  global $servername;
  global $usernamedb;
  global $passworddb;
  global $dbname;

  $conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
  $conn->query("INSERT INTO post 
  (userId,content,likes,date) 
  VALUES($userId,
  '$content',$likes,'$date')");
  $conn->close();
}
function insertNewFriendRequest(
  $userId1,$userId2
)
{
  global $servername;
  global $usernamedb;
  global $passworddb;
  global $dbname;

  $conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
  $conn->query("INSERT INTO friendship 
  (userIdSource,userIdTarget,status) 
  VALUES($userId1,
  $userId2,0)");
  $result=$conn->query("SELECT username FROM akun WHERE userId=$userId1 LIMIT 1");
  $row=$result->fetch_assoc();
  $username1=$row['username'];
  $content=array("userIdSource"=>$userId1,"username"=>$username1);
  $content = json_encode($content);
  
  $conn->close();
  insertNotification(1,$userId2,$content,$userId1);
}


function getUserFriendship($userId){
  global $servername;
  global $usernamedb;
  global $passworddb;
  global $dbname;

  $conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
  $sql="SELECT akunData.userId,akun.username,friendship.since
  FROM friendship
  INNER JOIN akunData ON  akunData.userId=CASE 
            WHEN userIdSource = $userId THEN userIdTarget 
             ELSE  userIdSource 
       END INNER JOIN akun ON akun.userId=akunData.userId
  WHERE (userIdSource = $userId OR userIdTarget  = $userId) AND status=1;";
  $result=$conn->query($sql);
  $row=json_encode(mysqli_fetch_all ($result, MYSQLI_ASSOC));
  $conn->close();
  return $row;
  
}

function acceptFriendRequest($userId1,$userId2){
  global $servername;
  global $usernamedb;
  global $passworddb;
  global $dbname;
  $date= getStringCurretDate();
  $conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
    $sql="UPDATE friendship SET status=1,since='$date' WHERE  (userIdSource=$userId2 AND userIdTarget=$userId1)";
  $conn->query($sql);
  $sql="UPDATE  notification SET readStatus=2 WHERE userId=$userId1 AND userIdSource=$userId2 AND type=1";
  $conn->query($sql);
    $conn->close();
  }
  function deleteFriend($userId1,$userId2){
  global $servername;
  global $usernamedb;
  global $passworddb;
  global $dbname;
  $date= getStringCurretDate();
  $conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
    $sql="DELETE FROM friendship  WHERE (userIdSource=$userId1 AND userIdTarget=$userId2) OR (userIdSource=$userId2 AND userIdTarget=$userId1)";
    $conn->query($sql);
    $sql="UPDATE  notification SET readStatus=2 WHERE (userId=$userId1 AND userIdSource=$userId2) OR (userId=$userId2 AND userIdSource=$userId1) AND type=1";
    $conn->query($sql);
    
    $conn->close();}

function getUserIdWithUsername($username){
  global $servername;
  global $usernamedb;
  global $passworddb;
  global $dbname;
  $conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
  $sql="SELECT userId FROM akun WHERE username='$username' LIMIT 1";
  $result=$conn->query($sql);
  $row=$result->fetch_assoc();
  $conn->close();
  if($row==null){return null;}
  return json_encode($row);
}
function returnBasicAnotherAkunDataModel($userId1,$userId2){ //return json model
global $servername;
global $usernamedb;
global $passworddb;
global $dbname;
$result1=returnBasicAkunDataModel($userId2);
                               if($result1==null){return null;}                             
$result1=json_decode($result1,true);
if($userId1==null){$result1["type"]=0;return json_encode($result1);}elseif($userId1==$userId2){$result1["type"]=3;return json_encode($result1);}
$conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
$sql="SELECT * FROM friendship WHERE (userIdSource=$userId1 AND userIdTarget=$userId2 ) OR (userIdSource=$userId2 AND userIdTarget=$userId1) LIMIT 1";
$friend=$conn->query($sql);
$row=$friend->fetch_assoc();
$conn->close();
if($row==null){$result1["type"]=1  ;return json_encode($result1);}elseif($row["status"]==0){if($row["userIdSource"]==$userId1){
  $result1["type"]=4;return json_encode($result1);}else{$result1["type"]=5;return json_encode($result1);}
  }else{
$result1["type"]=2;return json_encode($result1);
}




}


function insertNotification($type,$userId,$content,$userIdSource){
global $servername;
global $usernamedb;
global $passworddb;
global $dbname;
  $date= getUnixCurretDate();
$conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
$sql="INSERT INTO notification (type,userId,content,dateUnix,readStatus,userIdSource) VALUES($type,$userId,'$content','$date',0,$userIdSource)";
$conn->query($sql);
  $conn->close();
}

function readNotification($userId){
global $servername;
global $usernamedb;
global $passworddb;
global $dbname;
  
$conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
$sql="SELECT * FROM notification WHERE userId=$userId AND  (readStatus=0 OR readStatus=1)";
$result=$conn->query($sql);
  $row=json_encode(mysqli_fetch_all ($result, MYSQLI_ASSOC));
  if($row==null){return null;}
  $sql="UPDATE  notification SET readStatus=1 WHERE userId=$userId AND readStatus=0";
  $conn->query($sql);
  $conn->close();
  return $row;
}
function countNewNotification($userId){
global $servername;
global $usernamedb;
global $passworddb;
global $dbname;

$conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
$sql="SELECT COUNT(readStatus) AS countNew FROM notification WHERE userId=$userId AND readStatus=0";
$result=$conn->query($sql);
  $row=$result->fetch_assoc();
  if($row["countNew"]==0){return null;}
  $conn->close();
  return json_encode($row);
}




function updateBasicAkunData($userId,$bio,$ttl,$alamat){
global $servername;
global $usernamedb;
global $passworddb;
global $dbname;

$conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
$sql="UPDATE akunData SET bio='$bio',ttl='$ttl',alamat='$alamat' WHERE userId=$userId";
  $conn->query( $sql);
  $conn->close();
}
function updateBasicAuthData($userId,$email,$username){
  global $servername;
  global $usernamedb;
  global $passworddb;
  global $dbname;

  $conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
  $sql="UPDATE auth SET email='$email',username='$username' WHERE userId=$userId";
  $conn->query( $sql);
  $sql="UPDATE akun SET email='$email',username='$username' WHERE userId=$userId";
  $conn->query( $sql);
  $conn->close();
}
function getAllUser(){
  global $servername;
  global $usernamedb;
  global $passworddb;
  global $dbname;

  $conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
  $sql="SELECT * FROM akun";
  $result=$conn->query( $sql);
  $row=json_encode(mysqli_fetch_all ($result, MYSQLI_ASSOC));
  $conn->close();
  return $row;
}
function getSearchUser($username){
  global $servername;
  global $usernamedb;
  global $passworddb;
  global $dbname;

  $conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
  $sql="SELECT * FROM akun
WHERE username LIKE '$username%';";
  $result=$conn->query( $sql);
  $row=json_encode(mysqli_fetch_all ($result, MYSQLI_ASSOC));
  $conn->close();
  if($row=='[]'){return null;}
  return $row;
}


function deleteUser($userId){
  global $servername;
  global $usernamedb;
  global $passworddb;
  global $dbname;

  $conn = new mysqli($servername, $usernamedb, $passworddb, $dbname);
  $sql="DELETE FROM akun
WHERE userId=$userId";
  $conn->query( $sql);
  $sql="DELETE FROM auth
  WHERE userId=$userId";
    $conn->query( $sql);
  $sql="DELETE FROM akunData
  WHERE userId=$userId";
    $conn->query( $sql);
  $sql="DELETE FROM post
  WHERE userId=$userId";
    $conn->query( $sql);
  $sql="DELETE FROM notification
  WHERE userId=$userId OR userIdSource=$userId";
   $conn->query( $sql);
  $sql="DELETE FROM friendship WHERE (userIdSource=$userId OR userIdTarget=$userId)";
    $conn->query( $sql);
  $conn->close();

}
