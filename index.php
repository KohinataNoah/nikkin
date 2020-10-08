<?php
$siteTtl = 'トップページ';

require('parts/function.php');
include('parts/head.php');
include('parts/header.php');
?>
<div class="l_cont u_tp">
  <div class="l_cont_inner">
    <main class="l_main">
      <div class="l_main_inner">
        <h2 class="u_pageTtl">
          <span>NIKKINで、</span><br>
          <span>筋トレ日記。</span><br>
          <span>理想の筋肉へ。</span>
        </h2><!-- /.u_pageTtl -->
        <div class="u_tpLink">
          <span>会員登録は<br><a href="signup.php">コチラ</a></span><br>
          <span>既に会員の方は<br><a href="login.php">コチラ</a></span><br>
          <span><a href="guest.php">ゲストログイン</a>
        </div><!-- /. -->
      </div><!-- /.l_main_inner -->
    </main><!-- /.l_main -->
  </div><!-- /.l_cont_inner -->
</div><!-- /.l_cont -->
<?php
include('parts/footer.php');
