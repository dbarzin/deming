CREATE DATABASE deming CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE USER 'deming_user'@'%' IDENTIFIED BY 'demPasssword-123';
GRANT ALL ON deming.* TO deming_user;
GRANT PROCESS ON *.* TO 'deming_user'@'localhost';

FLUSH PRIVILEGES;
