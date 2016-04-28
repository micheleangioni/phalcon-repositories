<?php namespace MicheleAngioni\PhalconRepositories;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\ResultsetInterface;

class AbstractRepository
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
    public function all()
    {
        return $this->model->find();
    }


    // <--- QUERYING METHODS --->

    /**
     * Find a specific record.
     * Return false if not found.
     *
     * @param  int|string  $id
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
     * @param  int|string  $id
     * @throws ModelNotFoundException
     *
     * @return \Phalcon\Mvc\Model
     */
    public function findOrFail($id)
    {
        if (!$record = $this->model->findFirst(["id = :value:", 'bind' => ['value' => $id]])) {
            throw new ModelNotFoundException("Caught ModelNotFoundException in ".__METHOD__.' at line '.__LINE__.': Model not found.');
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
    public function firstOrFail()
    {
        if (!$record = $this->model->findFirst()) {
            throw new ModelNotFoundException("Caught ModelNotFoundException in ".__METHOD__.' at line '.__LINE__.': No models found.');
        }

        return $record;
    }

    /**
     * Return the first record querying input parameters.
     * Return false if no record is found.
     *
     * @param  array  $where
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
     * @param  array  $where
     * @throws ModelNotFoundException
     *
     * @return \Phalcon\Mvc\Model
     */
    public function firstOrFailBy(array $where = [])
    {
        $query = $this->model->query();

        $model = $this->applyWhere($query, $where)->limit(1)->execute()->getFirst();

        if (!$model) {
            throw new ModelNotFoundException("Caught ModelNotFoundException in ".__METHOD__.' at line '.__LINE__.': No model found.');
        }

        return $model;
    }

    /**
     * Return records querying input parameters.
     *
     * @param  array  $where
     * @return ResultsetInterface
     */
    public function getBy(array $where = [])
    {
        $query = $this->model->query();

        $query = $this->applyWhere($query, $where);

        return $query->execute();
    }

    /**
     * Return the first $limit records querying input parameters.
     *
     * @param int  $limit
     * @param array $where
     *
     * @return ResultsetInterface
     */
    public function getByLimit($limit, array $where = [])
    {
        $query = $this->model->query();

        $query = $this->applyWhere($query, $where)->limit($limit);

        return $query->execute();
    }

    /**
     * Return the first ordered $limit records querying input parameters.
     * $order can be 'desc' or 'asc'. $limit = 0 means no limits.
     *
     * @param string  $orderBy
     * @param array  $where
     * @param string  $order
     * @param int  $limit
     *
     * @return ResultsetInterface
     */
    public function getByOrder($orderBy, array $where = [], $order = 'desc', $limit = 0)
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
     * @param string  $whereInKey
     * @param array  $whereIn
     * @param string|null  $orderBy
     * @param string  $order
     * @param int  $limit
     *
     * @return ResultsetInterface
     */
    public function getIn($whereInKey, array $whereIn = [], $orderBy = null, $order = 'desc', $limit = 0)
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
     * @param string  $whereNotInKey
     * @param array  $whereNotIn
     * @param string|null  $orderBy
     * @param string  $order
     * @param int  $limit
     *
     * @return ResultsetInterface
     */
    public function getNotIn($whereNotInKey, array $whereNotIn = [], $orderBy = null, $order = 'desc', $limit = 0)
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
     * @param  int  $page
     * @param  int  $limit
     * @param  array  $where
     * @param  string|null  $orderBy
     * @param  string  $order
     *
     * @return ResultsetInterface
     */
    public function getByPage($page = 1, $limit = 10, array $where = [], $orderBy = null, $order = 'desc')
    {
        $query = $this->model->query();

        if ($orderBy) {
            $query = $this->applyWhere($query, $where)->orderBy($orderBy . ' ' . $order);
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
     * @param  array  $inputs
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return \Phalcon\Mvc\Model
     */
    public function create(array $inputs = [])
    {
        $model = clone $this->model;

        try {
            $result = $model->create($inputs);
        } catch (\Exception $e) {
            throw new \RuntimeException("Caught RuntimeException in ".__METHOD__.' at line '.__LINE__.': ' .$e->getMessage());
        }

        if(!$result) {
            $errorMessages = implode('. ', $model->getMessages());
            throw new \UnexpectedValueException("Caught UnexpectedValueException in ".__METHOD__.' at line '.__LINE__.': Model cannot be saved. Error messages: ' . $errorMessages);
        }

        return $model;
    }

    /**
     * Update all records.
     *
     * @param array $inputs
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
     * @param  int  $id
     * @param  array  $inputs
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return \Phalcon\Mvc\Model
     */
    public function updateById($id, array $inputs)
    {
        $inputs = $this->purifyInputs($inputs);

        $model = $this->findOrFail($id);

        try {
            $result = $model->update($inputs);
        } catch (\Exception $e) {
            throw new \RuntimeException("Caught RuntimeException in ".__METHOD__.' at line '.__LINE__.': ' .$e->getMessage());
        }

        if(!$result) {
            $errorMessages = implode('. ', $model->getMessages());
            throw new \UnexpectedValueException("Caught UnexpectedValueException in ".__METHOD__.' at line '.__LINE__.': Model cannot be updated. Error messages: ' . $errorMessages);
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

        try {
            $result = $model->save($inputs);
        } catch (\Exception $e) {
            throw new \RuntimeException("Caught RuntimeException in ".__METHOD__.' at line '.__LINE__.': ' .$e->getMessage());
        }

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
     * @param  int  $id
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return bool
     */
    public function destroy($id)
    {
        $model = $this->findOrFail($id);

        try {
            $result = $model->delete();
        } catch (\Exception $e) {
            throw new \RuntimeException("Caught RuntimeException in ".__METHOD__.' at line '.__LINE__.': ' .$e->getMessage());
        }

        if(!$result) {
            $errorMessages = implode('. ', $model->getMessages());
            throw new \UnexpectedValueException("Caught UnexpectedValueException in ".__METHOD__.' at line '.__LINE__.': Model cannot be deleted. Error messages: ' . $errorMessages);
        }

        return $result;
    }

    /**
     * Retrieve and delete the first record matching input parameters.
     * Throws exception if no record is found.
     *
     * @param array $where
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return bool
     */
    public function destroyFirstBy(array $where)
    {
        $model = $this->firstOrFailBy($where);

        try {
            $result = $model->delete();
        } catch (\Exception $e) {
            throw new \RuntimeException("Caught RuntimeException in ".__METHOD__.' at line '.__LINE__.': ' .$e->getMessage());
        }

        if(!$result) {
            $errorMessages = implode('. ', $model->getMessages());
            throw new \UnexpectedValueException("Caught UnexpectedValueException in ".__METHOD__.' at line '.__LINE__.': Model cannot be deleted. Error messages: ' . $errorMessages);
        }

        return $result;
    }

    /**
     * Retrieve and delete the all records matching input parameters.
     *
     * @param array $where
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
    public function count()
    {
        return $this->model->count();
    }

    /**
     * Count the number of records matching input parameters.
     *
     * @param  array  $where
     * @return int
     */
    public function countBy(array $where = [])
    {
        $model = clone $this->model;
        $model->assign($where);

        return $model->count();
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
     * Apply the where clauses to input query.
     *
     * @param  Criteria $query
     * @param  array $where
     *
     * @return Criteria
     */
    protected function applyWhere(Criteria $query, array $where)
    {
        $bindingArray = [];
        $counter = 1;

        foreach ($where as $key => $value) {
            if (is_null($value)) {
                if($counter == 1) {
                    $query = $query->where($key . ": IS NULL");

                } else {
                    $query = $query->andWhere($key . ": IS NULL");
                }
            } else {
                if($counter == 1) {
                    $query = $query->where($key . " = :value" . $counter . ":");
                } else {
                    $query = $query->andWhere($key . " = :value" . $counter . ":");
                }

                $bindingArray["value" . $counter] = $value;
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
