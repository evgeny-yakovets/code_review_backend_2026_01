<?php

namespace App\Model;

class Project
{
    /**
     * @var array
     */

    // $data property is public - breaks encapsulation and allows external code to mutate the model.
    // !!! Storing domain state in a generic array makes the model weakly typed and tightly coupled to the persistence format (DB row).
    // TODO: replace $_data with explicit typed properties (e.g. $id, $name, ...) and make it private.
    public $_data;
    
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
     * @return int
     */

    // No return type declared - makes it unclear what the method returns.
    public function getId()
    {
        // Relies on a magic array key ('id'). This tightly couples the domain model to the database schema.

        // Getting id directly from data array - no validation or type enforcement, no check if 'id' exists.
        // TODO: store id in a dedicated typed property and validate it on construction.
        return (int) $this->_data['id'];
    }

    /**
     * @return string
     */

    // !!! Serialization used inside a model - mixes concerns, makes testing harder.
    // The domain layer should not know about transport or presentation formats
    // TODO: move JSON serialization to a dedicated serializer.
    public function toJson()
    {
        // No error handling if _data is not serializable.
        // TODO: handle JSON encoding errors explicitly or throw an exception.
        return json_encode($this->_data);
    }
}
