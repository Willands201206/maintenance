/****************************************************************
* 機　能： 指定された文字列が日付文字列として妥当かチェックします。
* 引　数： ［入力］dateStr　チェック対象文字列
* 戻り値： true：妥当　　false：不適
****************************************************************/
function isValidDate(dateStr)
{
    // 正規表現による書式チェック
    if(!dateStr.match(/^\d{1,4}\/\d{1,2}\/\d{1,2}$/))
	{
        return false;
    }

	var dateArray = dateStr.split("/");
	var year  = parseInt(dateArray[0], 10);
	var month = parseInt(dateArray[1], 10) - 1; //月は0オリジン
	var day   = parseInt(dateArray[2], 10);


    if (month < 0 || 11 < month)
	{
        return false;
    }
	
    var daysArray = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    var endDay = daysArray[month];

    //うるう年の調整
    if (month == 1 && isLeapYear(year))
	{                    
        endDay = endDay + 1;
    }
    if (day < 1 || endDay < day)
	{
        return false;
    }
	
    return true;
}



/****************************************************************
* 機　能： 指定された文字列が期間（開始日／終了日）として
* 　　　　 妥当かチェックします。
* 引　数： ［入力］fromStr　チェック対象文字列（開始日）
* 　　　　 ［入力］toStr  　チェック対象文字列（終了日）
* 　　　　 ［入力］needBoth 開始日／終了日共にチェックを行うか？
* 戻り値： true：妥当　　false：不適
****************************************************************/
function isValidPeriod(fromStr, toStr, needBoth)
{
	//空文字フラグ
	isFromEmpty = (fromStr == "");
	isToEmpty   = (toStr == "");

	//妥当性フラグ
	isFromValid = isValidDate(fromStr);
	isToValid   = isValidDate(toStr);

	if (needBoth == true)
	{
		//◎両方必須の場合
		if (isFromValid == false || isToValid == false)
		{
			return false;
		}
	}
	else
	{
		//◎両方必須でない場合
		//入力された文字列が不正な場合はNG
		if ((isFromEmpty == false && isFromValid == false) ||
			(isToEmpty   == false && isToValid   == false))
		{
			return false;
		}
		
		//両方省略されている場合はOK
		if (isFromEmpty == true && isToEmpty == true)
		{
			return true;
		}
	}

	
	if (isFromEmpty == false && isToEmpty == false)
	{
		//両方入力されている場合は時系列のチェックを行う
		fromDate = new Date(fromStr);
		toDate   = new Date(toStr);
	
		if (fromDate.getTime() > toDate.getTime())
		{
			return false;
		}
	}
	
    return true;
}



/****************************************************************
* 機　能： うるう年判定
* 引　数： ［入力］year　チェック対象年（西暦）
* 戻り値： true：うるう年　　false：平年
****************************************************************/
function isLeapYear(year)
{
    if (year % 400 == 0)
	{
        return true;
    }
	
    if (year % 4 == 0 && year % 100 != 0)
	{
        return true;
    }
	
	return false;
}



/****************************************************************
* 機　能： 未入力判定
* 引　数： ［入力］判定対象のコンポーネントのname属性
* 戻り値： true：妥当　　false：不適
****************************************************************/
function minyuryoku_check(chk_name)
{
	var chk  = document.getElementsByName(chk_name);
	if(chk[0].value == '')
	{
		chk[0].focus();
		return false;
	}
	else
	{
		return true;
	}

}