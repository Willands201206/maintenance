<?PHP
	session_start();
	require_once "php_functions/common_functions.php";
	require_once "php_functions/ichiran_functions.php";
	echo sentoTagCreate("休館日一覧",kaishibi_kyukam,syuryobi_kyukam);
	text();
//DBのどこのテーブルか指定する
	$table 						= "kyukambi";
	$ID_change	 				= $_POST["ID_change"];
	$mode_serch 				= $_POST["mode_serch"];//開始・リセットから飛んできたかの判定
	$mode_Before_After 			= $_POST["Before_After"];//次の10件・前の10件飛んできたかの判定
	$mode					    = $_POST["mode"];//実行・↑・↓から飛んできたかの判定

	//初回表示かの判定処理
	if($_SESSION['kyukam-iti_first_access'] == NULL){
	$_SESSION['kyukam-iti_first_access']   = 1;//次回からスルーさせるため
	$mode_serch = "リセット";
	}

	if($mode_Before_After == "before"){ 
			//前の10件から	
			$_SESSION['kyukam-iti_currentpage'] = $_POST["currentpage_before"];
	}
	if($mode_Before_After == "after"){ 	
			//次の10件から
			$_SESSION['kyukam-iti_currentpage'] =  $_POST["currentpage_after"];
	}
	if($mode_serch != ""){ 	
			//初期化
			$_SESSION['kyukam-iti_currentpage'] =  $_POST["currentpage_start"];
	}
	if($_SESSION['kyukam-iti_currentpage'] == NULL){ 
	//$currentpageがNULLなら1ページ目とする
	//上の処理がなされなかった場合初期化される
			$_SESSION['kyukam-iti_currentpage'] = 1;
	}	
	$currentpage = $_SESSION['kyukam-iti_currentpage'];

//$currentpage_checkは実行ボタン・リセットボタンの際の$currentpageの初期化フラグ
		if($mode_serch == "開始"){ 	
//各セッションにフォームの内容を格納する
				$_SESSION['kyukam-iti_ID']				= $_POST["IDsearch_kyukam"];
				$_SESSION['kyukam-iti_title_search']	= $_POST["titlesearch_kyukam"];
				$_SESSION['kyukam-iti_kaishi_bi'] 		= $_POST["kaishibi_kyukam"];
				$_SESSION['kyukam-iti_syuryo_bi'] 		= $_POST["syuryobi_kyukam"];
				$_SESSION['kyukam-iti_newview']			= $_POST["newviewsearch"];
					}
		if($mode_serch == "リセット"){ 	
				$_SESSION['kyukam-iti_ID']				= "";
				$_SESSION['kyukam-iti_title_search']	= "";
				$_SESSION['kyukam-iti_kaishi_bi'] 		= "";
				$_SESSION['kyukam-iti_syuryo_bi'] 		= "";
				$_SESSION['kyukam-iti_newview']			= "";
	}
	
		


//検索条件作成
	$kyukam_SerchConditions =	kyukam_SerchConditions($_SESSION['kyukam-iti_ID'],$_SESSION['kyukam-iti_kaishi_bi'],$_SESSION['kyukam-iti_syuryo_bi'],$_SESSION['kyukam-iti_title_search'],$_SESSION['kyukam-iti_newview']);


//DB接続
	if (dbConnect($dbconn) == false)
	{
//エラー処理
		exit(dbErrorMessageCreate("DB接続に失敗しました。"));
	}
	echo Ikkatu();
	echo Minyuryoku_Title_Check();
	
	//DB検索
	pg_query($dbconn, "BEGIN"); //トランザクション開始
	
	$sql = "select * from {$table}  {$kyukam_SerchConditions} ";
	$result = pg_query($dbconn, $sql);
	pg_query($dbconn, "COMMIT");//トランザクション終了
	
	$total_data = pg_num_rows($result);
	
	//offsetの値設定	
	if($total_data == 0){
		$offset 	= 0;
	}else{
		$offset = $currentpage * 10 - 10; //「10」は1ページに表示する最大行事数の値
	}
	
	//10件表示するかの判断
	$hyojikensu = HyojiKensu($total_data,$currentpage);

//	error($result);
	
	
		if($mode == "実行"){ 	

			//更新フラグの確認
			//リザルトの新規取得
			pg_query($dbconn, "BEGIN"); //トランザクション開始
			
			$sql_kousim = "select * from {$table} {$kyukam_SerchConditions} order by hizuke DESC limit {$hyojikensu} offset {$offset}";
					$result_kousim = pg_query($dbconn, $sql_kousim);
			pg_query($dbconn, "COMMIT");//トランザクション終了
				if($sql_delete == NULL){ 
			$sql_update_cv = update_cale_and_newview($hyojikensu,$table,$result_kousim);
			if($sql_update_cv != NULL){
				pg_query($dbconn, "BEGIN"); //トランザクション開始
					pg_query($dbconn, $sql_update_cv);//実行
				pg_query($dbconn, "COMMIT");//トランザクション終了
			}
			//更新フラグ終了
				}
			//削除フラグ実行
			$sql_delete = delete_sql($hyojikensu,$table,$result);
			//sql_deleteがからでなければ実行
			if($sql_delete != NULL){
				pg_query($dbconn, "BEGIN"); //トランザクション開始
				pg_query($dbconn, $sql_delete);

				if ($result == false)
				{
					pg_query($dbconn, "ROLLBACK");
					exit(dbErrorMessageCreate("DB削除に失敗しました。", $sql_delete, $dbconn));
				}
				pg_query($dbconn, "COMMIT");
			}
			//削除フラグ終了
			}
			
//offsetの値設定	
	if(0 > $offset){
		 $offset = 0;
	}

//検索条件文作成

	$sql = "select * from {$table}  {$kyukam_SerchConditions} ";
	$result = pg_query($dbconn, $sql);

	pg_query($dbconn, "COMMIT");//トランザクション終了

	if ($result == false)
	{
//エラー処理
		exit(dbErrorMessageCreate("DB抽出に失敗しました。", $sql, $dbconn));
	}
		
//データ総数取得
	$total_data = pg_num_rows($result);

//何ページ目か表示
	PageCounter($total_data,$currentpage,$offset,$hyojikensu,$_SESSION['kyukam-iti_currentpage']);
	
//offsetの値設定	
	if(0 > $offset){
		 $offset = 0;
	}	
	
//DB再検索
	pg_query($dbconn, "BEGIN"); //トランザクション開始

//検索条件実行
	$sql = "select * from {$table} {$kyukam_SerchConditions} order by hizuke DESC limit {$hyojikensu} offset {$offset}";

	$result = pg_query($dbconn, $sql);

	pg_query($dbconn, "COMMIT");//トランザクション終了


//表一覧を表示する
	hyouview($hyojikensu,$result);








function text(){
echo <<< __searchbar__
<div class="all">
	<div class="header">
		<h2>休館日一覧</h2>
	</div>
			<div class="kyotsu">
				<p><a href="menu.php">管理メニュー</a></p>
				<p><a href="kyukambi_toroku.php">新規登録</a></p>
			</div>

__searchbar__;

}
/************************************************************/
//チェックボックスの値を確認する
/************************************************************/
function delete_sql($hyojikensu,$table,$result){
	$roop_end = $hyojikensu +1;
	for($i = 1; $i < $roop_end; $i++){
		$delete_submit = $_POST["delete{$i}"];
					if($delete_submit != ""){
						$delete_id .= "id = ".$delete_submit." " ;
						if(!$sql_delete == ""){//NULLではなかった
						$sql_delete .= delete($table,$delete_submit,1);
						}
						else{//NULLだった
						$sql_delete = delete($table,$delete_submit,0);
						}
					
					}
									}
	return $sql_delete;
}

function update_cale_and_newview($hyojikensu,$table,$result){
		$newvie			=array();
		$NeVi_check		=array();

		$i =1;
		
		while($row = pg_fetch_object($result)){
			$newvie[$i] = $_POST["newview{$i}"];
			$NeVi_check[$i] = $row->shinchaku_kokai;
			$id = $row->id;
			
			if($newvie[$i] != $NeVi_check[$i]){
					$sql_update_cv .= update_cale_and_newview_sql(1,$table,$id,$newvie[$i]);
				}
			//ループ終わり　$i+1
			$i++;
			}
			

		 return $sql_update_cv;

}
/************************************************************/
//検索バーの作成
/************************************************************/

function search(){
//

echo <<< __searchbar__
<form name="searchbar" method="POST" OnSubmit="return AllSerchCheck('IDsearch_kyukam','kaishibi_kyukam','syuryobi_kyukam')">
<div class="Kensaku-box">
<table class ="table1-t1">
	<tbody>
		<tr>
			<th rowspan=2 style='background=#663300' style='color=#F0E8D8'>検索</th>
			<th>ID</th>
			<th>タイトル</th>
			<th colspan="3">期間</th>
			<th>新着</th>

		</tr>
__searchbar__;
echo			"<td><input type='text' size='4' maxlength='4' name='IDsearch_kyukam'width='10px' value='{$_SESSION['kyukam-iti_ID']}'></td>";

echo			"<td><input type='text' size='20' maxlength='40' name='titlesearch_kyukam'width='10px' value='{$_SESSION['kyukam-iti_title_search']}'></td>";

echo			"<td><input type='text' size='8' maxlength='10' id='jquery-ui-datepicker-from' name='kaishibi_kyukam' value='{$_SESSION['kyukam-iti_kaishi_bi']}'></td>";
echo			"<td>～</td>";
echo			"<td><input type='text' size='8' maxlength='10' id='jquery-ui-datepicker-to' name='syuryobi_kyukam' value='{$_SESSION['kyukam-iti_syuryo_bi']}'></td>";

echo <<< __searchbar__
				</select></td>
			<td><select name="newviewsearch" width="30px">
__searchbar__;
								echo "<option value=''"; if($_SESSION['kyukam-iti_newview'] == ''){echo " selected";}echo "> </option>\n";
								echo "<option value='TRUE'";if($_SESSION['kyukam-iti_newview'] == 'TRUE'){echo " selected";}echo ">公開</option>\n";
								echo "<option value='FALSE'";if($_SESSION['kyukam-iti_newview'] == 'FALSE'){echo " selected";}echo ">非公開</option>\n";
echo <<< __searchbar__
				</select></td>
		</tr>
	</tbody>
</table>
</div>

<div class="Kensaku-btn">
<input type="hidden" name="currentpage_start" value="1">
<input type="submit" name="mode_serch" value="開始" class="button">
<input type="submit" name="mode_serch" value="リセット" class="button" onClick="document.searchbar.reset()">
</div>
</form>
__searchbar__;
}
/************************************************************/
//
/************************************************************/

function hyouview($hyou_hyouji,&$result){
//表示についての全般的な処理を行う関数
//$hyou_hyouji 表示回数

//検索した行事が一件も無かったときの処理
if (pg_num_rows($result) == 0){
echo		"<div class='Kensaku-box'><p>検索結果がありませんでした</p></div>";
exit;
}

//表の各タイトルを記載
echo <<< __EOS0__
<div class="g-ichiran">

<form name="hyo_form" method="POST">

<table class = "table1-t2">
	<tbody>
		<tr>
			<th>ID</th>
			<th>日付</th>
			<th>タイトル</th>
			<th>編集</th>
			<th>新着<br><input type="checkbox" name="new_ALL" value="" onclick="fncAllCheck('new_ALL','newview',$hyou_hyouji)"></th>
			<th>削除<br><input type="checkbox" name="dele_ALL" value="" onclick="fncAllCheck('dele_ALL','delete',$hyou_hyouji)"></th>
		</tr>
__EOS0__;
$i = 1;
while($row = pg_fetch_object($result)){//表示ループ開始
	$id = $row->id;
	$title = $row->title;
	$comment = $row->comment;
	$hizuke =   TimeChange($row->hizuke);
	$newview = $row->shinchaku_kokai;
	
	//文字コード設定
	mb_language("Japanese");
	mb_internal_encoding("utf-8");
	//タイトルを47文字にカット
	$title_edit = mb_substr($title,"0","47");
	
	echo		"<tr>";
	echo			"	<td>$id</td>										 			\n";
	echo			"	<td>$hizuke</td>								 			\n";
	echo			"	<td>$title_edit</td>								 			\n";
	echo			"	<td><a href='kyukambi_toroku.php?id=$id'>編集</a></td>				\n	";
	echo			"	<td><input type='hidden' name='newview$i' value='f' />	\n";
	echo			"	<input type='checkbox' name='newview$i' value='t'";if($newview == 't'){echo " checked='checked'";}if($title == "" or $comment == ""){echo "disabled='disabled'";}echo "></td>	\n";
	echo		"	<td><input type='checkbox' name='delete$i' value='$id'></td>		\n";
	echo		"</tr>";


	$i++;
}//表示ループ終わり
echo <<< __EOS1__
</tbody>
</table>
</div>
<div class="g-ichiran_ok"><input type="submit" name="mode" value="実行" class="button" onclick="return kakunin()"></div>
</form>


__EOS1__;

//function hyouview() end
}
?>
</body>

</html>