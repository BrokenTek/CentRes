DROP DATABASE IF EXISTS Centres;
CREATE DATABASE Centres;
USE Centres;

DROP USER IF EXISTS 'scott'@'localhost';
CREATE USER 'scott'@'localhost' IdENTIFIED BY 'tiger';
GRANT ALL PRIVILEGES ON * . * TO 'scott'@'localhost';