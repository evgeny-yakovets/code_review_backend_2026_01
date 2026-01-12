<?php

namespace App\Model;

class NotFoundException extends \Exception
{
    // The exception contains no domain-specific information.
    // It does not describe *what* was not found (Project? Task? ID? Query?).
    // This makes debugging, logging and API error responses very weak.

    // No default message is defined.
    // In the current code it is thrown as:
    //     throw new NotFoundException();
    // which produces an empty exception message.
    // This results in useless logs and meaningless API errors.

    // The exception does not carry any context (entity name, identifier, query, etc).
    // This prevents proper error reporting and makes troubleshooting production issues hard.

    // It extends the generic \Exception instead of a domain-specific base exception.
    // This makes it difficult to catch and handle domain errors separately
    // from infrastructure or system errors.

    // The exception does not define an error code or HTTP mapping (e.g. 404),
    // which is typically expected for a NotFound domain error in a web application.

    // TODO: Add a constructor that accepts contextual data
    // (e.g. entity name and identifier) and builds a meaningful message.
    //
    // Example:
    // new NotFoundException('Project', $projectId)
    //
    // TODO: Provide a default human-readable message.
    //
    // TODO: Consider extending a base DomainException
    // instead of the generic \Exception.
}
