<?php

namespace App\Model;

// !!! The domain model implements JsonSerializable.
// TODO: move JSON serialization to a dedicated serializer, remove implementation.
class Task implements \JsonSerializable
{
    /**
     * @var array
     */

    // !!! Storing domain state in a generic array makes the model weakly typed and tightly coupled to the persistence format (DB row).
    // TODO: replace $_data with explicit typed properties (e.g. $id, $name, ...) and make it private.
    private $_data;
    
    // Constructor does not declare a type for $data.
    // This allows passing anything (null, string, invalid array), which can break the model.
    // TODO: explicit parameters or create ProjectDTO class and validate required keys.

    // !!! Data mapping responsibility is embedded in the model. Should use Project serializer instead
    // TODO: implement serialization logic and validation in a dedicated class (e.g., ProjectSerializer.php).
    
    public function __construct($data)
    {
        $this->_data = $data;
    }

    /**
     * @return array
     */

    // !!! The domain model implements JsonSerializable.
    // !!! Serialization used inside a model - mixes concerns, makes testing harder.
    // The domain layer should not know about transport or presentation formats
    // TODO: move JSON serialization to a dedicated serializer.
    public function jsonSerialize(): array
    {
        // No error handling if _data is not serializable.
        // TODO: handle JSON encoding errors explicitly or throw an exception.
        return $this->_data;
    }
}
