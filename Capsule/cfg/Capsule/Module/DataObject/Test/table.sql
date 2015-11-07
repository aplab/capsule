CREATE TABLE IF NOT EXISTS %TABLE_NAME%  (
`id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT
COMMENT 'Уникальный идентификатор',
`name` VARCHAR(255)         NOT NULL
COMMENT 'Наименование',
`section_id` VARCHAR(255)         NOT NULL
COMMENT 'Рубрика',
PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = `utf8`
COMMENT 'Тестовый объект!';


