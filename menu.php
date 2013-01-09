<?PHP
	require_once "php_functions/common_functions.php";
	echo menu_sentoTagCreate("管理メニュー");

echo <<< __menu__
		
		<div class="all">
			<div class="header">
			<h2>管理メニュー</h2>
			</div>
			<div class="kanri">
			<h3><a href="gyoji_ichiran.php">行事一覧</a></h3>
			</div>
			<div class="kanri">
			<h3><a href="gyoji_toroku.php">行事登録</a></h3>
			</div>
			<div class="kanri">
			<h3><a href="kyukambi_ichiran.php">休館日一覧</a></h3>
			</div>
			<div class="kanri">
			<h3><a href="kyukambi_toroku.php">休館日登録</a></h3>
			</div>
			<div class="kanri">
			<h3><a href="kyukambi_ikkatsu_toroku.php">休館日一括登録</a></h3>
			</div>


		</div>
	</body>
</html>

__menu__;

?>
