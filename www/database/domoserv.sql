SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `domoserv` ;
CREATE SCHEMA IF NOT EXISTS `domoserv` DEFAULT CHARACTER SET utf8 ;
SHOW WARNINGS;
USE `domoserv` ;

-- -----------------------------------------------------
-- Table `room`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `room` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `room` (
  `room_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `room_name` VARCHAR(45) NOT NULL ,
  `house_name` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`room_id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE UNIQUE INDEX `room_name` ON `room` (`room_name` ASC, `house_name` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `module`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `module` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `module` (
  `module_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `room_id` INT(11) NOT NULL ,
  `zibase_id` VARCHAR(45) NOT NULL ,
  `module_protocol` INT(3) NOT NULL ,
  `module_name` VARCHAR(45) NOT NULL ,
  `module_type` INT(3) NOT NULL ,
  `module_description` VARCHAR(256) NULL DEFAULT NULL ,
  `notify` TINYINT(1) NOT NULL DEFAULT '0' ,
  `notify_title` VARCHAR(256) NULL DEFAULT NULL ,
  `notify_message` VARCHAR(512) NULL DEFAULT NULL ,
  PRIMARY KEY (`module_id`) ,
  CONSTRAINT `fk_module_room1`
    FOREIGN KEY (`room_id` )
    REFERENCES `room` (`room_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE UNIQUE INDEX `module_name` ON `module` (`room_id` ASC, `zibase_id` ASC, `module_protocol` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_module_room1_idx` ON `module` (`room_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `battery`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `battery` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `battery` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `module_id` INT(11) NOT NULL ,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `value` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_battery_module1`
    FOREIGN KEY (`module_id` )
    REFERENCES `module` (`module_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE UNIQUE INDEX `battery` ON `battery` (`module_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_battery_module1_idx` ON `battery` (`module_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `host_services`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `host_services` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `host_services` (
  `host_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `host` VARCHAR(15) NOT NULL DEFAULT '0.0.0.0' ,
  `port` TINYINT(5) NOT NULL DEFAULT '80' ,
  `room_id` INT(11) NOT NULL ,
  `description` VARCHAR(100) NULL DEFAULT NULL ,
  PRIMARY KEY (`host_id`) ,
  CONSTRAINT `fk_host_services_room1`
    FOREIGN KEY (`room_id` )
    REFERENCES `room` (`room_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE UNIQUE INDEX `host_services` ON `host_services` (`host` ASC, `port` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_host_services_room1_idx` ON `host_services` (`room_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `user` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(45) NOT NULL ,
  `firstname` VARCHAR(45) NOT NULL ,
  `lastname` VARCHAR(45) NOT NULL ,
  `password` VARCHAR(45) NOT NULL ,
  `userlevel` VARCHAR(45) NOT NULL DEFAULT 'user' ,
  `userlatitude` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`user_id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE UNIQUE INDEX `user` ON `user` (`username` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `usernotify`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `usernotify` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `usernotify` (
  `usernotify_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `devices` VARCHAR(45) NOT NULL DEFAULT 'all' ,
  `type` VARCHAR(32) NOT NULL ,
  `contact` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`usernotify_id`) ,
  CONSTRAINT `fk_usernotify_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE UNIQUE INDEX `usernotify` ON `usernotify` (`user_id` ASC, `devices` ASC, `type` ASC, `contact` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_usernotify_user1_idx` ON `usernotify` (`user_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `module_notify`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `module_notify` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `module_notify` (
  `modulenotify_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `module_id` INT(11) NOT NULL ,
  `usernotify_id` INT(11) NOT NULL ,
  PRIMARY KEY (`modulenotify_id`) ,
  CONSTRAINT `fk_module_notify_module1`
    FOREIGN KEY (`module_id` )
    REFERENCES `module` (`module_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_module_notify_usernotify1`
    FOREIGN KEY (`usernotify_id` )
    REFERENCES `usernotify` (`usernotify_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE UNIQUE INDEX `modulenotify` ON `module_notify` (`usernotify_id` ASC, `module_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_module_notify_module1` ON `module_notify` (`module_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_module_notify_usernotify1` ON `module_notify` (`usernotify_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `module_states`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `module_states` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `module_states` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `module_id` INT(11) NOT NULL ,
  `curdatetime` DATETIME NULL DEFAULT NULL ,
  `c` VARCHAR(45) NULL DEFAULT NULL ,
  `c1` VARCHAR(45) NULL DEFAULT NULL ,
  `c2` VARCHAR(45) NULL DEFAULT NULL ,
  `c3` VARCHAR(45) NULL DEFAULT NULL ,
  `c4` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_module_states_module1`
    FOREIGN KEY (`module_id` )
    REFERENCES `module` (`module_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE UNIQUE INDEX `module_states` ON `module_states` (`module_id` ASC, `curdatetime` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_module_states_module1_idx` ON `module_states` (`module_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ping_states`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ping_states` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `ping_states` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `host_id` INT(11) NOT NULL ,
  `datetime` DATETIME NOT NULL ,
  `status` TINYINT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_ping_states_host_services1`
    FOREIGN KEY (`host_id` )
    REFERENCES `host_services` (`host_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE UNIQUE INDEX `ping` ON `ping_states` (`host_id` ASC, `datetime` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_ping_states_host_services1_idx` ON `ping_states` (`host_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `teleinfo_mono`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `teleinfo_mono` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `teleinfo_mono` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `adco` VARCHAR(12) NOT NULL ,
  `optarif` VARCHAR(4) NOT NULL ,
  `isousc` TINYINT(2) NULL DEFAULT NULL ,
  `base` BIGINT(9) NULL DEFAULT NULL ,
  `hchc` BIGINT(9) NULL DEFAULT NULL ,
  `hchp` BIGINT(9) NULL DEFAULT NULL ,
  `ejphn` BIGINT(9) NULL DEFAULT NULL ,
  `ejphpm` BIGINT(9) NULL DEFAULT NULL ,
  `bbrhcjb` BIGINT(9) NULL DEFAULT NULL ,
  `bbrhpjb` BIGINT(9) NULL DEFAULT NULL ,
  `bbrhcjw` BIGINT(9) NULL DEFAULT NULL ,
  `bbrhpjw` BIGINT(9) NULL DEFAULT NULL ,
  `bbrhcjr` BIGINT(9) NULL DEFAULT NULL ,
  `bbrhpjr` BIGINT(9) NULL DEFAULT NULL ,
  `pejp` VARCHAR(4) NULL DEFAULT NULL ,
  `ptec` VARCHAR(4) NULL DEFAULT NULL ,
  `demain` VARCHAR(4) NULL DEFAULT NULL ,
  `iinst` TINYINT(3) NULL DEFAULT NULL ,
  `adps` TINYINT(3) NULL DEFAULT NULL ,
  `imax` TINYINT(3) NULL DEFAULT NULL ,
  `papp` INT(5) NULL DEFAULT NULL ,
  `hhphc` VARCHAR(1) NULL DEFAULT NULL ,
  `motdetat` VARCHAR(6) NULL DEFAULT NULL ,
  `pmax` INT(6) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE UNIQUE INDEX `timestamp` ON `teleinfo_mono` (`timestamp` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `teleinfo_tri`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `teleinfo_tri` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `teleinfo_tri` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `ADCO` VARCHAR(12) NOT NULL ,
  `OPTARIF` VARCHAR(4) NOT NULL ,
  `ISOUSC` TINYINT(2) NULL DEFAULT NULL ,
  `HCHP` BIGINT(9) NULL DEFAULT NULL ,
  `HCHC` BIGINT(9) NULL DEFAULT NULL ,
  `PTEC` VARCHAR(4) NULL DEFAULT NULL ,
  `IINST1` TINYINT(3) NULL DEFAULT NULL ,
  `IINST2` TINYINT(3) NULL DEFAULT NULL ,
  `IINST3` TINYINT(3) NULL DEFAULT NULL ,
  `IMAX1` TINYINT(3) NULL DEFAULT NULL ,
  `IMAX2` TINYINT(3) NULL DEFAULT NULL ,
  `IMAX3` TINYINT(3) NULL DEFAULT NULL ,
  `PMAX` INT(6) NULL DEFAULT NULL ,
  `PAPP` INT(5) NULL DEFAULT NULL ,
  `HHPHC` VARCHAR(1) NULL DEFAULT NULL ,
  `MOTDETAT` VARCHAR(6) NULL DEFAULT NULL ,
  `PPOT` TINYINT(2) NULL DEFAULT NULL ,
  `ADIR` TINYINT(3) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 9838
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE UNIQUE INDEX `timestamp_UNIQUE` ON `teleinfo_tri` (`timestamp` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `userhome`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `userhome` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `userhome` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `home` TINYINT(1) NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_userhome_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE UNIQUE INDEX `userhome` ON `userhome` (`user_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_userhome_user1_idx` ON `userhome` (`user_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `userlocation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `userlocation` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `userlocation` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `latitude` FLOAT NOT NULL ,
  `longitude` FLOAT NOT NULL ,
  `urange` FLOAT NOT NULL ,
  `metric` VARCHAR(1) NOT NULL DEFAULT 'K' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_userlocation_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE UNIQUE INDEX `lat_long_user` ON `userlocation` (`user_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_userlocation_user1` ON `userlocation` (`user_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `usertracking`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `usertracking` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `usertracking` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `longitude` FLOAT NOT NULL ,
  `latitude` FLOAT NOT NULL ,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_usertracking_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 646
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE UNIQUE INDEX `usertracking` ON `usertracking` (`user_id` ASC, `timestamp` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_usertracking_user1_idx` ON `usertracking` (`user_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `camip`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `camip` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `camip` (
  `camip_id` INT NOT NULL AUTO_INCREMENT ,
  `room_id` INT(11) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `brand` VARCHAR(45) NULL ,
  `model` VARCHAR(45) NULL ,
  `host` VARCHAR(15) NOT NULL DEFAULT '0.0.0.0' ,
  `port` TINYINT(5) NOT NULL DEFAULT 80 ,
  `user` VARCHAR(45) NOT NULL ,
  `pwd` VARCHAR(45) NOT NULL ,
  `alarm` TINYINT(1) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`camip_id`) ,
  CONSTRAINT `fk_camip_room1`
    FOREIGN KEY (`room_id` )
    REFERENCES `room` (`room_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE UNIQUE INDEX `camera` ON `camip` (`name` ASC, `host` ASC, `port` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_camip_room1` ON `camip` (`room_id` ASC) ;

SHOW WARNINGS;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `user`
-- -----------------------------------------------------
START TRANSACTION;
USE `domoserv`;
INSERT INTO `user` (`user_id`, `username`, `firstname`, `lastname`, `password`, `userlevel`, `userlatitude`) VALUES (1, 'admin', 'Administrator', 'DomoServ', '*F51AB0CE61F44E7DBC15CBB26966ADFCE2AEEB73', 'admin', NULL);

COMMIT;
