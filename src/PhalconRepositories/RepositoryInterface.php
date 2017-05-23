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

    public function getByLimit(int $limit, array $where = []);

    public function getByOrder(string $orderBy, array $where = [], string $order = 'desc', int $limit = 0);

    public function getIn(string $whereInKey, array $whereIn = [], string $orderBy = null, string $order = 'desc', int $limit = 0);

    public function getNotIn(string $whereNotInKey, array $whereNotIn = [], string $orderBy = null, string $order = 'desc', int $limit = 0);

    public function getInAndWhereByPage(int $page = 1, int $limit = 10, string $whereInKey = null, array $whereIn = [], array $where = [], string $orderBy = null, string $order = 'desc' );

    public function getByPage(int $page = 1, int $limit = 10, array $where = [], string $orderBy = null, string $order = 'desc');

    public function create(array $inputs = []);

    public function updateById($id, array $inputs);

    public function destroy($id);

    public function destroyFirstBy(array $where);

    public function count();

    public function countBy(array $where = []);
}
