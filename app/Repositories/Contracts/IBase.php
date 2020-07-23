<?php


namespace App\Repositories\Contracts;


interface IBase
{
    public function all();
    public function find($id);
    public function findWhere($column, $value);
    public function findWhereFirst($column, $value);
    public function paginate($perPage);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function groupByColumn($column);
    public function findFirst();
    public function orderAll($column, $direction);
    public function syncRelation($id, $relation, $data);
    public function detachRelation($id, $relation, $data);
    public function deleteRelation($id, $relation);
    public function findFirstBySeveralColumns(array $params);


}
