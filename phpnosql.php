<?php

namespace NoSQL;

use Medoo\Medoo;

class NoSQL
{
  protected static $docid_columns = [
    'sqlite' => [
      'store' => ['VARCHAR(64)', 'PRIMARY KEY', 'NOT NULL', 'UNIQUE'],
      'docid' => ['INTEGER', 'NOT NULL']
    ]
  ];

  protected static $store_columns = [
    'sqlite' => [
      'id' => ['INTEGER', 'PRIMARY KEY', 'AUTOINCREMENT'],
      'docid' => ['INTEGER', 'NOT NULL'],
      'field_type' => ['VARCHAR(30)', 'NOT NULL'],
      'field' => ['VARCHAR(64)', 'NOT NULL'],
      'value_type' => ['VARCHAR(30)', 'NOT NULL'],
      'value' => ['TEXT', 'DEFAULT NULL']
    ]
  ];

  public function __construct(
    protected Medoo $db
  ) {



    $this->db->create('docid', static::$docid_columns[$db->type]);
  }

  public function store(string $store)
  {
    $table = 'store_' . $store;

    $this->db->create($table, static::$store_columns[$this->db->type]);
    $this->db->query(
      'CREATE INDEX IF NOT EXISTS <docid_idx> on ' . $table . ' (<docid>);'
      .
      'CREATE INDEX IF NOT EXISTS <field_idx> on ' . $table . ' (<field>)'
    );

    return new Store($this->db, $store);
  }
}
