CREATE TABLE `%TABLE_NAME%` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Уникальный идентификатор',
  `name` varchar(255) NOT NULL COMMENT 'Наименование',
  `section_id` varchar(255) NOT NULL COMMENT 'Рубрика',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Тестовый объект!'