INSERT INTO device_types (name) VALUES 
    ('Smartfon'),
    ('Telefon'),
    ('Tablet'),
    ('Laptop'), 
    ('Smartwatch'), 
    ('PC'),
    ('Monitor'),
    ('TV'),
    ('Router'),
    ('Drukarka'),
    ('Skaner'),
    ('Konsola do gier'),
    ('Przystawka TV'),
    ('Projektor'),
    ('Głośnik'),
    ('Słuchawki'),
    ('Kamera'),
    ('Myszka'),
    ('Klawiatura'),
    ('Inne');

INSERT INTO device_manufacturers (name) VALUES 
    ('Samsung'), 
    ('Apple'), 
    ('Xiaomi'), 
    ('HP'), 
    ('Dell'),
    ('Lenovo'),
    ('Asus'),
    ('Acer'),
    ('Huawei'),
    ('OnePlus'),
    ('Sony'),
    ('LG'),
    ('Motorola'),
    ('Google'),
    ('Nokia'),
    ('Realme'),
    ('Oppo'),
    ('Vivo'),
    ('Honor'),
    ('ZTE'),
    ('Toshiba'),
    ('Panasonic'),
    ('Microsoft'),
    ('Razer'),
    ('Alienware'),
    ('MSI'),
    ('Gigabyte'),
    ('ASRock'),
    ('Corsair'),
    ('EVGA'),
    ('Thermaltake'),
    ('NZXT'),
    ('Fractal Design'),
    ('Cooler Master'),
    ('Be Quiet!'),
    ('Logitech'),
    ('XPG'),
    ('ADATA'),
    ('Crucial'),
    ('Kingston'),
    ('G.Skill'),
    ('HyperX'),
    ('Patriot Memory'),
    ('Team Group'),
    ('Silicon Power'),
    ('Transcend'),
    ('Western Digital'),
    ('Seagate'),
    ('SanDisk'),
    ('HGST'),
    ('Inne'),
    ('Hitachi');

INSERT INTO warehouse_parts_categories (name) VALUES 
('Matryca laptop'),
('Bateria Laptop'),
('Bateria smartfon'),
('Wyświetlacz smartfon'),
('Wyświetlacz tablet'),
('Dysk HDD/SSD'),
('Pamięć RAM'),
('Zasilacz'),
('Obudowa'),
('Płyta główna'),
('CPU'),
('Karta sieciowa'),
('Napęd optyczny'),
('Chłodzenie CPU PC'),
('Chłodzenie Laptop'),
('GPU'),
('Inne');


INSERT INTO localizations (name, postal_code, city, street, building_number) VALUES 
    ('Oddział w Poznaniu', '60-001', 'Poznań', 'ul. Przykładowa', '1'),
    ('Oddział w Warszawie', '00-001', 'Warszawa', 'ul. Przykładowa', '2'),
    ('Oddział w Krakowie', '30-001', 'Kraków', 'ul. Przykładowa', '3'),
    ('Oddział we Wrocławiu', '50-001', 'Wrocław', 'ul. Przykładowa', '4'),
    ('Oddział w Gdańsku', '80-001', 'Gdańsk', 'ul. Przykładowa', '5');

-- Wszystkie konta posiadają hasz hasła "admin"
INSERT INTO users (username, password_hash, first_name, last_name, email, phone_number, account_type) VALUES 
    ('admin', '$2y$10$/CT9ZCoh1jWONuSn8E7/3erLrNAuNKryAH/Kidq2bMWA2afpqRDWu', 'Admin', 'User', 'admin@example.com', '123-456-789', 'Admin'),
    ('employee1', '$2y$10$/CT9ZCoh1jWONuSn8E7/3erLrNAuNKryAH/Kidq2bMWA2afpqRDWu', 'John', 'Doe', 'employee1@example.com', '123-456-789', 'Employee'),
    ('employee2', '$2y$10$/CT9ZCoh1jWONuSn8E7/3erLrNAuNKryAH/Kidq2bMWA2afpqRDWu', 'Jane', 'Smith', 'employee2@example.com', '123-456-789', 'Employee'),
    ('intern1', '$2y$10$/CT9ZCoh1jWONuSn8E7/3erLrNAuNKryAH/Kidq2bMWA2afpqRDWu', 'Alice', 'Johnson', 'intern1@example.com', '123-456-789', 'Intern');


INSERT INTO device_models (manufacturer_id, device_type_id, name) VALUES 
    (1, 1, 'Galaxy S24 Ultra'),
    (2, 1, 'iPhone 15 Pro Max'),
    (3, 1, 'Xiaomi 14 Pro'),
    (4, 4, 'HP Spectre x360'),
    (5, 4, 'Dell XPS 13'),
    (6, 4, 'Lenovo ThinkPad X1 Carbon'),
    (7, 4, 'Asus ZenBook 14'),
    (8, 4, 'Acer Swift 3'),
    (9, 1, 'Huawei P60 Pro'),
    (10, 1, 'OnePlus 12 Pro'),
    (11, 8, 'Sony Bravia XR A90J'),
    (12, 8, 'LG OLED C1'),
    (13, 8, 'Samsung QN90A Neo QLED'),
    (14, 9, 'TP-Link Archer AX6000'),
    (15, 10, 'Canon PIXMA TS9120'),
    (16, 10, 'Epson EcoTank ET-4760'),
    (17, 11, 'Fujitsu ScanSnap iX1500'),
    (18, 12, 'Sony PlayStation 5'),
    (19, 12, 'Microsoft Xbox Series X'),
    (20, 13, 'Amazon Fire TV Stick 4K');

INSERT INTO device_model_codes (device_model_id, code_name) VALUES 
    (1, 'SM-S928B'),
    (2, 'A2899'),
    (3, 'M2102K1G'),
    (4, 'X360-14E'),
    (5, 'XPS13-9310'),
    (6, 'TPX1C-20U9S'),
    (7, 'UX425EA-KI784T'),
    (8, 'SF314-42-R9YN'),
    (9, 'P60Pro-NEO'),
    (10, 'OnePlus12Pro-NEO'),
    (11, 'XR-A90J'),
    (12, 'OLED-C1'),
    (13, 'QN90A-Neo-QLED'),
    (14, 'AX6000'),
    (15, 'PIXMA-TS9120'),
    (16, 'ET-4760'),
    (17, 'iX1500'),
    (18, 'PS5-Digital-Edition'),
    (19, 'Xbox-Series-X'),
    (20, 'Fire-TV-Stick-4K');

INSERT INTO manufacturer_device_types (manufacturer_id, device_type_id) VALUES 
    (1, 1), (1, 4), (1, 8), -- Samsung produkuje smartfony, laptopy i telewizory
    (2, 1), (2, 4), (2, 8), -- Apple produkuje smartfony, laptopy i telewizory
    (3, 1), (3, 4), (3, 8),  -- Xiaomi produkuje smartfony, laptopy i telewizory
    (4, 4), -- HP produkuje laptopy
    (5, 4), -- Dell produkuje laptopy
    (6, 4), -- Lenovo produkuje laptopy
    (7, 4), -- Asus produkuje laptopy
    (8, 4), -- Acer produkuje laptopy
    (9, 1), -- Huawei produkuje smartfony
    (10, 1), -- OnePlus produkuje smartfony
    (11, 8), -- Sony produkuje telewizory
    (12, 8), -- LG produkuje telewizory
    (13, 8), -- Samsung produkuje telewizory
    (14, 9), -- TP-Link produkuje routery
    (15, 10), -- Canon produkuje drukarki
    (16, 10), -- Epson produkuje drukarki
    (17, 11), -- Fujitsu produkuje skanery
    (18, 12), -- Sony produkuje konsole do gier
    (19, 12), -- Microsoft produkuje konsole do gier
    (51, 1), (51, 2), (51, 3), (51, 4), (51, 5), (51, 6), (51, 7), (51, 8), (51, 9), (51, 10), (51, 11), (51, 12), (51, 13), -- Inne produkuje przystawki TV
    (20, 13); -- Amazon produkuje przystawki TV