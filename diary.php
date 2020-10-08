<?php


$siteTtl = '筋トレ日記';
// 共通関数読み込み
require('parts/function.php');
// 画面表示ログ吐き出し
dStart();

// ログイン認証
require('parts/auth.php');

$currentPageNum = (!empty($_GET['p'])) ? (int)$_GET['p'] : 1;
if (!is_int($currentPageNum)) {
  d('不正な値が入りました。マイページへ遷移します。');
  header("Location:mypage.php");
  exit;
}

$listSpan = 10;
d('$currentMinNum:' . print_r($currentMinNum, true));

try {
  $dbh = connectDB();
  $sql = 'SELECT * FROM diary WHERE user_id = :user_id AND delete_flg = 0';
  $data = array(':user_id' => $_SESSION['user_id']);
  $stmt = queryPost($dbh, $sql, $data);
  // $dbDiaryData = $stmt->fetchAll();
  $dbDiaryNum = $stmt->rowCount();
  // d('$dbDiaryDataの中身：' . print_r($dbDiaryData, true));
  d('$dbDiaryNum:' . print_r($dbDiaryNum, true));
  $sql = 'SELECT * FROM diary WHERE user_id = :user_id AND delete_flg = 0 LIMIT :span OFFSET :currentMinNum';
  d('$sql:' . print_r($sql, true));
  d('$data' . print_r($data, true));
  $stmt = $dbh->prepare($sql);
  $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
  $stmt->bindValue(':span', $listSpan, PDO::PARAM_INT);
  $stmt->bindValue(':currentMinNum', ($currentPageNum - 1) * $listSpan, PDO::PARAM_INT);
  $stmt->execute();
  $dbDiaryData = $stmt->fetchAll();
} catch (Exception $e) {
  getErrorLog();
}

$totalPage = ($dbDiaryNum == 0) ? 1 : ceil($dbDiaryNum / $listSpan);

if ($currentPageNum === 1) {
  $minPageNum = 1;
  $maxPageNum = ($totalPage < 5) ? $totalPage : 5;
} elseif ($currentPageNum === 2) {
  $minPageNum = 1;
  $maxPageNum = ($totalPage < 5) ? $totalPage : 5;
} elseif ($currentPageNum === (int)($totalPage - 1)) {
  $minPageNum = ($totalPage < 5) ? 1 : $totalPage - 4;
  $maxPageNum = $totalPage;
} elseif ($currentPageNum === (int)$totalPage) {
  $minPageNum = ($totalPage < 5) ? 1 : $totalPage - 4;
  $maxPageNum = $totalPage;
} else {
  $minPageNum = $currentPageNum - 2;
  $maxPageNum = $currentPageNum + 2;
}


// 画面終了ログ吐き出し
dStop();

// head,header呼び出し
include('parts/head.php');
include('parts/header.php'); ?>

<div class="l_cont">
  <div class="l_pageTtl">
    <h2 class="b_pageTtl">
      <span>筋トレ日記　<?= $currentPageNum . '/' . $totalPage ?>ページ</span>
    </h2><!-- /.b_pageTtl -->
    <span class="is_err"><?= getErrMsg('common') ?></span><!-- /.is_err -->
  </div><!-- /.l_pageTtl -->
  <div class="l_cont_inner">
    <main class="l_main">
      <div class="l_main_inner">
        <p class="b_mediaText">あたらしく日記を書くには<a href="diaryedit.php">コチラ</a></p>
        <?php foreach ($dbDiaryData as $key => $val) : ?>
          <section class="b_media">
            <h3 class="e_headingLv3">
              <?= date('Y/m/d', strtotime($val['create_date'])) ?>日の日記
            </h3><!-- /.e_headingLv3 -->
            <div class="b_mediaText">
              <?= $val['diary'] ?>
            </div><!-- /.b_mediaText -->
            <div class="e_btn_wrapper">
              <a href="diaryedit.php?d_id=<?= $val['id'] ?>" class="e_btn e_btn__arrowRight">編集する</a>
            </div><!-- /.e_btn_wrapper -->
          </section><!-- /.b_media -->
        <?php endforeach; ?>
        <ul class="e_pageNation">
          <?php for ($i = $minPageNum; $i <= $maxPageNum; $i++) : ?>
            <li class="<?= ((int)$i === $currentPageNum) ? 'e_pageNation__active' : ''; ?>"><a href="diary.php?p=<?= $i ?>"><?= $i ?></a></li>
          <?php endfor; ?>
        </ul><!-- /.e_pageNation -->
      </div><!-- /.l main_inner -->
    </main><!-- /.l_main -->
    <?php include('parts/sidebar.php'); ?>
  </div><!-- /.l_cont_inner -->
</div><!-- /.l_cont -->
<?php
include('parts/footer.php');
