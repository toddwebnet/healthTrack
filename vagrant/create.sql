
CREATE USER 'health_track'@'%' IDENTIFIED BY 'jSuZ7ugR7SKB9Afm';
CREATE DATABASE IF NOT EXISTS `health_track`;
GRANT ALL PRIVILEGES ON `health_track`.* TO 'health_track'@'%';GRANT ALL PRIVILEGES ON `health_track\_%`.* TO 'health_track'@'%';
flush privileges;
