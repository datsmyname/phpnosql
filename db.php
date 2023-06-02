<?php
use Medoo\Medoo;

$db = new Medoo([
  'type' => 'sqlite',
  'database' => 'db.sqlite'
]);

$db->query('PRAGMA journal_mode = memory;');