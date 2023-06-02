<?php
require 'vendor/autoload.php';
require 'db.php';

use NoSQL\NoSQL;

$nosql = new NoSQL($db);
$userStore = $nosql->store('users');

$times = 1000;

$start = hrtime(true);

/*
for ($i = 0; $i < $times; $i++) {
  $userStore->insert([
    'name' => 'dat',
    'fullname' => 'Hồ Thành Luân',
    'age' => 20,
    'email' => 'lua_n@naver.com',
  ]);
}
*/

$userStore->findAll(['name' => 'asc', 'age' => 'desc']);

//var_dump($userStore->deleteByDocId(1000));

$eta = hrtime(true) - $start;
echo "\n";
echo $eta / 1e+6;
echo "\n";
