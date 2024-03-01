<?php
header('Content-Type: application/json');
if (!preg_match('/^[A-Z0-9]{3,4}$/i', $_SERVER['REQUEST_URI'], $matches)) die(json_encode(['error' => 'Invalid airport ID (usage: https://ctaf.aifr.us/airport_id)']));
$sql = mysqli_connect('127.0.0.1', 'aifr', 'aifr', 'NASR_INDEX');
if (mysqli_connect_errno()) die(json_encode(['error' => 'Database connection error']));
$query = "SELECT `name` FROM `INDEX` WHERE `preview` = 0 ORDER BY `id` DESC LIMIT 1";
$result = mysqli_query($sql, $query);
if (!$result) die(json_encode(['error' => 'Database query error']));
$row = mysqli_fetch_assoc($result);
$nasr_db = $row['name'];
mysqli_select_db($sql, $nasr_db);
if (mysqli_errno($sql)) die(json_encode(['error' => 'Database selection error']));
$airport_id = strtoupper($matches[0]);
if (strlen($airport_id) == 4 && substr($airport_id, 0, 1) == 'K') $airport_id = substr($airport_id, 1);
$query = "SELECT * FROM `FRQ` WHERE `SERVICED_FACILITY` = '$airport_id' AND `FREQ_USE` = 'CTAF'";
$result = mysqli_query($sql, $query);
if (!$result) die(json_encode(['error' => 'Database query error']));
$row = mysqli_fetch_assoc($result);
if (!$row) die(json_encode(['error' => 'Airport not found']));
// return the airport info
echo json_encode($row, JSON_PRETTY_PRINT);
