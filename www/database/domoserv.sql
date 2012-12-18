SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

SHOW WARNINGS;
DROP SCHEMA IF EXISTS `domoserv` ;
CREATE SCHEMA IF NOT EXISTS `domoserv` DEFAULT CHARACTER SET utf8 ;
SHOW WARNINGS;
USE `domoserv` ;

-- -----------------------------------------------------
-- Table `domoserv`.`room`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `domoserv`.`room` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `domoserv`.`room` (
  `room_id` INT NOT NULL AUTO_INCREMENT ,
  `room_name` VARCHAR(45) NULL ,
  `house_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`room_id`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `domoserv`.`module`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `domoserv`.`module` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `domoserv`.`module` (
  `module_id` INT NOT NULL AUTO_INCREMENT ,
  `module_protocol` INT(3) NOT NULL ,
  `zibase_id` VARCHAR(45) NOT NULL ,
  `module_name` VARCHAR(45) NOT NULL ,
  `module_type` INT(3) NOT NULL ,
  `module_description` VARCHAR(100) NULL DEFAULT NULL ,
  `room_id` INT NOT NULL ,
  `notify` TINYINT(1) NOT NULL ,
  `notify_title` VARCHAR(100) NULL ,
  `notify_message` VARCHAR(300) NULL ,
  PRIMARY KEY (`module_id`, `module_protocol`, `zibase_id`, `room_id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE INDEX `fk_module_room1` ON `domoserv`.`module` (`room_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `domoserv`.`ping_states`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `domoserv`.`ping_states` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `domoserv`.`ping_states` (
  `machine` VARCHAR(15) NOT NULL DEFAULT '0.0.0.0' ,
  `datetime` DATETIME NOT NULL ,
  `status` BINARY(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`machine`, `datetime`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `domoserv`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `domoserv`.`user` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `domoserv`.`user` (
  `user_id` INT NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(45) NOT NULL ,
  `firstname` VARCHAR(45) NOT NULL ,
  `lastname` VARCHAR(45) NOT NULL ,
  `password` VARCHAR(32) NOT NULL ,
  `userlevel` VARCHAR(45) NOT NULL DEFAULT 'user' ,
  `userlatitude` VARCHAR(45) NULL ,
  PRIMARY KEY (`user_id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `domoserv`.`usernotify`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `domoserv`.`usernotify` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `domoserv`.`usernotify` (
  `user_id` INT NOT NULL ,
  `devices` VARCHAR(45) NOT NULL ,
  `type` VARCHAR(32) NOT NULL ,
  `contact` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`user_id`, `devices`, `type`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE INDEX `fk_usernotify_user1` ON `domoserv`.`usernotify` (`user_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `domoserv`.`battery`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `domoserv`.`battery` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `domoserv`.`battery` (
  `module_protocol` INT(3) NOT NULL ,
  `zibase_id` VARCHAR(45) NOT NULL ,
  `curdate` DATE NOT NULL ,
  `curtime` TIME NOT NULL ,
  `value` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`module_protocol`, `zibase_id`) ,
  CONSTRAINT `fk_battery_module1`
    FOREIGN KEY (`module_protocol` , `zibase_id` )
    REFERENCES `domoserv`.`module` (`module_protocol` , `zibase_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `domoserv`.`module_states`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `domoserv`.`module_states` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `domoserv`.`module_states` (
  `module_protocol` INT(3) NOT NULL ,
  `zibase_id` VARCHAR(45) NOT NULL ,
  `curdatetime` DATETIME NULL ,
  `c` VARCHAR(45) NULL ,
  `c1` VARCHAR(45) NULL ,
  `c2` VARCHAR(45) NULL ,
  `c3` VARCHAR(45) NULL ,
  `c4` VARCHAR(45) NULL ,
  PRIMARY KEY (`module_protocol`, `zibase_id`) ,
  CONSTRAINT `fk_module_states_module1`
    FOREIGN KEY (`module_protocol` , `zibase_id` )
    REFERENCES `domoserv`.`module` (`module_protocol` , `zibase_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `domoserv`.`usertracking`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `domoserv`.`usertracking` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `domoserv`.`usertracking` (
  `user_id` INT NOT NULL ,
  `longitude` FLOAT NOT NULL ,
  `latitude` FLOAT NOT NULL ,
  `timestamp` TIMESTAMP NOT NULL ,
  PRIMARY KEY (`user_id`, `timestamp`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `domoserv`.`userhome`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `domoserv`.`userhome` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `domoserv`.`userhome` (
  `user_id` INT NOT NULL ,
  `timestamp` TIMESTAMP NOT NULL ,
  `home` TINYINT(1) NOT NULL ,
  PRIMARY KEY (`user_id`) )
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `domoserv`.`teleinfo_mono`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `domoserv`.`teleinfo_mono` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `domoserv`.`teleinfo_mono` (
  `timestamp` BIGINT(10) NOT NULL ,
  `rec_date` DATE NOT NULL ,
  `rec_time` TIME NOT NULL ,
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
  `motdetat` VARCHAR(6) NULL DEFAULT NULL )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE UNIQUE INDEX `timestamp` ON `domoserv`.`teleinfo_mono` (`timestamp` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `domoserv`.`teleinfo_tri`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `domoserv`.`teleinfo_tri` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `domoserv`.`teleinfo_tri` (
  `timestamp` BIGINT(10) NOT NULL ,
  `rec_date` DATE NOT NULL ,
  `rec_time` TIME NOT NULL ,
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
  `iinst1` TINYINT(3) NULL DEFAULT NULL ,
  `iinst2` TINYINT(3) NULL DEFAULT NULL ,
  `iinst3` TINYINT(3) NULL DEFAULT NULL ,
  `adir1` TINYINT(3) NULL DEFAULT NULL ,
  `adir2` TINYINT(3) NULL DEFAULT NULL ,
  `adir3` TINYINT(3) NULL DEFAULT NULL ,
  `imax1` TINYINT(3) NULL DEFAULT NULL ,
  `imax2` TINYINT(3) NULL DEFAULT NULL ,
  `imax3` TINYINT(3) NULL DEFAULT NULL ,
  `papp` INT(5) NULL DEFAULT NULL ,
  `hhphc` VARCHAR(1) NULL DEFAULT NULL ,
  `motdetat` VARCHAR(6) NULL DEFAULT NULL ,
  `ppot` TINYINT(2) NULL DEFAULT NULL )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;

SHOW WARNINGS;
CREATE UNIQUE INDEX `timestamp` ON `domoserv`.`teleinfo_tri` (`timestamp` ASC) ;

SHOW WARNINGS;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
