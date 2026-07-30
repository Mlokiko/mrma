<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potwierdzenie_RMA_#<?= $rma['id'] ?></title>
    <style>
        /* Styl dla przeglądarki */
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #000; background: #525659; display: flex; justify-content: center; margin: 0; padding: 20px; }
        .page { background: #fff; width: 210mm; min-height: 297mm; padding: 20mm; box-sizing: border-box; box-shadow: 0 0 10px rgba(0,0,0,0.5); }
        
        /* Layout dokumentu */
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; color: #1e293b; }
        .header p { margin: 2px 0; font-size: 14px; color: #475569; }
        
        h3 { border-bottom: 1px solid #cbd5e1; padding-bottom: 5px; margin-top: 25px; margin-bottom: 15px; font-size: 16px; color: #1e293b; }
        
        .grid-2 { display: flex; justify-content: space-between; gap: 20px; }
        .col { width: 48%; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 12px; text-align: left; font-size: 14px; }
        th { background-color: #f1f5f9; width: 35%; color: #475569; font-weight: 600; }
        
        .desc-box { border: 1px solid #cbd5e1; padding: 12px; font-size: 14px; min-height: 60px; border-radius: 4px; background: #f8fafc; }
        
        .terms { font-size: 10px; color: #475569; text-align: justify; margin-top: 40px; border-top: 1px dotted #000; padding-top: 15px; line-height: 1.4; }
        .terms h4 { margin: 0 0 5px 0; font-size: 11px; color: #000; }
        
        .signatures { display: flex; justify-content: space-between; margin-top: 50px; }
        .sig-box { border-top: 1px solid #000; width: 220px; text-align: center; font-size: 12px; padding-top: 5px; color: #475569; }
        
        /* Kontener i style przycisków */
        .action-buttons { display: flex; justify-content: center; gap: 15px; margin-bottom: 25px; }
        .btn-print { padding: 10px 20px; background: #2563eb; color: white; border: none; font-size: 16px; cursor: pointer; border-radius: 4px; }
        .btn-close { padding: 10px 20px; background: #64748b; color: white; border: none; font-size: 16px; cursor: pointer; border-radius: 4px; }
        .btn-print:hover { background: #1d4ed8; }
        .btn-close:hover { background: #475569; }

        /* Wymuszenie czystego wydruku A4 */
        @media print {
            @page { size: A4 portrait; margin: 10mm; }
            body { background: transparent; padding: 0; }
            .page { box-shadow: none; width: 100%; min-height: auto; padding: 0; margin: 0; }
            .action-buttons { display: none; } /* Ukrywamy panel z przyciskami na PDFie */
            .terms { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

    <div class="page">
        <div class="action-buttons">
            <button class="btn-print" onclick="window.print()">Drukuj</button>
            <button class="btn-close" onclick="window.close()">Zamknij</button>
        </div>

        <div class="header">
            <div>
                <h1>mRMA Serwis IT</h1>
                <p>NIP: 123-456-78-90 | Tel: +48 123 456 789</p>
                <p>ul. Przykładowa 12, 00-001 Miasto</p>
            </div>
            <div style="text-align: right;">
                <h1 style="color: #2563eb;">RMA #<?= htmlspecialchars($rma['id']) ?></h1>
                <p><strong>Data przyjęcia:</strong> <?= date('d.m.Y H:i', strtotime($rma['created_at'])) ?></p>
                <p><strong>Przyjmujący:</strong> <?= htmlspecialchars($rma['user_name']) ?></p>
            </div>
        </div>

        <div class="grid-2">
            <div class="col">
                <h3>Dane Klienta</h3>
                <table>
                    <tr><th>Imię i Nazwisko:</th><td><?= htmlspecialchars(($rma['client_first_name'] ?? '') . ' ' . ($rma['client_last_name'] ?? '')) ?></td></tr>
                    <tr><th>Telefon:</th><td><?= htmlspecialchars($rma['primary_phone']) ?></td></tr>
                    <tr><th>Adres E-mail:</th><td><?= htmlspecialchars($rma['email'] ?? '---') ?></td></tr>
                </table>
            </div>
            <div class="col">
                <h3>Szczegóły Finansowe</h3>
                <table>
                    <tr><th>Szacowany Koszt:</th><td><?= $rma['estimated_cost'] ? number_format($rma['estimated_cost'], 2) . ' PLN' : 'Brak wyceny' ?></td></tr>
                    <tr><th>Max. budżet klienta:</th><td><?= $rma['max_approved_cost'] ? number_format($rma['max_approved_cost'], 2) . ' PLN' : '---' ?></td></tr>
                    <tr><th>Wpłacona zaliczka:</th><td><strong><?= $rma['advance_payment'] ? number_format($rma['advance_payment'], 2) . ' PLN' : 'Brak' ?></strong></td></tr>
                </table>
            </div>
        </div>

        <h3>Urządzenie</h3>
        <table>
            <tr>
                <th>Producent i Model:</th>
                <td><?= htmlspecialchars(($rma['manufacturer_name'] ?? '---') . ' ' . ($rma['model_name'] ?? '---')) ?></td>
            </tr>
            <tr>
                <th>Numer Seryjny (SN/IMEI):</th>
                <td><?= htmlspecialchars($rma['serial_number'] ?: 'Brak / Nieczytelny') ?></td>
            </tr>
            <tr>
                <th>Kod blokady (PIN):</th>
                <td><?= htmlspecialchars($rma['device_lock_code'] ?: 'Brak / Nie podano') ?></td>
            </tr>
            <tr>
                <th>Zalanie cieczą:</th>
                <td>
                    <?php 
                        $ld = $rma['liquid_damage_status'];
                        if ($ld === 'None') echo 'Nie zgłoszono';
                        elseif ($ld === 'Reported_At_Intake') echo 'Tak, zgłoszono przy przyjęciu';
                        else echo 'Wymaga weryfikacji w diagnozie';
                    ?>
                </td>
            </tr>
        </table>

        <h3>Zgłaszana usterka / Opis problemu</h3>
        <div class="desc-box">
            <?= nl2br(htmlspecialchars($rma['issue_description'])) ?>
        </div>

        <div class="terms">
            <h4>Krótki regulamin świadczenia usług serwisowych:</h4>
            1. Serwis dokłada wszelkich starań, aby naprawa została wykonana rzetelnie i w możliwie najkrótszym czasie. Standardowy czas diagnozy wynosi do 14 dni roboczych.<br>
            2. Serwis <strong>nie ponosi odpowiedzialności</strong> za dane zapisane na nośnikach pamięci w urządzeniu powierzonym do naprawy. Obowiązek wykonania kopii zapasowej leży po stronie Klienta.<br>
            3. W przypadku urządzeń po zalaniu cieczą lub upadku, diagnoza oraz naprawa wiąże się z ryzykiem powiększenia się usterki. Serwis nie ponosi odpowiedzialności za pogorszenie stanu urządzenia w takich przypadkach.<br>
            4. Klient wyraża zgodę na przetwarzanie danych osobowych podanych w formularzu w celu realizacji zlecenia serwisowego, zgodnie z RODO.<br>
            5. Zgłoszony sprzęt należy odebrać w ciągu <strong>30 dni</strong> od momentu poinformowania o zakończeniu naprawy. Za sprzęt nieodebrany po tym terminie, Serwis ma prawo naliczać opłatę magazynową (5 PLN za każdy dzień zwłoki), a po upływie 90 dni sprzęt może ulec złomowaniu lub przepaść na poczet kosztów magazynowania.
        </div>

        <div class="signatures">
            <div class="sig-box">
                Czytelny podpis Pracownika Serwisu
            </div>
            <div class="sig-box" style="font-weight: bold;">
                Czytelny podpis Klienta
                <div style="font-size: 9px; font-weight: normal; margin-top: 3px;">Akceptuję regulamin oraz kosztorys naprawy</div>
            </div>
        </div>

    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>