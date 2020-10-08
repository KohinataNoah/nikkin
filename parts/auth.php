<?php

d('ログイン認証を行います。');
if (!empty($_SESSION['user_id'])) {
  d('ログイン済みユーザーです。');
  if ((int)$_SESSION['user_id'] === 1) {
    d('ゲストログインユーザーです。');
    $guestFlg = true;
  }
  if ($_SESSION['login_time'] + $_SESSION['login_limit'] < time()) {
    d('ログイン有効期限を超過しています。');
    session_destroy();
    d('ログイン状態を消去しました。');
    d('ログインページへ移動します。');
    header("Location:login.php");
    exit;
  } else {
    d('ログイン有効期限内のユーザーです。');
    d('ログイン有効期限を更新します。');
    $_SESSION['login_time'] = time();
    if (basename($_SERVER['PHP_SELF']) === 'login.php') {
      d('マイページへ移動します。');
      header("Location:mypage.php");
      exit;
    }
  }
} else {
  d('……未ログインユーザーです。');
  if (basename($_SERVER['PHP_SELF']) == 'signup.php') {
    // なにもしない。この回避方法はスマートではないが思いつきませんでした。
  } elseif (basename($_SERVER['PHP_SELF']) !== 'login.php') {
    d('ログインページに移動します。');
    header("Location:login.php");
    exit;
  }
}
