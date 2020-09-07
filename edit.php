<?php

  require('function.php');
  debug('「「「「「「「「「「「「「「');
  debug('「「「「「「メモの編集画面「「「「「「');
  debug('「「「「「「「「「「「「「「');

  debugLogStart();

  require('auth.php');

  $m_id = (!empty($_GET['m_id']))? $_GET['m_id']:'';
  $dbFormData = (!empty($m_id))? getMemo($_SESSION['user_id'],$m_id):'';
  $viewData = getMemoOne($m_id);
  debug('メモID：'.$m_id);
  debug('フォーム用DBデータ：'.print_r($dbFormData,true));

  if (!empty($m_id) && empty($dbFormData)) {
    debug('GETパラメーターのメモIDが違います。マイページへ遷移します。');
    header("Location: index.php");
  }

  if ($_POST) {
    debug('POST送信があります。');
    debug('POST情報：'.print_r($_POST,true));

    $memo = $_POST['memo'];

    if ($dbFormData !== $memo) {
      validRequired($memo,'memo');
      validMaxLen($memo,'memo');

      if (empty($err_msg)) {
        debug('バリデーションOKです。');

        try{
          $dbh = dbConnect();
          $sql = 'UPDATE memo SET contents = :memo WHERE user_id = :u_id AND id = :m_id AND delete_flg = 0';
          $data = array(':memo' => $memo,':u_id' => $_SESSION['user_id'],':m_id' => $m_id);

          $stmt = queryPost($dbh,$sql,$data);

          debug('SQL:'.$sql);
          debug('流し込みデータ：'.print_r($data,true));

          header("Location: index.php");
        } catch(Exception $e) {
          error_log('エラー発生：'.$e->getMessage());
          $err_msg['common'] = MSG08;
        }
      }
    }
  }
  debug('画面表示終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
 ?>

<?php
$siteTitle = 'メモの編集';
require('head.php');

 ?>

<body>
  <?php
  require('header.php');
   ?>
    <main>
      <section class="site-width position">
        <form class="form" method="post">
          <h1>メモの編集</h1>
          <textarea name="memo" rows="8" cols="80"><?php echo sanitize($viewData['contents']) ?></textarea>
          <div class="count">
            <span class="js-count">0</span>/255
          </div>
          <input type="submit" name="btn" value="保存する" class="btn">
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
