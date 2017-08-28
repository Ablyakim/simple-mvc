<?php

namespace App\Repository;

use Framework\Db\Query\QueryBuilder;

/**
 * Class ImageRepository
 */
class ImageRepository extends AbstractRepository
{
    /**
     * @var array
     */
    protected $allowedFieldsToSave = [
        'mimeType',
        'extension',
        'name',
        'path',
        'originName',
        'size'
    ];

    /**
     * @param array $imageData
     *
     * @return int
     *
     * @throws \Exception
     */
    public function save($imageData)
    {
        $qb = new QueryBuilder();

        if (!empty($imageData['id'])) {

            $qb->where('id = :id')
                ->setParameter('id', $imageData['id']);

            return $this->updateData($qb, $imageData);
        } else {
            return $this->insertData($qb, $imageData);
        }
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function loadById($id)
    {
        $qb = new QueryBuilder();

        $qb->select('*')
            ->from($this->getTableName())
            ->where('id = :id')
            ->setParameter('id', $id);

        return $this->db->executeByQueryBuilder($qb)->fetch();
    }

    /**
     * @inheritDoc
     */
    public function getAllowedFieldsToSave()
    {
        return $this->allowedFieldsToSave;
    }

    /**
     * @inheritDoc
     */
    public function getTableName()
    {
        return 'image';
    }
}