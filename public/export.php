<?php
header('Content-type: text/html; charset=utf-8');
$mysqli = new mysqli("localhost", "root", "", "bot");
if ($mysqli->connect_errno) {
    echo "Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

/*
$res = $mysqli->query("SELECT * FROM products");
$pr = [];
while ($row = $res->fetch_assoc()) {
   $pr[] = $row; 
}
$cats = [
    '📱 iPhone' => 4,
	'⌚️ iWatch' => 6,
	'💻iPad + Macbook' => 5,
	'Samsung' => 2,
	'Xiaomi' => 3,
];
$sql = "INSERT INTO `products` (`id`, `category_id`, `position`, `name`, `model`, `country`, `quantity`, `one_hand`, `price`, `price_opt`, `addition_count`, `addition_price`, `buy_count`, `created_at`, `updated_at`) VALUES ";
foreach($pr as $p){
	if($p['subcat'] == 'Без подкатегории'){
		$cat = $cats[$p['cat']];
	} else {
		$cat = $cats[$p['subcat']];
	}
	$q[] = "({$p['id']}, $cat, {$p['product_order']}, '{$p['tovar']}', '{$p['model']}', '{$p['lang']}','{$p['cnt']}', '{$p['maxcnt']}', '{$p['price']}', '{$p['opt_price']}', 0, 0, 0, '2017-08-17 11:12:30', '2017-08-17 11:12:30')";
}
*/
mysqli_query($mysqli,"SET NAMES 'utf8'");
mysqli_query($mysqli,"SET CHARACTER SET 'utf8'");
mysqli_query($mysqli,"SET SESSION collation_connection = 'utf8_general_ci'");
$res = $mysqli->query("SELECT * FROM users");
$pr = [];
while ($row = $res->fetch_assoc()) {
   $pr[] = $row; 
}
$sql = "INSERT INTO `clients` (`id`, `first_name`, `last_name`, `username`, `uid`, `country`, `city`, `step`, `type`, `active`,`created_at`, `updated_at`) VALUES ";
foreach($pr as $p){
	$q[] = "({$p['id']}, '{$p['name']}', '{$p['last']}', '{$p['username']}', '{$p['uid']}', '','{$p['city']}', 0, '{$p['type']}', '{$p['auth']}','2017-08-17 11:12:30', '2017-08-17 11:12:30')";
}

echo $sql.implode(",<br>",$q); exit;