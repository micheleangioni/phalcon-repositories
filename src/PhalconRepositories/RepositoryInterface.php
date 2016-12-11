<?php

namespace MicheleAngioni\PhalconRepositories;

interface RepositoryInterface
{
    public function all();

    public function find($id);

    public function findOrFail($id);

    public function first();

    public function firstOrFail();

    public function firstBy(array $where = []);

    public function firstOrFailBy(array $where = []);

    public function getBy(array $where = []);

    public function getByLimit($limit, array $where = []);

    public function getByOrder($orderBy, array $where = [], $order = 'desc', $limit = 0);

    public function getIn($whereInKey, array $whereIn = [], $orderBy = NULL, $order = 'desc', $limit = 0);

    public function getNotIn($whereNotInKey, array $whereNotIn = [], $orderBy = NULL, $order = 'desc', $limit = 0);

    public function getByPage($page = 1, $limit = 10, array $where = [], $orderBy = NULL, $order = 'desc');

    public function create(array $inputs = []);

    public function updateById($id, array $inputs);

    public function destroy($id);

    public function destroyFirstBy(array $where);

    public function count();

    public function countBy(array $where = []);
}
