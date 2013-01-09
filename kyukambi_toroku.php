<?php
	session_start();
	require_once "php_functions/common_functions.php";
	require_once "php_functions/ichiran_functions.php";
	echo menu_sentoTagCreate("休館日登録");
	echo "<body>\n";
	$update_id = $_GET["id"];
	$mode = $_POST["mode"];
	$action = $_POST["action"];													//実行条件判断
	$title = $_POST["title"];													//タイトル
	$comment = $_POST["comment"];												//コメント
	$hizuke = $_POST["hizuke"];													//日付
	define("title_max",80);														//タイトル文字数制限
	define("comment_max",80);													//コメント文字数制限

if(isset($_POST["k_new"])){
	$k_new = 1;																//新着情報に公開
}
else{
	$k_new = 0;																//新着情報に公開
}

//リロード時にデータを反映
if($mode == "insert"){
	$h_title = $_POST["title"];
	$h_comment = $_POST["comment"];
	$h_hizuke = $_POST["hizuke"];
	$shinchaku_kokai = $_POST["k_new"];
}

//プレビュー表示時用のフォームデータを保存
	$_SESSION["check_title"] = $title;
	$_SESSION["check_comment"] = $comment;
	$_SESSION["check_hizuke"] = $hizuke;
	$_SESSION["check_k_new"] = $k_new;

//編集遷移判断
	$_SESSION["check_id"] = $_GET["id"];

	//●DB接続
	if (dbConnect($dbconn) == false)
	{
		//エラー処理
		exit(dbErrorMessageCreate("DB接続に失敗しました。"));
	}
	
//Updateするためのデータをテキストボックスに反映させるために抽出
if($update_id != ""){
	$sql = 	"select * from kyukambi where id = $update_id ";
	$result = pg_query($dbconn, $sql);

	if ($result == false){
		exit(dbErrorMessageCreate("DB抽出に失敗しました。", $sql, $dbconn));
	}
						
	//$resultの結果を読み込み
	$row = pg_fetch_object($result);
	$h_title = $row->title;
	$h_comment = $row->comment;
	$h_hizuke = TimeChange($row->hizuke);
	$shinchaku_kokai = $row->shinchaku_kokai;
}

//UPdate実行
if($update_id != "" and $mode == "insert" ){

	//リロード時にデータを反映
	$h_title = $_POST["title"];
	$h_comment = $_POST["comment"];
	$h_hizuke = $_POST["hizuke"];
	$shinchaku_kokai = $_POST["k_new"];

	//エラー処理　1ならUPdateしない
	$error =0 ;
	$sql = 	"select * from kyukambi where id = $update_id ";
	$result = pg_query($dbconn, $sql);

	
	//$resultの結果を読み込み
	$row = pg_fetch_object($result);
	$check_hizuke = TimeChange($row->hizuke);
	$sql =	"select * from kyukambi where hizuke = '" . $hizuke . "' and id <> " . $update_id . ";";
	$result = pg_query($dbconn, $sql);

	if ($result == false){

		exit(dbErrorMessageCreate("DB抽出に失敗しました。", $sql, $dbconn));

	}
							
	if(pg_num_rows($result) != 0){
		//echo "そのデータは既に登録済みです。update<br>";
		$error = 1;
	}
	//エラーがなかった場合　updateを実行する
	if($error == 0){
		if($action == "実行"){
			$sql = 	"update kyukambi set ".
			"title = '" . $title . "',comment = '" . $comment . "'," .
			"hizuke = '" . $hizuke . "', shinchaku_kokai = '" . $k_new . "',".
			"sakusei_sha = '" . $_SERVER['REMOTE_USER'] . "'".
			"where id = " . $update_id . ";";
	//			$_SESSION['gyoji-iti_ID'] 			= $update_id;
			pg_query($dbconn, "BEGIN"); //トランザクション開始
			$result = pg_query($dbconn, $sql);
			if ($result == false){

				pg_query($dbconn, "ROLLBACK");
				exit(dbErrorMessageCreate("DB登録に失敗しました。", $sql, $dbconn));
			}
			pg_query($dbconn, "COMMIT");
			echo "<script language='JavaScript'>document.location = 'kyukambi_ichiran.php';</script>";
		}
		else if($action == "トップページのプレビュー"){
			echo "<script language='JavaScript'>window.open('../new/index.html?preview=kyukambi_preview');</script>";
		}
		else if($action == "博物館のご案内のプレビュー"){
			echo "<script language='JavaScript'>window.open('../new/museum/index.html?preview=kyukambi_preview');</script>";
		}
	}
}
?>
		<div class="all">
			<form action="" id='form' name="form" method="POST">
				<div class="header">
				<h2>休館日登録</h2>
				</div>
				<div class="kyotsu">
				<p><a href="menu.php">管理メニュー</a></p>
				<p><a href="kyukambi_ichiran.php">休館日一覧</a></p>
				</div>

				<div class="gyoji-left1">
					<p class="toroku1">タイトル</p>
				</div>
				<div class="gyoji-right1">
<!--				<p class="toroku2"><input type="text" name="title" size="30" maxlength="40" value="<?= $h_title ?>"/></p>-->
					<p class="toroku2"><textarea cols="40" rows="4" maxlength="<?= title_max; ?>" name="title" class="title"><?= $h_title; ?></textarea></p>
				</div>
				<div class="gyoji-left1">
					<p class="toroku1">コメント</p>
				</div>
				<div class="gyoji-right1">
					<p class="toroku2"><textarea cols="40" rows="4" maxlength="<?= comment_max; ?>" name="comment" class="comment"><?= $h_comment; ?></textarea></p>
				</div>
				<div class="gyoji-left1">
					<p class="toroku1" >日付</p>
				</div>
				<div class="gyoji-right1">
					<p class="toroku2"><input type="text" name="hizuke" id="jquery-ui-datepicker-to" maxlength='10' size="30" value="<?= $h_hizuke ?>"/></p>
				</div>
				<div class="gyoji-left2">
					<pre class="toroku1">新着情報に公開 <?php echo "<input type='checkbox' name='k_new' value='t'";if($shinchaku_kokai == 't'){echo " checked='checked'";}echo ">	\n"; ?></pre>
					<p class="toroku1">
						<input type="submit" name="action" value="トップページのプレビュー" class="button-pre" onclick="return stay_here();">
					</p>
				</div>
				<div class="gyoji-right2">
					<p>　</p>
					<p class="toroku2">
						<input type="submit" name="action" value="博物館のご案内のプレビュー" class="button-pre" onclick="return stay_here();">
					</p>
				</div>
				<div class="kyotsu_ok">
					<p>
						<input type="hidden" name="preview" value="kyukambi_preview">
						<input type="hidden" name="mode" value="insert">
						<input type="submit" name="action" value="実行" class="button" onclick="return stay_here();">
						<?php 	//UPDATEしたさい重複チェックに引っかかった場合のエラー文。			
							if($error == 1){echo	"<p style='color:red; font-weight: bold;'>そのデータは既に登録済みです。</p>";}
						?>
					</p>
				</div>
			</form>
		</div>
	</body>
</html>

<script language="JavaScript">
<!--

function stay_here(){
	if (kyukanbitoroku_minyuryoku_check() == false) return false;
	form.target = "";
	document.getElementById('form').action = '';
}

function goToHakubutsukan_preview(){
	if (kyukanbitoroku_minyuryoku_check() == false) return false;
	form.target = "newwindow";
	window.open("","newwindow");
	document.getElementById('form').action = '../museum/index.php';
	document.getElementById('submit').click();
}

function goToTop_preview(){
	if (kyukanbitoroku_minyuryoku_check() == false) return false;
	form.target = "newwindow";
	window.open("","newwindow");
	document.getElementById('form').action = '../index.php';
	document.getElementById('submit').click();
}

function kyukanbitoroku_minyuryoku_check(){

	if(document.form.k_new.checked == true){
		var title_chk = minyuryoku_check("title");

		if(title_chk == false){
			alert('タイトルが未入力です。');
			return false;
		}

		var comment_chk = minyuryoku_check("comment");

		if(comment_chk == false){
			alert('コメントが未入力です。');
			return false;
		}
	}

	var hizuke_chk = minyuryoku_check("hizuke");
	var hizuke = document.form.hizuke.value;


	if(hizuke_chk == false){
		alert('日付が未入力です。');
		return false;
	}

	var hizuke_ayamari_chk = isValidDate(hizuke);

	if(hizuke_ayamari_chk == false){
		alert('期間に誤りがあります。')
		return false;

	}
}

	$(function() {
		$('textarea.title').maxlength({'feedback': <?= title_max; ?>});
		$('textarea.comment').maxlength({'feedback': <?= comment_max; ?>});
	});


//-->
</script>
<?php

if($update_id == "" and $mode == "insert"){
	//●重複チェック
	$sql =	"select * from kyukambi where hizuke = '$hizuke'";
	$result = pg_query($dbconn, $sql);

	if ($result == false){

		exit(dbErrorMessageCreate("DB抽出に失敗しました。", $sql, $dbconn));

	}

	if(pg_num_rows($result) == 0){
		if($action == "実行"){
			//●DB登録
			$sql = 	"insert into kyukambi (title,comment,hizuke,shinchaku_kokai,sakusei_sha)".
					"values ('" . $title . "','" . $comment . "','" . $hizuke . "','" . $k_new . "','" . $_SERVER['REMOTE_USER'] . "');";
			pg_query($dbconn, "BEGIN"); //トランザクション開始
			$result = pg_query($dbconn, $sql);

			if ($result == false){

				pg_query($dbconn, "ROLLBACK");
				exit(dbErrorMessageCreate("DB登録に失敗しました。", $sql, $dbconn));
			}
			pg_query($dbconn, "COMMIT");
			//検索条件を初期化
			$_SESSION['kyukam-iti_first_access'] = NULL;
			echo "<script language='JavaScript'>document.location = 'kyukambi_ichiran.php';</script>";
		}
		else if($action == "トップページのプレビュー"){
			echo "<script language='JavaScript'>window.open('../new/index.html?preview=kyukambi_preview');</script>";
		}
		else if($action == "博物館のご案内のプレビュー"){
			echo "<script language='JavaScript'>window.open('../new/museum/index.html?preview=kyukambi_preview');</script>";
		}
	}
	else{

		echo "<p style='color:red; font-weight: bold;'>そのデータは既に登録済みです。</p>";

	}
}
?>
