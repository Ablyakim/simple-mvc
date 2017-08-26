CREATE TABLE `task` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `content` text NULL
) COLLATE 'utf8_general_ci';