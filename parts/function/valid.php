<?php

// 空白チェック
function checkBlank($s, $k)
// {
//   if (empty($s)) {
//     global $errMsg;
//     $errMsg[$k] = MSG01;
//   }
// }
// ０をOKにしたいため、以下に変更
{
  if ($s === '') {
    global $errMsg;
    $errMsg[$k] = MSG01;
  }
}
// 半角英数字チェック
function checkHalf($s, $k)
{
  if (!preg_match('/^[a-zA-Z0-9]+$/', $s)) {
    global $errMsg;
    $errMsg[$k] = MSG02;
  }
}
// 半角数字チェック
function checkNum($s, $k)
{
  if (!preg_match('/^[0-9]+$/', $s)) {
    global $errMsg;
    $errMsg[$k] = MSG10;
  }
}
// Eメール形式チェック
function checkEmailForm()
{
  global $errMsg, $email;
  if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
    $errMsg['email'] = MSG03;
  }
}
// 最小文字数チェック
function checkMinLen($s, $k, $m = 8)
{
  if (mb_strlen($s) < $m) {
    global $errMsg;
    $errMsg[$k] = $m . MSG04;
  }
}
// 最大文字数チェック
function checkMaxLen($s, $k, $m = 255)
{
  if (mb_strlen($s) > $m) {
    global $errMsg;
    $errMsg[$k] = $m . MSG05;
  }
}
// メールアドレス重複チェック
function checkEmailDub()
{
  try {
    d('Eメール重複チェックを行います。');
    global $email, $errMsg;
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $email);
    $stmt = queryPost($dbh, $sql, $data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!empty($result['email'])) {
      $errMsg['email'] = MSG06;
    }
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
    $errMsg['common'] = MSG07;
  }
}
// 同値チェック
function checkSame($s, $s2, $k, $msg = MSG08)
{
  if ($s !== $s2) {
    global $errMsg;
    $errMsg[$k] = $msg;
  }
}
// メールアドレスチェック
function checkEmail()
{
  global $email;
  checkMaxLen($email, 'email');
  checkEmailForm();
  checkEmailDub();
}
// パスワードチェック
function checkPass($s, $k)
{
  checkHalf($s, $k);
  checkMaxLen($s, $k);
  checkMinLen($s, $k);
}
