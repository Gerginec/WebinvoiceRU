<?php
// WebinvoiceRU v 0.2
// Разработчик: Абдуев Арсланали Мурадович
// E-mail: gerginec@gmail.com
session_start();
require_once './../db_mars/connect_db.php';
$link = mysqli_connect($host, $user, $password, $database) 
        or die('Ошибка ' . mysqli_error($link));

mysqli_set_charset($link, 'utf8');
$usr = $_GET['catalog'];
$barcode_html = htmlentities($_POST['barcode']);
$sort_id_html = htmlentities($_POST['sort_id']);
$category_html = htmlentities($_POST['category']);
$name_html = htmlentities($_POST['name']);
$price_html = htmlentities($_POST['price']);
$opt_price_html = htmlentities($_POST['opt_price']);
$count_html = htmlentities($_POST['count']);
$random = $_POST['sort_id'].mt_rand(0,99999999999);
$category_html_auto = $usr;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style.css?v=<? echo bin2hex(random_bytes(5)); ?>">
	<script type="text/javascript" src="jquery.min.js"></script>
	<link rel="manifest" href="site.webmanifest">
	<title>Накладная | WebinvoiceRU</title>
</head>
<body>
	<!--
	 <script>
		setInterval(function(){ 
$(".content").load("index.php .content"); 
}, 1000);
	</script>
-->
	<header>
	<div class="n">
<p class="n_p">Накладная от <?php echo date("d.m.Y");?> г.</p>
</div>
<p class="copyright">Open-source project WebinvoiceRU</p>
</header>
<?php 
if (isset($_GET['clear'])) {
	$sql = mysqli_query($link, "UPDATE `derbent_admin` SET `count` = '' WHERE `derbent_admin`.`id`>=0 ");
	$sql = mysqli_query($link, "DELETE FROM `derbent` WHERE `id`>'0'");
	echo "ТОВАР УДАЛЕН!";
	// ДОБАВИТЬ ОЧИСТКУ НАКЛАДНОЙ ТОЖЕ
//	echo "<style>
//	.content { display: none;}
//	.print_div {display: none;}
//	</style>";
//	echo "<h1>Очистка выполнена!</h1>";
}
?>
<div class="content">


<?php
if (isset($_POST['name'])) {
	if (isset($_GET['red_id'])) {

		$sql = mysqli_query($link, "UPDATE `derbent_admin` SET `name` = '$name_html', `sort_id` = '$sort_id_html', `count` = '$count_html' WHERE `id`={$_GET['red_id']}");
		echo "ТОВАР ИЗМЕНЁН!";
		echo "<meta http-equiv=\"refresh\" content=\"0;URL=/derbent/\">";
		echo $_POST['count'];
	}
	else {
		$sql = mysqli_query($link, "INSERT INTO `derbent_admin` (`name`, `sort_id`, `count`) VALUES ('$name_html', '$sort_id_html', '')");
		echo "ТОВАР ДОБАВЛЕН В БАЗУ!";
		echo "<meta http-equiv=\"refresh\" content=\"0;URL=/derbent/\">";
	}
}

if (isset($_GET['del_id'])) {
	echo "Вы точно хотите удалить товар ". $_POST['name'] ." ?";
	//РАЗРАБОТАТЬ МЕТОД УВЕДОМЛЕНИЯ ПЕРЕД УДАЛЕНИЕМ.
	$sql = mysqli_query($link, "DELETE FROM `derbent_admin` WHERE `id`={$_GET['del_id']}");
	echo "ТОВАР УДАЛЕН!";
	echo "<meta http-equiv=\"refresh\" content=\"0;URL=/derbent/\">";
}
if (isset($_GET['delprice_id'])) {
	$sql = mysqli_query($link, "DELETE FROM `derbent` WHERE `id`={$_GET['delprice_id']}");
	echo "ТОВАР УДАЛЕН!";
	#echo "<meta http-equiv=\"refresh\" content=\"0;URL=/derbent/\">";
}

if (isset($_GET['print'])) {
echo "<style> .content {display: none;} 
	img {display: none;}
	.tbtb {width: 25px;}
	.tb {font-size: 14px; border: 1px solid #000000;}
	.tb td {padding-left: 5px; border: 1px solid #000000;}
	.tb th {border: 1px solid #000000;}
	.tb_del {display: none;}
	</style>";
	#echo "<script>print();</script>";
}


if (isset($_GET['add_id'])) {
	$sql = mysqli_query($link, "INSERT INTO `derbent` (`name`, `sort_id`, `count`) SELECT `name`, `sort_id`, `count` FROM `derbent_admin` WHERE `id`={$_GET['add_id']}");
		#echo "<meta http-equiv=\"refresh\" content=\"0;URL=/derbent/\">";
		echo "<h1>".$name_html."</h1>";
}

if (isset($_GET['red_id'])) {
	$sql = mysqli_query($link, "SELECT * FROM `derbent_admin` WHERE `id`='{$_GET['red_id']}'");
	$product = mysqli_fetch_array($sql);
	#echo "<h1>FFFFFSDFSFSDF</h1>";
}

if (isset($_GET['add_pr'])) {
		$sql = mysqli_query($link, "INSERT INTO `derbent` SELECT * FROM `derbent_admin` WHERE `count`> '0'");
}

$add_sql = "<form action=\"\" class=\"add_menu\" method=\"POST\">
<table border=\"0\"><tr>
<td>
<input type=\"text\" placeholder=\"Введите название\" name=\"name\" 
value=\"".$product['name']."\">
</td>
<td>
<input type=\"text\" placeholder=\"Введите номер сортировки\" name=\"sort_id\" 
value=\"".$product['sort_id']."\">
</td>
<td>
<input type=\"text\" placeholder=\"Сколько упаковок?\" name=\"count\" 
value=\"".$product['count']."\">
</td>
<td>
<input class=\"inputsubmit_admin\" type=\"submit\" value=\"Добавить\">
</td>
</table>
</form>";
echo "<div class='menu'>";
echo $add_sql;
echo "<a class='a' href=\"https://arsik2.site/derbent/?print\">Печать</a>";
echo "&nbsp;";
echo "<a class='a' href=\"https://arsik2.site/derbent/?add_pr\">Добавить отмеченные</a>";
echo "&nbsp;";
echo "<a class='a' href=\"https://arsik2.site/derbent/?clear\">Очистка</a>";
echo "</div>";
?>

<?php 
 $sql = mysqli_query($link, "set @n:=0");
 $sql = mysqli_query($link, "SELECT @n:=@n+1 as `num`, `id`, `sort_id`, `name`, `count` FROM `derbent_admin` ORDER BY `sort_id` ASC, `name` ASC");
 echo "<table class='tb'><tr class='tbtr'><th>№</th><th></th><th>Кол-во</th><th></th><th>Наименование / Админ-панель</th><th class='tbtr2'></th></tr>";
    while ($chek_list = mysqli_fetch_array($sql)) {
       echo "<tr><td class='tbtb'>". $chek_list['num'] ."</td>" .
            "<td class='tbtb2'><a href=\"?add_id={$chek_list['id']}\"><img src=\"add.png\" width=\"23\" 
   height=\"23\" alt=\"Добавить\"></a></td>";
   #"<td class='tbtb2'><input type='text' name='count' value='". $chek_list['count'] . "' class='input_tb'></td>" .
   if ($chek_list['count'] !='') {
   echo "<td class='tbtb2'><p>{$chek_list['count']} уп</p></td>";
   } else {echo "<td class='tb_count'><p>{$chek_list['count']}</p></td>";}
  echo "<td class='tbtb2'><a href=\"?red_id={$chek_list['id']}\"><img src=\"edit.png\" width=\"23\" 
   height=\"23\" alt=\"Изменить\"></a></td>" .
            "<td>". $chek_list['name'] . "</td>" .
            "<td class='tbtb2'><a href=\"?del_id={$chek_list['id']}\"><img src=\"del.png\" width=\"23\" 
   height=\"23\" alt=\"Удалить\"></a></td><tr>";
    }
 echo "</table></div>";
#-----------------------------
 $sql = mysqli_query($link, "set @n:=0");
 $sql = mysqli_query($link, "SELECT @n:=@n+1 as `num`, `id`, `sort_id`, `name`, `count` FROM `derbent` ORDER BY `sort_id` ASC, `name` ASC");
 echo "<div class='print_div'>";
 echo "<table class='tb'><tr class='tbtr'><th>№</th><th>Наименование</th><th>Кол-во</th><th class='tb_del'></th></tr>";
    while ($chek_list = mysqli_fetch_array($sql)) {
       echo "<tr><td class='tbtb'>". $chek_list['num'] . "</td>" .
            "<td>". $chek_list['name'] . "</td>" . 
            "<td class='tb_count'>". $chek_list['count'] . " уп</td>" .
            "<td class='tb_del'><a href=\"?delprice_id={$chek_list['id']}\"><img src=\"del.png\" width=\"23\" 
   height=\"23\" alt=\"Удалить\"></a></td></tr>";
    }
 echo "</table></div>";
 if (isset($_GET['red_id'])) {

 } else {
   echo "<script>
	window.onscroll = function() {
	localStorage.setItem('value', window.pageYOffset);
};
localStorage.getItem('value') && window.scrollTo(0, localStorage.getItem('value'));
</script>";
 }
?>
<!--
<script>
	window.onscroll = function() {
	localStorage.setItem('value', window.pageYOffset);
};
localStorage.getItem('value') && window.scrollTo(0, localStorage.getItem('value'));
</script>
-->
</body>
</html>