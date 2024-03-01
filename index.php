<?php
header('Content-Type: application/json');
$airport_ids = explode(',', substr($_SERVER['REQUEST_URI'], 1));
if (empty($airport_ids)) die(json_encode(['error' => 'No airport IDs provided']));
foreach ($airport_ids as $airport_id) if (!preg_match('/^[a-zA-Z0-9-]+$/', $airport_id)) die(json_encode(['error' => 'Invalid airport ID']));
$results = [];
$sql = mysqli_connect('127.0.0.1', 'aifr', 'aifr', 'NASR_INDEX');
if (mysqli_connect_errno()) die(json_encode(['error' => 'Database connection error']));
$query = "SELECT `name` FROM `INDEX` WHERE `preview` = 0 ORDER BY `id` DESC LIMIT 1";
$result = mysqli_query($sql, $query);
if (!$result) die(json_encode(['error' => 'Database query error']));
$row = mysqli_fetch_assoc($result);
$nasr_db = $row['name'];
mysqli_select_db($sql, $nasr_db);
if (mysqli_errno($sql)) die(json_encode(['error' => 'Database selection error']));
foreach ($airport_ids as $airport_id) {
    $airport_id = strtoupper(trim($airport_id));
    if (strlen($airport_id) == 4 && substr($airport_id, 0, 1) == 'K') $airport_id = substr($airport_id, 1);
    $airport_id = mysqli_real_escape_string($sql, $airport_id);
    $query = "SELECT * FROM `FRQ` WHERE `SERVICED_FACILITY` = '$airport_id' AND `FREQ_USE` = 'CTAF'";
    $result = mysqli_query($sql, $query);
    if (!$result) die(json_encode(['error' => 'Database query error']));
    $row = mysqli_fetch_assoc($result);
    if (!$row) die(json_encode(['error' => 'Airport not found']));
    $results[$airport_id] = $row;
}
echo json_encode($results, JSON_PRETTY_PRINT);
