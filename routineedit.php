<?php

// 共通関数読み込み
require('parts/function.php');
// 画面表示ログ吐き出し
dStart();

// ログイン認証
require('parts/auth.php');
d('$_GET:' . print_r($_GET, true));

$routineId = (!empty($_GET['r_id'])) ? $_GET['r_id'] : '';
$dbFormData = (!empty($routineId)) ? getRoutine($_SESSION['user_id'], $routineId) : '';
$editFlg = (empty($dbFormData)) ? false : true;

$siteTtl = ($editFlg) ? '日課編集ページ' : '日課登録ページ';

d('日課ID' . print_r($routineId, true));
d('フォーム用DBデータ：' . print_r($dbFormData, true));

// パラメータ改ざんチェック（GETパラメータはあるが改ざんされている（URLを操作された）場合、正しい商品データが取れないのでマイページへ遷移させる）
if (!empty($routineId) && empty($dbFormData)) {
  d('GETパラメータの日課IDが違います。マイページへ遷移します。');
  header("Location:mypage.php");
  exit;
}

if (!empty($_POST)) {
  d('POST送信を確認しました。');
  d('POST情報：' . print_r($_POST, true));
  // 変数にユーザー情報を代入
  $routine = $_POST['routine'];
  $delete = (!empty($_POST['delete'])) ?  true : false;
  $delete_check = (!empty($_POST['delete_check'])) ? true : false;

  // バリデーション
  if (empty($dbFormData)) {
    // 新規の場合
    checkBlank($routine, 'routine');
    checkMaxLen($routine, 'routine');
  } else {
    // 編集の場合
    if ($dbFormData['routine'] !== $routine) {
      checkBlank($routine, 'routine');
      checkMaxLen($routine, 'routine');
    }
    if ($delete && !$delete_check) {
      $errMsg['delete_check'] = '削除するにはチェックを入れてください。';
    }
  }
  if (empty($errMsg)) {
    d('バリデーションチェックOK。');
    // バリデーションOK。
    exitGuest();

    try {
      if ($editFlg) {
        if ($delete) {
          d('削除します。');
          $dbh = connectDb();
          $sql = 'UPDATE routine SET delete_flg = 1 WHERE user_id = :user_id AND id = :routine_id';
          $data = array(':user_id' => $_SESSION['user_id'], ':routine_id' => $routineId);
          $stmt = queryPost($dbh, $sql, $data);
          if ($stmt) {
            d('削除されました。');
            $_SESSION['msg'] = '削除しました！';
            header("Location:routine.php");
            exit;
          }
        } else {
          d('更新です。');
          $dbh = connectDB();
          $sql = 'UPDATE routine SET routine = :routine WHERE user_id = :user_id AND id = :routine_id';
          $data = array(':routine' => $routine, ':user_id' => $_SESSION['user_id'], ':routine_id' => $routineId);
          $stmt = queryPost($dbh, $sql, $data);
          if ($stmt) {
            d('日課を更新しました。');
            $_SESSION['msg'] = '日課を更新しました。';
            $routineAfter = getRoutine($_SESSION['user_id'], $routineId);
            d('新しい日記の中身：' . print_r($routineAfter, true));
            header("Location:routine.php");
            exit;
          }
        }
      } else {
        // 新規の日課の場合
        d('日課の新規登録です。');
        $dbh = connectDB();
        $sql = 'INSERT INTO routine(user_id,routine,create_date,update_date) VALUES (:user_id, :routine, :create_date, :update_date)';
        $data = array(':user_id' => $_SESSION['user_id'], ':routine' => $routine, ':create_date' => date('Y-m-d H:i:s'), ':update_date' => date('Y-m-d H:i:s'));
        $stmt = queryPost($dbh, $sql, $data);
        if ($stmt) {
          $_SESSION['msg'] = '日課を登録しました！';
          header("Location:routine.php");
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
      <span><?= ($editFlg) ? '日課編集ページ' : '日課記入ページ'; ?></span>
    </h2><!-- /.b_pageTtl -->
    <span class="is_err"><?= getErrMsg('common') ?></span><!-- /.is_err -->
  </div><!-- /.l_pageTtl -->
  <div class="l_cont_inner">
    <main class="l_main">
      <div class="l_main_inner">
        <form action="" method="post" class="b_formUnit">
          <label class="b_form <?= isErr('routine') ?>">
            <h3 class="e_headingLv4">日課</h3><!-- /.e_headeingLv4 -->
            <span class="b_formMsg">
              <?= getErrMsg('routine') ?>
            </span><br>
            <input type="text" name="routine" class="b_formText" value="<?= $dbFormData['routine'] ?>">
          </label><!-- /.b_form -->
          <div class="e_btn_wrapper">
            <input type="submit" value="<?= ($editFlg) ? '更新' : '登録'; ?>" class="e_btn">
          </div><!-- /.e_btn_wrapper -->
          <?php if ($editFlg) : ?>
            <span class="b_formMsg">削除するにはコチラのチェックボックスにチェックを入れてください。</span><br>
            <span class="is_err"><?= getErrMsg('delete_check') ?></span><!-- /.is_err --><br>
            <input type="checkbox" name="delete_check">
            <div class="e_btn_wrapper">
              <input type="submit" name="delete" value="削除" class="e_btn">
            </div><!-- /.e_btn_wrapper -->
          <?php endif; ?>
        </form><!-- /.b_formUnit -->
      </div><!-- /.l main_inner -->
    </main><!-- /.l_main -->
    <?php include('parts/sidebar.php'); ?>
  </div><!-- /.l_cont_inner -->
</div><!-- /.l_cont -->
<?php
include('parts/footer.php');
