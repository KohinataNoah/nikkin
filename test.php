<?php

$siteTtl = 'テスト';
// 共通関数読み込み
require('parts/function.php');

// ログイン認証
require('parts/auth.php');

try {
  $dbh = connectDB();
  $sql = 'SELECT id, routine, checked , update_date FROM routine WHERE user_id = :user_id AND delete_flg = 0';
  $data = array(':user_id' => $_SESSION['user_id']);
  $stmt = queryPost($dbh, $sql, $data);
  $dbRoutineData = $stmt->fetchAll();
  var_dump($dbRoutineData);

  $sql = 'SELECT id, diary FROM diary WHERE user_id = :user_id AND delete_flg = 0';
  $stmt = queryPost($dbh, $sql, $data);
  $dbDiaryData = $stmt->fetchAll();
  var_dump($dbDiaryData);

  $sql = 'SELECT username, target FROM users WHERE id = :id';
  $data = array(':id' => $_SESSION['user_id']);
  $stmt = queryPost($dbh, $sql, $data);
  $dbUserData = $stmt->fetch();
  var_dump($dbUserData);
} catch (Exception $e) {
  getErrorLog();
}
$dbRoutineDataNum = count($dbRoutineData);
d('$dbRoutineDataNum:' . print_r($dbRoutineDataNum, true));
var_dump($dbRoutineDataNum);
for ($i = 0; $i < $dbRoutineDataNum; $i++) {
  $routineDate[$i] = date('Y-m-d', strtotime($dbRoutineData[$i]['update_date']));
}
var_dump($routineDate);
