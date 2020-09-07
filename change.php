  <?php

  require('function.php');
  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
  debug('「「「「「「「「「「プロフの変更「「「「「「「「「「');
  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');

  debugLogStart();

  require('auth.php');

   ?>

  <?php
    $siteTitle = 'プロフの変更';
    require('head.php');
   ?>

  <body>
    <?php
      require('header.php');
     ?>
    <main>
      <section class="site-width position">
        <form class="form" method="post">
          <h1>プロフの変更</h1>
          <a href="nchange.php" class="btn a-btn">画像・ニックネーム変更</a>
          <a href="echange.php" class="btn a-btn">email変更</a>
          <a href="pchange.php" class="btn a-btn">パスワード変更</a>
        </form>
      </section>
    </main>
  </body>
</html>
