<?php

  require('function.php');
  debug('「「「「「「「「「「「「「「');
  debug('「「「「「「メモの確認画面「「「「「「');
  debug('「「「「「「「「「「「「「「');

  debugLogStart();

  require('auth.php');

  $m_id = (!empty($_GET['m_id']))? $_GET['m_id']:'';
  $dbFormData = (!empty($m_id))? getMemo($_SESSION['user_id'],$m_id):'';
  $dbMemoData = getMemoList();
  $viewData = getMemoOne($m_id);

  if (!empty($m_id) && empty($dbFormData)) {
    debug('GETパラメーターのメモIDが違います。マイページへ遷移します。');
    header("Location: index.php");
  }

  debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
 ?>

<?php
$siteTitle = 'メモの確認';
require('head.php');

 ?>

<body>
  <?php
  require('header.php');
   ?>
    <main>
      <section class="site-width position">
        <form class="form" method="post">
          <h1>メモの確認</h1>
          <textarea name="memo" rows="8" cols="80"><?php echo sanitize($viewData['contents']) ?></textarea>
          <div class="count">
            <span class="js-count">0</span>/255
          </div>
          <a href="edit.php?m_id=<?php echo $viewData['id'] ?>" class="btn a-btn">編集する</a>
        </form>
      </section>
    </main>
    <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
  <script src="js/app.js"></script>
  </body>
</html>
