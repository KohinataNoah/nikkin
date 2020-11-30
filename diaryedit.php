<?php

// 体重の正規表現：/^[0-9]{1,3}[.][0-9]{1}$/u

$siteTtl = 'マイページ';
// 共通関数読み込み
require('parts/function.php');
// 画面表示ログ吐き出し
dStart();

// ログイン認証
require('parts/auth.php');

$diaryId = (!empty($_GET['d_id'])) ? $_GET['d_id'] : '';
$dbFormData = (!empty($diaryId)) ? getDiary($_SESSION['user_id'], $diaryId) : '';
$editFlg = (empty($dbFormData)) ? false : true;

d('商品ID' . print_r($diaryId, true));
d('フォーム用DBデータ：' . print_r($dbFormData, true));

// パラメータ改ざんチェック（GETパラメータはあるが改ざんされている（URLを操作された）場合、正しい商品データが取れないのでマイページへ遷移させる）
if (!empty($diaryId) && empty($dbFormData)) {
  d('GETパラメータの日記IDが違います。マイページへ遷移します。');
  header("Location:mypage.php");
  exit;
}

if (!empty($_POST)) {
  d('POST送信を確認しました。');
  d('POST情報：' . print_r($_POST, true));
  $diary = $_POST['diary'];
  $delete = (!empty($_POST['delete'])) ? true : false;
  $delete_check = (!empty($_POST['delete_check'])) ? true : false;
  // バリデーション
  if (empty($dbFormData)) {
    checkBlank($diary, 'diary');
  } else {
    // 編集の場合
    if ($dbFormData['diary'] !== $diary) {
      checkBlank($diary, 'diary');
    }
    if ($delete && !$delete_check) {
      $errMsg['delete_check'] = '削除するにはチェックを入れてください。';
    }
  }
  if (empty($errMsg)) {
    d('バリデーションチェックOKです。');
    // exitGuest();
    try {
      if ($editFlg) {
        if ($delete) {
          d('削除します。');
          $dbh = connectDb();
          $sql = 'UPDATE diary SET delete_flg = 1 WHERE user_id = :user_id AND id = :diary_id';
          $data = array(':user_id' => $_SESSION['user_id'], ':diary_id' => $diaryId);
          $stmt = queryPost($dbh, $sql, $data);
          if ($stmt) {
            d('削除しました。');
            $_SESSION['msg'] = '日記を削除しました。';
            header("Location:diary.php");
            exit;
          }
        }
        d('更新です。');
        // 日記の更新の場合
        $dbh = connectDB();
        $sql = 'UPDATE diary SET diary = :diary WHERE user_id = :user_id AND id = :diary_id';
        $data = array(':diary' => $diary, ':user_id' => $_SESSION['user_id'], ':diary_id' => $diaryId);
        $stmt = queryPost($dbh, $sql, $data);
        if ($stmt) {
          d('日記を更新しました。');
          $_SESSION['msg'] = '日記を更新しました！';
          $diaryAfter = getDiary($_SESSION['user_id'], $diaryId);
          d('新しい日記の中身：' . print_r($diaryAfter, true));
          header("Location:diary.php");
          exit;
        }
      } else {
        // 新規の日記の場合
        d('日記の新規登録です。');
        $dbh = connectDB();
        $sql = 'INSERT INTO diary( user_id, diary,create_date) VALUES ( :user_id, :diary, :create_date )';
        $data = array(':user_id' => $_SESSION['user_id'], ':diary' => $diary, ':create_date' => date('Y-m-d H:i:s'));
        $stmt = queryPost($dbh, $sql, $data);
        if ($stmt) {
          $_SESSION['msg'] = '日記を記入しました！';
          header("Location:diary.php");
          exit;
        }
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
      <span><?= ($editFlg) ?  '日記編集ページ' :  '日記記入ページ'; ?></span>
    </h2><!-- /.b_pageTtl -->
    <span class="is_err"><?= getErrMsg('common') ?></span><!-- /.is_err -->
  </div><!-- /.l_pageTtl -->
  <div class="l_cont_inner">
    <main class="l_main">
      <div class="l_main_inner">
        <form action="" method="post" class="b_formUnit">
          <span class="is_err"><?= getErrMsg('common') ?></span><!-- /.is_err -->
          <label class="b_form <?= isErr('diary') ?>">
            <h3 class="e_headingLv4">
              <?= (!empty($dbFormData)) ? date('Y年n月j日', strtotime($dbFormData['create_date'])) : date('Y年n月j日'); ?> の 日記
            </h3><!-- /.e_headingLv3 -->
            <span class="b_formMsg">
              <?= getErrMsg('diary') ?>
            </span><br>
            <textarea name="diary" id="" cols="50" rows="10" class="b_formTextarea"><?= getFormData('diary') ?></textarea><!-- /#.b_formTextarea --><br>
            <div class="e_btn_wrapper">
              <input type="submit" value="登録" class="e_btn">
            </div><!-- /.e_btn_wrapper -->
            <?php if ($editFlg) : ?>
              <span class="b_formMsg">削除するにはコチラのチェックボックスにチェックを入れてください。</span><br>
              <span class="is_err"><?= getErrMsg('delete_check') ?></span><!-- /.is_err --><br>
              <input type="checkbox" name="delete_check">
              <div class="e_btn_wrapper">
                <input type="submit" name="delete" value="削除" class="e_btn">
              </div><!-- /.e_btn_wrapper -->
            <?php endif; ?>
          </label><!-- /.b_formUnit -->
        </form><!-- /.b_form -->
      </div><!-- /.l main_inner -->
    </main><!-- /.l_main -->
    <?php include('parts/sidebar.php'); ?>
  </div><!-- /.l_cont_inner -->
</div><!-- /.l_cont -->
<?php
include('parts/footer.php');
