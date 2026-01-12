<?php

namespace App\Storage;

use App\Model;

// TODO: better to use intrface for Storage here. 
// This will help to switch storage implementations (DB, in-memory, file-based) without changing dependent code.
class DataStorage
{
    /**
     * @var \PDO 
     */
    // PDO property is public. This let to use external sql and lead to security issues or failures.
    // TODO: should be private or protected and used only with class methods. 
    public $pdo;

    public function __construct()
    {
        // !!! Hard-coded database credentials - this is a security risk and makes configuration inflexible between environments.
        // TODO: use environment variables or a secure vault instead.
        $this->pdo = new \PDO('mysql:dbname=task_tracker;host=127.0.0.1', 'user');

        // !!! No error handling mode set - makes debugging difficult.
        // TODO: $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    // !!! DataStorage mixes multiple responsibilities (DB connection, query execution, data mapping).
    // This violates the Single Responsibility Principle and makes testing and maintenance harder.
    // TODO: separate concerns into dedicated classes (e.g., DatabaseConnection, ProjectRepository, TaskRepository).

    /**
     * @param int $projectId
     * @throws Model\NotFoundException
     */
    // Type mismatch - property could be in any type and let to use sql injection here.
    // TODO: add int type for $projectId

    // Return type missing - makes it unclear what the method returns.
    // TODO: declare return type as Model\Project (or ?Model\Project if null is allowed).
    // TODO: strongly recommended to use Repository pattern here instead of direct DataStorage usage.
    public function getProjectById($projectId)
    {
        // !!! String concatenation in query - leads to SQL injection vulnerabilities.
        // TODO: use prepared statements with bound parameters.

        // SELECT * fetches unnecessary data and tightly couples code to the table schema.
        $stmt = $this->pdo->query('SELECT * FROM project WHERE id = ' . (int) $projectId);

        // No error handling for query execution - makes debugging difficult.
        // TODO: check for false return value from query().
        if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            // Missing serrialization - makes it unclear how data is retrieved.
            // TODO: Use a dedicated mapper or factory to create Project from database rows (example ProjectSerializer.php).
            return new Model\Project($row);
        }

        // The code cannot distinguish between "not found" and "database failure".
        // Both cases result in the same exception, which hides real infrastructure problems.
        // TODO: detect query errors separately and throw different exceptions.

        // Exception contains no context (e.g. project id, SQL error).
        // TODO: add a meaningful message and consider logging.
        throw new Model\NotFoundException();
    }

    /**
     * @param int $project_id
     * @param int $limit
     * @param int $offset
     */

    // Return type missing - makes it unclear what the method returns.
    // TODO: add return type as array and specify array content as Model\Task[]

    // Type mismatch - $limit, $offset properties could be in any type and let to use sql injection here.
    // TODO: add int type for $limit and $offset

    // Limit and offset have no default value - may lead to errors if not provided.
    // TODO: add default values for $limit and $offset (null or 10 for example).

    // $project_id different style than other methods - leads to inconsistency.
    // TODO: rename to $projectId
    // TODO: strongly recommended to use Repository pattern here instead of direct DataStorage usage.
    public function getTasksByProjectId(int $project_id, $limit, $offset)
    {
        // !!! String concatenation in query - leads to SQL injection vulnerabilities.
        // TODO: Use prepared statements (as for limit and offset) with bound parameters.

        // Offset missed in query string - leads to incorrect query execution.
        // TODO: add offset to SQL query (LIMIT ? OFFSET ?).

        // Possibly not all (*) fields are needed - may lead to performance issues.
        // Also, if we don't need limit and/or offset, we should add logic to get data without them.
        $stmt = $this->pdo->query("SELECT * FROM task WHERE project_id = $project_id LIMIT ?, ?");
        $stmt->execute([$limit, $offset]);

        $tasks = [];
        // \PDO::FETCH_ASSOC missed in fetchAll() - may lead to unexpected data structure.
        // TODO: add \PDO::FETCH_ASSOC as parameter to fetchAll().

        // No error handling for query execution - makes debugging difficult.
        // TODO: check for false return value from query() and execute().
        // Check comments from getProjectById() method about error handling.

        // Getting all rows in memory - may lead to performance issues with large datasets.
        foreach ($stmt->fetchAll() as $row) {
            // Same implicit data mapping issue as in getProjectById().
            $tasks[] = new Model\Task($row);
        }

        return $tasks;
    }

    /**
     * @param array $data
     * @param int $projectId
     * @return Model\Task
     */

    // Better to use specific object except of $data (DTO) - makes it unclear what data is expected.
    // TODO: create NewTaskDTO class and replace array $data with NewTaskDTO $data

    // Return type and projectId type (int) missed here

    // TODO: strongly recommended to use Repository pattern here instead of direct DataStorage usage.
    public function createTask(array $data, $projectId)
    {
        // Use $data as DTO object and set projectId
        // Also, project_id code style inconsistent with other methods - leads to inconsistency.
        $data['project_id'] = $projectId;

        // !!! String concatenation in query - leads to SQL injection vulnerabilities.
        // TODO: Use prepared statements with bound parameters.
        $fields = implode(',', array_keys($data));
        $values = implode(',', array_map(function ($v) {
            return is_string($v) ? '"' . $v . '"' : $v;
        }, $data));

        // Using query() instead of prepared statements for INSERT is unsafe 
        // Also, can break if values contain quotes or special characters.
        // TODO: Use prepared statements with bound parameters.
        $this->pdo->query("INSERT INTO task ($fields) VALUES ($values)");

        // No error handling for query execution - makes debugging difficult.
        // TODO: check for false return value from query() and execute().
        // Check comments from getProjectById() method about error handling.

        // Using SELECT MAX(id) is unsafe in concurrent environments, it can return the wrong row.
        // TODO: Use $this->pdo->lastInsertId() instead.
        $data['id'] = $this->pdo->query('SELECT MAX(id) FROM task')->fetchColumn();

        return new Model\Task($data);
    }
}
