CREATE TABLE IF NOT EXISTS %TABLE_NAME%  (
`id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT
COMMENT 'Уникальный идентификатор',
`login` VARCHAR(255)         NOT NULL
COMMENT 'Логин',
`password` VARCHAR(255)         NOT NULL
COMMENT 'Пароль',
PRIMARY KEY (`id`),
UNIQUE KEY `login` (`login`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = `utf8`;


