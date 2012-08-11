<?php

$installer = $this;

$installer->startSetup();
$installer->run("
ALTER TABLE `mage16`.`bookinfo_data` ADD COLUMN `ts` TIMESTAMP  NOT NULL AFTER `json`;
");
$installer->endSetup();

