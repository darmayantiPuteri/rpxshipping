<?php

$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE belvg_brands CHANGE entity_id entity_id INT(10) UNSIGNED NOT NULL;
");

$installer->endSetup();


