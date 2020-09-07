<?php

  require('function.php');

  debug('「「「「「「「「「「「「「「「「「「');
  debug('「「「「「「「メモを作成「「「「「「');
  debug('「「「「「「「「「「「「「「「「「「');

  debugLogStart();

  require('auth.php');

  if (!empty($_POST)) {
    debug('POST送信があります。');
    debug('POST送信情報：'.print_r($_POST,true));

    $memo = $_POST['memo'];

    validRequired($memo,'memo');

    if (empty($err_msg)) {
      debug('未入力チェックOKです。');

      validMaxLen($memo,'memo');

      if (empty($err_msg)) {
        debug('バリデーションOKです。');

        try{
          $dbh = dbConnect();
          $sql = 'INSERT INTO memo (user_id,contents,create_date,update_date) VALUES (:u_id,:memo,:create_date,:update_date)';
          $data = array(
            ':u_id' => $_SESSION['user_id'],
            ':memo' => $memo,
            ':create_date' => date('Y-m-d H:i:s'),
            ':update_date' => date('Y-m-d H:i:s')
          );

          debug('SQL：' .$sql);
          debug('流し込みデータ：' .print_r($data,true));

          $stmt = queryPost($dbh,$sql,$data);

          header("Location: index.php");

        } catch(Exception $e) {
          error_log('エラー発生：' .$e->getMessage());
          $err_msg['common'] = MSG08;
        }
      }
    }
  }

 ?>

  <?php
   $siteTitle = '新規メモ作成';
   require('head.php');
   ?>
  <body>
    <?php
    require('header.php');
     ?>
    <main>
      <section class="site-width position">
        <form class="form" method="post">
          <h1>メモしよう！</h1>
          <textarea name="memo" rows="8" cols="80"></textarea>
          <div class="count">
            <span class="js-count">0</span>/255
          </div>
          <input type="submit" name="btn" value="作成する" class="btn">
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
