<?php
require_once("../db.php");
require_once("../utils.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $keyword = isset($_GET['search']) ? $_GET['search'] : '';
        $sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'user_id';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
        
        $columns = [
            'first_name', 
            'last_name', 
            'email', 
            'course', 
            'address', 
            'phone_number',
            'gender', 
            'role', 
            'is_verified'
        ];
        
        $selectColumns = 'user_id, first_name, last_name, email, phone_number, gender, course, address, birthdate, profile_url, role, is_verified, created_at, updated_at';
        
        $queryParams = [];
        $whereClauses = [];

        if (!empty($keyword)) {
            foreach ($columns as $column) {
                $whereClauses[] = "$column LIKE ?";
                $queryParams[] = "%" . $keyword . "%";
            }
        }

        $query = "SELECT $selectColumns FROM users";
        
        if (!empty($whereClauses)) {
            $query .= " WHERE " . implode(" OR ", $whereClauses);
        }
        
        $query .= " ORDER BY $sortBy $order";

        $statement = $pdo->prepare($query);
        $statement->execute($queryParams);
        $users = $statement->fetchAll();

        if (!empty($users)) {
            response(200, $users, 'Users fetched successfully!');
        } else {
            response(200, [], 'No users found. Showing all records.');
        }
    } catch (PDOException $e) {
        response(500, null, 'An error occurred: ' . $e->getMessage());
    }
} else {
    response(405, null, 'Only GET requests are allowed.');
}
?>
