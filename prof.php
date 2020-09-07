<?php

  require('function.php');

  debug('「「「「「「「「「「「「「「「「「「「「「');
  debug('「「「「「「「「プロフ登録「「「「「「「「');
  debug('「「「「「「「「「「「「「「「「「「「「「');

  debugLogStart();

  require('auth.php');

  if (!empty($_POST)) {
    debug('POST送信あります。');
    debug('POST情報：'.print_r($_POST,true));
    debug('FILE情報：'.print_r($_FILES,true));

    $name = $_POST['name'];
    $img = (!empty($_FILES['img']['name']))? uploadImg($_FILES['img'],'img'):'';

    validRequired($name,'name');
    validRequired($img,'img');

    if (empty($err_msg)) {
      debug('未入力チェックOKです');

      validMaxLen($name,'name');

      if (empty($err_msg)) {
        debug('バリデーションOKです。');

        try{
          $dbh = dbConnect();
          $sql = 'INSERT INTO users (name,img,update_date) VALUES (:name,:img,:update_date)';
          $data = array(':name' => $name,':img' => $img,':update_date' => date('Y-m-d H:i:s'));

          debug('SQL：' .$sql);
          debug('流し込みデータ：' .print_r($data,true));

          $stmt = queryPost($dbh,$sql,$data);

          header("Location: index.php");

        } catch (Exception $e){
          error_log('エラー発生：' .$e->getMessage());
          $err_msg['common'] = MSG08;
        }
      }
    }
  }
  debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
 ?>

<?php
$siteTitle = 'プロフ登録';
require('head.php');

 ?>

<body>
  <?php
  require('header.php');
   ?>
    <main>
      <section class="site-width position">
        <form class="form" method="post" enctype="multipart/form-data">
          <h1>プロフ登録</h1>
          <p>画像をドラッグ＆ドロップ</p>
          <div class="img-register">
            <label class="area-drop">
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="img" value="画像をドラッグ＆ドロップ" class="input-file">
            </label>
          </div>
          <p>ニックネーム</p>
          <input type="text" name="name" placeholder="ニックネーム" class="input">
          <input type="submit" name="btn" value="登録する" class="btn">
        </form>
      </section>
    </main>
  </body>
</html>
