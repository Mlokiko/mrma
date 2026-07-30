<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karta_Gwarancyjna_RMA_#<?= htmlspecialchars($rma['id'] ?? 'N/A') ?></title>
    <style>
        /* Styl poglądowy dla ekranu monitora */
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1e293b; background: #525659; display: flex; justify-content: center; margin: 0; padding: 20px; }
        .page { background: #fff; width: 210mm; min-height: 297mm; padding: 20mm; box-sizing: border-box; box-shadow: 0 0 15px rgba(0,0,0,0.4); position: relative; }
        
        /* Certyfikat / Nagłówek */
        .badge-top { text-align: center; font-size: 11px; text-transform: uppercase; letter-spacing: 2px; color: #64748b; margin-bottom: 5px; }
        .doc-title { text-align: center; font-size: 26px; font-weight: bold; margin: 0 0 30px 0; color: #0f172a; border-bottom: 3px double #0f172a; padding-bottom: 15px; text-transform: uppercase; letter-spacing: 1px; }
        
        .meta-grid { display: flex; justify-content: space-between; margin-bottom: 30px; font-size: 14px; line-height: 1.5; }
        .company-data strong { font-size: 16px; color: #0f172a; }
        .document-meta { text-align: right; }
        
        h3 { font-size: 15px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #cbd5e1; padding-bottom: 6px; margin-top: 25px; margin-bottom: 12px; color: #1e293b; }
        
        /* Tabele danych */
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #cbd5e1; padding: 10px 12px; text-align: left; font-size: 14px; }
        th { background-color: #f8fafc; width: 30%; color: #475569; font-weight: 600; }
        
        /* Sekcje warunków customowych */
        .content-box { border: 1px solid #cbd5e1; padding: 15px; font-size: 14px; line-height: 1.6; border-radius: 4px; background: #f8fafc; min-height: 50px; }
        
        /* Regulamin i zapisy prawne gwarancji */
        .warranty-terms { font-size: 11px; color: #475569; text-align: justify; margin-top: 35px; border-top: 1px solid #0f172a; padding-top: 15px; line-height: 1.5; }
        .warranty-terms h4 { margin: 0 0 8px 0; font-size: 12px; color: #0f172a; text-transform: uppercase; }
        .warranty-terms ol { margin: 0; padding-left: 18px; }
        .warranty-terms li { margin-bottom: 4px; }
        
        /* Sekcja Podpisów */
        .signatures { display: flex; justify-content: space-between; margin-top: 60px; page-break-inside: avoid; }
        .sig-box { border-top: 1px dashed #64748b; width: 230px; text-align: center; font-size: 11px; padding-top: 6px; color: #64748b; }
        
        /* Przycisk akcji */
        .action-buttons { display: flex; justify-content: center; gap: 15px; margin-bottom: 25px; }
        .btn-print { padding: 10px 20px; background: #10b981; color: white; border: none; font-size: 15px; font-weight: bold; cursor: pointer; border-radius: 4px; }
        .btn-close { padding: 10px 20px; background: #64748b; color: white; border: none; font-size: 15px; font-weight: bold; cursor: pointer; border-radius: 4px; }

        @media print {
            .print-actions { display: none !important; }
            @page { size: A4 portrait; margin: 12mm; }
            body { background: transparent; padding: 0; color: #000; }
            .page { box-shadow: none; width: 100%; min-height: auto; padding: 0; margin: 0; }
        }
    </style>
</head>
<body>

    <div class="page">
        <div class="print-actions" style="display: flex; justify-content: center; gap: 15px; margin-bottom: 25px;">
            <button class="btn-print" onclick="window.print()">Drukuj</button>
            <button class="btn-close" onclick="window.close()">Zamknij</button>
        </div>

        <div class="badge-top">Dokument Potwierdzenia Serwisowego</div>
        <div class="doc-title">Karta Gwarancyjna</div>

        <div class="meta-grid">
            <div class="company-data">
                <strong>mRMA Serwis IT</strong><br>
                ul. Przykładowa 12, 00-001 Miasto<br>
                NIP: 123-456-78-90 | Tel: +48 123 456 789<br>
                E-mail: serwis@mrma-system.pl
            </div>
            <div class="document-meta">
                <strong>Zgłoszenie:</strong> #<?= htmlspecialchars($rma['id'] ?? 'N/A') ?><br>
                <strong>Data wydania:</strong> <?= $issuedDate->format('d.m.Y') ?><br>
                <strong>Ważność do:</strong> <span style="font-weight: bold; color: #000;"><?= $expiryDate->format('d.m.Y') ?></span><br>
                <strong>Okres ochrony:</strong> <?= (int)($warrantyMonths ?? 0) ?> miesięcy
            </div>
        </div>

        <h3>Podmiot Gwarancji (Klient)</h3>
        <table>
            <tr>
                <th>Zleceniodawca:</th>
                <td>
                    <?php if (!empty($rma['company_name'])): ?>
                        <strong><?= htmlspecialchars($rma['company_name'] ?? '') ?></strong> (Partner B2B)
                    <?php else: ?>
                        <strong><?= htmlspecialchars(($rma['client_first_name'] ?? '') . ' ' . ($rma['client_last_name'] ?? '')) ?></strong>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Telefon kontaktowy:</th>
                <td><?= htmlspecialchars($rma['primary_phone'] ?? '---') ?></td>
            </tr>
        </table>

        <h3>Specyfikacja Sprzętu</h3>
        <table>
            <tr>
                <th>Urządzenie:</th>
                <td><?= htmlspecialchars((($rma['manufacturer_name'] ?? '') . ' ' . ($rma['model_name'] ?? '')) ?: '---') ?></td>
            </tr>
            <tr>
                <th>Identyfikator (SN / IMEI):</th>
                <td><?= htmlspecialchars($rma['serial_number'] ?? 'Nie podano / Nieczytelny') ?></td>
            </tr>
        </table>

        <?php if ($useGeneric || (empty($warrantyScope) && empty($warrantyCovered))): ?>
            <h3>Zakres ochrony gwarancyjnej</h3>
            <div class="content-box">
                Gwarancja została udzielona na kompleksową usługę naprawy sprzętu zrealizowaną w ramach zgłoszenia serwisowego <strong>RMA #<?= htmlspecialchars($rma['id'] ?? '') ?></strong>. Ochrona obejmuje elementy konstrukcyjne, podzespoły oraz czynności techniczne wyszczególnione w dokumentacji technicznej i kosztorysie zamknięcia zlecenia.
            </div>
        <?php else: ?>
            <?php if (!empty($warrantyScope)): ?>
                <h3>Zakres wykonanej naprawy</h3>
                <div class="content-box">
                    <?= nl2br(htmlspecialchars($warrantyScope ?? '')) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($warrantyCovered)): ?>
                <h3>Elementy objęte bezpośrednią gwarancją</h3>
                <div class="content-box" style="border-left: 3px solid #10b981;">
                    <?= nl2br(htmlspecialchars($warrantyCovered ?? '')) ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="warranty-terms">
            <h4>Warunki gwarancji serwisu technicznego:</h4>
            <ol>
                <li>Gwarancja obowiązuje od daty wydania sprzętu przez okres wskazany w nagłówku niniejszego dokumentu.</li>
                <li>Gwarancją objęte są wyłącznie wady powstałe z przyczyn tkwiących w zamontowanych częściach zamiennych lub wynikające z błędów montażowych serwisu.</li>
                <li><strong>Gwarancja nie obejmuje:</strong> uszkodzeń mechanicznych (pęknięcia, zmiażdżenia), uszkodzeń termicznych, wad powstałych na skutek nieprawidłowego użytkowania urządzenia oraz uszkodzeń wywołanych czynnikami zewnętrznymi (np. przepięcia elektryczne).</li>
                <li>Jakikolwiek kontakt urządzenia z cieczą (zalanie, zawilgocenie) w trakcie trwania okresu gwarancyjnego skutkuje <strong>natychmiastową i całkowitą utratą praw gwarancyjnych</strong>, bez względu na to, jakiego elementu dotyczyła pierwotna naprawa.</li>
                <li>Stwierdzenie zerwania plomb gwarancyjnych serwisu lub śladów ingerencji osób trzecich/innych serwisów wewnątrz urządzenia unieważnia niniejszą gwarancję.</li>
                <li>W przypadku zgłoszenia reklamacyjnego, Klient zobowiązany jest dostarczyć urządzenie do punktu serwisowego wraz z niniejszą Kartą Gwarancyjną.</li>
            </ol>
        </div>

        <div class="signatures">
            <div class="sig-box">Pieczęć i podpis Technika Wydającego</div>
            <div class="sig-box">Potwierdzam odbiór sprawnego sprzętu<br>oraz akceptuję warunki gwarancji (Podpis Klienta)</div>
        </div>
    </div>
</body>
</html>