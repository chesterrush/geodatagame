CREATE DATABASE IF NOT EXISTS koordinatenSpiel;
USE koordinatenSpiel;

CREATE TABLE IF NOT EXISTS eingaben (
    id INT AUTO_INCREMENT PRIMARY KEY,
    koordinatenEingabe VARCHAR(255) NOT NULL,
    erkanntesFormat VARCHAR(50),
    istKorrekt TINYINT(1) DEFAULT 0,
    korrektesFormat VARCHAR(50),
    zeitstempel TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

