 <?php

use \libAllure\Session;
use \libAllure\DatabaseFactory;

function getJsonSeatChange($type, $seatId, $username = null) {
	if ($username == Session::getUser()->getUsername()) {
		$username = 'self';
	}
	
	return array(
		'type' => $type,
		'seat' => $seatId,
		'username' => $username,
	);
}

function getSeats($eventId) {
	$sql = 'SELECT s.seat, u.username FROM seatingplan_seat_selections s JOIN users u ON s.user = u.id WHERE s.event = :event';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':event', $eventId);
	$stmt->execute();

	return $stmt->fetchAll();
}

function getUserInSeat($eventId, $seatId) {
	$sql = 'SELECT s.seat, s.user, u.username FROM seatingplan_seat_selections s JOIN users u ON s.user = u.id WHERE s.event = :event AND s.seat = :seat LIMIT 1';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':event', $eventId);
	$stmt->bindValue(':seat', $seatId);
	$stmt->execute();

	$seatSelection = $stmt->fetchRow();

	if (!empty($seatSelection)) {
		return $seatSelection['username'];
	} else {
		return false;
	}
}

function setSeatForUser($eventId, $userId, $seatId) {
	if (getUserInSeat($eventId, $seatId)) {
		return; // dont overwrite
	}

	// if getSeatForUser != null, insert

	$sql = 'UPDATE seatingplan_seat_selections s SET seat = :seat WHERE event = :event and USER = :user';
	$stmt = DatabaseFactory::getInstance()->prepare($sql);
	$stmt->bindValue(':seat', $seatId);
	$stmt->bindValue(':event', $eventId);
	$stmt->bindValue(':user', $userId);
	$stmt->execute();
}

?>