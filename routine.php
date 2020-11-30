<?php


$siteTtl = '筋トレ日記';
// 共通関数読み込み
require('parts/function.php');
// 画面表示ログ吐き出し
dStart();

try {
  $dbh = connectDB();
  $sql = 'SELECT id, routine, checked FROM routine WHERE user_id = :user_id AND delete_flg = 0';
  $data = array(':user_id' => $_SESSION['user_id']);
  $stmt = queryPost($dbh, $sql, $data);
  $dbRoutineData = $stmt->fetchAll();
  // $result = $stmt->fetchAll();
  d('$dbRoutineDataの中身：' . print_r($dbRoutineData, true));
} catch (Exception $e) {
  getErrorLog();
}


// ログイン認証
require('parts/auth.php');

// 画面終了ログ吐き出し
dStop();

// head,header呼び出し
include('parts/head.php');
include('parts/header.php'); ?>

<div class="l_cont">
  <div class="l_pageTtl">
    <h2 class="b_pageTtl">
      <span>筋トレ日記</span>
    </h2><!-- /.b_pageTtl -->
    <span class="is_err"><?= getErrMsg('common') ?></span><!-- /.is_err -->
  </div><!-- /.l_pageTtl -->
  <div class="l_cont_inner">
    <main class="l_main">
      <div class="l_main_inner">
        <section class="b_media">
          <p class="b_mediaText">クリックすると編集できます。新規作成は<a href="routineedit.php">コチラ</a></p><!-- /.b_mediaText -->
          <?php foreach (array_reverse($dbRoutineData) as $key => $val) :  ?>
            <a href="routineedit.php?r_id=<?= $val['id'] ?>" class="e_routineList" style="display:block">
              <i class="fas fa-check-circle"></i>
              <div class="e_routineListText"><?= $val['routine'] ?></div><!-- /.e_routineListText -->
            </a><!-- /.e_routineList -->
          <?php endforeach; ?>
        </section><!-- /.b_media -->
      </div><!-- /.l main_inner -->
    </main><!-- /.l_main -->
    <?php include('parts/sidebar.php'); ?>
  </div><!-- /.l_cont_inner -->
</div><!-- /.l_cont -->
<?php
include('parts/footer.php');
