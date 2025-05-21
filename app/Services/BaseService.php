<?php

namespace App\Services;

use App\Interfaces\Repositories\BaseRepositoryInterface;
use App\Interfaces\Services\BaseServiceInterface;

abstract class BaseService implements BaseServiceInterface
{
    /**
     * @var BaseRepositoryInterface
     */
    protected $repository;

    /**
     * BaseService constructor.
     *
     * @param BaseRepositoryInterface $repository
     */
    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function getAll(array $columns = ['*'])
    {
        return $this->repository->all($columns);
    }

    /**
     * @inheritDoc
     */
    public function getPaginated(int $perPage = 15, array $columns = ['*'])
    {
        return $this->repository->paginate($perPage, $columns);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    /**
     * @inheritDoc
     */
    public function update(string $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $id)
    {
        return $this->repository->delete($id);
    }

    /**
     * @inheritDoc
     */
    public function find(string $id, array $columns = ['*'])
    {
        return $this->repository->find($id, $columns);
    }

    /**
     * @inheritDoc
     */
    public function findBy(string $field, $value, array $columns = ['*'])
    {
        return $this->repository->findBy($field, $value, $columns);
    }
} 