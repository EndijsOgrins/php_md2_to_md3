-- Create database
CREATE DATABASE IF NOT EXISTS rally 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_latvian_ci;

USE rally;

-- Sacensību tabula
CREATE TABLE IF NOT EXISTS sacensibas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nosaukums VARCHAR(255) NOT NULL,
    norises_vieta VARCHAR(255) NOT NULL,
    datums_no DATE NOT NULL,
    datums_lidz DATE NOT NULL
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_latvian_ci;

INSERT INTO sacensibas (nosaukums, norises_vieta, datums_no, datums_lidz) VALUES
('2025 Latvijas rallijs', 'Latvija, Cēsis', '2025-06-01', '2025-06-04'),
('2025 Ziemas čempionāts', 'Latvija, Sigulda', '2025-02-10', '2025-02-12'),
('2024 Baltijas kauss', 'Lietuva, Viļņa', '2024-09-15', '2024-09-18'),
('2024 Zemgales rallijs', 'Latvija, Jelgava', '2024-05-20', '2024-05-22'),
('2023 Latvijas čempionāts', 'Latvija, Rīga', '2023-07-01', '2023-07-04'),
('2023 Baltijas kauss', 'Igaunija, Tallina', '2023-08-20', '2023-08-22');

-- Sponsoru tabula
CREATE TABLE IF NOT EXISTS sponsori (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kompanijas_nosaukums VARCHAR(255) NOT NULL UNIQUE,
    url VARCHAR(255),
    logo VARCHAR(255),
    talrunis VARCHAR(20),
    piezimes TEXT
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_latvian_ci;

INSERT INTO sponsori (kompanijas_nosaukums, url, logo, talrunis, piezimes) VALUES
('Latvijas Balzams', 'https://amberlb.lv', 'lb_logo.png', '+371 67000001', 'Galvenais sponsors'),
('Circle K', 'https://circlek.lv', 'ck_logo.png', '+371 67000002', 'Degvielas partners'),
('LMT', 'https://lmt.lv', 'lmt_logo.png', '+371 67000003', 'Komunikāciju partners'),
('Michelin', 'https://michelin.com', 'michelin_logo.png', '+371 67000004', 'Riepu sponsors'),
('Red Bull', 'https://redbull.com', 'redbull_logo.png', '+371 67000005', 'Enerģijas dzēriena sponsors'),
('Castrol', 'https://www.castrol.com', 'castrol_logo.png', '+371 67000006', 'Eļļas sponsors'),
('Delfi', 'https://delfi.lv', 'delfi_logo.png', '+371 67000007', 'Mediju partners');

-- Savienojuma tabula
CREATE TABLE IF NOT EXISTS sacensibas_sponsori (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sacensibas_id INT NOT NULL,
    sponsora_id INT NOT NULL,
    FOREIGN KEY (sacensibas_id) REFERENCES sacensibas(id) ON DELETE CASCADE,
    FOREIGN KEY (sponsora_id) REFERENCES sponsori(id) ON DELETE CASCADE,
    UNIQUE KEY unique_relationship (sacensibas_id, sponsora_id) 
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_latvian_ci;

-- Savienojumi
INSERT IGNORE INTO sacensibas_sponsori (sacensibas_id, sponsora_id) VALUES
(1, 1),
(1, 1), -- Test duplicate
(1, 2),
(1, 3),
(1, 5),

(2, 1),
(2, 4),
(2, 5),
(2, 6),

(3, 1),
(3, 2),
(3, 6),

(4, 3),
(4, 4),
(4, 7),

(5, 1),
(5, 3),
(5, 5),

(6, 2),
(6, 4),
(6, 6),
(6, 7);