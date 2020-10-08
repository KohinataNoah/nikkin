<?php

$siteTtl = '退会ページ';
// 共通関数読み込み
require('parts/function.php');
// 画面表示ログ吐き出し
dStart();

// ログイン認証
require('parts/auth.php');

if (!empty($_POST)) {
  d('ポスト送信を確認しました。');
  exitGuest();
  try {
    d('退会します。');
    // データが増えたらさらに追加する
    $dbh = connectDB();
    $sql = 'UPDATE users SET delete_flg = 1 WHERE id = :user_id';
    $sql2 = 'UPDATE diary SET delete_flg = 1 WHERE user_id = :user_id';
    $data = array(':user_id' => $_SESSION['user_id']);
    $stmt = queryPost($dbh, $sql, $data);
    $stmt2 = queryPost($dbh, $sql2, $data);
    if (!empty($stmt)) {
      d('退会しました。');
      session_destroy();
      d('トップページへ移動します。');
      header("Location:index.php");
      exit;
    }
  } catch (Exception $e) {
    getErrorLog();
  }
}

// 画面終了ログ吐き出し
dStop();

// head,header呼び出し
include('parts/head.php');
include('parts/header.php'); ?>

<div class="l_cont hp_1col">
  <div class="l_pageTtl">
    <h2 class="b_pageTtl">
      <span>退会ページ</span>
    </h2><!-- /.b_pageTtl -->
  </div><!-- /.l_pageTtl -->
  <div class="l_cont_inner">
    <main class="l_main">
      <div class="l_main_inner">
        <form action="" method="post" class="b_formUnit">
          <span class="is_err"><?= getErrMsg('common') ?></span><!-- /.is_err -->
          <h3 class="e_headingLv4">退会する</h3><!-- /.e_headingLv3 -->
          <div class="e_btn_wrapper">
            <input type="submit" value="退会する" class="e_btn" name="submit">
          </div><!-- /.e_btn_wrapper -->
        </form><!-- /.b_formUnit -->
      </div><!-- /.l main_inner -->
    </main><!-- /.l_main -->
    <?php include('parts/sidebar.php'); ?>
  </div><!-- /.l_cont_inner -->
</div><!-- /.l_cont -->
<?php
include('parts/footer.php');
