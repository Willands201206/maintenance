<?PHP
	session_start();
	require_once "php_functions/common_functions.php";
	require_once "php_functions/ichiran_functions.php";
	echo sentoTagCreate("行事一覧",kaishibi_search,syuryobi_search);
	text();
//	$_SESSION['gyoji-iti_ID'] = NULL;
//DBのどこのテーブルか指定する
	$table 						= "gyoji";
	$yusendo 					= "hyoji_yusendo";
	$ID_change	 				= $_POST["ID_change"];
	$mode_serch 				= $_POST["mode_serch"];//開始・リセットから飛んできたかの判定
	$mode_Before_After 			= $_POST["Before_After"];//次の10件・前の10件飛んできたかの判定
	$mode					    = $_POST["mode"];//実行・↑・↓から飛んできたかの判定
	
	//初回表示かの判定処理
	if($_SESSION['gyoji-iti_first_access'] == NULL){
	$_SESSION['gyoji-iti_first_access']   = 2;//次回からスルーさせるため
	$mode_serch = "リセット";
	}
	
	if($_SESSION['gyoji-iti_first_access'] == 1){
	$_SESSION['gyoji-iti_first_access']   = 2;//次回からスルーさせるため
	}
	
	if($mode_Before_After == "before"){ 
			//前の10件から	
			$_SESSION['gyoji-iti_currentpage'] = $_POST["currentpage_before"];
	}
	if($mode_Before_After == "after"){ 	
			//次の10件から
			$_SESSION['gyoji-iti_currentpage'] =  $_POST["currentpage_after"];
	}
	if($mode_serch != ""){ 	
			//初期化
			$_SESSION['gyoji-iti_currentpage'] =  $_POST["currentpage_star"];
	}
	if($_SESSION['gyoji-iti_currentpage'] == NULL){ 
	//$currentpageがNULLなら1ページ目とする
	//上の処理がなされなかった場合初期化される
			$_SESSION['gyoji-iti_currentpage'] = 1;
	}	
	$currentpage = $_SESSION['gyoji-iti_currentpage'];

//$currentpage_checkは実行ボタン・リセットボタンの際の$currentpageの初期化フラグ
		if($mode_serch == "開始"){ 	
//各セッションにフォームの内容を格納する
				$_SESSION['gyoji-iti_ID']				= $_POST["IDsearch"];
				$_SESSION['gyoji-iti_gyouji_kubun']	 	= $_POST["gyojisearch"];
				$_SESSION['gyoji-iti_title']			= $_POST["titlesearch"];
				$_SESSION['gyoji-iti_kaishi_bi'] 		= $_POST["kaishibi_search"];
				$_SESSION['gyoji-iti_syuryo_bi'] 		= $_POST["syuryobi_search"];
				$_SESSION['gyoji-iti_cale']	 			= $_POST["calesearch"];
				$_SESSION['gyoji-iti_newview']			= $_POST["newviewsearch"];
					}
		if($mode_serch == "リセット"){ 	
				$_SESSION['gyoji-iti_ID']				= "";
				$_SESSION['gyoji-iti_gyouji_kubun']		= "";
				$_SESSION['gyoji-iti_title']			= "";
				$_SESSION['gyoji-iti_kaishi_bi'] 		= "";
				$_SESSION['gyoji-iti_syuryo_bi'] 		= "";
				$_SESSION['gyoji-iti_cale']	 			= "";
				$_SESSION['gyoji-iti_newview']			= "";
	}
	
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
	
//検索条件作成	
	$SerchConditions =  SerchConditions($_SESSION['gyoji-iti_ID'],$_SESSION['gyoji-iti_gyouji_kubun'],$_SESSION['gyoji-iti_title'],$_SESSION['gyoji-iti_kaishi_bi'],$_SESSION['gyoji-iti_syuryo_bi'],$_SESSION['gyoji-iti_cale'],$_SESSION['gyoji-iti_newview']);
//検索条件文作成

	$sql = "select * from {$table} {$SerchConditions}";
	$result = pg_query($dbconn, $sql);

	pg_query($dbconn, "COMMIT");//トランザクション終了

	if ($result == false)
	{
//エラー処理
		exit(dbErrorMessageCreate("DB抽出に失敗しました。", $sql, $dbconn));
	}
	
//データ総数取得
	$total_data = pg_num_rows($result);

//offsetの値設定	
	if($total_data == 0){
		$offset 	= 0;
	}else{
		$offset = $currentpage * 10 - 10; //「10」は1ページに表示する最大行事数の値
	}

//10件表示するかの判断
	$hyojikensu = HyojiKensu($total_data,$currentpage);


//検索条件作成
//	$SerchConditions =  SerchConditions($_SESSION['gyoji-iti_ID'],$_SESSION['gyoji-iti_gyouji_kubun'],$_SESSION['gyoji-iti_title'],$_SESSION['gyoji-iti_kaishi_bi'],$_SESSION['gyoji-iti_syuryo_bi'],$_SESSION['gyoji-iti_cale'],$_SESSION['gyoji-iti_newview']);

//	error($result);
//ページマタギ処理初期化
	$page_change = 0;

	if($mode == "↑"){ 
	
				pg_query($dbconn, "BEGIN"); //トランザクション開始
					if($offset == 0){$hyojikensu = $hyojikensu -1 ;}
							$sql_ue = change_yusendo_sql($table,$SerchConditions,$hyojikensu,$offset);
							$result_ue = pg_query($dbconn, $sql_ue);
				pg_query($dbconn, "COMMIT");//トランザクション終了
				
			$sql_ue_submit = change_yusendo($table,$offset,$currentpage,$total_data,$result_ue,$ID_change,0,$page_change);
			if($sql_ue_submit == false){//アップデート失敗処理
			search();
			 echo "<div class='Kensaku-box'><p>優先度変更ができませんでした。</p></div>"; 
			 exit;}
			 
			 
				pg_query($dbconn, "BEGIN"); //トランザクション開始
				        pg_query($dbconn, $sql_ue_submit);
				pg_query($dbconn, "COMMIT");//トランザクション終了
				
				if($offset == 0){$hyojikensu = $hyojikensu +1;}//引いたものを戻す
					}
					
					
	if($mode == "↓"){ 
	
					pg_query($dbconn, "BEGIN"); //トランザクション開始
					if($offset == 0){$hyojikensu = $hyojikensu -1 ;}
							$sql_sita = change_yusendo_sql($table,$SerchConditions,$hyojikensu,$offset);
							$result_sita = pg_query($dbconn, $sql_sita);
					pg_query($dbconn, "COMMIT");//トランザクション終了
			$sql_sita_submit = change_yusendo($table,$offset,$currentpage,$total_data,$result_sita,$ID_change,1,$page_change);
			if($sql_sita_submit == false){//アップデート失敗処理
			search();
			 echo "<div class='Kensaku-box'><p>優先度変更ができませんでした。</p></div>"; 
			 exit;}
			 
				 	pg_query($dbconn, "BEGIN"); //トランザクション開始
							pg_query($dbconn, $sql_sita_submit);
					pg_query($dbconn, "COMMIT");//トランザクション終了
				
				if($offset == 0){$hyojikensu = $hyojikensu +1;}//引いたものを戻す
		}
	//優先度変更時特殊状態のページ遷移処理
	$_SESSION['gyoji-iti_currentpage'] = $_SESSION['gyoji-iti_currentpage'] + $page_change;
	$currentpage = $_SESSION['gyoji-iti_currentpage'];
	


	if($mode == "実行"){ 
			//更新フラグの確認
			//リザルトの新規取得
			pg_query($dbconn, "BEGIN"); //トランザクション開始
					$sql = "select * from {$table} {$SerchConditions} order by {$yusendo} DESC limit {$hyojikensu} offset {$offset}";
					$result = pg_query($dbconn, $sql);
			pg_query($dbconn, "COMMIT");//トランザクション終了
				if($sql_delete == NULL){ 
			$sql_update_cv = update_cale_and_newview($hyojikensu,$table,$result);
			if($sql_update_cv != NULL){
				pg_query($dbconn, "BEGIN"); //トランザクション開始
					pg_query($dbconn, $sql_update_cv);//実行
				pg_query($dbconn, "COMMIT");//トランザクション終了
			}
			//更新フラグ終了
		}
	//実行ボタンから飛んできた

	
			//削除フラグ実行1
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


//検索条件作成	
	$SerchConditions =  SerchConditions($_SESSION['gyoji-iti_ID'],$_SESSION['gyoji-iti_gyouji_kubun'],$_SESSION['gyoji-iti_title'],$_SESSION['gyoji-iti_kaishi_bi'],$_SESSION['gyoji-iti_syuryo_bi'],$_SESSION['gyoji-iti_cale'],$_SESSION['gyoji-iti_newview']);

//offsetの値設定	
	if(0 > $offset){
		 $offset = 0;
	}

//検索条件文作成

	$sql = "select * from {$table} {$SerchConditions}";
	$result = pg_query($dbconn, $sql);

	pg_query($dbconn, "COMMIT");//トランザクション終了

	if ($result == false)
	{
//エラー処理
		exit(dbErrorMessageCreate("DB抽出に失敗しました。", $sql, $dbconn));
	}
		
//データ総数取得
	$total_data = pg_num_rows($result);

//10件表示するかの判断
	$hyojikensu = HyojiKensu($total_data,$currentpage);

//何ページ目か表示
	PageCounter($total_data,$currentpage,$offset,$hyojikensu,$_SESSION['gyoji-iti_currentpage']);




//DB再検索
	pg_query($dbconn, "BEGIN"); //トランザクション開始

//offsetの値設定	
	if(0 > $offset){
		 $offset = 0;
	}

//検索条件実行
	$sql = "select * from {$table} {$SerchConditions} order by {$yusendo} DESC limit {$hyojikensu} offset {$offset}";

	$result = pg_query($dbconn, $sql);

	pg_query($dbconn, "COMMIT");//トランザクション終了

//表一覧を表示する 
	hyouview($hyojikensu,$result,$mode);








function text(){
echo <<< __searchbar__
<div class="all">
	<div class="header">
		<h2>行事一覧</h2>
	</div>
			<div class="kyotsu">
				<p><a href="menu.php">管理メニュー</a></p>
				<p><a href="gyoji_toroku.php">新規登録</a></p>
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
		$cal 			=array();
		$cal_check 		=array();
		$newvie			=array();
		$NeVi_check		=array();

		$i =1;
		
		while($row = pg_fetch_object($result)){
			$cal[$i] = $_POST["cale{$i}"];
			$newvie[$i] = $_POST["newview{$i}"];
			$cal_check[$i] 	= $row->calendar_kokai;
			$NeVi_check[$i] = $row->shinchaku_kokai;
			$id = $row->id;
			
			if($cal[$i] != $cal_check[$i] ||$newvie[$i] != $NeVi_check[$i]){
					if($cal[$i] == 'f' && $newvie[$i] == 't'){
					}
					else{
						$sql_update_cv .= update_cale_and_newview_sql(0,$table,$id,$newvie[$i],$cal[$i]);
					}
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
<form name="searchbar" method="POST" OnSubmit="return AllSerchCheck('IDsearch','kaishibi_search','syuryobi_search')">
<div class="Kensaku-box">
<table class ="table1-t1">
	<tbody>
		<tr>
			<th rowspan=2 style='background=#663300' style='color=#F0E8D8'>検索</th>
			<th>ID</th>
			<th>行事区分</th>
			<th>タイトル</th>
			<th colspan="3">期間</th>
			<th>カレンダー</th>
			<th>新着</th>

		</tr>
__searchbar__;
echo			"<td><input type='text' size='4' maxlength='4' name='IDsearch'width='10px' value='{$_SESSION['gyoji-iti_ID']}'></td>";
echo			"<td><select name='gyojisearch' width='30px' value='{$_SESSION['gyoji-iti_gyouji_kubun']}'>";

								echo "<option value=''"; if($_SESSION['gyoji-iti_gyouji_kubun'] == ''){echo " selected";}echo "> </option>\n";
								echo "<option value='1'";if($_SESSION['gyoji-iti_gyouji_kubun'] == '1'){echo " selected";}echo ">特別展示</option>\n";
								echo "<option value='2'";if($_SESSION['gyoji-iti_gyouji_kubun'] == '2'){echo " selected";}echo ">絵はがき教室</option>\n";
								echo "<option value='3'";if($_SESSION['gyoji-iti_gyouji_kubun'] == '3'){echo " selected";}echo ">イベント</option>\n";
echo				"</select></td>";
echo			"<td><input type='text' size='20' maxlength='40' name='titlesearch' value='{$_SESSION['gyoji-iti_title']}'></td>";
echo			"<td><input type='text' size='8' maxlength='10' id='jquery-ui-datepicker-from' name='kaishibi_search' value='{$_SESSION['gyoji-iti_kaishi_bi']}'></td>";
echo			"<td>～</td>";
echo			"<td><input type='text' size='8' maxlength='10' id='jquery-ui-datepicker-to' name='syuryobi_search' value='{$_SESSION['gyoji-iti_syuryo_bi']}'></td>";
echo			"<td><select name='calesearch' width='30px'>";
								echo "<option value=''"; if($_SESSION['gyoji-iti_cale'] == ''){echo " selected";}echo "> </option>\n";
								echo "<option value='TRUE'";if($_SESSION['gyoji-iti_cale'] == 'TRUE'){echo " selected";}echo ">公開</option>\n";
								echo "<option value='FALSE'";if($_SESSION['gyoji-iti_cale'] == 'FALSE'){echo " selected";}echo ">非公開</option>\n";
echo <<< __searchbar__
				</select></td>
			<td><select name="newviewsearch" width="30px">
__searchbar__;
								echo "<option value=''"; if($_SESSION['gyoji-iti_newview'] == ''){echo " selected";}echo "> </option>\n";
								echo "<option value='TRUE'";if($_SESSION['gyoji-iti_newview'] == 'TRUE'){echo " selected";}echo ">公開</option>\n";
								echo "<option value='FALSE'";if($_SESSION['gyoji-iti_newview'] == 'FALSE'){echo " selected";}echo ">非公開</option>\n";
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

function hyouview($hyou_hyouji,&$result,$mode){
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

<div class="Yajirushi-left">
<form name="hyo_form" method="POST" onsubmit="return send_check()">
<input type="submit" name="mode" value="↑">
<input type="submit" name="mode" value="↓">
</div>

<table class = "table1-t2">
	<tbody>
		<tr>
			<th>優先度</th>
			<th>ID</th>
			<th>行事区分</th>
			<th>タイトル</th>
			<th>開始日</th>
			<th>終了日</th>
			<th>段落名</th>
			<th>編集</th>
			<th>カレ<br><input type="checkbox" name="cale_ALL" value="" onclick="fncAllCheck('cale_ALL','cale',$hyou_hyouji)"/></th>
			<th>新着<br><input type="checkbox" name="new_ALL" value="" onclick="fncAllCheck('new_ALL','newview',$hyou_hyouji)"></th>
			<th>削除<br><input type="checkbox" name="dele_ALL" value="" onclick="fncAllCheck('dele_ALL','delete',$hyou_hyouji)"></th>
		</tr>
__EOS0__;
$i = 1;
while($row = pg_fetch_object($result)){//表示ループ開始
	$id = $row->id;
	$gyoujikubun = $row->gyoji_kubun;
	$title = $row->title;
	$comment = $row->comment;
	$kaishibi = TimeChange($row->kaishi_bi);
	$syuryoubi =   TimeChange($row->shuryo_bi);
	$danrakumei =$row->danraku_mei;
	$cale = $row->calendar_kokai;
	$newview = $row->shinchaku_kokai;
	
	//文字コード設定
	mb_language("Japanese");
	mb_internal_encoding("utf-8");
	//タイトルを17文字にカット
	$title_edit = mb_substr($title,"0","17");
	
	echo		"<tr>";
	echo			"<td><input type='radio' name='ID_change' value='$i'";
	//一番最初のラジオボタンにチェックを入れる処理（初期設定）
	if($i ==1 ){
		echo " checked";
	}
	
	//↓ボタンが押された時にチェックを入れる処理
	if($mode == "↓" ){
	
		if($_POST["ID_change"]+1==$i){
		
			echo " checked";
		}
	}
	
	//↑ボタンが押された時にチェックを入れる処理
	if($mode == "↑"){	
		//通常処理
		if($_POST["ID_change"]-1==$i){
		
			echo " checked";
		}
		//ページを跨いだときの処理
		if($_POST["ID_change"]+9==$i){
		
			echo " checked";
		}
	}
	
	echo "></td>";
	echo			"	<td>$id</td>										 			\n";
	echo			"	<td>".gyojiKubunNameGet($gyoujikubun)."</td>									 			\n";
	echo			"	<td>$title_edit</td>								 			\n";
	echo			"	<td>$kaishibi</td>								 			\n";
	echo			"	<td>$syuryoubi</td>								 			\n";
	echo			"	<td>$danrakumei</td>								     			\n";
	echo			"	<td><a href='gyoji_toroku.php?id=$id'>編集</a></td>				\n	";
	echo			"	<td><input type='hidden' name='cale$i' value='f' />	\n";
	echo			"	<input type='checkbox' name='cale$i' value='t'";if($cale == 't'){echo " checked='checked'";}echo "/></td>	\n";
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

<script language="JavaScript">  
<!--  
sent = false;  
//2重投稿を防止する  
function send_check(){  
    if(sent){  
        
        return false  
    }else{  
        sent = true  
        return true  
    }  
}  
// -->  
</script>