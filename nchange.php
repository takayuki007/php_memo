<?php

  require('function.php');
  debug('「「「「「「「「「「「「「「「「「「「「「「');
  debug('「「「「「「「画像・ニックネーム変更「「「「');
  debug('「「「「「「「「「「「「「「「「「「「「「「');

  debugLogStart();

  require('auth.php');

  $dbFormData = getUser($_SESSION['user_id']);

  debug('取得したユーザー情報：' .print_r($dbFormData,true));

  if (!empty($_POST)) {
    debug('POST送信あります。');
    debug('POST情報：' .print_r($_POST,true));

    $img = $_POST['img'];
    $name = $_POST['name'];

    // if ($dbFormData['img'] !== $img) {
    //   validimg($img,'img');
    // }

    if ($dbFormData['name'] !== $name) {
      validMaxLen($name,'name');
    }

    if (empty($err_msg)) {
      debug('バリデーションOKです');

      try{
        $dbh = dbConnect();
        $sql = 'UPDATE users SET img = :img, name = :name WHERE id = :u_id AND delete_flg = 0';
        $data = array(':img' => $img, ':name' => $name, ':u_id' => $dbFormData['id']);

        $stmt = queryPost($dbh.$sql,$data);

        if ($stmt) {
          debug('クエリ成功');
          debug('メモ一覧へ遷移します。');
          header("Location: index.php");
        } else {
          debug('クエリ失敗');
          $err_msg['common'] = MSG08;
        }
      } catch (Exception $e) {
        error_log('エラー発生：' .$e->getMessage());
        $err_msg['common'] = MSG08;
      }
    }
  }

  debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<画面表示終了');
 ?>


<?php
$siteTitle = '画像・ニックネーム変更';
require('head.php');

 ?>

<body>
  <?php
  require('header.php');
   ?>
    <main>
      <section class="site-width position">
        <form class="form" method="post">
          <h1>画像・ニックネーム変更</h1>
          <p>画像をドラッグ＆ドロップ</p>
          <div class="img-register">
            <input type="file" name="img" value="画像をドラッグ＆ドロップ">
          </div>
          <p>ニックネーム</p>
          <input type="text" name="name" placeholder="ニックネーム" class="input">
          <input type="submit" name="btn" value="変更する" class="btn">
        </form>
      </section>
    </main>
  </body>
</html>
