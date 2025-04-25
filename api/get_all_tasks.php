<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        // Require user_id from query (admin user)
        if (!isset($_GET['user_id'])) {
            response(400, null, 'Missing required parameter: user_id');
            exit;
        }

        $adminUserId = $_GET['user_id'];
        $keyword = isset($_GET['search']) ? $_GET['search'] : '';
        $sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'tasks.created_at';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

        // Searchable columns
        $searchableColumns = [
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.course',
            'users.address',
            'users.phone_number',
            'users.gender',
            'users.role',
            'tasks.title',
            'tasks.description',
            'user_tasks.task_status'
        ];

        // Columns to return
        $selectColumns = "
            users.user_id,
            users.first_name,
            users.last_name,
            users.email,
            users.phone_number,
            users.gender,
            users.course,
            users.address,
            users.birthdate,
            users.profile_url,
            users.role,
            users.is_verified,
            users.created_at AS user_created_at,
            users.updated_at AS user_updated_at,
            tasks.task_id,
            tasks.title,
            tasks.description,
            tasks.created_at AS task_created_at,
            tasks.updated_at AS task_updated_at,
            user_tasks.task_status,
            user_tasks.assigned_at,
            user_tasks.updated_at AS assignment_updated_at
        ";

        // No admin check anymore, just filter tasks by the admin's created tasks
        $queryParams = [$adminUserId];
        $whereClauses = [
            "tasks.created_by = ?" // Only tasks created by the admin user
        ];

        // Add search filters if keyword exists
        if (!empty($keyword)) {
            $searchConditions = [];
            foreach ($searchableColumns as $column) {
                $searchConditions[] = "$column LIKE ?";
                $queryParams[] = "%" . $keyword . "%"; // bind search terms with wildcards
            }
            $whereClauses[] = '(' . implode(" OR ", $searchConditions) . ')'; // join with OR condition
        }

        // Query with INNER JOINs (return all users with their tasks, but only the tasks created by the admin)
        $query = "
            SELECT $selectColumns
            FROM users
            LEFT JOIN user_tasks ON users.user_id = user_tasks.user_id
            LEFT JOIN tasks ON user_tasks.task_id = tasks.task_id
            WHERE " . implode(" AND ", $whereClauses) . "
            ORDER BY $sortBy $order
        ";

        // Prepare and execute the query
        $statement = $pdo->prepare($query);
        $statement->execute($queryParams);
        $results = $statement->fetchAll();

        if (!empty($results)) {
            response(200, $results, 'User tasks fetched successfully!');
        } else {
            response(200, [], 'No tasks found for this user.');
        }
    } catch (PDOException $e) {
        response(500, null, 'An error occurred: ' . $e->getMessage());
    }
} else {
    response(405, null, 'Only GET requests are allowed.');
}
?>
