<?php

  require('function.php');

  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
  debug('「「「「「「「「「「退会ページ「「「「「「「「「「「「');
  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

  debugLogStart();
  require('auth.php');

  if (!empty($_POST)) {
    debug('POST送信あります。');

    try{
      $dbh = dbConnect();
      $sql = 'UPDATE users SET delete_flg = 1 WHERE id = :us_id;';
      $data = array(':us_id' => $_SESSION['user_id']);

      $stmt = queryPost($dbh,$sql,$data);

      if ($stmt) {
        session_destroy();
        debug('セッション変数の中身：'.print_r($_SESSION,true));
        debug('新規会員登録ページへ遷移します。');
        header("Location: signup.php");
      } else {
        debug('クエリが失敗しました。');
        $err_msg['common'] = MSG08;
      }
    } catch (Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG08;
    }
  }
 ?>

<?php
$siteTitle = '退会';
require('head.php');

 ?>

<body>
  <?php
  require('header.php');
   ?>
   <main>
     <section class="site-width position">
       <form class="form" method="post">
         <h1>退会しますか？</h1>
         <input type="submit" name="btn unsub" value="退会する" class="btn">
       </form>
     </section>
   </main>
 </body>
</html>
