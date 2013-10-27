<?php

namespace Trismegiste\Mikromongo\Persistence;

use Trismegiste\Mikromongo\Transformer\Serializer;
use Trismegiste\Mikromongo\Transformer\Unserializer;

/**
 * Repository of mongo document
 */
class Repository //implements RepositoryInterface
{

    protected $collection;
    protected $serializer;
    protected $unserializer;

    public function __construct(\MongoCollection $coll, Serializer $serial, Unserializer $unserial)
    {
        $this->collection = $coll;
        $this->serializer = $serial;
        $this->unserializer = $unserial;
    }

    /**
     * {@inheritDoc}
     */
    public function persist(Persistable $doc)
    {
        $struc = $this->unserializer->toArray(serialize($doc));

        if (is_null($doc->getId())) {
            unset($struc['_id']);
        }

        $this->collection->save($struc);
        $doc->setId($struc['_id']);
    }

    /**
     * {@inheritDoc}
     */
    public function findByPk($pk)
    {
        $id = new \MongoId($pk);
        $struc = $this->collection->findOne(array('_id' => $id));
        if (is_null($struc)) {
            throw new NotFoundException($pk);
        }

        return $this->createFromDb($struc);
    }

    /**
     * {@inheritDoc}
     */
    public function createFromDb(array $struc)
    {
        return unserialize($this->serializer->fromArray($struc));
    }

}
