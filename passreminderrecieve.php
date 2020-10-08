<?php

$siteTtl = '認証キー入力ページ';


// 共通関数読み込み
require('parts/function.php');
// 画面表示ログ吐き出し
dStart();

if (empty($_SESSION['auth_key'])) {
  d('セッションのauth_keyがありません。トップページへ移動します。');
  header("Location:index.php");
  exit;
}

if ($_POST) {
  d('POST送信を確認しました。');
  $key = $_POST['key'];
  checkBlank($key, 'key');
  if (empty($errMsg)) {
    d('空白チェックOK。');
    checkHalf($key, 'key');
    if ($key !== $_SESSION['auth_key']) {
      $errMsg['key'] = '認証キーが違います';
    }
    if (time() > $_SESSION['auth_key_limit']) {
      $errMsg['common'] = '認証キーの使用期限が過ぎています。再度認証キーの発行をお願いします。';
    }
  }
  if (empty($errMsg)) {
    d('バリデーションチェックOK。');
    $pass = makeRandKey();
    d('新しいパスワード：' . print_r($pass, true));
    try {
      $dbh = connectDB();
      $sql = 'UPDATE users SET password = :password WHERE email = :email AND delete_flg = 0';
      $data = array(':password' => password_hash($pass, PASSWORD_DEFAULT), ':email' => $_SESSION['auth_email']);
      $stmt = queryPost($dbh, $sql, $data);
      if ($stmt) {
        d('パスワードを再設定しました。');
        $from = 'info@nikkin.com';
        $to = $_SESSION['auth_email'];
        $subject = 'パスワード再発行完了 | NIKKIN';
        $comment = <<<EOT
本メールアドレス宛にパスワードの再発行をいたしました。
下記のURLにて再発行パスワードをご入力いただき、ログインください。

ログインページ：
再発行パスワード：{$pass}

//////////////////////////////
NIKKINカスタマーセンター
Email info@nikkin.com
//////////////////////////////
EOT;
        sendMail($from, $to, $subject, $comment);
        session_unset();
        $_SESSION['msg'] = 'メールを送信しました。';
      }
    } catch (Exception $e) {
      getErrorLog();
    }
  }
}

// 画面終了ログ吐き出し
dStop();

// head,header呼び出し
include('parts/head.php');
include('parts/header.php'); ?>

<div class="l_cont hp_1col">
  <div class="l_cont_inner">
    <main class="l_main">
      <div class="l_main_inner">
        <form action="" method="post" class="b_formUnit">
          <span class="is_err"><?= getErrMsg('common') ?></span><!-- /.is_err -->
          <p>パスワード再発行用の認証キー入力してください。</p>
          <label class="b_form">
            <h3 class="e_headingLv4">認証キー</h3><!-- /.e_headingLv3 -->
            <span class="b_formMsg">
              <?= getErrMsg('key') ?>
            </span><br>
            <input class="b_formText" type="text" name="key" placeholder="認証キー">
          </label><!-- /.b_form -->
          <div class="e_btn_wrapper">
            <input type="submit" value="送信" class="e_btn">
          </div><!-- /.e_btn_wrapper -->
          <input type="hidden" name="email" value="<??>">
        </form><!-- /.b_form -->
      </div><!-- /.l main_inner -->
    </main><!-- /.l_main -->
    <?php include('parts/sidebar.php'); ?>
  </div><!-- /.l_cont_inner -->
</div><!-- /.l_cont -->
<?php
include('parts/footer.php');
