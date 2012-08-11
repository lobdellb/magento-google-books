<?php

$installer = $this;

$installer->startSetup();
$installer->run("
ALTER TABLE `mage16`.`bookinfo_data` ADD COLUMN `smallthumbnailpath` VARCHAR(256)  AFTER `ts`,
 ADD COLUMN `thumbnailpath` VARCHAR(256)  AFTER `smallthumbnailpath`;");
$installer->endSetup();

