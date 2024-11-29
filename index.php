<?php
    include "config/db.php";
   
    header("Content-Type: application/json");
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    // HANDLE THE REQUEST DOES NOT EXIST
    $request = isset($_GET['request']) ? explode("/", trim($_GET['request'], "/")) : [];

    // HANDLE HTTP VERB
    $requestMethod;

    // GET TASK ID PRESENT ON THE URL
    $taskID = isset($_GET["id"]) ? trim($_GET["id"], "/") : null;

    switch($requestMethod) {
        case 'POST':
            createTask();
            break;
           
        case 'GET':
            if ($taskID) {
                getTask($taskID); // Get a single task by ID
            } else {
                getTasks(); // Get all tasks
            }
            break;
   
        case 'PUT':
        case 'PATCH':
            updateTask($taskID); // Update task with the specified ID
            break;
   
        case 'DELETE':
            if ($taskID) {
                deleteTask($taskID); // Delete task by ID
            } else {
                deleteAllTasks(); // Delete all tasks
            }
            break;
   
        default:
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
            break;
    }
        mysqli_close($connection);//close the database connection
?>

<?php
    function createTask(){
        global $connection;

        $data = json_decode(file_get_contents("php://input"), true);

        $title = $data['title'];
        $description = $data['description'];

        if(!empty($title)){
           
            $sql = "INSERT INTO tasks (title, description) VALUES ('$title', '$description')";

            if(mysqli_query($connection, $sql)){
                http_response_code(201);
                echo json_encode(["message" => "Task created successfully"]);
            }else{
                http_response_code(500);
                echo json_encode(["message" => "Error creative task"]);
            }
   
        }else{
            http_response_code(400);
            echo json_encode(["message" => "Title is required"]);
        }
    }

    function getTasks(){
        global $connection;

        $sql = "SELECT * FROM tasks";
        $result = mysqli_query($connection, $sql);
       
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
        ECHO json_encode($tasks);
    }
    function getTask($param){
        global $connection;
   
        $sql = "SELECT * FROM tasks WHERE id = $param";
        $result = mysqli_query($connection, $sql);
   
        if($row = mysqli_fetch_assoc($result)){
            echo json_encode($row);
            exit; //para matigil na yung pag duplicate
        } else {
            http_response_code(404);
            echo json_encode(["message" => "task not found"]);
            exit; //para matigil na yung pag duplicate
        }
    }
    // Function to update a task by ID
    function updateTask($param) {
        global $connection;

        if ($param) {
            $data = json_decode(file_get_contents("php://input"), true);

            $title = $data['title'] ?? null;
            $description = $data['description'] ?? null;

            if ($title || $description) {
                $updates = [];
                if ($title) {
                    $updates[] = "title = '$title'";
                }
                if ($description) {
                    $updates[] = "description = '$description'";
                }

                $sql = "UPDATE tasks SET " . implode(", ", $updates) . " WHERE id = $param";

                if (mysqli_query($connection, $sql)) {
                    echo json_encode(["message" => "Task updated successfully"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Error updating task"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["message" => "At least one field (title or description) is required to update"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Task ID is required"]);
        }
    }

    // Function to delete a task by ID
    function deleteTask($param) {
        global $connection;

        if ($param) {
            $sql = "DELETE FROM tasks WHERE id = $param";

            if (mysqli_query($connection, $sql)) {
                echo json_encode(["message" => "Task deleted successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error deleting task"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Task ID is required"]);
        }
    }

    // Function to delete all tasks
    function deleteAllTasks() {
        global $connection;

        $sql = "DELETE FROM tasks";

        if (mysqli_query($connection, $sql)) {
            echo json_encode(["message" => "All tasks deleted"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error deleting all tasks"]);
        }
    }

?>
