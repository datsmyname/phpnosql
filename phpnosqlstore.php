<?php

namespace NoSQL;

use Exception;
use Medoo\Medoo;

class Store
{
  protected string $table;

  public function __construct(
    protected Medoo $db,
    protected string $store
  ) {
    $this->table = 'store_' . $store;
  }

  public function lastDocId(): int|null
  {
    return $this->db->get('docid', 'docid', ['store' => $this->store]);
  }

  public function insert(array $data): int
  {
    $docId = $this->lastDocId() + 1;

    $insertData = [];
    foreach ($data as $field => $value) {
      [$field_type, $field] = field_encode($field);
      [$value_type, $value] = field_encode($value);
      $insertData[] = [
        'docid' => $docId,
        'field_type' => $field_type,
        'field' => $field,
        'value_type' => $value_type,
        'value' => $value
      ];
    }
    $this->db->insert($this->table, $insertData);

    if ($this->db->has('docid', ['store' => $this->store])) {
      $this->db->update('docid', ['docid' => $docId], ['store' => $this->store]);
    } else {
      $this->db->insert('docid', ['store' => $this->store, 'docid' => $docId]);
    }

    return $docId;
  }

  public function insertMany(array $data): array
  {
    $docIds = [];

    foreach ($data as $d) {
      $docIds[] = $this->insert($d);
    }

    return $docIds;
  }

  public function updateOrInsert(array $data, bool $autoGenerateIdOnInsert = true): int
  {
  }

  public function updateOrInsertMany(array $data, bool $autoGenerateIdOnInsert = true): array
  {
  }

  /**
   * Get all documents
   * @param string|array|null $orderBy $fieldName or $orderBy array($fieldName => $order). $order can be "asc" or "desc"
   * @param int|null $limit the amount of data record to limit
   * @param int|null $offset the amount of data record to skip
   * @return array
   */
  public function findAll(string|array $orderBy = null, int $limit = null, int $offset = null): array
  {
    $datas = [];

    $where = [];

    if (!is_null($orderBy)) {
      if (is_string($orderBy)) {
        $where['ORDER'] = $orderBy;
      } else {
        foreach ($orderBy as $field => $order) {
          $where['ORDER'][$field] = $order;
        }
      }
    }

    if (!is_null($limit)) {
      $where['LIMIT'] = $limit;
    }

    if (!is_null($offset)) {
      $where['LIMIT'] = [$offset, $limit !== null ? $limit : -1];
    }

    $this->db->select(
      $this->table,
      ['@docid'],
      $where,
      function ($data) use (&$datas) {
        $datas[] = $this->findByDocId($data['docid']);
      }
    );

    return $datas;
  }

  public function findByDocId(int $docId): array|null
  {
    $found = false;
    $docData = ['_docId' => $docId];

    $this->db->select(
      $this->table,
      ['field_type', 'field', 'value_type', 'value'],
      ['docid' => $docId],
      function ($data) use (&$docData, &$found) {
        $field = field_decode($data['field_type'], $data['field']);
        $value = field_decode($data['value_type'], $data['value']);
        $docData[$field] = $value;
        $found = true;
      }
    );

    return $found ? $docData : null;
  }

  public function findOneBy(array $criteria): array|null
  {
  }

  /**
   * Get the amount of all documents stored
   * @return int
   */
  public function count(): int
  {
    return $this->db->query('SELECT COUNT(DISTINCT <docid>) FROM <' . $this->table . '>;')->fetch()[0];
  }

  function deleteBy(array $criteria, int $returnOption = Query::DELETE_RETURN_BOOL): array|bool|int
  {
  }

  /**
   * Delete one document with its id
   * @param int  $docId The id of a document located in the store.
   * @return bool
   */
  public function deleteByDocId(int $docId): int|bool
  {
    try {
      $this->db->delete($this->table, ['docid' => $docId]);
      return $docId;
    } catch (Exception $e) {
    }
    return false;
  }
}

function field_encode($value)
{
  $type = gettype($value);
  if ($value == 'array' || $value == 'object') $value = json_encode($value);

  $value = strval($value);

  return [$type, $value];
}

function field_decode($type, $value)
{
  if ($type == 'integer') return intval($value);
  if ($type == 'boolean') return boolval($value);
  if ($type == 'double') return doubleval($value);
  if ($type == 'object') return json_decode($value);
  if ($type == 'array') return json_decode($value, true);
  if ($type == 'NULL') return null;
  return $value;
}
