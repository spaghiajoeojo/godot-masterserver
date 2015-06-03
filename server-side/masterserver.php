<?php
/*************************************************************************/
/*  					  Godot PHP Master Server                        */
/*************************************************************************/
/*                                                                       */
/*  This PHP script is a server-side master server for games developed   */
/*  with Godot Engine. Client-side part of the software are contained    */
/*  in MasterServer.gd file distributed in the same zip of this file.    */
/*                                                                       */
/*************************************************************************/
/*                                                                       */
/* Copyright (c) 2015 Lorenzo Beccaro.                                   */
/*                                                                       */
/* Permission is hereby granted, free of charge, to any person obtaining */
/* a copy of this software and associated documentation files (the       */
/* "Software"), to deal in the Software without restriction, including   */
/* without limitation the rights to use, copy, modify, merge, publish,   */
/* distribute, sublicense, and/or sell copies of the Software, and to    */
/* permit persons to whom the Software is furnished to do so, subject to */
/* the following conditions:                                             */
/*                                                                       */
/* The above copyright notice and this permission notice shall be        */
/* included in all copies or substantial portions of the Software.       */
/*                                                                       */
/* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,       */
/* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF    */
/* MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.*/
/* IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY  */
/* CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,  */
/* TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE     */
/* SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.                */
/*************************************************************************/



$action = $_POST["ac"];

if($action == "get")
{
	get_games();
}
else if($action == "add")
{
	if(!empty($_POST["ip"]))
	{
		add_game($_POST["ip"],$_POST["name"]);
	}
	else
	{
		add_game($_POST["name"]);
	}
}
else if($action == "del")
{
	if(!empty($_POST["ip"]))
	{
		del_game_by_ip($_POST["ip"]);
	}
	else
	{
		del_game_by_name($_POST["name"]);
	}
}

function connect()
{	
	$servername = "localhost";	$username = "YOUR_USERNAME";	$password = "YOUR_PASSWORD";	$dbname = "YOUR_DATABASE"; $driver = "YOUR_DRIVER";
	
	
	$col = $driver.':host='.$servername.';dbname='.$dbname;
	 
	
	try {
	  $db = new PDO($col , $username, $password); 
	}
	 
	
	catch(PDOException $e) {
	 
	  
	  echo 'Error: '.$e->getMessage();
	}
	
	
	return $db;
}

function add_game($name, $ip)
{	
	$time = time();
	$conn = connect();
	$stmt = $conn->prepare("INSERT INTO games (ip, name, timestamp) VALUES(:ip,:name,:time) ON DUPLICATE KEY UPDATE name = VALUES(name), timestamp = VALUES(timestamp)");
	if($ip==null)
	{
		$ip = get_client_ip();
	}
	
	$stmt->bindParam(":ip",$ip);
	$stmt->bindParam(":name",$name);
	$stmt->bindParam(":time",$time);
	
	try
	{
		$stmt->execute();
	}
	catch(PDOException $e) {
		echo 'Error: '.$e->getMessage();
	}
	
}

function del_game_by_ip($ip)
{
	$conn = connect();
	$stmt = $conn->prepare("DELETE FROM games WHERE ip=:ip");
	$stmt->bindParam(":ip",$ip);

	try
	{
		$stmt->execute();
	}
	catch(PDOException $e) {
		echo 'Error: '.$e->getMessage();
	}
	
}

function del_game_by_name($name)
{
	$conn = connect();
	$stmt = $conn->prepare("DELETE FROM games WHERE name=:name");
	$stmt->bindParam(":name",$name);
	try
	{
		$stmt->execute();
	}
	catch(PDOException $e) {
		echo 'Error: '.$e->getMessage();
	}
	
}

function get_games()
{
	$conn = connect();
	$sql = "SELECT * FROM games";
	$result = $conn->query($sql);

	$i = 0;
	$outp = "{\n";
	
	foreach ($conn->query($sql) as $row) {
		if ($outp != "{\n") {$outp .= ",\n";}
		$outp .= '"'.$i.'":{"Name":"'  . $row["name"] . '",';
        $outp .= '"IP":"'   . $row["ip"]        . '",';
		$outp .= '"Timestamp":'. $row["timestamp"]     . "}"; 
		$i++;
    }
	$outp .="\n}";
	header('Content-Type: application/json');
	echo $outp;
	
}

function get_client_ip() 
{
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}


?>
