<?php

$siteTtl = 'ゲストログイン';

// 共通関数読み込み
require('parts/function.php');
// 画面表示ログ吐き出し
dStart();

try {
  $dbh = connectDB();
  $sql = 'SELECT password,id FROM users WHERE email = :email';
  $data = array(':email' => 'guestuser@guestuser.co.jp');
  $stmt = queryPost($dbh, $sql, $data);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($stmt) {
    d('ログインクエリ成功しました。');
    if (!empty($result) && password_verify('guestuserpass', array_shift($result))) {
      d('パスワード認証成功。');
      $sesLimit = 60 * 60;
      $_SESSION['login_time'] = time();
      if (!empty($passSave)) $sesLimit *= 24 * 30;
      d('ログインリミットは' . $sesLimit / 3600 . '時間です。');
      $_SESSION['login_limit'] = $sesLimit;
      $_SESSION['user_id'] = $result['id'];
      $_SESSION['msg'] = 'ログインしました！';
      header('Location:mypage.php');
      exit;
    }
  }
} catch (Exception $e) {
  getErrorLog();
  header("Location:index.php");
  exit;
}
