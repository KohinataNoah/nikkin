<?php

$siteTtl = 'マイページ';
// 共通関数読み込み
require('parts/function.php');
d('メッセージ：' . print_r($_SESSION['msg'], true));
// 画面表示ログ吐き出し
dStart();

// ログイン認証
require('parts/auth.php');

try {
  $dbh = connectDB();
  $sql = 'SELECT id, routine, checked , update_date FROM routine WHERE user_id = :user_id AND delete_flg = 0';
  $data = array(':user_id' => $_SESSION['user_id']);
  $stmt = queryPost($dbh, $sql, $data);
  $dbRoutineData = $stmt->fetchAll();
  d('$dbroutinedata:' . print_r($dbRoutineData, true));

  $sql = 'SELECT id, diary FROM diary WHERE user_id = :user_id AND delete_flg = 0';
  $stmt = queryPost($dbh, $sql, $data);
  $dbDiaryData = $stmt->fetchAll();

  $sql = 'SELECT username, target FROM users WHERE id = :id';
  $data = array(':id' => $_SESSION['user_id']);
  $stmt = queryPost($dbh, $sql, $data);
  $dbUserData = $stmt->fetch();
  d('$dbUserData:' . print_r($dbUserData, true));
} catch (Exception $e) {
  getErrorLog();
}
$dbRoutineDataNum = count($dbRoutineData);
d('$dbRoutineDataNum:' . print_r($dbRoutineDataNum, true));
for ($i = 0; $i < $dbRoutineDataNum; $i++) {
  $routineDate[$i] = date('Y-m-d', strtotime($dbRoutineData[$i]['update_date']));
  if ($routineDate[$i] !== date('Y-m-d') && $routineDate[$i]['checked'] = 1) {
    d('日付が変わりました。日課を更新します。');
    try {
      $sql = 'UPDATE routine SET checked = 0 WHERE id = :id';
      $data = array(':id' => $dbRoutineData[$i]['id']);
      $stmt = queryPost($dbh, $sql, $data);
      $dbRoutineData[$i]['checked'] = 0;
    } catch (Exception $e) {
      getErrorLog();
    }
  }
}
d('$routineDate:' . print_r($routineDate, true));



// 画面終了ログ吐き出し
dStop();

// head,header呼び出し
include('parts/head.php');
include('parts/header.php'); ?>

<div class="l_cont">
  <div class="l_pageTtl">
    <h2 class="b_pageTtl">
      <span>マイページ</span>
    </h2><!-- /.b_pageTtl -->
    <span class="is_err"><?= getErrMsg('common') ?></span><!-- /.is_err -->
  </div><!-- /.l_pageTtl -->
  <div class="l_cont_inner">
    <main class="l_main">
      <div class="l_main_inner">
        <section class="b_media">
          <div class="b_media_inner">
            <h3 class="e_headingLv3">
              <span>今日の日課</span>
            </h3><!-- /.e_headingLv3 -->
            <ul class="b_mediaText">
              <?= (empty($dbRoutineData)) ? 'まだ日課が登録されていません。' : ''; ?>
              <?php foreach ($dbRoutineData as $key => $val) : ?>
                <li class="e_routineList js_check <?= ($val['checked']) ? 'js_check__checked' : ''; ?>" data-routineid="<?= $val['id'] ?>">
                  <i class="fas fa-check-circle"></i>
                  <div class="e_routineListText"><?= $val['routine'] ?></div><!-- /.e_routineListText -->
                </li><!-- /.e_routineList -->
              <?php endforeach; ?>
            </ul><!-- /.b_mediaText -->
          </div><!-- /.b_media_inner -->
        </section><!-- /.b_media -->
        <section class="b_media">
          <div class="b_media_inner">
            <h3 class="e_headingLv3">
              <span>目標！</span>
            </h3><!-- /.e_headingLv3 -->
            <div class="b_mediaText">
              <p><?= (!empty($dbUserData['target'])) ? $dbUserData['target'] : 'まだ目標が登録されていません。'; ?></p>

            </div><!-- /.b_mediaText -->
          </div><!-- /.b_media_inner -->
        </section><!-- /.b_media -->
      </div><!-- /.l main_inner -->
    </main><!-- /.l_main -->
    <?php include('parts/sidebar.php'); ?>
  </div><!-- /.l_cont_inner -->
</div><!-- /.l_cont -->
<?php
include('parts/footer.php');
