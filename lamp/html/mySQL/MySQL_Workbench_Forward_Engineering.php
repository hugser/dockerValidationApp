sudo cp -r /home/hserodio/Documents/Projects/MAXIV/scripts/javascript/ValidationAPP/  .

-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`ATTR_CLASS`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`ATTR_CLASS` (
  `ATTR_CLASS_ID` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`ATTR_CLASS_ID`),
  UNIQUE INDEX `ATTR_CLASS_ID_UNIQUE` (`ATTR_CLASS_ID` ASC),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`DEVS_CLASS`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`DEVS_CLASS` (
  `DEVS_CLASS_ID` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`DEVS_CLASS_ID`),
  UNIQUE INDEX `DEVS_CLASS_ID_UNIQUE` (`DEVS_CLASS_ID` ASC),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`CLASS`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`CLASS` (
  `CLASS_ID` INT NOT NULL,
  `ATTR_CLASS_ID` INT NOT NULL,
  `DEVS_CLASS_ID` INT NOT NULL,
  PRIMARY KEY (`CLASS_ID`),
  UNIQUE INDEX `ATTR_CLASS_ID_UNIQUE` USING BTREE (`ATTR_CLASS_ID`),
  UNIQUE INDEX `DEVS_CLASS_ID_UNIQUE` (`DEVS_CLASS_ID` ASC),
  CONSTRAINT `fk_CLASS_1`
    FOREIGN KEY (`ATTR_CLASS_ID`)
    REFERENCES `mydb`.`ATTR_CLASS` (`ATTR_CLASS_ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_CLASS_2`
    FOREIGN KEY (`DEVS_CLASS_ID`)
    REFERENCES `mydb`.`DEVS_CLASS` (`DEVS_CLASS_ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`STAGE`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`STAGE` (
  `STAGE_ID` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`STAGE_ID`),
  UNIQUE INDEX `BEAM_ID_UNIQUE` (`STAGE_ID` ASC),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`VERSION`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`VERSION` (
  `VERSION_ID` INT NOT NULL AUTO_INCREMENT,
  `stamp` VARCHAR(45) NULL,
  `name` VARCHAR(45) NULL,
  `comment` TINYTEXT NULL,
  PRIMARY KEY (`VERSION_ID`),
  UNIQUE INDEX `VERSION_ID_UNIQUE` (`VERSION_ID` ASC),
  UNIQUE INDEX `stamp_UNIQUE` (`stamp` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`ACC`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`ACC` (
  `ACC_ID` INT NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`ACC_ID`),
  UNIQUE INDEX `ACC_ID_UNIQUE` (`ACC_ID` ASC),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`VALIDATION`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`VALIDATION` (
  `VALIDATION_ID` INT NOT NULL AUTO_INCREMENT,
  `STAGE_ID` INT NOT NULL,
  `ACC_ID` INT NOT NULL,
  `VERSION_ID` INT NOT NULL,
  UNIQUE INDEX `STAGE_ID_UNIQUE` (`STAGE_ID` ASC),
  INDEX `fk_VALIDATION_2_idx` (`VERSION_ID` ASC),
  UNIQUE INDEX `VERSION_ID_UNIQUE` (`VERSION_ID` ASC),
  PRIMARY KEY (`VALIDATION_ID`),
  UNIQUE INDEX `VALIDATION_ID_UNIQUE` (`VALIDATION_ID` ASC),
  UNIQUE INDEX `ACC_ID_UNIQUE` (`ACC_ID` ASC),
  CONSTRAINT `fk_VALIDATION_1`
    FOREIGN KEY (`STAGE_ID`)
    REFERENCES `mydb`.`STAGE` (`STAGE_ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_VALIDATION_2`
    FOREIGN KEY (`VERSION_ID`)
    REFERENCES `mydb`.`VERSION` (`VERSION_ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_VALIDATION_3`
    FOREIGN KEY (`ACC_ID`)
    REFERENCES `mydb`.`ACC` (`ACC_ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`CLASS_VALIDATION`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`CLASS_VALIDATION` (
  `CLASS_VALIDATION_ID` INT NOT NULL,
  `VALIDATION_ID` INT NOT NULL,
  `CLASS_ID` INT NOT NULL,
  `value` VARCHAR(45) NULL,
  `input_form` VARCHAR(45) NULL,
  `MinMax` VARCHAR(45) NULL,
  `error` VARCHAR(45) NULL,
  PRIMARY KEY (`CLASS_VALIDATION_ID`),
  UNIQUE INDEX `CLASS_ID_UNIQUE` (`CLASS_ID` ASC),
  UNIQUE INDEX `VALIDATION_ID_UNIQUE` (`VALIDATION_ID` ASC),
  CONSTRAINT `fk_CLASS_VALIDATION_1`
    FOREIGN KEY (`CLASS_ID`)
    REFERENCES `mydb`.`CLASS` (`CLASS_ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_CLASS_VALIDATION_2`
    FOREIGN KEY (`VALIDATION_ID`)
    REFERENCES `mydb`.`VALIDATION` (`VALIDATION_ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
