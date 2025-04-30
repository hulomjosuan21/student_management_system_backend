CREATE TABLE `users` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone_number` VARCHAR(20) NOT NULL,
  `gender` ENUM('Male','Female') NOT NULL,
  `course` VARCHAR(100) NOT NULL,
  `address` TEXT NOT NULL,
  `birthdate` DATE NOT NULL,
  `profile_url` TEXT DEFAULT NULL,
  `verification_code` VARCHAR(6) DEFAULT NULL,
  `role` ENUM('admin','user') NOT NULL DEFAULT 'user',
  `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
);

-- test
INSERT INTO `users` (
  `first_name`, `last_name`, `email`, `password`, `phone_number`, `gender`, `course`,
  `address`, `birthdate`, `profile_url`, `verification_code`, `role`, `is_verified`
) VALUES
('John', 'Doe', 'john.doe@example.com', PASSWORD('123'), '09123456789', 'Male', 'BSIT', '123 Street, City', '2000-01-01', NULL, NULL, 'user', 1),
('Jane', 'Smith', 'jane.smith@example.com', PASSWORD('123'), '09123456788', 'Female', 'BSCS', '456 Avenue, City', '2001-02-02', NULL, NULL, 'user', 1),
('Alice', 'Brown', 'alice.brown@example.com', PASSWORD('123'), '09123456787', 'Female', 'BSIS', '789 Road, City', '2002-03-03', NULL, NULL, 'user', 1),
('Bob', 'Johnson', 'bob.johnson@example.com', PASSWORD('123'), '09123456786', 'Male', 'BSIT', '101 Blvd, City', '2003-04-04', NULL, NULL, 'admin', 1),
('Charlie', 'Davis', 'charlie.davis@example.com', PASSWORD('123'), '09123456785', 'Male', 'BSCS', '202 Lane, City', '2004-05-05', NULL, NULL, 'user', 1);

CREATE TABLE `tasks` (
  `task_id` INT(11) NOT NULL AUTO_INCREMENT,
  `created_by` INT(11) NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `due_date` DATETIME NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`task_id`),
  FOREIGN KEY (`created_by`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
);

CREATE TABLE `asigned_tasks` (
  `asigned_task_id` INT(11) NOT NULL AUTO_INCREMENT,
  `created_by` INT(11) NOT NULL,
  `task_id` INT(11) NOT NULL,
  `assign_to` INT(11) NOT NULL,
  `task_status` ENUM('complete','pending') NOT NULL DEFAULT 'pending',
  `attachment_url` TEXT DEFAULT NULL,
  `assigned_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_task_id`),
  FOREIGN KEY (`created_by`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`assign_to`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`task_id`) REFERENCES `tasks`(`task_id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_assignment`(`created_by`, `task_id`, `assign_to`)
);
