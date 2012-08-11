<?php
$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `bookinfo_data`;
CREATE TABLE  `bookinfo_data` (
  `book_id` int  NOT NULL AUTO_INCREMENT,
  `isbn` VARCHAR(30) ,
  `json` TEXT,
  PRIMARY KEY (`book_id`)
)
ENGINE = InnoDB DEFAULT CHARSET=utf8;");


$installer->endSetup();


