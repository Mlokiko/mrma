CREATE DATABASE IF NOT EXISTS mRMA;
USE mRMA;

--
-- Producenci, Urządzenia, Modele
--

CREATE TABLE device_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE, -- np. Laptop, Smartfon, Smartwatch, TV
    is_active BOOLEAN DEFAULT TRUE  -- Czy typ urządzenia jest aktywny (czy można go używać w zgłoszeniach). Nie chcemy usuwać typów, bo mogą być powiązane ze zgłoszeniami, ale możemy je dezaktywować, żeby nie były już widoczne przy dodawaniu nowych zgłoszeń
);

CREATE TABLE device_manufacturers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE, -- np. Samsung, Apple, Xiaomi
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE manufacturer_device_types (    -- Tabela łącząca: Jakie typy urządzeń produkuje dany producent
    manufacturer_id INT NOT NULL,
    device_type_id INT NOT NULL,
    PRIMARY KEY (manufacturer_id, device_type_id),
    FOREIGN KEY (manufacturer_id) REFERENCES device_manufacturers(id) ON DELETE CASCADE,
    FOREIGN KEY (device_type_id) REFERENCES device_types(id) ON DELETE CASCADE
);

CREATE TABLE device_models (
    id INT AUTO_INCREMENT PRIMARY KEY,
    manufacturer_id INT NOT NULL,
    device_type_id INT NOT NULL,
    name VARCHAR(100) NOT NULL, -- np. Galaxy S24 Ultra
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (manufacturer_id) REFERENCES device_manufacturers(id) ON DELETE RESTRICT,
    FOREIGN KEY (device_type_id) REFERENCES device_types(id) ON DELETE RESTRICT
);

CREATE TABLE device_model_codes (   -- Nazwy kodowe modeli urządzeń (np. SM-S928B dla Galaxy S24 Ultra)
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_model_id INT NOT NULL,
    code_name VARCHAR(100) NOT NULL UNIQUE, -- np. SM-S928B
    FOREIGN KEY (device_model_id) REFERENCES device_models(id) ON DELETE CASCADE
);

CREATE TABLE localizations (    -- Lokalizacja serwisu (np. oddział w Poznaniu, oddział w Warszawie)
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL, -- np. Oddział w Poznaniu
    postal_code VARCHAR(20),
    city VARCHAR(100),
    street VARCHAR(100),
    building_number VARCHAR(20)
);

CREATE TABLE users (    -- Użytkownicy systemu (pracownicy serwisu, praktykanci, administratorzy systemu)
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone_number VARCHAR(20),
    account_type ENUM('Employee', 'Intern', 'Admin') DEFAULT 'Employee',
    last_login DATETIME
);

CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_type ENUM('Individual', 'Company') NOT NULL DEFAULT 'Individual', -- Czy klient to osoba fizyczna (Individual) czy firma (Company);
    first_name VARCHAR(50) NOT NULL, -- Gdy klient ma status "Company", jest to nazwa firmy
    last_name VARCHAR(50), -- Może być puste, jeśli to firma (Gdy klient ma status "Company", jest to imię/nazwisko reprezentanta)
    nip VARCHAR(20)  -- NIP, wykorzystywany gdy klient jest "Company"
    primary_phone VARCHAR(20) NOT NULL,
    additional_phones JSON,  -- Format JSON np: [{"number": "+48123...", "description": "do żony"}, {"number": "...", "description": "firmowy"}]
    email VARCHAR(100),
    preferred_contact ENUM('Phone', 'SMS', 'Email') DEFAULT NULL,

    -- Te trzy poniższe wartości najlepiej aktualizować z poziomu PHP po każdym zakończonym RMA
    rma_count INT DEFAULT 0,
    total_spent DECIMAL(10, 2) DEFAULT 0.00,
    internal_note TEXT  -- np. "Klient często dzwoni z pretensjami, że nie odbieramy telefonu" - tylko dla pracowników serwisu
);

CREATE TABLE client_relations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    related_client_id INT NOT NULL,
    relation_type VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Indeksy wydajnościowe
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (related_client_id) REFERENCES clients(id) ON DELETE CASCADE,
    -- Zabezpieczenie przed zdublowaniem tej samej relacji
    UNIQUE KEY unique_user_relation (client_id, related_client_id)
);

-- Partnerzy serwisu (firmy zewnętrzne, które współpracują z serwisem, i zlecają nam zadania (dają zgłoszenia))
CREATE TABLE partners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(150) NOT NULL,
    representative_first_name VARCHAR(50),
    representative_last_name VARCHAR(50),
    primary_phone VARCHAR(20) NOT NULL,
    additional_phones JSON,  -- Format JSON, tak samo jak u klientów
    email VARCHAR(100),
    address_location VARCHAR(255), -- Adres firmy zewnętrznej
    internal_note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--
-- Zgłoszenia RMA
--

CREATE TABLE rma (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Cyfra reprezentująca zgłoszenie
    localization_id INT NOT NULL, -- Gdzie przyjęto (Oddział Poznań, Warszawa, itp.)
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,  -- Używamy DATETIME zamiast TIMESTAMP, żeby użytkownik mógł ręcznie cofnąć/zmienić datę
    ended_at DATETIME DEFAULT NULL, -- Kiedy zakończono zgłoszenie (skończono nad nim prace), NULL jeśli jeszcze nie zakończone (nowe, w trakcie diagnozy, oczekiwanie na części, itp.)
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,     -- Zmieniane za każdym razem gdy zmienia się status zgłoszenia????????????????????
    picked_up_at DATETIME DEFAULT NULL;     -- Kiedy klient odebrał swój sprzęt (również kiedy za niego zapłacił)
    client_id INT, -- NULL jeśli klient to Partner
    partner_id INT, -- NULL jeśli klient to Individual/Company
    device_model_id INT NOT NULL, -- Odwołanie do tabeli z modelami (z poprzedniego kroku), załatwia Typ/Producenta/Model
    serial_number VARCHAR(100),
    device_lock_code VARCHAR(255) DEFAULT NULL, -- Hasło, pin, wzór blokady
    advance_payment DECIMAL(10,2) DEFAULT 0.00,     -- Zaliczka na naprawę klienta
    issue_description TEXT NOT NULL,
    liquid_damage_status ENUM(
        'None', -- Nie zalany
        'Reported_At_Intake', -- Zgłoszono przy przyjmowaniu
        'Reported_Old_Unrelated', -- Stare/niezwiązane z problemem urządzenia zgłoszonym do serwisu
        'Found_During_Diag_Relevant', -- Znaleziono - ma/może mieć wpływ na zgłoszony problem
        'Found_During_Diag_Irrelevant' -- Znaleziono - raczej bez wpływu na zgłoszony problem
    ) DEFAULT 'None',
    received_by_user_id INT NOT NULL, -- Kto fizycznie kliknął "Utwórz zgłoszenie"
    is_express BOOLEAN DEFAULT FALSE, -- Czy zgłoszenie jest ekspresowe (priorytetowe)
    estimated_cost DECIMAL(10, 2),    -- Prawdopodobny koszt naprawy urządzenia klienta, podawany w trakcie przyjęcia zgłoszenia
    max_approved_cost DECIMAL(10, 2), -- Górna granica budżetu klienta, podawana w trakcie przyjmowania zgłoszenia
    parts_cost DECIMAL(10, 2) DEFAULT 0.00,     -- Koszt części (np. wyświetlacza, palmrest)
    internal_cost DECIMAL(10, 2) DEFAULT 0.00, -- Koszt zużycia np. Grotów, Chusteczek, Pasty termoprzewodzącej, itp. (koszt materiałów eksploatacyjnych), koszt pracownika (np. 30 zł/h) * czas pracy nad zgłoszeniem (w godzinach)
    final_cost DECIMAL(10, 2),      -- Finalny koszt który ponosi klient 
    payment_method ENUM('Cash', 'Card', 'Blik') DEFAULT NULL,
    status ENUM('Nowe', 'W diagnozie', 'Czeka na części', 'W naprawie', 'Gotowe', 'Wydane', 'Reklamacja', 'Anulowane') DEFAULT 'Nowe',
    -- status ENUM('Nowe', 'Diagnozowanie', 'Oczekiwanie na części', 'W naprawie', 'Gotowe', 'Wydane', 'Reklamacja', 'Anulowane') DEFAULT 'Nowe',

    warranty_months INT DEFAULT NULL,
    warranty_issued_at DATETIME DEFAULT NULL,
    warranty_scope TEXT DEFAULT NULL,
    warranty_covered TEXT DEFAULT NULL;
    
    -- KLUCZE OBCE
    FOREIGN KEY (localization_id) REFERENCES localizations(id) ON DELETE RESTRICT,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT,
    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE RESTRICT,
    FOREIGN KEY (device_model_id) REFERENCES device_models(id) ON DELETE RESTRICT,
    FOREIGN KEY (received_by_user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- HISTORIA ZMIAN RMA (Kto, co i kiedy robił)
CREATE TABLE rma_user_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rma_id INT NOT NULL,
    user_id INT NOT NULL,
    action_description VARCHAR(255) NOT NULL,   -- Co dany pracownik zrobił, np. "Rozpoczęcie diagnozy", "Wlutowanie układu ładowania", "Testy końcowe"
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rma_id) REFERENCES rma(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

CREATE TABLE rma_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rma_id INT NOT NULL,
    user_id INT NOT NULL, -- Kto zmienił status (kto kliknął)
    old_status ENUM('Nowe', 'W diagnozie', 'Czeka na części', 'W naprawie', 'Gotowe', 'Wydane', 'Reklamacja', 'Anulowane'), -- old_status może być NULL tylko przy tworzeniu nowego zlecenia
    new_status ENUM('Nowe', 'W diagnozie', 'Czeka na części', 'W naprawie', 'Gotowe', 'Wydane', 'Reklamacja', 'Anulowane') NOT NULL,
    note VARCHAR(255),  -- Opcjonalna notatka, np. "Oczekiwanie na układ z Chin" przy statusie 'Czeka na części'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rma_id) REFERENCES rma(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

CREATE TABLE rma_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rma_id INT NOT NULL,
    user_id INT NOT NULL, -- Kto utworzył/zapisał notatkę
    note_text TEXT NOT NULL, -- Treść notatki
    is_internal BOOLEAN DEFAULT TRUE, -- Czy notatka jest tylko dla pracowników serwisu (true) czy może być widoczna dla klienta w panelu klienta (false)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Data napisania notatki
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP, -- Data edycji. Początkowo ma wartość NULL. Zostanie automatycznie zaktualizowana przy każdej operacji UPDATE na tym wierszu.
    FOREIGN KEY (rma_id) REFERENCES rma(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

CREATE TABLE uploaded_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, -- Kto dodał plik
    
    -- Relacje (Plik może należeć do RMA LUB do ogólnego Modelu jako baza wiedzy)
    rma_id INT NULL, 
    device_model_id INT NULL, 
    note_id INT NULL,
    
    original_name VARCHAR(255) NOT NULL, -- np. "asus_x509_working_bios.bin" lub "skan_faktury.pdf"
    stored_name VARCHAR(255) NOT NULL,   -- np. "60a1f39b20cc4d.bin" (losowa, bezpieczna nazwa na dysku)
    file_path VARCHAR(255) NOT NULL,     -- np. "uploads/rma_123/" lub "uploads/knowledge_base/"
    file_size INT NOT NULL,              -- Rozmiar w bajtach (do wyświetlania np. "2.4 MB")
    mime_type VARCHAR(100) NOT NULL,     -- Typ pliku, np. "application/octet-stream", "application/pdf"
    
    file_category ENUM('Document', 'Image', 'Firmware_BIOS', 'Schematic_Boardview', 'Other') NOT NULL, -- Podział na kategorie ułatwi filtrowanie plików w systemie
    
    is_internal BOOLEAN DEFAULT TRUE,    -- Czy plik jest widoczny dla klienta (np. skan protokołu wydania - FALSE, wsad BIOS - TRUE)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (rma_id) REFERENCES rma(id) ON DELETE CASCADE,
    FOREIGN KEY (device_model_id) REFERENCES device_models(id) ON DELETE CASCADE,
    FOREIGN KEY (note_id) REFERENCES rma_notes(id) ON DELETE CASCADE
);

--
--  Magazyn
--

CREATE TABLE warehouse_parts_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE -- np. Bateria, Matryca, Wyświetlacz, Dysk
);

CREATE TABLE warehouse_parts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    manufacturer VARCHAR(100), -- Producent samej części (np. BOE, LG, WD) a nie producent urządzenia docelowego
    part_model_code VARCHAR(100), -- np. NT156FHM-N63 (matryca), BM4Y (bateria do Poco F3, Mi 11i, Redmi K40 pro)
    compatible_device_ids JSON,  -- Kompatybilność z danymi modelami urządzeń (np. matryca laptopowa która wchodzi jednocześnie do thinkpada t420 oraz t430 ) zapisana jako JSON (tablica ID z tabeli device_models), ułatwia to obsługę w PHP (json_decode/json_encode)
    test_compatible_device_ids JSON, -- Jak wyżej, tylko dla części testowych (np. matrycca laptopowa która ma kompatybilne złącze, ale ma nieprawidłowy rozmiar, np. 14" zamiast 15")
    
    condition_status ENUM(
        'Nowy', 
        'Refabrykowany - Jak Nowy', 'Refabrykowany - Normalne Ślady', 'Refabrykowany - Znaczne Ślady', 
        'Używany - Jak Nowy', 'Używany - Normalne Ślady', 'Używany - Znaczne Ślady', 
        'Na Części', 'Testowy'
    ) NOT NULL,
    
    color VARCHAR(50),
    item_type ENUM('Assembly', 'Incomplete_Assembly', 'Part') NOT NULL,
    is_original BOOLEAN DEFAULT TRUE,
    market_price DECIMAL(10, 2),  -- Cena rynkowa części w danym stanie/kolorze/wariancie
    price_checked_at DATE,  -- Ostatnia data sprawdzenia ceny rynkowej części
    quantity INT DEFAULT 0,     -- Liczba sztuk danej części w magazynie
    description TEXT,  -- Opis słowny, np. "niekompatybilne z zwykłym Redmi K40"
    storage_location VARCHAR(100), -- np. Pudło wyświetlacze xiaomi, piwnica
    technical_attributes JSON,  -- Tutaj lądują wszystkie dodatkowe atrybuty (Hz, cale, rozdzielczość, typy złącza, rozmiary) w formacie JSON
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES warehouse_parts_categories(id) ON DELETE RESTRICT
);

CREATE TABLE warehouse_tools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL, -- np. Chusteczki bezpyłowe, Grot T12-ILS
    quantity INT DEFAULT 0,
    unit VARCHAR(20) DEFAULT 'szt', -- np. szt, ml, paczka
    storage_location VARCHAR(100),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);