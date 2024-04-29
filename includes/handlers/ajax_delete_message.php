<?php
include("../../config/config.php");

if(isset($_POST['id'])) {
	$id = $_POST['id'];

	$stmt = $con->prepare("DELETE FROM messages WHERE id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
}

?>
