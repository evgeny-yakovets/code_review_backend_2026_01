<?php

// wrong namespace - should be App\Controller
namespace Api\Controller;

// App\Model is used for NotFoundException, better to import class dirrectly - App\Model\NotFoundException
use App\Model;
use App\Storage\DataStorage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

// Controller could be extended from BaseController. 
// Security checks, error response and etc. could be implemented in BaseContoller.
class ProjectController 
{
    /**
     * @var DataStorage
     */
    private $storage;

    // !!! Controller depends directly on DataStorage - this tightly couples the HTTP layer to persistence and makes testing difficult
    // TODO: should be depend on a domain service or repository interface instead.
    public function __construct(DataStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param Request $request
     * 
     * @Route("/project/{id}", name="project", method="GET")
     */

    // No return type declared.
    // TODO: declare return type as Response or JsonResponse.

    // Method name is generic - does not describe the action performed (actualy - getting project data in json format).
    // TODO: rename to getProject or getProjectAction.
    
    // id parametr could be missed in Request - should validate input and handle id missing.
    // TODO: use a request DTO or Symfony parameter validation to enforce input constraints.
    public function projectAction(Request $request)
    {
        try {
            // Route parameter is accessed as a raw string and passed directly to storage.
            // TODO: Validate and cast id to int, or use a value resolver / DTO.
            // TODO: using of value resolver will simplify this projectId check (also could be enhanced with other checks in future).
            // In this way, catching of NotFoundException not needed here and could be implemented only once.
            $project = $this->storage->getProjectById($request->get('id'));

            // Controller calls domain model serialization (toJson()).
            // This violates separation of concerns: controllers should return DTOs or use serializers.
            // TODO: use JsonResponse with a model or model serializer.
            return new Response($project->toJson());
        } catch (Model\NotFoundException $e) {
            // Exception is not logged, which makes troubleshooting and monitoring difficult.

            // The exception message and context are ignored and replaced by a hard-coded string.
            // This loses valuable debugging information.
            // TODO: use the exception message or map it to a structured API error response.

            // TODO: return JsonResponse with a structured error payload.
            return new Response('Not found', 404);
        } catch (\Throwable $e) {
            // Exception is not logged, which makes troubleshooting and monitoring difficult.

            // Catching Throwable hides programming errors and makes them look like 500 responses.
            // This makes debugging extremely hard.

            // TODO: Let unexpected errors bubble up to the global exception handler.
            return new Response('Something went wrong', 500);
        }
    }

    /**
     * @param Request $request
     *
     * @Route("/project/{id}/tasks", name="project-tasks", method="GET")
     */

    // No return type declared.
    // TODO: declare return type as Response or JsonResponse.

    // Method name does not clearly describe what it does.
    // TODO: rename to getProjectTasks.
    public function projectTaskPagerAction(Request $request)
    {
        // id, limit and offset are taken directly from the request without validation or type conversion.
        // Missing handling of NotFoundException and storage-level errors.
        // This method may throw and result in an uncontrolled 500 response.
        // TODO: validate input and handle domain and infrastructure exceptions explicitly.

        // It is not obvious that id means projectId.
        // TODO: use a request DTO or route parameter binding with meaningful names (/project/{projectId}/tasks).
        // TODO: using of value resolver will simplify this projectId check (also could be enhanced with other checks in future).
        $tasks = $this->storage->getTasksByProjectId(
            $request->get('id'),
            $request->get('limit'),
            $request->get('offset')
        );

        // json_encode() is called directly on domain objects (Model\Task).
        // This will produce invalid or empty JSON unless Task implements JsonSerializable.
        // TODO: Map domain objects to DTOs and use JsonResponse.
        return new Response(json_encode($tasks));
    }

    /**
     * @param Request $request
     *
     * @Route("/project/{id}/tasks", name="project-create-task", method="PUT")
     */

    // No return type declared.
    // TODO: declare return type as JsonResponse.

    // Request parameters are not validated.
    // TODO: use a request DTO for input validation and mapping.
    public function projectCreateTaskAction(Request $request)
    {
        // No csrf token validation - may lead to CSRF vulnerabilities.
        // TODO: implement CSRF protection for state-changing operations (in a BaseContoller for example and extend it).

        // getProjectById() throws NotFoundException when the project does not exist.
        // This null check is therefore dead code and indicates a misunderstanding of the storage contract.
        // TODO: Remove the null check and handle NotFoundException instead (see dataStorage comments).

        // It is not obvious that id means projectId.
        // TODO: use a request DTO or route parameter binding with meaningful names (/project/{projectId}/tasks).
        // TODO: using of value resolver will simplify this projectId check (also could be enhanced with other checks in future).
		$project = $this->storage->getProjectById($request->get('id'));
		if (!$project) {
			return new JsonResponse(['error' => 'Not found']);
		}
		
        // Superglobal $_REQUEST is used instead of Symfony Request.
        // This bypasses Symfony’s input handling, validation and security mechanisms.
        // TODO: Use request DTO instead $request, replace $_REQUEST with DTO object.

        // No validation or sanitization of input data - may lead to invalid data or security issues.
        // TODO: Validate and sanitize input data before using it to create a task.

        // Also, use serializer to map createTask() data.
		return new JsonResponse(
			$this->storage->createTask($_REQUEST, $project->getId())
		);
    }
}
