<?php
 
$installer = $this;
 
$installer->startSetup();
 
$installer->run("
    DELETE FROM `{$this->getTable('directory/country_region')}` WHERE country_id = 'ID';

    INSERT INTO `{$this->getTable('directory/country_region')}` (`region_id`,`country_id`,`code`,`default_name`) VALUES
    ('500','ID','ID-AC','Special Region of Aceh'),
    ('501','ID','ID-BA','Bali'),
    ('502','ID','ID-BB','BangkaÐBelitung Islands'),
    ('503','ID','ID-BT','Banten'),
    ('504','ID','ID-BE','Bengkulu'),
    ('505','ID','ID-JT','Central Java'),
    ('506','ID','ID-KT','Central Kalimantan'),
    ('507','ID','ID-ST','Central Sulawesi'),
    ('508','ID','ID-JI','East Java'),
    ('509','ID','ID-KI','East Kalimantan'),
    ('510','ID','ID-NT','East Nusa Tenggara'),
    ('511','ID','ID-GO','Gorontalo'),
    ('512','ID','ID-JK','Jakarta (Special City District)'),
    ('513','ID','ID-JA','Jambi'),
    ('514','ID','ID-LA','Lampung'),
    ('515','ID','ID-MA','Maluku'),
    ('516','ID','ID-KU','North Kalimantan'),
    ('517','ID','ID-MU','North Maluku'),
    ('518','ID','ID-SA','North Sulawesi'),
    ('519','ID','ID-SU','North Sumatra'),
    ('520','ID','ID-PA','Special Region of Papua'),
    ('521','ID','ID-RI','Riau'),
    ('522','ID','ID-KR','Riau Islands Province'),
    ('523','ID','ID-SG','Southeast Sulawesi'),
    ('524','ID','ID-KS','South Kalimantan'),
    ('525','ID','ID-SN','South Sulawesi'),
    ('526','ID','ID-SS','South Sumatra'),
    ('527','ID','ID-JB','West Java'),
    ('528','ID','ID-KB','West Kalimantan'),
    ('529','ID','ID-NB','West Nusa Tenggara'),
    ('530','ID','ID-PB','Special Region of West Papua'),
    ('531','ID','ID-SR','West Sulawesi'),
    ('532','ID','ID-SB','West Sumatra'),
    ('533','ID','ID-YO','Special Region of Yogyakarta');
    
    INSERT INTO `{$this->getTable('directory/country_region_name')}` (`locale`,`region_id`,`name`) VALUES
    ('en_US','500','Special Region of Aceh'),
    ('en_US','501','Bali'),
    ('en_US','502','BangkaÐBelitung Islands'),
    ('en_US','503','Banten'),
    ('en_US','504','Bengkulu'),
    ('en_US','505','Central Java'),
    ('en_US','506','Central Kalimantan'),
    ('en_US','507','Central Sulawesi'),
    ('en_US','508','East Java'),
    ('en_US','509','East Kalimantan'),
    ('en_US','510','East Nusa Tenggara'),
    ('en_US','511','Gorontalo'),
    ('en_US','512','Jakarta (Special City District)'),
    ('en_US','513','Jambi'),
    ('en_US','514','Lampung'),
    ('en_US','515','Maluku'),
    ('en_US','516','North Kalimantan'),
    ('en_US','517','North Maluku'),
    ('en_US','518','North Sulawesi'),
    ('en_US','519','North Sumatra'),
    ('en_US','520','Special Region of Papua'),
    ('en_US','521','Riau'),
    ('en_US','522','Riau Islands Province'),
    ('en_US','523','Southeast Sulawesi'),
    ('en_US','524','South Kalimantan'),
    ('en_US','525','South Sulawesi'),
    ('en_US','526','South Sumatra'),
    ('en_US','527','West Java'),
    ('en_US','528','West Kalimantan'),
    ('en_US','529','West Nusa Tenggara'),
    ('en_US','530','Special Region of West Papua'),
    ('en_US','531','West Sulawesi'),
    ('en_US','532','West Sumatra'),
    ('en_US','533','Special Region of Yogyakarta');
                ");
 
$installer->endSetup();
 
?>