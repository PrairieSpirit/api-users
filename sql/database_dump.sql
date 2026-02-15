--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(8) NOT NULL,
  `phone` varchar(8) NOT NULL,
  `pas` varchar(8) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_users_login_pas` (`login`,`pas`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;

INSERT INTO `users` VALUES (1,'root','00000000','rootpass'),(2,'user1','09900001','pass1'),(3,'user2','09900002','pass2'),(4,'user3','09900003','pass3'),(5,'user4','09900004','pass4'),(6,'user5','09900005','pass5'),(7,'user6','09900006','pass6'),(8,'user7','09900007','pass7'),(9,'user8','09900008','pass8'),(10,'user9','09900009','pass9');

UNLOCK TABLES;

