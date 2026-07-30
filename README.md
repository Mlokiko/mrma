mRMA - wewnętrzny system zgłoszeń (RMA) dla serwisów elektroniki (PC, smartfony, laptopy, itp.)



Strona napisana w czystym PHP, HTML, CSS, JS, SQL. Do ostylowania posłużył bootstrap. Strona pisana przy pomocy AI



Strona w trakcie budowy, jest to wczesna alfa



Dostępna funkcjonalność (co działa):

- Dodawanie nowych zgłoszeń

- Przeglądanie/wyszukiwanie zgłoszeń

- Generowanie dokumentów (przyjęcia, gwarancji, ekspertyza)

- Notatki do danego zgłoszenia

- Zarządzanie użytkownikami systemu



Co zostanie dodane:

- Zarządzanie magazynem (części typu ekrany, baterie,)

- Wyświetlanie magazynu w momencie przyjmowania zgłoszenia

- Udostępnianie plików (zdjęcia urządzenia przed/po przyjęciu, wsady biosowe, zgrane kopie danych itp.)

- Panel klienta/partnera (do śledzenia statusu naprawy)

- System generowania komunikatów email/sms (dla klientów o zmianie statusu zgłoszenia, użytkowników systemu o ważnych zmianach, administratorowi o próbach logownia itp.)

- Automatyczne generowanie listów przewozowych / zamawianie kuriera

- obsługa innych języków (angielski, ukraiński)



Uruchomienie lokalnie:

- zainstalować XAMPP

- folder mrma przenieść do htdocs

- W phpmyadmin wykonać Database_schema.sql oraz Database_przykładowe_dane.sql (podstawowe dane potrzebne do działania strony)

- Zmienić nazwę pliku .env.example na .env, uzupełnić go o prawdziwe dane

- Dostęp do strony po localhost/mrma. Standardowy wbudowany użytkownik to Admin (hasło admin).




