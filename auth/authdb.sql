-- All lists of IDs will be text fields with each ID being separated by a comma
-- For example: a sample entry for the friends field would be: "25,67,89" all formatted as text
-- The program reading the data is responsible for transforming this list into an actual array

CREATE TABLE IF NOT EXISTS `users` (`id` int NOT NULL AUTO_INCREMENT, `name` text NOT NULL, `password` text NOT NULL, `email` text NOT NULL, `active` int NOT NULL, `games` text NOT NULL, `friends` text NOT NULL, `infriends` text NOT NULL, `outfriends` text NOT NULL, `bio` text NOT NULL, PRIMARY KEY (`id`)) CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `tokens` (`id` int NOT NULL AUTO_INCREMENT, `token` text NOT NULL, `user` int NOT NULL, `expiration` int NOT NULL, PRIMARY KEY (`id`)) CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `appsecret` (`id` int NOT NULL AUTO_INCREMENT, `appsecret` text NOT NULL, `name` TEXT NOT NULL, PRIMARY KEY(`id`)) CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `tokencode` (`id` int NOT NULL AUTO_INCREMENT, `code` text NOT NULL, `appsecret` int NOT NULL, `token` int NOT NULL, `expiration` int NOT NULL, PRIMARY KEY(`id`)) CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- The following portion is sample data

-- JVapLItBH0ytdJCsa
INSERT INTO `appsecret` (appsecret, name) VALUES ('$2y$10$CY9bBnwwgBTG5.K4X0iu5.zuA/kyRtVZnLejAKujs880QDw5bwTRS', 'Zero³ Website');


