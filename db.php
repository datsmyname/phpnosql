<?php
use Medoo\Medoo;
use NoSQL\NoSQL;

$db = new Medoo([
  'type' => 'sqlite',
  'database' => 'db.sqlite'
]);

$db->query('PRAGMA journal_mode = memory;');

$nosql = new NoSQL($db);