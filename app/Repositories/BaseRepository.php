<?php

namespace App\Repositories;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class BaseRepository
{
    /*
     * Prefix:
     *  _gte or _lte for filter a range
     *  _ne to exclude a value
     *  _like to search like
     *  q to search all
     */

    protected int $pageSize = 20;
    protected array $sortFields = [];
    protected array $fullTextSearchFields = [];
    protected array $filterFields = [];
    private string $searchLikePrefix = '_like';
    private array $orderValues = ['desc', 'asc'];
    private BaseModel $model;

    public function __construct()
    {
        $this->setModel();
    }

    /**
     * Set model
     */
    private function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    abstract protected function getModel();

    /**
     * Get all
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * Get by condition
     *
     * @param array $conditions
     * @return mixed
     */
    public function getBy(array $conditions = []): mixed
    {
        return $this->model->where($conditions);
    }

    /**
     * Set filter conditions
     *
     * @param         $collection
     * @param         $filterField
     * @param         $filterValue
     *
     * @return mixed
     */
    protected function setFilter($collection, $filterField, $filterValue): mixed
    {
        if (!in_array($filterField, $this->filterFields)) {
            return $collection;
        }

        // support operators _like
        if (strpos($filterField, $this->searchLikePrefix) > 0) {
            $dbFieldLength = strlen($filterField) - strlen($this->searchLikePrefix);
            $dbField = substr($filterField, 0, $dbFieldLength);

            if (is_string($filterValue)) {
                return $collection->where($dbField, 'like', "%$filterValue%");
            }
            if (is_array($filterValue)) {
                return $collection->where(function ($query) use ($dbField, $filterValue) {
                    foreach ($filterValue as $value) {
                        $query->orWhere($dbField, 'like', "%$value%");
                    }
                });
            }
            return $collection;
        }

        if (is_array($filterValue)) {
            return $collection->whereIn($filterField, $filterValue);
        } else {
            return $collection->where($filterField, $filterValue);
        }
    }

    /**
     * Create
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function create(array $attributes = []): mixed
    {
        return $this->model->create($attributes);
    }

    /**
     * Create many
     *
     * @param array $records
     *
     * @return bool
     */
    public function createMany(array $records = []): bool
    {
        if (empty($records)) {
            return false;
        }
        $currentTime = Carbon::now()->format('Y-m-d H:i:s');
        foreach ($records as $key => $value) {
            if (empty($value['id'])) {
                $records[$key]['id'] = (string)Str::orderedUuid();
            }
            if (empty($value['created_at'])) {
                $records[$key]['created_at'] = $currentTime;
            }
            if (empty($value['updated_at'])) {
                $records[$key]['updated_at'] = $currentTime;
            }
        }
        return $this->model->insert($records);
    }

    /**
     * Create or update a record matching the attributes, and fill it with values.
     *
     * @param array $attributes
     * @param array $values
     * @return Model|static
     */
    public function updateOrCreate(array $attributes, array $values = []): Model|static
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    /**
     * Insert new records or update the existing ones.
     * All databases except SQL Server require the columns in the second argument of the upsert method to have a "primary" or "unique" index.
     * In addition, the MySQL database driver ignores the second argument of the upsert method and always uses the "primary" and "unique" indexes of the table to detect existing records.
     *
     * @param array $values
     * @param array|string $uniqueBy
     * @param array|null $update
     * @return int
     */
    public function upsert(array $values, array|string $uniqueBy, array $update = null): int
    {
        return $this->model->upsert($values, $uniqueBy, $update);
    }

    /**
     * Update
     *
     * @param       $id
     * @param array $attributes
     *
     * @return mixed
     */
    public function update($id, array $attributes = []): mixed
    {
        $result = $this->find($id);
        if ($result) {
            $result->update($attributes);
            return $result;
        }

        return false;
    }

    /**
     * Update by condition
     *
     * @param array $conditions
     * @param array $attributes
     * @return bool|null
     */
    public function updateBy(array $conditions, array $attributes): bool|null
    {
        return $this->model->where($conditions)->update($attributes);
    }

    /**
     * Get one
     *
     * @param $id
     *
     * @return mixed
     */
    public function find($id): mixed
    {
        return $this->model->find($id);
    }

    /**
     * Delete
     *
     * @param $id
     *
     * @return bool
     */
    public function delete($id): bool
    {
        $result = $this->find($id);
        if ($result) {
            $result->delete();

            return true;
        }

        return false;
    }

    /**
     * Delete by condition
     *
     * @param array $conditions
     *
     * @return bool|null
     */
    public function deleteBy(array $conditions): bool|null
    {
        return $this->model->where($conditions)->delete();
    }

    /**
     * Get by conditions
     *
     * @param array $conditions
     * @param string[] $columns
     *
     * @return mixed
     */
    public function getByConditions(array $conditions = [], array $columns = ['*'], array $relations = []): mixed
    {
        $collection = $this->getCollections();

        // Apply conditions and paginate
        return $this->applyConditions($collection, $conditions, $columns, $relations);
    }

    /**
     * Get collection by conditions
     *
     * @param array $conditions
     *
     * @return mixed
     */
    public function getAllByConditions(array $conditions = []): mixed
    {
        $collection = $this->getCollections();

        // Apply search condition
        $collection = $this->applySearch($collection, $conditions);

        // Apply filter by condition
        $collection = $this->applyFilter($collection, $conditions);

        // Apply sort by condition
        $collection = $this->applySorts($collection, $conditions);

        //Apply advanced by condition
        return $this->applyAdvanced($collection, $conditions)->get();
    }

    /**
     * Get collections for getByConditions
     *
     *
     * @return BaseModel
     */
    protected function getCollections(): BaseModel
    {
        return $this->model;
    }

    /**
     * Apply conditions and paginate
     *
     * @param          $collection
     * @param array $conditions
     * @param string[] $columns
     *
     * @return mixed
     */
    protected function applyConditions($collection, array $conditions = [], array $columns = ['*'], array $relations = []): mixed
    {
        // Apply search condition
        $collection = $this->applySearch($collection, $conditions);

        // Apply filter by condition
        $collection = $this->applyFilter($collection, $conditions);

        // Apply sort by condition
        $collection = $this->applySorts($collection, $conditions);

        //Apply advanced by condition
        $collection = $this->applyAdvanced($collection, $conditions);

        // Load relations
        if (count($relations)) {
            $collection = $collection->with($relations);
        }

        // Apply pagination by condition
        return $this->applyPagination($collection, $conditions, $columns);
    }

    /**
     * Apply full text search
     *
     * @param         $collection
     * @param array $conditions
     *
     * @return mixed
     */
    protected function applySearch($collection, array $conditions = []): mixed
    {
        if (!empty($this->fullTextSearchFields) && !empty($conditions['q']) && is_string($conditions['q'])) {
            $searchValue = trim($conditions['q']);
            return $collection->whereFullText($this->fullTextSearchFields, $searchValue);
        }

        return $collection;
    }

    /**
     * Apply filter by conditions
     *
     * @param         $collection
     * @param array $conditions
     *
     * @return mixed
     */
    protected function applyFilter($collection, array $conditions = []): mixed
    {
        if (!empty($this->filterFields) && !empty($conditions) && is_array($conditions)) {
            foreach ($conditions as $filterField => $filterValue) {
                $collection = $this->setFilter($collection, $filterField, $filterValue);
            }
        }

        return $collection;
    }

    /**
     * Apply sort by conditions
     *
     * @param         $collection
     * @param array $conditions
     *
     * @return mixed
     */
    protected function applySorts($collection, array $conditions = []): mixed
    {
        if (!empty($this->sortFields) && !empty($conditions['_sort']) && is_string($conditions['_sort']) && !empty($conditions['_order']) && is_string($conditions['_order'])) {
            $sortFields = explode(',', $conditions['_sort']);
            $orderValues = explode(',', $conditions['_order']);
            foreach ($sortFields as $sortIndex => $sortField) {
                $orderValue = isset($orderValues[$sortIndex]) ? strtolower($orderValues[$sortIndex]) : null;
                if (in_array($sortField, $this->sortFields) && in_array($orderValue, $this->orderValues)) {
                    $collection = $collection->orderBy($sortField, $orderValue);
                }
            }
        } else {
            $collection = $collection->orderBy('created_at', 'desc');
        }

        return $collection;
    }

    /**
     * Apply advance conditions
     *
     * @param         $collection
     * @param array $conditions
     *
     * @return mixed
     */
    protected function applyAdvanced($collection, array $conditions = []): mixed
    {
        return $collection;
    }

    /**
     * Paginate by conditions
     *
     * @param         $collection
     * @param array $conditions
     * @param array $columns
     *
     * @return mixed
     */
    protected function applyPagination($collection, array $conditions = [], array $columns = ['*']): mixed
    {
        $pageSize = isset($conditions['_limit']) ? intval($conditions['_limit']) : $this->pageSize;
        $page = isset($conditions['_page']) ? intval($conditions['_page']) : 1;

        return $collection->paginate($pageSize, $columns, 'page', $page);
    }

    /**
     * Search record by id with trashed
     *
     * @param string $id
     * @return mixed
     */
    public function findWithTrashed($id): mixed
    {
        return $this->getCollections()->withTrashed()->find($id);
    }

    public function getByWithTrash(array $conditions = []): mixed
    {
        return $this->model->withTrashed()->where($conditions);
    }

    /**
     * Apply date filtering conditions to a query based on start and end dates.
     *
     * @param mixed $collection
     * @param array $conditions
     * @param string $column
     * @param string $fieldName
     * @return mixed
     */

    public function applyDateRangeFilters(mixed $collection, array $conditions,  string $column,  string $fieldName): mixed
    {
        $gteKey = $fieldName . '_gte';
        $lteKey = $fieldName . '_lte';
        if (isset($conditions[$gteKey]) || isset($conditions[$lteKey])) {
            $gteValue = $conditions[$gteKey] ?? null;
            $lteValue = $conditions[$lteKey] ?? null;
            if (isset($gteValue) && isset($lteValue)) {
                $startDate = Carbon::make($gteValue)->startOfDay()->format(config('format.datetime'));
                $endDate = Carbon::make($lteValue)->endOfDay()->format(config('format.datetime'));
                $collection = $collection->whereBetween($column, [$startDate, $endDate]);
            } elseif (isset($gteValue)) {
                $startDate = Carbon::make($gteValue)->startOfDay()->format(config('format.datetime'));
                $collection = $collection->where($column, '>=', $startDate);
            } elseif (isset($lteValue)) {
                $endDate = Carbon::make($lteValue)->endOfDay()->format(config('format.datetime'));
                $collection = $collection->where($column, '<=', $endDate);
            }
        }
        return $collection;
    }
}
