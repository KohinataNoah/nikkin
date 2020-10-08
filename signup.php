<?php

$siteTtl = '新規会員登録';
// 共通関数読み込み
require('parts/function.php');
// 画面表示ログ吐き出し
dStart();

// ログイン認証
require('parts/auth.php');

if (!empty($_POST)) {
  d('POST送信を確認しました。');
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $passRe = $_POST['passRe'];
  checkBlank($email, 'email');
  checkBlank($pass, 'pass');
  checkBlank($passRe, 'passRe');
  if (empty($errMsg)) {
    d('空白チェックOK。');
    checkEmail();
    checkPass($pass, 'pass');
    checkPass($pass, 'pass');
    checkSame($pass, $passRe, 'passRe');
    if (empty($errMsg)) {
      d('バリデーションチェックOK。');
      d('登録を行います。');
      try {
        $dbh = connectDB();
        $sql = 'INSERT INTO users (email,password,create_date,login_date) VALUE (:email,:password,:create_date,:login_date)';
        $data = array(':email' => $email, ':password' => password_hash($pass, PASSWORD_DEFAULT), ':create_date' => date('Y-m-d H:i:s'), ':login_date' => date('Y-m-d H:i:s'));
        $stmt = queryPost($dbh, $sql, $data);
        if ($stmt) {
          d('登録完了しました。');
          $_SESSION['user_id'] = $dbh->lastInsertId();
          $_SESSION['login_time'] = time();
          $_SESSION['login_limit'] = 60 * 60;
          header("Location:mypage.php");
          exit;
        }
      } catch (Exception $e) {
        getErrorLog();
      }
    }
  }
} else {
  d('POST送信を確認できませんでした。');
  $email = '';
  $pass = '';
  $passRe = '';
}

// 画面終了ログ吐き出し
dStop();

// head,header呼び出し
include('parts/head.php');
include('parts/header.php'); ?>

<div class="l_cont hp_1col">
  <div class="l_pageTtl">
    <h2 class="b_pageTtl">
      <span>新規会員登録</span>
    </h2><!-- /.b_pageTtl -->
  </div><!-- /.l_pageTtl -->
  <div class="l_cont_inner">
    <main class="l_main">
      <div class="l_main_inner">
        <form action="" method="post" class="b_formUnit">
          <span class="is_err"><?= getErrMsg('common') ?></span><!-- /.is_err -->
          <label class="b_form <?= isErr('email') ?>">
            <h3 class="e_headingLv4">メールアドレス<span class="e_label">必須</span><!-- /.e_label -->
            </h3><!-- /.e_headingLv3 -->
            <span class="b_formMsg">
              <?= getErrMsg('email') ?>
            </span><br>
            <input class="b_formText" type="text" name="email" placeholder="メールアドレス">
          </label><!-- /.b_form -->
          <label class="b_form <?= isErr('pass') ?>">
            <h3 class="e_headingLv4">パスワード<span class="e_label">必須</span><!-- /.e_label -->
            </h3><!-- /.e_headingLv3 -->
            <span class="b_formMsg">
              <?= getErrMsg('pass') ?>
            </span><br>
            <input class="b_formText" type="password" name="pass" placeholder="パスワード">
          </label><!-- /.b_form -->
          <label class="b_form <?= isErr('passRe') ?>">
            <h3 class="e_headingLv4">パスワード（再入力）<span class="e_label">必須</span><!-- /.e_label -->
            </h3><!-- /.e_headingLv3 -->
            <span class="b_formMsg">
              <?= getErrMsg('passRe') ?>
            </span><br>
            <input class="b_formText" type="password" name="passRe" placeholder="パスワード（再入力）">
          </label><!-- /.b_form -->
          <div class="e_btn_wrapper">
            <input type="submit" value="登録" class="e_btn js_btn">
          </div><!-- /.e_btn_wrapper -->
        </form><!-- /.b_formUnit -->
      </div><!-- /.l main_inner -->
    </main><!-- /.l_main -->
    <?php include('parts/sidebar.php'); ?>
  </div><!-- /.l_cont_inner -->
</div><!-- /.l_cont -->
<?php
include('parts/footer.php');
