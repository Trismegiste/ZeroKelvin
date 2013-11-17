<?php

/*
 * ZeroKelvin
 */

namespace Trismegiste\ZeroKelvin\MongoDb;

use Trismegiste\ZeroKelvin\Transformer;

/**
 * Repository of mongo document
 */
class Repository implements RepositoryInterface
{

    const FIELD_FOREIGN = '@foreign';

    protected $collection;
    protected $transformer;

    public function __construct(\MongoCollection $coll, Transformer $tr)
    {
        $this->collection = $coll;
        $this->transformer = $tr;
    }

    /**
     * {@inheritDoc}
     */
    public function persist($doc)
    {
        $struc = $this->transformer->toArray($doc);
        $foreignKey = false;
        foreach ($struc as $item) {
            if (false === $foreignKey) {
                $foreignKey = [];
                continue;
            }
            $foreignKey[] = $item['_id'];
        }
        $struc[0][self::FIELD_FOREIGN] = $foreignKey;

        foreach ($struc as $item) {
            $this->collection->save($item);
        }

        return $struc[0]['_id'];
    }

    /**
     * {@inheritDoc}
     */
    public function findByPk($pk)
    {
        $root = $this->collection->findOne(array('_id' => $pk));
        if (is_null($root)) {
            throw new NotFoundException($pk);
        }

        $foreign = $root[self::FIELD_FOREIGN];
        $embedded = [$root];
        $cursor = $this->collection->find(['_id' => ['$in' => $foreign]]);
        foreach ($cursor as $ref) {
            $embedded[] = $ref;
        }

        return $this->createFromDb($embedded);
    }

    /**
     * {@inheritDoc}
     */
    public function createFromDb(array $struc)
    {
        if (!array_key_exists(self::FIELD_FOREIGN, $struc[0])) {
            throw new \InvalidArgumentException("The root entity does ot contain the references list");
        }
        if (count($struc) != (1 + count($struc[0][self::FIELD_FOREIGN]))) {
            throw new \InvalidArgumentException("The number of referenced entities does not match");
        }
        unset($struc[0][self::FIELD_FOREIGN]);


        return $this->transformer->fromArray($struc);
    }

}
