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
{	$servername = "localhost";	$username = "unitydit_master";	$password = "bloppy-doppiy789";	$dbname = "unitydit_master_server";
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	// Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	return $conn;
}

function add_game($name, $ip)
{	
	$conn = connect();
	if($ip!=null)
	{
		$sql = "INSERT INTO games (ip, name, timestamp)	VALUES ('".$ip."', '".$name."', '".time()."') ON DUPLICATE KEY UPDATE name = VALUES(name), timestamp = VALUES(timestamp)";
	}
	else
	{
		$sql = "INSERT INTO games (ip, name, timestamp)	VALUES ('".get_client_ip()."', '".$name."', '".time()."') ON DUPLICATE KEY UPDATE name = VALUES(name), timestamp = VALUES(timestamp)";
	}
	if (mysqli_query($conn, $sql)) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sql . "<br>" . mysqli_error($conn);
	}
	mysqli_close($conn);
}

function del_game_by_ip($ip)
{
	$conn = connect();
	$sql = "DELETE FROM games WHERE ip='".$ip."'";

	if (mysqli_query($conn, $sql)) {
		echo "Record deleted successfully";
	} else {
		echo "Error: " . $sql . "<br>" . mysqli_error($conn);
	}
	mysqli_close($conn);
}

function del_game_by_name($name)
{
	$conn = connect();
	$sql = "DELETE FROM games WHERE name='".$name."'";

	if (mysqli_query($conn, $sql)) {
		echo "Record deleted successfully";
	} else {
		echo "Error: " . $sql . "<br>" . mysqli_error($conn);
	}
	mysqli_close($conn);
}

function get_games()
{
	$conn = connect();
	$SQL = "SELECT * FROM games";
	$result = mysqli_query($conn,$SQL);

	$i = 0;
	$outp = "{\n";
	while($rs = $result->fetch_array(MYSQLI_ASSOC)) {
		if ($outp != "{\n") {$outp .= ",\n";}
		$outp .= '"'.$i.'":{"Name":"'  . $rs["name"] . '",';
		$outp .= '"IP":"'   . $rs["ip"]        . '",';
		$outp .= '"Timestamp":'. $rs["timestamp"]     . "}"; 
		$i++;
	}
	$outp .="\n}";
	header('Content-Type: application/json');
	echo $outp;
	mysqli_close($conn);
}

function get_client_ip() {
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
