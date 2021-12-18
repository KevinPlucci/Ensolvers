DROP TABLE IF EXISTS `task_list`;
CREATE TABLE IF NOT EXISTS `task_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL COMMENT 'Name of the task',
  `status` enum('OK','PEND') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'PEND' COMMENT 'Status of the task',
  `creation_date` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Date of creation',
  `modif_date` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Modification time',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;