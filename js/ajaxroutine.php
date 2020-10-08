<?php

require('../parts/function.php');
$siteTtl = 'ajax処理';

dStart();
d('$_POST:' . print_r($_POST, true));

if (isset($_POST['routineId'])) {
  d('POST送信を確認しました。');
  $routineId = $_POST['routineId'];
  try {
    $dbh = connectDB();
    $sql = 'SELECT checked FROM routine WHERE id = :id AND user_id = :user_id';
    $data = array(':id' => $routineId, ':user_id' => $_SESSION['user_id']);
    $stmt = queryPost($dbh, $sql, $data);
    $isChecked = $stmt->fetch();
    if ($isChecked['checked']) {
      $sql = 'UPDATE routine SET checked = 0 WHERE id = :id AND user_id = :user_id';
      $stmt = queryPost($dbh, $sql, $data);
    } else {
      $sql = 'UPDATE routine SET checked = 1 WHERE id = :id AND user_id = :user_id';
      $stmt = queryPost($dbh, $sql, $data);
    }
    d('$isChecked:' . print_r($isChecked, true));


    // $sql = 'UPDATE checked = 1 FROM routine WHERE id = :routine_id AND user_id = :user_id';
    // $stmt = queryPost($dbh, $sql, $data);
    // if ($stmt) {
    //   d('チェックしました。');
    // }
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
    header("Location:mypage.php");
    exit;
  }
}
