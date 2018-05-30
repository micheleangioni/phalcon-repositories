<?php

namespace MicheleAngioni\PhalconRepositories;

use Phalcon\Mvc\MongoCollection;

class AbstractCollectionRepository implements RepositoryInterface
{
    /**
     * @var MongoCollection
     */
    protected $model;

    /**
     * Return all records.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->model->find();
    }

    // <--- QUERYING METHODS --->

    /**
     * Find a specific record.
     * Return false if not found.
     *
     * @param  string $id
     *
     * @return MongoCollection|false
     */
    public function find($id)
    {
        return $this->model->findById($id);
    }

    /**
     * Find a specific record.
     * Throws exception if not found.
     *
     * @param  string  $id
     * @throws ModelNotFoundException
     *
     * @return MongoCollection
     */
    public function findOrFail($id)
    {
        if (!$record = $this->find($id)) {
            throw new ModelNotFoundException("Caught ModelNotFoundException in " . __METHOD__ . ' at line ' . __LINE__ . ': Model not found.');
        }

        return $record;
    }

    /**
     * Return the first record of the table.
     * Return false if no record is found.
     *
     * @return MongoCollection|false
     */
    public function first()
    {
        return $this->model->findFirst();
    }

    /**
     * Return the first record.
     * Throws exception if no record is found.
     *
     * @throws ModelNotFoundException
     * @return MongoCollection|false
     */
    public function firstOrFail()
    {
        if (!$record = $this->model->findFirst()) {
            throw new ModelNotFoundException("Caught ModelNotFoundException in " . __METHOD__ . ' at line ' . __LINE__ . ': No models found.');
        }

        return $record;
    }

    /**
     * Return the first record querying input parameters.
     * Return false if no record is found.
     *
     * @param  array  $where
     *
     * @return MongoCollection|false
     */
    public function firstBy(array $where = [])
    {
        return $this->model->findFirst([$where]);
    }

    /**
     * Return the first record querying input parameters.
     * Throws exception if no record is found.
     *
     * @param  array $where
     * @throws ModelNotFoundException
     *
     * @return MongoCollection
     */
    public function firstOrFailBy(array $where = [])
    {
        $record = $this->model->findFirst([$where]);

        if (!$record) {
            throw new ModelNotFoundException("Caught ModelNotFoundException in " . __METHOD__ . ' at line ' . __LINE__ . ': No model found.');
        }

        return $record;
    }

    /**
     * Return records querying input parameters.
     *
     * @param  array $where
     *
     * @return array
     */
    public function getBy(array $where = [])
    {
        return $this->model->find([$where]);
    }

    /**
     * Return the first $limit records querying input parameters.
     *
     * @param int $limit
     * @param array $where
     *
     * @return array
     */
    public function getByLimit(int $limit, array $where = [])
    {
        $input = [
            $where,
            'limit' => $limit
        ];

        return $this->model->find($input);
    }

    /**
     * Return the first ordered $limit records querying input parameters.
     * $order can be 'desc' or 'asc'. $limit = 0 means no limits.
     *
     * @param string $orderBy
     * @param array $where
     * @param string $order
     * @param int $limit
     *
     * @return array
     */
    public function getByOrder(string $orderBy, array $where = [], string $order = 'desc', int $limit = 0)
    {
        if ($order === 'desc') {
            $code = -1;
        } else if ($order === 'asc') {
            $code = 1;
        } else {
            throw new \InvalidArgumentException('$order must be desc or asc');
        }

        $input = [
            $where,
            'sort'  => [
                $orderBy => $code,
            ]
        ];

        if ($limit) {
            $input['limit']  = $limit;
        }

        return $this->model->find($input);
    }

    /**
     * Return the first ordered $limit records querying input parameters.
     * $limit = 0 means no limits.
     *
     * @param string $whereInKey
     * @param array $whereIn
     * @param string|null $orderBy
     * @param string $order
     * @param int $limit
     *
     * @return array
     */
    public function getIn(string $whereInKey, array $whereIn = [], string $orderBy = null, string $order = 'desc', int $limit = 0)
    {
        $where = [
            $whereInKey => [
                '$in'=> $whereIn
            ]
        ];

        if ($order === 'desc') {
            $code = -1;
        } else if ($order === 'asc') {
            $code = 1;
        } else {
            throw new \InvalidArgumentException('$order must be desc or asc');
        }

        if (!$orderBy) {
            $input = [$where];
        } else {
            $input = [
                $where,
                'sort'  => [
                    $orderBy => $code,
                ]
            ];
        }

        if ($limit) {
            $input['limit']  = $limit;
        }

        return $this->model->find($input);
    }

    /**
     * Return the first ordered $limit records querying input parameters.
     * $limit = 0 means no limits.
     *
     * @param int $page
     * @param int $limit
     * @param string $whereInKey
     * @param array $whereIn
     * @param array $where
     * @param string|null $orderBy
     * @param string $order
     *
     * @return array
     */
    public function getInAndWhereByPage(int $page = 1, int $limit = 10, string $whereInKey = null, array $whereIn = [], array $where = [], string $orderBy = null, string $order = 'desc' )
    {
        $where = [
            $whereInKey => [
                '$in'=> $whereIn
            ]
        ];

        if ($order === 'desc') {
            $code = -1;
        } else if ($order === 'asc') {
            $code = 1;
        } else {
            throw new \InvalidArgumentException('$order must be desc or asc');
        }

        if (!$orderBy) {
            $input = [$where];
        } else {
            $input = [
                $where,
                'sort'  => [
                    $orderBy => $code,
                ]
            ];
        }

        if ($limit) {
            $input['limit']  = $limit;
            $input['skip'] = $limit * ($page - 1);
        }

        return $this->model->find($input);
    }

    /**
     * Return the first ordered $limit records querying input parameters.
     * $limit = 0 means no limits.
     *
     * @param string $whereNotInKey
     * @param array $whereNotIn
     * @param string|null $orderBy
     * @param string $order
     * @param int $limit
     *
     * @return array
     */
    public function getNotIn(string $whereNotInKey, array $whereNotIn = [], string $orderBy = null, string $order = 'desc', int $limit = 0)
    {
        $where = [
            $whereNotInKey => [
                '$nin'=> $whereNotIn
            ]
        ];

        if ($order === 'desc') {
            $code = -1;
        } else if ($order === 'asc') {
            $code = 1;
        } else {
            throw new \InvalidArgumentException('$order must be desc or asc');
        }

        if (!$orderBy) {
            $input = [$where];
        } else {
            $input = [
                $where,
                'sort'  => [
                    $orderBy => $code,
                ]
            ];
        }

        if ($limit) {
            $input['limit']  = $limit;
        }

        return $this->model->find($input);
    }

    /**
     * Return all results that have a required relationship.
     *
     * @param  string $relation
     * @param  array $where
     * @param  int $hasAtLeast = 1
     *
     * @return array
     */
    public function getHas(string $relation, array $where = [], int $hasAtLeast = 1)
    {
        // TODO
        throw new \BadMethodCallException('TODO Method');
    }

    /**
     * Return the first result that has a required relationship.
     * Return null if no record is found.
     *
     * @param  string $relation
     * @param  array $where
     * @param  int $hasAtLeast = 1
     *
     * @return array
     */
    public function hasFirst(string $relation, array $where = [], int $hasAtLeast = 1)
    {
        // TODO
        throw new \BadMethodCallException('TODO Method');
    }

    /**
     * Return the first result that have a required relationship.
     * Throws exception if no record is found.
     *
     * @param  string $relation
     * @param  array $where
     * @param  int $hasAtLeast = 1
     *
     * @throws ModelNotFoundException
     *
     * @return array
     */
    public function hasFirstOrFail(string $relation, array $where = [], int $hasAtLeast = 1)
    {
        // TODO
        throw new \BadMethodCallException('TODO Method');
    }

    /**
     * Return all results that have a required relationship with input constraints.
     *
     * @param  string $relation
     * @param  array $where
     * @param  array $whereHas
     *
     * @return array
     */
    public function whereHas(string $relation, array $where = [], array $whereHas = [])
    {
        // TODO
        throw new \BadMethodCallException('TODO Method');
    }

    /**
     * Get ordered results by Page.
     *
     * @param  int $page
     * @param  int $limit
     * @param  array $where
     * @param  string|null $orderBy
     * @param  string $order
     *
     * @return array
     */
    public function getByPage(int $page = 1, int $limit = 10, array $where = [], string $orderBy = null, string $order = 'desc')
    {
        if ($order === 'desc') {
            $code = -1;
        } else if ($order === 'asc') {
            $code = 1;
        } else {
            throw new \InvalidArgumentException('$order must be desc or asc');
        }

        $input = [
            $where,
            'limit' => $limit,
            'skip' => $limit * ($page - 1)
        ];

        if ($orderBy) {
            $input['sort'] = [$orderBy => $code];
        }

        return $this->model->find($input);
    }

    /**
     * Perform an Aggregation search.
     * Eg.
     * $this::aggregate(
     * [
     *   [
     *     '$group' => [
     *       '_id' => '$article_id',
     *       'count'  => [
     *         '$sum' => 1,
     *       ],
     *     ],
     *   ],
     * ]
     * );
     *
     * @param  array  $match
     * @param  array  $project
     * @param  array  $group
     * @param  int  $limit
     *
     * @return array
     */
    public function getAggregate(array $match = [], array $project = [], array $group = [], int $limit = 0)
    {
        $aggregateArray = [];

        if (count($match) > 0) {
            $matchArray = [
                '$match' => $match
            ];

            $aggregateArray[] = $matchArray;
        }

        if (count($project) > 0) {
            $projectArray = [
                '$project' => $project
            ];

            $aggregateArray[] = $projectArray   ;
        }

        if (count($group) > 0) {
            $groupArray = [
                '$group' => $group
            ];

            $aggregateArray[] = $groupArray;
        }

        if ($limit > 0) {
            $aggregateArray[]['$limit'] = $limit;
        }

        $result = $this->model::aggregate($aggregateArray);

        return $result->toArray();
    }


    // <--- CREATING / UPDATING / DELETING METHODS --->

    /**
     * Create a new record.
     *
     * @param  array $inputs
     *
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return MongoCollection
     */
    public function create(array $inputs = [])
    {
        $record = clone $this->model;

        foreach ($inputs as $key => $value) {
            $record->$key = $value;
        }

        $result = $record->save();

        if (!$result) {
            $errorMessages = '';

            foreach ($record->getMessages() as $message ) {
                $errorMessages .= $message . '. ';
            }

            throw new \UnexpectedValueException("Caught UnexpectedValueException in " . __METHOD__ . ' at line ' . __LINE__ . ': Model cannot be saved. Error messages: ' . $errorMessages);
        }

        return $record;
    }

    /**
     * Update all records.
     *
     * @param array $inputs
     *
     * @return mixed
     */
    public function updateAll(array $inputs)
    {
        // TODO
        // https://forum.phalconphp.com/discussion/770/how-to-upsert-update-or-insert-mongodb-in-odm-
        // $this->getConnection()->{$this->getSource()}->update($criteria, $new_object, array('upsert' => true))
        throw new \BadMethodCallException('TODO Method');
    }

    /**
     * Update an existing record, retrieved by id.
     *
     * @param  string $id
     * @param  array $inputs
     *
     * @throws ModelNotFoundException
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return MongoCollection
     */
    public function updateById($id, array $inputs)
    {
        $inputs = $this->purifyInputs($inputs);
        $record = $this->findOrFail($id);

        foreach ($inputs as $key => $value) {
            $record->$key = $value;
        }

        $result = $record->save();

        if (!$result) {
            $errorMessages = '';

            foreach ($record->getMessages() as $message ) {
                $errorMessages .= $message . '. ';
            }

            throw new \UnexpectedValueException("Caught UnexpectedValueException in " . __METHOD__ . ' at line ' . __LINE__ . ': Model cannot be updated. Error messages: ' . $errorMessages);
        }

        return $record;
    }

    /**
     * Update the first record matching input parameters.
     *
     * @param array $where
     * @param array $inputs
     * @throws ModelNotFoundException
     *
     * @return MongoCollection
     */
    public function updateFirstBy(array $where, array $inputs)
    {
        // Retrieve the record
        $record = $this->firstOrFailBy($where);

        // Update it

        foreach ($inputs as $key => $value) {
            $record->$key = $value;
        }

        $result = $record->save();

        if (!$result) {
            $errorMessages = '';

            foreach ($record->getMessages() as $message ) {
                $errorMessages .= $message . '. ';
            }

            throw new \UnexpectedValueException("Caught UnexpectedValueException in " . __METHOD__ . ' at line ' . __LINE__ . ': Model cannot be updated. Error messages: ' . $errorMessages);
        }

        return $record;
    }

    /**
     * Update all records matching input parameters.
     *
     * @param array $where
     * @param array $inputs
     *
     * @return mixed
     */
    public function updateBy(array $where, array $inputs)
    {
        // TODO
        throw new \BadMethodCallException('TODO Method');
    }

    /**
     * Update the record matching input parameters.
     * If no record is found, create a new one.
     *
     * @param array $where
     * @param array $inputs
     *
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return MongoCollection
     */
    public function updateOrCreateBy(array $where, array $inputs = [])
    {
        // TODO
        throw new \BadMethodCallException('TODO Method');
    }

    /**
     * Delete input record.
     *
     * @param  string $id
     *
     * @throws ModelNotFoundException
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return bool
     */
    public function destroy($id)
    {
        $record = $this->findOrFail($id);

        $result = $record->delete();

        if (!$result) {
            $errorMessages = '';

            foreach ($record->getMessages() as $message ) {
                $errorMessages .= $message . '. ';
            }

            throw new \UnexpectedValueException("Caught UnexpectedValueException in " . __METHOD__ . ' at line ' . __LINE__ . ': Model cannot be deleted. Error messages: ' . $errorMessages);
        }

        return $result;
    }

    /**
     * Retrieve and delete the first record matching input parameters.
     * Throws exception if no record is found.
     *
     * @param array $where
     *
     * @throws ModelNotFoundException
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return bool
     */
    public function destroyFirstBy(array $where)
    {
        $record = $this->firstOrFailBy($where);

        $result = $record->delete();

        if (!$result) {
            $errorMessages = '';

            foreach ($record->getMessages() as $message ) {
                $errorMessages .= $message . '. ';
            }

            throw new \UnexpectedValueException("Caught UnexpectedValueException in " . __METHOD__ . ' at line ' . __LINE__ . ': Model cannot be deleted. Error messages: ' . $errorMessages);
        }

        return $result;
    }

    /**
     * Retrieve and delete the all records matching input parameters.
     *
     * @param array $where
     *
     * @return mixed
     */
    public function destroyBy(array $where)
    {
        // TODO
        throw new \BadMethodCallException('TODO Method');
    }

    /**
     * Truncate the table.
     *
     * @return mixed
     */
    public function truncate()
    {
        // TODO
        throw new \BadMethodCallException('TODO Method');
    }

    // <--- COUNT METHODS --->

    /**
     * Count the number of records.
     *
     * @return int
     */
    public function count()
    {

        return $this->countBy([]);
    }

    /**
     * Count the number of records matching input parameters.
     *
     * @param array $match
     * @return int
     */
    public function countBy(array $match = [])
    {
        $aggregateArray = [];

        if (count($match) > 0) {
            $matchArray = [
                '$match' => $match
            ];

            $aggregateArray[] = $matchArray;
        }

        $aggregateArray[] = [
            '$count' => 'number'
        ];

        $result = $this->model::aggregate($aggregateArray)->toArray();

        if (count($result)) {
            return $result[0]['number'];
        }

        return 0;
    }

    /**
     * Count all records that have a required relationship and matching input parameters..
     *
     * @param  string $relation
     * @param  array $where
     * @param  array $whereHas
     *
     * @return int
     */
    public function countWhereHas(string $relation, array $where = [], array $whereHas = [])
    {
        // TODO
        throw new \BadMethodCallException('TODO Method');
    }

    /**
     * Remove keys from the $inputs array beginning with '_' .
     *
     * @param  array $inputs
     *
     * @return array
     */
    protected function purifyInputs(array $inputs)
    {
        foreach ($inputs as $key => $input) {
            if ($key[0] === '_') {
                unset($inputs[$key]);
            }
        }
        return $inputs;
    }
}
