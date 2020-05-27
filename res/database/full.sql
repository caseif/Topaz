START TRANSACTION;

-- create users table

CREATE TABLE `users` (
  `id` INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(64) NOT NULL,
  `name` VARCHAR(128) NOT NULL,
  `pass_hash` VARCHAR(60) NOT NULL,
  `register_time` INT(8) NOT NULL,
  `active` TINYINT(1) NOT NULL,
  `permission_mask` INT(4) NOT NULL DEFAULT '4'
);

-- create categories table

CREATE TABLE `categories` (
  `id` INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `display` VARCHAR(64) NOT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT '1'
);

-- create posts table

CREATE TABLE `posts` (
  `id` INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(128) NOT NULL,
  `content` MEDIUMTEXT NOT NULL,
  `create_time` int(8) NOT NULL,
  `update_time` int(8) NOT NULL DEFAULT '-1',
  `author` INT(8),
  `category` INT(8),
  `visible` TINYINT(1) NOT NULL DEFAULT '1',
  `about` TINYINT(1) NOT NULL DEFAULT '0'
);

-- add foreign key for posts.author

ALTER TABLE `posts`
  ADD KEY `fk_users.id` (`author`),
  ADD CONSTRAINT `fk_users.id`
    FOREIGN KEY (`author`)
    REFERENCES `users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  ADD KEY `fk_categories.id` (`category`),
  ADD CONSTRAINT `fk_categories.id`
    FOREIGN KEY (`category`)
    REFERENCES `categories` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
;

COMMIT;
