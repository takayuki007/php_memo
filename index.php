<?php

  require('function.php');

  debug('「「「「「「「「「「「「「「「「「「「「「');
  debug('「「「「トップページ「「「「「「「「「「「');
  debug('「「「「「「「「「「「「「「「「「「「「「');

  debugLogStart();

  require('auth.php');

  $dbMemoData = getMemoList();

  debug('画面表示処理終了＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜');
 ?>

<?php
$siteTitle = 'メモ一覧';
require('head.php');

 ?>

<body>
  <?php
  require('header.php');
   ?>
    <main>
      <section class="site-width position">
        <h1>メモ一覧</h1>
        <div class="memo-index">
          <?php
            foreach ($dbMemoData as $val):
           ?>
            <div class="memo">
              <a href="memo.php?m_id=<?php echo $val['id'] ?>" class="memo-a">
                <div class="memo-img">
                  <img src="img/xxxx.png" alt="プロフ画像">
                </div>
                <div class="memo-content">
                  <p><?php echo sanitize($val['contents']); ?></p>
                </div>
              </a>
            </div>
          <?php
            endforeach;
           ?>
        </div>
        <a href="create.php" class="btn a-btn">メモを作成する</a>
      </section>
    </main>
  </body>
</html>
