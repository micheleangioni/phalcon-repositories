<?php

namespace MicheleAngioni\PhalconRepositories;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\ResultsetInterface;

class AbstractRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * Return all records.
     *
     * @return ResultsetInterface
     */
    public function all(): ResultsetInterface
    {
        return $this->model->find();
    }


    // <--- QUERYING METHODS --->

    /**
     * Find a specific record.
     * Return false if not found.
     *
     * @param  int|string $id
     *
     * @return \Phalcon\Mvc\Model|false
     */
    public function find($id)
    {
        return $this->model->findFirst(["id = :value:", 'bind' => ['value' => $id]]);
    }

    /**
     * Find a specific record.
     * Throws exception if not found.
     *
     * @param  int|string $id
     * @throws ModelNotFoundException
     *
     * @return \Phalcon\Mvc\Model
     */
    public function findOrFail($id): Model
    {
        if (!$record = $this->model->findFirst(["id = :value:", 'bind' => ['value' => $id]])) {
            throw new ModelNotFoundException("Caught ModelNotFoundException in " . __METHOD__ . ' at line ' . __LINE__ . ': Model not found.');
        }

        return $record;
    }

    /**
     * Return the first record of the table.
     * Return false if no record is found.
     *
     * @return \Phalcon\Mvc\Model|false
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
     * @return \Phalcon\Mvc\Model
     */
    public function firstOrFail(): Model
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
     * @param  array $where
     *
     * @return \Phalcon\Mvc\Model|false
     */
    public function firstBy(array $where = [])
    {
        $query = $this->model->query();
        $model = $this->applyWhere($query, $where)->limit(1)->execute()->getFirst();

        return $model;
    }

    /**
     * Return the first record querying input parameters.
     * Throws exception if no record is found.
     *
     * @param  array $where
     *
     * @throws ModelNotFoundException
     *
     * @return \Phalcon\Mvc\Model
     */
    public function firstOrFailBy(array $where = []): Model
    {
        $query = $this->model->query();
        $model = $this->applyWhere($query, $where)->limit(1)->execute()->getFirst();

        if (!$model) {
            throw new ModelNotFoundException("Caught ModelNotFoundException in " . __METHOD__ . ' at line ' . __LINE__ . ': No model found.');
        }

        return $model;
    }

    /**
     * Return records querying input parameters.
     *
     * @param  array $where
     *
     * @return ResultsetInterface
     */
    public function getBy(array $where = []): ResultsetInterface
    {
        $query = $this->model->query();
        $query = $this->applyWhere($query, $where);

        return $query->execute();
    }

    /**
     * Return the first $limit records querying input parameters.
     *
     * @param int $limit
     * @param array $where
     *
     * @return ResultsetInterface
     */
    public function getByLimit(int $limit, array $where = []): ResultsetInterface
    {
        $query = $this->model->query();
        $query = $this->applyWhere($query, $where)->limit($limit);

        return $query->execute();
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
     * @return ResultsetInterface
     */
    public function getByOrder(string $orderBy, array $where = [], string $order = 'desc', int $limit = 0): ResultsetInterface
    {
        $query = $this->model->query();
        $query = $this->applyWhere($query, $where)->orderBy($orderBy . ' ' . $order);

        if ($limit) {
            $query->limit($limit);
        }

        return $query->execute();
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
     * @return ResultsetInterface
     */
    public function getIn(string $whereInKey, array $whereIn = [], string $orderBy = null, string $order = 'desc', int $limit = 0): ResultsetInterface
    {
        $query = $this->model->query();
        $query->inWhere($whereInKey, $whereIn);

        if ($orderBy) {
            $query->orderBy($orderBy . ' ' . $order);
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->execute();
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
     * @return ResultsetInterface
     */
    public function getNotIn(string $whereNotInKey, array $whereNotIn = [], string $orderBy = null, string $order = 'desc', int $limit = 0): ResultsetInterface
    {
        $query = $this->model->query();
        $query->notInWhere($whereNotInKey, $whereNotIn);

        if ($orderBy) {
            $query->orderBy($orderBy . ' ' . $order);
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->execute();
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
     * @return ResultsetInterface
     */
    public function getInAndWhereByPage(int $page = 1, int $limit = 10, string $whereInKey = null, array $whereIn = [], array $where = [], string $orderBy = null, string $order = 'desc' ): ResultsetInterface
    {
        $query = $this->model->query();

        if (count($where) > 0){
            $query = $this->applyWhere($query, $where);
        }

        if ($whereInKey){
            $query->inWhere($whereInKey, $whereIn);
        }

        if ($orderBy) {
            $query->orderBy($orderBy . ' ' . $order);
        }

        if ($limit) {
            $query->limit($limit, $limit * ($page - 1));
        }

        return $query->execute();
    }

    /**
     * Return all results that have a required relationship.
     *
     * @param  string $relation
     * @param  array $where
     * @param  int $hasAtLeast = 1
     *
     * @return ResultsetInterface
     */
    public function getHas($relation, array $where = [], $hasAtLeast = 1)
    {
        // TODO
    }

    /**
     * Return the first result that has a required relationship.
     * Return null if no record is found.
     *
     * @param  string $relation
     * @param  array $where
     * @param  int $hasAtLeast = 1
     *
     * @return ResultsetInterface
     */
    public function hasFirst($relation, array $where = [], $hasAtLeast = 1)
    {
        // TODO
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
     * @return ResultsetInterface
     */
    public function hasFirstOrFail($relation, array $where = [], $hasAtLeast = 1)
    {
        // TODO
    }

    /**
     * Return all results that have a required relationship with input constraints.
     *
     * @param  string $relation
     * @param  array $where
     * @param  array $whereHas
     *
     * @return ResultsetInterface
     */
    public function whereHas($relation, array $where = [], array $whereHas = [])
    {
        // TODO
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
     * @return ResultsetInterface
     */
    public function getByPage(int $page = 1, int $limit = 10, array $where = [], string $orderBy = null, string $order = 'desc'): ResultsetInterface
    {
        $query = $this->model->query();

        if (count($where) > 0){
            $query = $this->applyWhere($query, $where);
        }

        if ($orderBy) {
            $query->orderBy($orderBy . ' ' . $order);
        }

        if ($limit) {
            $query->limit($limit, $limit * ($page - 1));
        }

        return $query->execute();
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
     * @return \Phalcon\Mvc\Model
     */
    public function create(array $inputs = []): Model
    {
        $model = clone $this->model;

        $result = $model->create($inputs);

        if (!$result) {
            $errorMessages = implode('. ', $model->getMessages());
            throw new \UnexpectedValueException("Caught UnexpectedValueException in " . __METHOD__ . ' at line ' . __LINE__ . ': Model cannot be saved. Error messages: ' . $errorMessages);
        }

        return $model;
    }

    /**
     * Update all records.
     *
     * @param array $inputs
     *
     * @return mixed
     */
    public function update(array $inputs)
    {
        // TODO in PHQL or Raw SQL since the query below does NOT update only input columns but all columns

        /*
        return $this->model->save($inputs);
        */
    }

    /**
     * Update an existing record, retrieved by id.
     *
     * @param  int $id
     * @param  array $inputs
     *
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return \Phalcon\Mvc\Model
     */
    public function updateById($id, array $inputs): Model
    {
        $inputs = $this->purifyInputs($inputs);
        $model = $this->findOrFail($id);

        $result = $model->update($inputs);

        if (!$result) {
            $errorMessages = implode('. ', $model->getMessages());
            throw new \UnexpectedValueException("Caught UnexpectedValueException in " . __METHOD__ . ' at line ' . __LINE__ . ': Model cannot be updated. Error messages: ' . $errorMessages);
        }

        return $model;
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
     * @return \Phalcon\Mvc\Model
     */
    public function updateOrCreateBy(array $where, array $inputs = [])
    {
        /*
        $inputs = $this->purifyInputs($inputs);

        // TODO Add the $where condition!!

        $model = clone $this->model;
        $model->assign($inputs);

        $result = $model->save($inputs);

        if(!$result) {
            $errorMessages = implode('. ', $model->getMessages());
            throw new \UnexpectedValueException("Caught UnexpectedValueException in ".__METHOD__.' at line '.__LINE__.': Model cannot be saved nor updated. Error messages: ' . $errorMessages);
        }

        return $model;
        */
    }

    /**
     * Delete input record.
     *
     * @param  int $id
     *
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return bool
     */
    public function destroy($id): bool
    {
        $model = $this->findOrFail($id);

        $result = $model->delete();

        if (!$result) {
            $errorMessages = implode('. ', $model->getMessages());
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
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return bool
     */
    public function destroyFirstBy(array $where): bool
    {
        $model = $this->firstOrFailBy($where);

        $result = $model->delete();

        if (!$result) {
            $errorMessages = implode('. ', $model->getMessages());
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
    }

    /**
     * Truncate the table.
     *
     * @return mixed
     */
    public function truncate()
    {
        // TODO
    }


    // <--- COUNT METHODS --->

    /**
     * Count the number of records.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Count the number of records matching input parameters.
     *
     * @param  array $where
     *
     * @return int
     */
    public function countBy(array $where = []): int
    {
        $bindArr = [];
        $condArr = [];

        $intIndex = 0;

        foreach ($where as $key => $val){
            if (is_array($val)){
                $value = $val[0];
                $op = $val[1];
            } else {
                $value = $val;
                $op = is_null($value) ? 'IS' : '=';
            }

            if (is_null($value)){
                $condArr[] = $key . ' ' . $op . ' NULL';
            } else {
                $this->applyCountSubOr($key, $op, $value, $bindArr, $condArr, $intIndex);
            }
        }

        $condStr = implode(" AND ", $condArr);

        return $this->model->count([
            $condStr,
            "bind" => $bindArr
        ]);
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
    public function countWhereHas($relation, array $where = [], array $whereHas = [])
    {
        // TODO
    }


    // <--- INTERNALLY USED METHODS --->

    /**
     * Build a bracketed or statement of the form (key1 = :value: OR key2 = :value: OR ...)
     * This is a helper function to countBy method
     *
     * @param string $key the keys key1, key2 ... separated by magical '%OR%'
     * @param string $op the operator to apply
     * @param string $value the value to match
     * @param array $bindArr bind array (passed by reference)
     * @param array $condArr the search condition array (passed as reference)
     * @param int $intIndex the index of the integer bind (passed by reference)
     */
    protected function applyCountSubOr($key, $op, $value, &$bindArr, &$condArr, &$intIndex) {
        // Split key by magical '%OR%'
        $orKeys = explode("%OR%", $key);
        $orCounter = 0;
        $whereStr = "";

        foreach ($orKeys as $orK) {
            if ($orCounter === 0){
                $whereStr .= "(";
            } else {
                $whereStr .= " OR ";
            }
            $orCounter++;

            if (is_int($value)) {
                $bindArr[] = $value;
                $whereStr .= $orK . ' ' . $op . ' ?' . $intIndex;
                $intIndex ++;
            } else {
                $bindArr[$orK] = $value;
                $whereStr .= $orK . ' ' . $op . ' :' . $orK . ':';
            }
        }

        $whereStr .= ")";

        $condArr[] = $whereStr;
    }

    /**
     * Build a bracketed or statement of the form (key1 = :value: OR key2 = :value: OR ...)
     * This is a helper function to applyWhere method
     *
     * @param string $key the keys key1, key2 ... separated by magical '%OR%'
     * @param string $op the operator to apply
     * @param string $value the value to match
     * @param array $bindingArray bind array (passed by reference)
     * @param int $counter the index of the bind (passed by reference)
     *
     * @return string
     */
    protected function applySubOr($key, $op, $value, &$bindingArray, &$counter){
        // Split key by magical '%OR%'

        $orKeys = explode("%OR%", $key);
        $whereStr = "";
        $orCounter = 0;

        foreach ($orKeys as $orK){
            if ($orCounter === 0){
                $whereStr .= "(";
            } else{
                $whereStr .= " OR ";
                //NOTE: Counter only needs to be increased when there is at least one OR applied
                $counter ++;
            }
            $orCounter ++;

            $whereStr .= $orK . " " . $op . " :value" . $counter . ":" ;
            $bindingArray["value" . $counter] = $value;

        }
        $whereStr .= ")";

        return($whereStr);
    }

    /**
     * Apply the where clauses to input query.
     *
     * @param  Criteria $query
     * @param  array $where
     *         $where can be: 'key' => 'value'
     *                    or: 'key' => ['value', 'operator'] with operator e.g. like
     *
     * @return Criteria
     */
    public function applyWhere(Criteria $query, array $where)
    {
        $bindingArray = [];
        $counter = 1;

        foreach ($where as $key => $val) {
            if (!is_array($val)){
                $value = $val;
                $op = is_null($value) ? 'IS' : '=';
            } else {
                $value =$val[0];
                $op = $val[1];
            }

            if (is_null($value)) {
                if ($counter == 1) {
                    $query = $query->where($key . " " . $op . " " . "NULL");
                } else {
                    $query = $query->andWhere($key . " " . $op . " ". "NULL");
                }
            } else {
                if ($counter == 1) {
                    $query = $query->where($this->applySubOr($key, $op, $value, $bindingArray, $counter));
                } else {
                    $query = $query->andWhere($this->applySubOr($key, $op, $value, $bindingArray, $counter));
                }
            }

            $counter++;
        }
        return $query->bind($bindingArray);
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
