<?php

/**
 * Persistence
 */

namespace Trismegiste\ZeroKelvin\MongoDb;

/**
 * A contract for a repository
 */
interface RepositoryInterface
{

    /**
     * Transforms an object tree into a arrays list and persists it 
     * into the database layer
     * 
     * @param object $doc
     * 
     * @return string the primary key of the root entity
     */
    function persist($doc);

    /**
     * Finds an object from the database for a given primary key and
     * maps it with a transformer into a real object.
     *
     * @param string $id the primary key
     * 
     * @return object
     *
     * @throws NotFoundException When no object found for this pk
     */
    function findByPk($id);

    /**
     * Creates an instance and maps this object with data retrieved from 
     * database. Usefull when iterating over a MongoCursor
     * 
     * @param array $struc a raw list of flat arrays coming from database
     * 
     * @return object
     */
    function createFromDb(array $struc);
}
