<?php

DEFINE ('USER', 'remote');
DEFINE ('HOST', '10.62.63.147');
DEFINE ('NAME', 'comurede');
DEFINE ('PASS', '712306Ma');
/*
DEFINE ('USER', 'root');
DEFINE ('HOST', '191.252.100.202');
DEFINE ('NAME', 'comurede');
DEFINE ('PASS', '');
*/############################################
DEFINE ('GMAIL', 'tacaindoagua@gmail.com');
DEFINE ('SENHA', 'Comux01$');

############################################
/*
sudo nano mariadb.conf.d/50-server.cnf

bind-address=0.0.0.0

GRANT ALL PRIVILEGES ON *.* TO 'remote'@'%' IDENTIFIED BY '712306Ma' WITH GRANT OPTION;

ALTER TABLE `comurede`.`sensores_agua` 
ADD COLUMN `sensor` INT NULL AFTER `status`;


ALTER TABLE `comurede`.`sensores_agua` 
CHANGE COLUMN `cep` `cep` VARCHAR(8) NULL DEFAULT NULL ;


update sensores_agua set cep='24130400', sensor=1 where id>0

update sensores_luz set cep='24130400', sensor=1 where id>0

ALTER TABLE `comurede`.`triagem` 
CHANGE COLUMN `cep` `cep` VARCHAR(8) NULL DEFAULT NULL ;

ALTER TABLE `comurede`.`relatorios` 
CHANGE COLUMN `cep` `cep` VARCHAR(8) NULL DEFAULT NULL ;





*/