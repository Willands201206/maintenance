<?php
/****************************************************************
* 機　能： htmlの先頭タグを返却します。
* 　　　　 （パラメータのページ名称はtitleタグに埋め込みます）
* 引　数： ［入力］$pageName　ページ名称
* 戻り値： 先頭タグ（文書型宣言、htmlタグ（開始のみ）、head、
* 　　　　 body（開始のみ）、JavaScript非対応時の記述）
****************************************************************/
function sentoTagCreate($pageName,$form_fromName,$form_toName)
{
	$tag = <<< __sentoTag__
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>{$pageName}</title>
<meta name="robots" content="none" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="imagetoolbar" content="false" />
<script type="text/javascript" src="scripts/maintenance.js"></script>
<link rel="stylesheet" href="css/maintenance.css" type="text/css" />
<link href="css/smoothness/jquery-ui-1.9.0.custom.css" rel="stylesheet">
<link rel="stylesheet" href="css/jquery.ui.all.css">
<script type="text/javascript" src="js/jquery-1.8.2.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.12.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker-ja.js"></script>
<script src="js/jquery.maxlength.js" type="text/javascript"></script>
<script type="text/javascript"> <!--
jQuery( function() {

$("#jquery-ui-datepicker-to").keyup(function(){
	if($('form input[name="{$form_toName}"]').val() == ''){
		$('#jquery-ui-datepicker-from').datepicker('option', 'maxDate', '');
		$('#jquery-ui-datepicker-to').datepicker('hide');
	}else if($('form input[name="{$form_toName}"]').val().match(/^([0-9]{4})\/(0[1-9]|1[012])\/(0[1-9]|[12][0-9]|3[01])$/)){
		var str=$('form input[name="{$form_toName}"]').val();
		$('#jquery-ui-datepicker-from').datepicker('option', 'maxDate',  new Date(str));
	}
});   

$("#jquery-ui-datepicker-from").keyup(function(){
	if($('form input[name="{$form_fromName}"]').val() == ''){
		$('#jquery-ui-datepicker-to').datepicker('option', 'minDate', '');
		$('#jquery-ui-datepicker-from').datepicker('hide');
	}else if($('form input[name="{$form_fromName}"]').val().match(/^([0-9]{4})\/(0[1-9]|1[012])\/(0[1-9]|[12][0-9]|3[01])$/)){
		var str=$('form input[name="{$form_fromName}"]').val();
		$('#jquery-ui-datepicker-to').datepicker('option', 'minDate', new Date(str));
	}
});   

	var dates = jQuery( '#jquery-ui-datepicker-from, #jquery-ui-datepicker-to' ) . datepicker( {
		showAnim: 'clip',
		changeMonth: true,
		numberOfMonths: 1,
		showCurrentAtPos: 0,
		onSelect: function( selectedDate ) {
			var option = this . id == 'jquery-ui-datepicker-from' ? 'minDate' : 'maxDate',
				instance = jQuery( this ) . data( 'datepicker' ),
				date = jQuery . datepicker . parseDate(
					instance . settings . dateFormat ||
					jQuery . datepicker . _defaults . dateFormat,
					selectedDate, instance . settings );
			dates . not( this ) . datepicker( 'option', option, date );
		}
	} );
} );

$(function(){
    $('#datepicker').datepicker();
});
// -->
</script>
</head>
<body>
<noscript>
<h2><font color=red>JavaScriptを有効にしてから本ページを再表示してください。</font></h2>
</noscript>
__sentoTag__;

	return $tag;
}

/****************************************************************
* 機　能： htmlの先頭タグを返却します。
* 　　　　 （パラメータのページ名称はtitleタグに埋め込みます）
* 引　数： ［入力］$pageName　ページ名称
* 戻り値： 先頭タグ（文書型宣言、htmlタグ（開始のみ）、head、
* 　　　　 body（開始のみ）、JavaScript非対応時の記述）
****************************************************************/
function menu_sentoTagCreate($pageName)
{
	$tag = <<< __sentoTag__
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>{$pageName}</title>
<meta name="robots" content="none" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="imagetoolbar" content="false" />
<script type="text/javascript" src="scripts/maintenance.js"></script>
<link rel="stylesheet" href="css/maintenance.css" type="text/css" />
<link href="css/smoothness/jquery-ui-1.9.0.custom.css" rel="stylesheet">
<link rel="stylesheet" href="css/jquery.ui.all.css">
<script type="text/javascript" src="js/jquery-1.8.2.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.12.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker-ja.js"></script>
<script src="js/jquery.maxlength.js" type="text/javascript"></script>
<script type="text/javascript"> <!--
jQuery( function() {


	var dates = jQuery( '#jquery-ui-datepicker-from, #jquery-ui-datepicker-to' ) . datepicker( {
		showAnim: 'clip',
		changeMonth: true,
		numberOfMonths: 1,
		showCurrentAtPos: 0,
		onSelect: function( selectedDate ) {
			var option = this . id == 'jquery-ui-datepicker-from' ? 'minDate' : 'maxDate',
				instance = jQuery( this ) . data( 'datepicker' ),
				date = jQuery . datepicker . parseDate(
					instance . settings . dateFormat ||
					jQuery . datepicker . _defaults . dateFormat,
					selectedDate, instance . settings );
			dates . not( this ) . datepicker( 'option', option, date );
		}
	} );
} );

$(function(){
    $('#datepicker').datepicker();
});
// -->
</script>
</head>
<body>
<noscript>
<h2><font color=red>JavaScriptを有効にしてから本ページを再表示してください。</font></h2>
</noscript>
__sentoTag__;

	return $tag;
}



/****************************************************************
* 機　能： データベースに接続します。
* 　　　　 （接続情報は非公開フォルダにあるコンフィグファイルから取得）
* 引　数： ［出力］$dbconn　接続リソース
* 戻り値： true：正常終了、false：異常終了
****************************************************************/
function dbConnect(&$dbconn)
{

	$iniArray = parse_ini_file(dirname(__FILE__) ."/../../../config.ini", true);

	$dbconn = pg_connect("host=".$iniArray["database"]["host"]." ".
						 "dbname=".$iniArray["database"]["dbname"]." ".
						 "user=".$iniArray["database"]["user"]." ".
						 "password=".$iniArray["database"]["password"]);

	if ($dbconn == false)
	{
		return false;
	}
	else
	{
		return true;
	}
}



/****************************************************************
* 機　能： データベースアクセス時に発生したエラーメッセージを出力します。
* 引　数： ［入力］$baseMessage　基本エラーメッセージ
* 　　　　 ［入力］$sql　SQL文（省略可）
* 　　　　 ［入力］$dbconn　接続リソース（省略可）
* 戻り値： true：エラーメッセージ
****************************************************************/
function dbErrorMessageCreate($baseMessage, $sql = null, $dbconn = null)
{
	$message = $baseMessage."<br>\n行番号：".__LINE__."<br>\n";

	if ($sql != null)
	{
		$message .= "SQL：".$sql."<br>\n";
	}

	if ($dbconn != null)
	{
		$message .= "詳細情報：".pg_last_error($dbconn)."<br>\n";
	}

	return $message;
}



/****************************************************************
* 機　能： 指定された行事区分コードに対応する行事の名称を取得します。
* 引　数： ［入力］$code　行事区分コード
* 戻り値： 行事名称
****************************************************************/
function gyojiKubunNameGet($code)
{
    switch($code)
    {
        case 1: return "特別展示";
        case 2: return "絵はがき教室";
        case 3: return "イベント";
        default: return "";
    }
}



?>

	