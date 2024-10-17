<?php

/*


 $COMMAND="SELECT * FROM akun; ";
$conn->query($COMMAND);
   $COMMAND="SELECT * FROM auth ";
$conn->query($COMMAND);
   $COMMAND="SELECT * FROM akunData ";
$conn->query($COMMAND);
   $COMMAND="SELECT * FROM post ";
$conn->query($COMMAND);
   $COMMAND="SELECT * FROM notification ";
$conn->query($COMMAND);
   $COMMAND="SELECT * FROM friendship" ;
$conn->query($COMMAND);

table name{
akun
auth
akunData
post
notification
friendship
}

$command="CREATE TABLE akun (
userId INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
email VARCHAR(50),
username VARCHAR(50))";
 $conn->query($command);
 
$command="CREATE TABLE auth (
userId INT UNSIGNED  PRIMARY KEY,
email VARCHAR(50),
username VARCHAR(50),
password VARCHAR(50))";
 $conn->query($command);
 

$command="CREATE TABLE akunData (
userId INT UNSIGNED  PRIMARY KEY,
profileImage LONGBLOB,
bio VARCHAR(256),
alamat VARCHAR(256),
ttl VARCHAR(256))";
 $conn->query($command);
 
 $command="CREATE TABLE post (
 postId INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 userId INT  UNSIGNED ,
 content VARCHAR(256),
 date VARCHAR(64),
 likes BIGINT UNSIGNED)";
  $conn->query($command);
  
$command="CREATE TABLE notification (
 notificationId INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 userId INT UNSIGNED  ,
 content VARCHAR(256),
 dateUnix INT UNSIGNED  ,
 type TINYINT UNSIGNED ,
 readStatus TINYINT  UNSIGNED,
 userIdSource INT)";
  $conn->query($command);
  
$command="CREATE TABLE friendship (
 relationId INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 userIdSource INT UNSIGNED ,
 userIdTarget INT  UNSIGNED  ,
 since VARCHAR(256),
 status TINYINT  UNSIGNED)";
  $conn->query($command);
*/




/*
$COMMAND="DROP TABLE akun; ";
$conn->query($COMMAND);
   $COMMAND="DROP TABLE auth ";
$conn->query($COMMAND);
   $COMMAND="DROP TABLE akunData ";
$conn->query($COMMAND);
   $COMMAND="DROP TABLE post ";
$conn->query($COMMAND);
   $COMMAND="DROP TABLE notification ";
$conn->query($COMMAND);
   $COMMAND="DROP TABLE friendship" ;
$conn->query($COMMAND);
*/




/*
register

{"email":"email","username":"username","password":"password"}

login
{"username":"username","password":"password"}

insert post
{
"userId":1,
"content":"hello my name is rasil saam,genrikan",
"likes":0
}


get user pust
{"userId":userId}
*/
?>
