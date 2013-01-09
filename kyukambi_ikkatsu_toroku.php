<?php
	require_once "php_functions/common_functions.php";

	$fromDay = null;
	$toDay   = null;
	$dbconn  = null;
	$warningMessage = "";
	$wariningDateList = array();

	if (isset($_POST["kaishibi"]))
	{
		global $fromDay, $toDay;
		
		//実行ボタン押下時の処理
		$fromDay = $_POST["kaishibi"];
		$toDay   = $_POST["shuryobi"];

		//開始日以降、最初の火曜日を探す
		$fromDayWeek = date('w', strtotime($fromDay));

		switch ($fromDayWeek)
		{
			case 0:
			case 1: $tuesday = getFirstTuesday(2 - $fromDayWeek);
					break;
			case 2: $tuesday = $fromDay;
					break;
			case 3:
			case 4:
			case 5:
			case 6: $tuesday = getFirstTuesday(9 - $fromDayWeek);
					break;
		}

		//期間（終了）を超えるまで実施する
		$kyukambiArray = array();
		
		while (strtotime($tuesday) <= strtotime($toDay))
		{
			if (isHoliday($tuesday) == false)
			{
				//火曜日が祝日でない場合は定休日
				$kyukambiArray[] = $tuesday;
			}
			else
			{
				//火曜日が祝日の場合は翌日（平日）を振替定休日とする
				$nextDay = getNextDay($tuesday);
				
				while (isHoliday($nextDay) == true)
				{
					$nextDay = getNextDay($nextDay);
				}
				
				$kyukambiArray[] = $nextDay;
			}
		
			$tuesday = getNextTuesday($tuesday);
		}
		
		if (count($kyukambiArray) == 0)
		{
			$warningMessage = "指定された期間に休館日は発生しませんでした。\n";
		}
		else
		{
			//DB登録
			if (kyukambiInsert($kyukambiArray) == true)
			{
				//休館日一覧画面に自動遷移
				$_SESSION['gyoji-iti_first_access'] = NULL;
				echo "<script language='JavaScript'>document.location = 'kyukambi_ichiran.php';</script>";
			}
			else
			{
				$warningMessage = "以下の日付の休館日は既に登録されている為、処理を中止しました。<br>\n".
									"削除してから再度実行してください。";
			}
		}
	}
	
	
	//開始日直近の火曜日取得
	function getFirstTuesday($addCount)
	{
		global $fromDay;
		
		return date("Y/m/d", strtotime("+".$addCount." day" , 
					strtotime($fromDay)));
	}

	//次の火曜日取得
	function getNextTuesday($currentTuesday)
	{
		return date("Y/m/d", strtotime("+7 day",
					strtotime($currentTuesday)));
	}

	//翌日取得
	function getNextDay($currentDay)
	{
		return date("Y/m/d", strtotime("+1 day",
					strtotime($currentDay)));
	}

	//祝日判定
	function isHoliday($targetDay)
	{
		//祝日
		//※以下の可変の祝日は月曜日にしかならない為、考慮不要
		//	成人の日：1月第2月曜日、海の日：7月第3月曜日
		//	敬老の日：9月第3月曜日、体育の日：10月第2月曜日

		$holidayList = array(array("1/1", "元日"),
							 array("2/11","建国記念の日"),
							 array("4/29", "昭和の日"),
							 array("5/3", "憲法記念日："),
							 array("5/4", "みどりの日"),
							 array("5/5", "こどもの日"),
							 array("11/3", "文化の日"),
							 array("11/23", "勤労感謝の日"),
							 array("12/23", "天皇誕生日"));

		list($year, $month, $day) = explode("/", $targetDay);

		for ($i = 0; $i < count($holidayList); $i++)
		{
			list($month_h, $day_h) = explode("/", $holidayList[$i][0]);
		
			if ((int)$month == (int)$month_h &&
				(int)$day == (int)$day_h)
			{
				return true;
			}
		}
		
		//春分の日、秋分の日
		if ((int)$month == 3 &&
			(int)$day == (int)(20.8431+0.242194*($year-1980)-(int)(($year-1980)/4)))
		{
			return true;
		}

		if ((int)$month == 9 &&
			(int)$day == (int)(23.2488+0.242194*($year-1980)-(int)(($year-1980)/4)))
		{
			return true;
		}

		//振替休日
		//※火曜日が振替休日になるのは5/4が日曜日の時の5/6のみ
		if ((int)$month == 5 && (int)$day == 6)
		{
			$day0504 = $year."/05/04";
			if (date("w", strtotime($day0504)) == 0)
			{
				return true;
			}
		}
	
		return false;
	}
	
	//年取得
	function getYear($targetDay)
	{
		return (int)date("\Y", strtotime($targetDay));
	}

	
	//休館日登録
	function kyukambiInsert($kyukambiArray)
	{
		global $dbconn;
		
		if (dbConnect($dbconn) == false)
		{
			exit(dbErrorMessageCreate("DB接続に失敗しました。"));
		}

		//重複チェック
		if (isDuplicate($kyukambiArray) == true)
		{
			return false;
		}
		
		//DB登録
		pg_query($dbconn, "BEGIN"); //トランザクション開始

		for ($i = 0; $i < count($kyukambiArray); $i++)
		{
			$sql = "insert into kyukambi (title, hizuke, shinchaku_kokai, sakusei_sha)".
						"values ('休館日', '".$kyukambiArray[$i]."', false, '".$_SERVER['REMOTE_USER']."')";

			$result = pg_query($dbconn, $sql);

			if ($result == false)
			{
				pg_query($dbconn, "ROLLBACK");
				exit(dbErrorMessageCreate("DB登録に失敗しました。", $sql, $dbconn));
			}
		}

		pg_query($dbconn, "COMMIT");
		
		return true;
	}
	
	
	//重複チェック
	function isDuplicate($kyukambiArray)
	{
		global $dbconn, $fromDay, $toDay, $wariningDateList;

		$sql = "select * from kyukambi where hizuke between '".$fromDay."' and '".$toDay."'";

		$result = pg_query($dbconn, $sql);

		if ($result == false)
		{
			exit(dbErrorMessageCreate("DB抽出に失敗しました。", $sql, $dbconn));
		}

		while($row = pg_fetch_object($result))
		{
			for ($i = 0; $i < count($kyukambiArray); $i++)
			{
				if (strtotime($row->hizuke) == strtotime($kyukambiArray[$i]))
				{
					$wariningDateList[] = $kyukambiArray[$i];
				}
			}
		}

		if (count($wariningDateList) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
?>


<?php
	$pageName = "休館日一括登録";
	echo sentoTagCreate($pageName,kaishibi,shuryobi);
?>

 
<script type="text/javascript">
<!--
	//入力チェック
	function executionConfirm()
	{
		//入力チェック
		if (isValidPeriod(document.inputForm.kaishibi.value,
						 document.inputForm.shuryobi.value, true) == false)
		{
			alert("期間に誤りがあります。");
			return false;
		}

		if (confirm("登録を行いますか？") == false)
		{
			return false;
		}
		
		return true;
	}
// -->
</script>


		<div class="all">
			<form name="inputForm" action="" method="POST"
					onSubmit="return executionConfirm()">
				<div class="header">
				<h2><?= $pageName ?></h2>
				</div>
				<div class="kyotsu">
				<p><a href="menu.php">管理メニュー</a></p>
				<p><a href="kyukambi_toroku.php">休館日登録</a></p>
				<p><a href="kyukambi_ichiran.php">休館日一覧</a></p>
				</div>
				<div class="gyoji-left1">
					<p class="toroku1">期間</p>
				</div>

				<div class="gyoji-right1">
					<p class="toroku2"><input type="text" name="kaishibi" 
						size="10" id="jquery-ui-datepicker-from" />～<input type="text" 
						name="shuryobi" size="10" id="jquery-ui-datepicker-to" />
					</p>
				</div>

				<div class="kyotsu">
					<p>	<input type="hidden" name="mode" value="write">
						<input type="submit" value="実行" class="button">
					</p>
				</div>
 
			</form>
<?php
	if ($warningMessage != "")
	{
		echo "<div class=\"error_message\">\n".$warningMessage."<br><br>\n";

		if (count($wariningDateList) > 0)
		{
			for ($i = 0; $i < count($wariningDateList); $i++)
			{
				echo $wariningDateList[$i].($i != count($wariningDateList) - 1 ? ", " : "");
			}
			
			echo "</div>\n";
		}
	}
?>
		</div>
 
	</body>
</html>
