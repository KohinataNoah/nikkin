<body>
  <header class="l_header">
    <p style="display:none;" class="js_topMsg"><?= getSessionFlash('msg') ?></p>
    <div class="l_header_inner">
      <div class="b_header">
        <h1 class="b_siteTtl">
          <a href="index.php">NIKKIN</a>
        </h1><!-- /.b_pageTtl -->
        <nav class="l_headerNav">
          <div class="b_headerNav">
            <ul>
              <?php if (empty($_SESSION['user_id'])) : ?>
                <li><a href="login.php">ログイン</a></li>
                <li><a href="signup.php">新規登録</a></li>
              <?php else : ?>
                <li><a href="mypage.php">マイページ</a></li>
                <li><a href="logout.php">ログアウト</a></li>
              <?php endif; ?>
            </ul>
          </div><!-- /.b_headerNav -->
        </nav><!-- /.l_headerNav -->
      </div><!-- /.b_header -->
    </div><!-- /.l_header_inner -->
  </header><!-- /.l_header -->