<?php

$siteTtl = 'ログイン';

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
  $passSave = $_POST['passSave'];
  checkBlank($email, 'email');
  checkBlank($pass, 'pass');
  if (empty($errMsg)) {
    d('空白チェックOK。');
    checkEmail();
    checkPass($pass, 'pass');
    checkPass($pass, 'pass');
    if (empty($errMsg)) {
      d('バリデーションチェックOK。');
      d('ログインを行います。');
      try {
        $dbh = connectDB();
        $sql = 'SELECT password,id FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($stmt) {
          d('ログインクエリ成功しました。');
          if (!empty($result) && password_verify($pass, array_shift($result))) {
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
          } else {
            d('パスワードが違います。');
            $errMsg['common'] = 'Eメールアドレスまたはパスワードが違います。';
          }
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
  $passSave = '';
}

// 画面終了ログ吐き出し
dStop();

// head,header呼び出し
include('parts/head.php');
include('parts/header.php'); ?>

<div class="l_cont hp_1col">
  <div class="l_pageTtl">
    <h2 class="b_pageTtl">
      <span>ログイン</span>
    </h2><!-- /.b_pageTtl -->
  </div><!-- /.l_pageTtl -->
  <div class="l_cont_inner">
    <main class="l_main">
      <div class="l_main_inner">
        <form action="" method="post" class="b_formUnit">
          <span class="is_err"><?= getErrMsg('common') ?></span><!-- /.is_err -->
          <label class="b_form <?= isErr('email') ?>">
            <h3 class="e_headingLv4">メールアドレス</h3><!-- /.e_headingLv3 -->
            <span class="b_formMsg">
              <?= getErrMsg('email') ?>
            </span><br>
            <input class="b_formText" type="text" name="email" placeholder="メールアドレス">
          </label><!-- /.b_form -->
          <label class="b_form <?= isErr('pass') ?>">
            <h3 class="e_headingLv4">パスワード</h3><!-- /.e_headingLv3 -->
            <span class="b_formMsg">
              <?= getErrMsg('pass') ?>
            </span><br>
            <input class="b_formText" type="password" name="pass" placeholder="パスワード">
          </label><!-- /.b_form -->
          <label class="b_form">
            ログインしたままにする
            <input type="checkbox" name="passSave">
          </label><!-- /.b_form -->
          <div class="e_btn_wrapper">
            <input type="submit" value="ログイン" class="e_btn">
          </div><!-- /.e_btn_wrapper -->
          <p>
            パスワードを忘れてしまった方は<a href="passreminder.php">コチラ</a>
          </p>
        </form><!-- /.b_formUnit -->
      </div><!-- /.l main_inner -->
    </main><!-- /.l_main -->
    <?php include('parts/sidebar.php'); ?>
  </div><!-- /.l_cont_inner -->
</div><!-- /.l_cont -->
<?php
include('parts/footer.php');
