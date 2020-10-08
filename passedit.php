<?php

$siteTtl = 'パスワード変更';
// 共通関数読み込み
require('parts/function.php');
// 画面表示ログ吐き出し
dStart();

// ログイン認証
require('parts/auth.php');
$dbFormData = getUser($_SESSION['user_id']);

if (!empty($_POST)) {

  d('POST送信を確認しました。');
  $passOld = $_POST['passOld'];
  $passNew = $_POST['passNew'];
  $passNewRe = $_POST['passNewRe'];
  checkBlank($passOld, 'passOld');
  checkBlank($passNew, 'passNew');
  checkBlank($passNewRe, 'passNewRe');

  if (empty($errMsg)) {

    d('空白チェックOK。');
    checkPass($passOld, 'passOld');
    checkPass($passNew, 'passNew');
    checkPass($passNewRe, 'passNewRe');
    checkSame($passNew, $passNewRe, 'passNewRe');
    if (!password_verify($passOld, $dbFormData['password'])) {
      $errMsg['passOld'] = 'パスワードが違います';
    }
    if ($passOld === $passNew) {
      $errMsg['passNew'] = MSG11;
    }
    if (empty($errMsg)) {
      d('バリデーションチェックOK。');
      d('パスワード変更を行います。');
      exitGuest();
      try {
        $dbh = connectDB();
        $sql = 'UPDATE users SET password = :password WHERE id = :user_id';
        $data = array(':password' => password_hash($passNew, PASSWORD_DEFAULT), ':user_id' => $_SESSION['user_id']);
        $stmt = queryPost($dbh, $sql, $data);
        if ($stmt) {
          d('パスワード変更が完了しました。');
          $_SESSION['msg'] = 'パスワードを変更しました。';
          d('新しいパスワード：' . print_r($passNew));
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
  $passOld = '';
  $passNew = '';
  $passNewRe = '';
}

// 画面終了ログ吐き出し
dStop();

// head,header呼び出し
include('parts/head.php');
include('parts/header.php'); ?>

<div class="l_cont">
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
          <label class="b_form <?= isErr('passOld') ?>">
            <h3 class="e_headingLv4">現在のパスワード</h3><!-- /.e_headingLv3 -->
            <span class="b_formMsg">
              <?= getErrMsg('passOld') ?>
            </span><br>
            <input class="b_formText" type="password" name="passOld" placeholder="現在のパスワード">
          </label><!-- /.b_form -->
          <label class="b_form <?= isErr('passNew') ?>">
            <h3 class="e_headingLv4">新しいパスワード</h3><!-- /.e_headingLv3 -->
            <span class="b_formMsg">
              <?= getErrMsg('passNew') ?>
            </span><br>
            <input class="b_formText" type="password" name="passNew" placeholder="新しいパスワード">
          </label><!-- /.b_form -->
          <label class="b_form <?= isErr('passNewRe') ?>">
            <h3 class="e_headingLv4">新しいパスワード（再入力）</h3><!-- /.e_headingLv3 -->
            <span class="b_formMsg">
              <?= getErrMsg('passNewRe') ?>
            </span><br>
            <input class="b_formText" type="password" name="passNewRe" placeholder="新しいパスワード（再入力）">
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
