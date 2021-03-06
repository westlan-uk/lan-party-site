<?php

require_once '../../includes/common.php';

use \libAllure\Sanitizer;
use \libAllure\DatabaseFactory;

$ipAddress = Sanitizer::getInstance()->filterString('ipAddress');

if ($ipAddress == null) {
	die ('ERROR:IP Address not specified');
}

$event = Events::nextEvent();

$sql = 'SELECT u.username FROM authenticated_machines a LEFT JOIN events e ON a.event = e.id JOIN users u ON a.user = u.id WHERE a.ip = :ipAddress AND e.id = :eventId ORDER BY e.date DESC LIMIT 1';
$stmt = DatabaseFactory::getInstance()->prepare($sql);
$stmt->bindValue(':ipAddress', $ipAddress);
$stmt->bindValue(':eventId', $event['id']);
$stmt->execute();

if ($stmt->numRows() == 0) {
	die ('Error:IP Address not found.');
} else {
	$machineAuthentication = $stmt->fetchRow();
	die ($machineAuthentication['username']);
}


?>
