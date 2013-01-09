<?PHP
/************************************************************/
//「前の十件」と「次の十件」表示を行う関数 
/************************************************************/
	require_once "php_functions/common_functions.php";

function Before_After($total_data,$currentpage){
//「前の十件」と「次の十件」表示を行う関数
	if($currentpage != 1){
//1ページでなければ前の10件を表示
		$currentpage_before = $currentpage -1;
		
echo		"<div class='Kensu-mae'><a HREF='' onclick='document.before.submit();return false;'>前の10件</a>";
echo		"<form name='before' method='POST' action=''value='before'>";
echo		"<input type=hidden name='Before_After' value='before'>";
echo		"<input type=hidden name='currentpage_before' value='$currentpage_before'>";
echo		"</form></div>";
		
						}

	if(Ceil($total_data/10) != $currentpage){
//最終ページでなければ次の10件を表示
			$currentpage_after = $currentpage + 1;

echo		"<div class='Kensu-tsugi'><a HREF='' onclick='document.after.submit();return false;' >次の10件</a>";
echo		"<form name='after' method='POST' action=''>";
echo		"<input type=hidden name='Before_After' value='after'>";
echo		"<input type=hidden name='currentpage_after' value='$currentpage_after'>";
echo		"</form></div>";

//		echo "<div class='Kensu-tsugi'><a href='?currentpage=$currentpage_after'>次の10件</a></div>\n";$currentpage_after

											}

}

/************************************************************/
//表示件数を10件表示するかの判定を行う関数
/************************************************************/
function HyojiKensu($total_data,$currentpage){
	$mod = $total_data % 10;

	if(Ceil($total_data/10) != $currentpage || $mod == 0){

		return 10;

											}

	else{
	
		return $mod;

		}

}
/****************************************************************
* 機　能： 何件目か表示するための関数
* 引　数： ［入力］$total_data　　検索した条件の最大数
* 　　　　 ［入力］$currentpage　　何ページ目かの値
* 　　　　 ［入力］$start　　$offsetに使用する値
* 戻り値： $tag：スクリプトを書いた文字列
****************************************************************/

function PageCounter($total_data,&$currentpage,&$start,&$hyojikensu,&$session_currentpage){
	if($total_data == 0){
	$start 	= 0;
	$end	= 0;
	
	}
	else{
		$start = $currentpage *10 -10 +1;

		if(Ceil($total_data/10) != $currentpage){

		$end   = $currentpage *10 ;
												}

		else{

		$end  = $total_data;

		}
	}
	
	//検索条件を指定した状態で行事の更新をした後にそのページ内に表示できる行事がない時に１ページ目に移動する処理
	
	if($start > $total_data){
	
		//1～10と表示するための設定
		$start = 1;
		$end   = 10;
			
	
		
		$currentpage = 1; //1ページ
		$hyojikensu = 10; //10件表示する
		$session_currentpage=1; //関数を出た後も1ページ目を保持するためにセッションに設定
		
	
		
	}
	
	if($total_data != 0){
	//次の10件・前の10件を表示
	Before_After($total_data,$currentpage);
	}

	
	//検索表を表示
	search();
	
	echo "<div name='pagecunter' class='Kensu-now'><p>",$start,"～",$end,"　/ ",$total_data,"　件</p></div>";

//offset のために0にしておく
	$start = $start -1;
}
/****************************************************************
* 機　能： javascriptによる未入力チェック
* 引　数： 
* 戻り値： $tag：スクリプトを書いた文字列
****************************************************************/

function Minyuryoku_Title_Check(){

$tag = <<< __ikkatu__
<script type="text/javascript"><!--

			function minyuryoku_title_check(chk_name){
				var chk  = document.getElementsByName(chk_name);
				
				if(chk[0].value == ""){
					alert('未入力です。');
					chk[0].focus();
				}
			}
// --></script>

__ikkatu__;
}




/****************************************************************
* 機　能： javascriptによる一括チェック処理・入力チェック
* 引　数： ［入力］
* 戻り値： $tag：スクリプトを書いた文字列
****************************************************************/
function Ikkatu(){
$tag = <<< __ikkatu__
	<script type="text/javascript"><!--
  function fncAllCheck(chk_ALL,chk,num){
  	
  //一括チェック処理 chk_ALL 一括チェック　chk　単体チェックのフォーム名、ループ回数
	var chk_ALLTF = document.getElementsByName(chk_ALL);
	num = num +1;
	for(var i=1; i<num; i++){
		//検索文字列の作成　named
		var named = new String();
		named += chk;
		named += i;
		var chkTF = document.getElementsByName(named);
		
		if(chk == 'delete'){
			chkTF[0].checked = chk_ALLTF[0].checked;
							}
		else{
		
			if(chkTF[1].disabled == chk_ALLTF[0].disabled){
			
				chkTF[1].checked = chk_ALLTF[0].checked;
	
			}
		}

	}	
  }

		function AllSerchCheck(ID,START_DAY,END_DAY){

var chk = document.getElementsByName(START_DAY);
	if(chk[0].value != ""){
		if(isZen(chk[0].value)==false){
			alert('開始日は半角数字、日付形式(oooo/oo/oo)以外無効です。');
			return false;
		}


		if(ckDateFormat(chk[0].value)==false){
			alert('開始日は日付形式(oooo/oo/oo)で入力してください。');
			return false;
		}


		if(ckDate(chk[0].value)==false){
			alert('開始日はありえない日時です。');
			return false;
		}
	}


	var chk = document.getElementsByName(END_DAY);
	if(chk[0].value != ""){
		if(isZen(chk[0].value)==false){
			alert('終了日は半角数字、日付形式(oooo/oo/oo)以外無効です。');
			return false;
		}


		if(ckDateFormat(chk[0].value)==false){
			alert('終了日は日付形式(oooo/oo/oo)で入力してください。');
			return false;
		}


		if(ckDate(chk[0].value)==false){
			alert('終了日はありえない日時です。');
			return false;
		}
	}


	if(Id_Serch_check(ID) == false){
			return false;
		}
		if(isValidPeriod(fromStr, toStr, false) == false){
			return false;
		}

	}

	function Id_Serch_check(chk_name){
			var chk  = document.getElementsByName(chk_name);
			if(chk[0].value == '0'){
				alert('ID= 0 は入力禁止です。');
				chk[0].focus();
				return false;
			}
			if(chk[0].value.match(/[^0-9]+/) != null){
				alert('ID は半角数字のみ有効です。');
				chk[0].focus();
				return false;
				}

		}

	function isZen(str){
		for(var i=0; i<str.length; i++){
			var len=escape(str.charAt(i)).length;
			if(len>=4){
				return false;
			}
		}
			//正常時の処理なし
		return true;
	}

		//yyyy/mm/dd以外はじくがありえない日付が通ってしまう
	function ckDateFormat(str) {
		// 正規表現による書式チェック
		if(!str.match(/^([0-9]{4})\/(0[1-9]|1[012])\/(0[1-9]|[12][0-9]|3[01])$/)){
			return false;
		}
	}

	function ckDate(str) {
		var vYear = str.substr(0, 4) - 0;
		var vMonth = str.substr(5, 2) - 1; // Javascriptは、0-11で表現
		var vDay = str.substr(8, 2) - 0;
		// 月,日の妥当性チェック
		if(vMonth >= 0 && vMonth <= 11 && vDay >= 1 && vDay <= 31){
			var vDt = new Date(vYear, vMonth, vDay);
			if(isNaN(vDt)){
				return false;
			}else if(vDt.getFullYear() == vYear && vDt.getMonth() == vMonth && vDt.getDate() == vDay){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	function kakunin(){
		if(confirm("更新/削除を行いますか？")== true){
			return true;
			}
		else{
			return false;
		}
	}

// --></script>
__ikkatu__;
	return $tag;

}
/****************************************************************
* 機　能： 英文形式の日付を Unix タイムスタンプに変換する関数
* 引　数： ［入力］$time　　変換したい時間
* 戻り値： $time：変換した文字列
****************************************************************/

function TimeChange($time){

	return date("Y/m/d",strtotime("{$time}"));;
	
}

/****************************************************************
* 機　能： 行事一覧用検索条件のSQL文を作成
* 引　数： ［入力］$id_search　　IDの検索条件
* 　　　　 ［入力］$gyouji_search　　検索を行う行事区分の検索条件
* 　　　　 ［入力］$title_search　　タイトルの検索条件
* 　　　　 ［入力］$kaishibi_search　　開始日の検索条件
* 　　　　 ［入力］$syuryobi_search　　終了日の検索条件
* 　　　　 ［入力］$cale_search　　カレンダー公開の検索条件
* 　　　　 ［入力］$newview_search　　新着公開の検索条件
* 戻り値： $SerchConditions：SQL文を返す　全部NULLだった場合　return
****************************************************************/

function SerchConditions($id_search,$gyouji_search,$title_search,$kaishibi_search,$syuryobi_search,$cale_search,$newview_search){
	$start = 0;//検索条件が一回目の記述か判別
	if($id_search != "" || $gyouji_search != "" || $gyouji_search != "" || $title_search !="" || $kaishibi_search !="" || $syuryobi_search !="" || $cale_search !="" || $newview_search !=""){
	//全ての変数がNULLではなかった場合whereを代入
	$SerchConditions = "where ";
	}
	else {
	return ;
	}
	if(!$id_search == ""){
			$SerchConditions .= "id = '{$id_search}' ";
			$start = 1;
	}
	
	if(!$gyouji_search == ""){
		if(!$start == 0){
				$SerchConditions .= "and gyoji_kubun = '{$gyouji_search}' ";
			}
		else{
				$SerchConditions .= "gyoji_kubun = '{$gyouji_search}'";
				$start = 1;
			}

	}
	
	if($title_search != ""){
		if(!$start == 0){
			$SerchConditions .= "and title like '%{$title_search}%' ";
		}
		else{
			$SerchConditions .= "title like '%{$title_search}%' ";
			$start = 1;
		}
	}
	if($kaishibi_search != "" && $syuryobi_search != ""){
						if(!$start == 0){
							$SerchConditions .= "and kaishi_bi <= '{$syuryobi_search}' and kaishi_bi >= '{$kaishibi_search}' and 
													 shuryo_bi <= '{$syuryobi_search}' and shuryo_bi >= '{$kaishibi_search}'";
						}
						else{
							$SerchConditions .= "kaishi_bi <= '{$syuryobi_search}' and kaishi_bi >= '{$kaishibi_search}' and 
													 shuryo_bi <= '{$syuryobi_search}' and shuryo_bi >= '{$kaishibi_search}'";
							$start = 1;
						}
		}
		else{
					if(!$kaishibi_search == "" ){
						if(!$start == 0){
							$SerchConditions .= "and kaishi_bi >= '{$kaishibi_search}' and shuryo_bi >= '{$kaishibi_search}' ";
						}
						else{
							$SerchConditions .= "kaishi_bi >= '{$kaishibi_search}' and shuryo_bi >= '{$kaishibi_search}' ";
							$start = 1;
												}	

					}
					if(!$syuryobi_search == ""){
							if(!$start == 0){
							$SerchConditions .= "and kaishi_bi <= '{$syuryobi_search}' and shuryo_bi <= '{$syuryobi_search}' ";
						}
						else{
							$SerchConditions .= "kaishi_bi <= '{$syuryobi_search}' and shuryo_bi <= '{$syuryobi_search}'  ";
							$start = 1;
						}
												}
			}
			
	if(!$cale_search == ""){
			if(!$start == 0){
				$SerchConditions .= "and calendar_kokai = {$cale_search} ";
			}
			else{
				$SerchConditions .= "calendar_kokai = {$cale_search} ";
				$start = 1;
			}

	}
	if(!$newview_search == ""){
	
				if(!$start == 0){
				$SerchConditions .= "and shinchaku_kokai = {$newview_search} ";
			}
			else{
				$SerchConditions .= "shinchaku_kokai = {$newview_search} ";
				$start = 1;
			}

	}


return $SerchConditions;
}

/****************************************************************
* 機　能： 検索したデータがなかった場合のエラー処理
* 引　数： ［入力］$result　　抽出した結果データ
* 戻り値： 　エラー文を表示
****************************************************************/


function error($result){

$error = pg_num_rows($result);
if($error == 0){
//$hyou_hyoujiが10以上の時中断
echo		"<div class='Kensaku-box'><p>検索結果がありませんでした</p></div>";
exit;
}

}
/****************************************************************
* 機　能： 優先度変更するためのデータを抽出する範囲を広くしたSQL文を作成
* 引　数： ［入力］$table　　テーブル名
* 　　　　 ［入力］$SerchConditions　　現在の検索条件
* 　　　　 ［入力］$hyojikensu　何件まで抽出する
* 　　　　 ［入力］$offset　　何件から抽出する
* 戻り値： $sql：SQL文を返す　false：失敗
****************************************************************/

function change_yusendo_sql($table,$SerchConditions,$hyojikensu,$offset){
		$hyojikensu = $hyojikensu +2;
		if($offset != 0){
			$offset = $offset -1;
		}
		$sql = "select * from {$table} {$SerchConditions} order by hyoji_yusendo DESC limit {$hyojikensu} offset {$offset}";
		
		return $sql;
}
/****************************************************************
* 機　能： 優先度変更するためのSQL文を作成
* 引　数： ［入力］$table　　テーブル名
* 　　　　 ［入力］$offset　　表示しているページの最初の件数
* 　　　　 ［入力］$result　　SQLからの結果
* 　　　　 ［入力］$ID_change　ラジオボタンの値
* 　　　　 ［入力］$up_down　0なら↑　1なら↓の判別
* 戻り値： $sql：SQL文を返す　false：失敗
****************************************************************/

function change_yusendo($table,$offset,$currentpage,$total_data,$result,$ID_change,$up_down,&$page_change){

		if($ID_change == 1  && $up_down == 0){//ページ跨ぎ処理 ↑なら1ページ戻る
				$page_change = $page_change - 1;
		}
		if($ID_change == 10 && $up_down == 1){//ページ跨ぎ処理 ↑なら1ページ進む
				$page_change = $page_change + 1;
		}

		$lastnum_chehk 	= ($currentpage -1 ) * 10 + $ID_change;
		//最大件数と同値ならfalseを返す
		if($total_data == $lastnum_chehk && $up_down == 1){ return false;}
		
		if($offset != 0){$i = 0;}
		else{if($ID_change == 1 && $up_down == 0){return false;}
				$ID_change = $ID_change -1;}
		$i = 0;
		while($row = pg_fetch_object($result)){
				$id[$i] 			= $row->id;
				$yusendo[$i] 		= $row->hyoji_yusendo;
				$i++;
												}
		if($up_down == 0){//$up_down=0 ならUPと判断 "koshin_sha  = '".$_SERVER['REMOTE_USER']."'"追加する
			$change = $ID_change -1 ;
			$sql  = "UPDATE $table set hyoji_yusendo = $yusendo[$change]   ,koshin_sha  = '".$_SERVER['REMOTE_USER']."' where id = $id[$ID_change] ;";
			$sql .= "UPDATE $table set hyoji_yusendo = $yusendo[$ID_change],koshin_sha  = '".$_SERVER['REMOTE_USER']."' where id = $id[$change] ;";
						}
		if($up_down == 1){//$up_down=0 ならdownと判断
			$change = $ID_change +1 ;
			$sql  = "UPDATE $table set hyoji_yusendo = $yusendo[$change]   , koshin_sha  = '".$_SERVER['REMOTE_USER']."'    where id = $id[$ID_change] ;";
			$sql .= "UPDATE $table set hyoji_yusendo = $yusendo[$ID_change], koshin_sha  = '".$_SERVER['REMOTE_USER']."' where id = $id[$change] ;";

		}
		return $sql;	
}
/****************************************************************
* 機　能： DELETのSQL文を作成
* 引　数： ［入力］$table　　テーブル名
* 　　　　 ［入力］$delete_submit　　削除する
* 　　　　 ［入力］$fastflag　　処理を行うのが初めてか
* 戻り値： $sql：SQL文を返す　
****************************************************************/

function delete($table,$delete_submit,$fastflag){

		if($fastflag == 0){
		$sql  = "DELETE FROM ".$table." WHERE id = {$delete_submit} ;";
						}
		if($fastflag == 1){
		$sql  = "DELETE FROM ".$table." WHERE id = {$delete_submit} ; ";
		$i++;
						}
		return $sql;	
}
/****************************************************************
* 機　能： UPDATEのSQL文を作成
* 引　数： ［入力］$table　　テーブル名
* 　　　　 ［入力］$iti_kyu　　行事一覧・休館日一覧どちらで使うのか
* 　　　　 ［入力］$id　　更新を行うID
* 　　　　 ［入力］$newvie　　更新を行う新着公開
* 　　　　 ［入力］$cal　　更新を行うカレンダー公開・休館日は指定せずともよい
* 戻り値： $sql：SQL文を返す　
****************************************************************/

function update_cale_and_newview_sql($iti_kyu,$table,$id,$newvie,$cal = NULL){
			
			if($iti_kyu == 0){
			$newvie = Trance_true_false($newvie);
			$cal	= Trance_true_false($cal);
			$sql  = "UPDATE $table set calendar_kokai = ".$cal." , shinchaku_kokai = ".$newvie." , koshin_sha = '".$_SERVER['REMOTE_USER']."' where id = $id ;";		
			}
			if($iti_kyu == 1){
			$newvie = Trance_true_false($newvie);
			$sql  = "UPDATE $table set shinchaku_kokai = ".$newvie." , koshin_sha = '".$_SERVER['REMOTE_USER']."' where id = $id ;";		
			}

			return $sql;
}
/****************************************************************
* 機　能： tをTRUEに、fをFALSEに変換する
* 引　数： ［入力］$Trance_true_false　　変換する文字
* 戻り値： $Trance_true_false：変換した文字
****************************************************************/

function Trance_true_false($Trance_true_false){
			if($Trance_true_false == 't'){
				$Trance_true_false = 'TRUE';
			}
			if($Trance_true_false == 'f'){
				$Trance_true_false = 'FALSE';
			}

			return $Trance_true_false;
}

/****************************************************************
* 機　能： 休館日一覧用検索条件のSQL文を作成
* 引　数： ［入力］$id_search　　IDの検索条件
* 　　　　 ［入力］$kaishibi_search　　開始日の検索条件
* 　　　　 ［入力］$syuryobi_search　　終了日の検索条件
* 　　　　 ［入力］$title_search　　タイトルの検索条件
* 　　　　 ［入力］$newview_search　　新着公開の検索条件
* 戻り値： $SerchConditions：SQL文を返す　全部NULLだった場合　return
****************************************************************/

function kyukam_SerchConditions($id_search,$kaishibi_search,$syuryobi_search,$title_search,$newview_search){
	$start = 0;//検索条件が一回目の記述か判別
	if($id_search != "" ||  $kaishibi_search !="" || $syuryobi_search !="" || $title_search !="" ||  $newview_search !=""){
	//全ての変数がNULLではなかった場合whereを代入
	$SerchConditions = "where ";
	}
	else {
	return ;
	}
	if(!$id_search == ""){
			$SerchConditions .= "id = {$id_search} ";
			$start = 1;
	}
	
	if($kaishibi_search != "" && $syuryobi_search != ""){
						if(!$start == 0){
							$SerchConditions .= "and hizuke >= '{$kaishibi_search}' and hizuke <= '{$syuryobi_search}'  ";
													 
						}
						else{
							$SerchConditions .= " hizuke >= '{$kaishibi_search}' and hizuke <= '{$syuryobi_search}'  ";
							$start = 1;
						}

	}
	
	else{
					if(!$kaishibi_search == "" ){
						if(!$start == 0){
							$SerchConditions .= "and hizuke >= '{$kaishibi_search}' ";
						}
						else{
							$SerchConditions .= " hizuke >= '{$kaishibi_search}' ";
							$start = 1;
												}	

					}
					if(!$syuryobi_search == ""){
							if(!$start == 0){
							$SerchConditions .= "and hizuke <= '{$syuryobi_search}' ";
						}
						else{
							$SerchConditions .= " hizuke <= '{$syuryobi_search}'  ";
							$start = 1;
						}
												}
	}
			

	if($title_search != ""){
		if(!$start == 0){
			$SerchConditions .= "and title like '%{$title_search}%' ";
		}
		else{
			$SerchConditions .= "title like '%{$title_search}%' ";
			$start = 1;
		}
	}

	if(!$newview_search == ""){
	
				if(!$start == 0){
				$SerchConditions .= "and shinchaku_kokai = {$newview_search} ";
			}
			else{
				$SerchConditions .= "shinchaku_kokai = {$newview_search} ";
				$start = 1;
			}

	}


return $SerchConditions;
}

?>