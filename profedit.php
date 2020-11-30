<?php

$siteTtl = 'プロフィール編集';
// 共通関数読み込み
require('parts/function.php');
// 画面表示ログ吐き出し
dStart();

// ログイン認証
require('parts/auth.php');
$dbFormData = getUser($_SESSION['user_id']);
d('ユーザー情報の中身：' . print_r($dbFormData, true));

if (!empty($_POST)) {
  d('POST送信を確認しました。');
  $name = $_POST['name'];
  $target = $_POST['target'];
  if ($name !== $dbFormData['username']) {
    checkBlank($name, 'name');
  }
  if ($target !== $dbFormData['target']) {
    checkBlank($target, 'target');
  }
  if (empty($errMsg)) {
    d('空白チェックOK。');
    // exitGuest();
    try {
      $dbh = connectDB();
      $sql = 'UPDATE users SET username = :username,target = :target WHERE id = :id';
      $data = array(':username' => $name, ':target' => $target, ':id' => $_SESSION['user_id']);
      $stmt = queryPost($dbh, $sql, $data);
      if ($stmt) {
        d('プロフィールを編集しました。');
        $userDataAfter = getUser($_SESSION['user_id']);
        d('変更後のユーザー情報：' . print_r($userDataAfter, true));
        $_SESSION['msg'] = 'プロフィールを編集しました。';
        d('セッション変数の中身：' . print_r($_SESSION, true));
        header("Location:mypage.php");
        exit;
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

<div class="l_cont">
  <div class="l_pageTtl">
    <h2 class="b_pageTtl">
      <span>プロフィール編集</span>
    </h2><!-- /.b_pageTtl -->
  </div><!-- /.l_pageTtl -->
  <div class="l_cont_inner">
    <main class="l_main">
      <div class="l_main_inner">
        <form action="" method="post" class="b_formUnit">
          <span class="is_err"><?= getErrMsg('common') ?></span><!-- /.is_err -->
          <label class="b_form <?= isErr('name') ?>">
            <h3 class="e_headingLv4">名前（ニックネーム）</h3><!-- /.e_headingLv3 -->
            <span class="b_formMsg">
              <?= getErrMsg('name') ?>
            </span><br>
            <input class="b_formText" type="text" name="name" placeholder="名前（ニックネーム）" value="<?= $dbFormData['username']; ?>">
          </label><!-- /.b_form -->
          <label class="b_form <?= isErr('target') ?>">
            <h3 class="e_headingLv4">目標</h3><!-- /.e_headingLv3 -->
            <span class="b_formMsg">
              <?= getErrMsg('target') ?>
            </span><br>
            <textarea name="target" id="" cols="30" rows="10" class="b_formTextarea" placeholder="例：３月２０日までに６０キロまで痩せる！"><?= $dbFormData['target'] ?></textarea><!-- /#.b_formTextarea -->
          </label><!-- /.b_form -->
          <div class="e_btn_wrapper">
            <input type="submit" value="登録" class="e_btn js_btn">
          </div><!-- /.e_btn_wrapper -->
        </form>
      </div><!-- /.l main_inner -->
    </main><!-- /.l_main -->
    <?php include('parts/sidebar.php'); ?>
  </div><!-- /.l_cont_inner -->
</div><!-- /.l_cont -->
<?php
include('parts/footer.php');
