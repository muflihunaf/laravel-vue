<?php

namespace App\Interfaces\Repositories;

interface BaseRepositoryInterface
{
    /**
     * Get all records
     *
     * @param array $columns
     * @return mixed
     */
    public function all(array $columns = ['*']);

    /**
     * Get paginated records
     *
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate(int $perPage = 15, array $columns = ['*']);

    /**
     * Create new record
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Update record
     *
     * @param string $id
     * @param array $data
     * @return mixed
     */
    public function update(string $id, array $data);

    /**
     * Delete record
     *
     * @param string $id
     * @return mixed
     */
    public function delete(string $id);

    /**
     * Find record by ID
     *
     * @param string $id
     * @param array $columns
     * @return mixed
     */
    public function find(string $id, array $columns = ['*']);

    /**
     * Find record by field
     *
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @return mixed
     */
    public function findBy(string $field, $value, array $columns = ['*']);
} 