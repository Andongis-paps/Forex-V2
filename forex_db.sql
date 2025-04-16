/*
SQLyog Ultimate v11.33 (64 bit)
MySQL - 5.7.33 : Database - forex
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`forex` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `forex`;

/*Table structure for table `tblusers` */

DROP TABLE IF EXISTS `tblusers`;

CREATE TABLE `tblusers` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) DEFAULT NULL,
  `Username` varchar(25) DEFAULT NULL,
  `Password` varchar(100) DEFAULT NULL,
  `Email` varchar(75) DEFAULT NULL,
  `SecurityCode` varchar(25) DEFAULT NULL,
  `BranchID` int(11) DEFAULT NULL,
  `LevelID` int(11) DEFAULT NULL,
  `WebLevelID` int(11) DEFAULT NULL,
  `Blocked` tinyint(1) DEFAULT NULL,
  `BranchCode` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*Data for the table `tblusers` */

insert  into `tblusers`(`UserID`,`Name`,`Username`,`Password`,`Email`,`SecurityCode`,`BranchID`,`LevelID`,`WebLevelID`,`Blocked`,`BranchCode`) values (2,'Andong','AdminAcc','ayokongahehe',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'Aarone','MasterDev','qweasdzxc',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,'Killua','Zoldyck','anikahehe',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,'Gon','Freecs','jakandbato',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'Burnok','cityland','sementeryo',NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
