<?php
require 'vendor/autoload.php';
require 'db.php';

$userStore = $nosql->store('users');

$times = 1;

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

($userStore->findAll());

//var_dump($userStore->deleteByDocId(1000));

$eta = hrtime(true) - $start;
echo "\n";
echo $eta / 1e+6;
