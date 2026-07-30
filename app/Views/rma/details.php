<?php require_once 'app/Views/layout/header.php'; ?>

<?php
// Weryfikacja i dobór ikonki z pakietu Bootstrap Icons do gwarancji
$warrantyIcon = 'bi-shield';
$warrantyColor = 'text-secondary';
if (!empty($rma['warranty_issued_at']) && !empty($rma['warranty_months'])) {
    $issued = new DateTime($rma['warranty_issued_at']);
    $expires = clone $issued;
    $expires->modify('+' . $rma['warranty_months'] . ' months');
    $now = new DateTime();
    
    if ($now > $expires) {
        $warrantyIcon = 'bi-shield-slash';
        $warrantyColor = 'text-danger';
    } else {
        $warrantyIcon = 'bi-shield-check';
        $warrantyColor = 'text-success';
    }
}
?>

<style>
    .pattern-container { position: relative; width: 120px; height: 120px; pointer-events: none; margin-left: auto; }
    .pattern-grid { display: grid; grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(3, 1fr); width: 100%; height: 100%; position: absolute; top: 0; left: 0; z-index: 2; }
    .pattern-node { display: flex; justify-content: center; align-items: center; font-size: 7px; font-weight: bold; color: transparent; position: relative; }
    .pattern-node::after { content: ''; position: absolute; width: 8px; height: 8px; background: var(--bs-secondary); border-radius: 50%; z-index: -1; }
    .pattern-node.active { color: #ffffff !important; }
    .pattern-node.active::after { background: var(--bs-primary); width: 16px; height: 16px; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.25); }
    .pattern-node.start-node::after { background: var(--bs-success) !important; box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.25) !important; }
    .pattern-svg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; }
    .pattern-line { fill: none; stroke: var(--bs-primary); stroke-width: 4; stroke-linecap: round; stroke-linejoin: round; }
    
    /* Pomocnicze obramowania podsumowań kosztów */
    .finance-item { text-align: center; }
    @media (min-width: 768px) {
        .finance-item.border-md-start { border-left: 2px solid var(--bs-border-color); }
    }
</style>

<div class="row justify-content-center">
    <div class="view-wrapper-lg">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4 p-lg-5">

                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center border-bottom pb-4 mb-4 gap-4">
                    
                    <div class="d-flex align-items-center gap-2 w-100 w-lg-auto">
                        <div class="btn-group shadow-sm">
                            <?php if ($adjacent['prev']): ?>
                                <a href="<?= BASE_URL ?>rma/<?= $adjacent['prev'] ?>" class="btn btn-outline-secondary px-3" title="Poprzednie"><i class="bi bi-chevron-left"></i></a>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary px-3 disabled"><i class="bi bi-chevron-left"></i></button>
                            <?php endif; ?>
                            
                            <?php if ($adjacent['next']): ?>
                                <a href="<?= BASE_URL ?>rma/<?= $adjacent['next'] ?>" class="btn btn-outline-secondary px-3" title="Następne"><i class="bi bi-chevron-right"></i></a>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary px-3 disabled"><i class="bi bi-chevron-right"></i></button>
                            <?php endif; ?>
                        </div>

                        <form method="GET" action="<?= BASE_URL ?>rma_list" class="m-0 ms-2 flex-grow-1 flex-lg-grow-0">
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" class="form-control" placeholder="ID lub nazwa..." autocomplete="off" required style="max-width: 160px;">
                            </div>
                        </form>
                    </div>

                    <div class="text-center order-first order-lg-0 w-100 w-lg-auto">
                        <h2 class="h3 mb-1 text-body fw-bold">Zgłoszenie #<?= htmlspecialchars($rma['id']) ?></h2>
                        <p class="text-muted small m-0">
                            Przyjęto: <strong class="text-body"><?= date('d.m.Y H:i', strtotime($rma['created_at'])) ?></strong> przez <strong class="text-body"><?= htmlspecialchars($rma['user_name']) ?></strong>
                        </p>
                    </div>

                    <div class="text-end w-100 w-lg-auto">
                        <a href="<?= BASE_URL ?>rma_list" onclick="if(window.history.length > 1) { window.history.back(); return false; }" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-return-left me-1"></i> Wróć
                        </a>
                    </div>
                </div>

                <div class="d-flex justify-content-center justify-content-lg-end gap-2 mb-4 flex-wrap">
                    <button type="button" id="btnToggleWarranty" class="btn btn-outline-primary d-inline-flex align-items-center">
                        <i class="bi <?= $warrantyIcon ?> me-2 fs-5"></i> Gwarancja
                    </button>
                    <a href="<?= defined('BASE_URL') ? BASE_URL . 'rma_pdf/' . $rma['id'] : BASE_URL . 'rma_pdf&id=' . $rma['id'] ?>" target="_blank" class="btn btn-success d-inline-flex align-items-center">
                        <i class="bi bi-file-earmark-pdf me-2"></i> Karta PDF
                    </a>
                    <a href="<?= BASE_URL ?>rma_notes/<?= $rma['id'] ?>" class="btn btn-primary d-inline-flex align-items-center" style="background-color: var(--bs-purple); border-color: var(--bs-purple);">
                        <i class="bi bi-journal-text me-2"></i> Notatki
                    </a>
                </div>

                <div class="row g-4 mb-4">

                    <div class="col-12 col-xl-4">
                        <div class="card h-100 bg-body-tertiary border-light-subtle shadow-sm">
                            <div class="card-body p-4 d-flex flex-column">
                                <h3 class="h6 text-muted fw-bold text-uppercase border-bottom pb-2 mb-3"><i class="bi bi-wrench-adjustable me-2"></i> Serwis</h3>
                                
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                    <span class="text-muted small fw-semibold">Aktualny Status:</span>
                                    <div class="d-flex gap-2 align-items-center w-50">
                                        <form method="POST" action="<?= BASE_URL ?>rma/<?= $rma['id'] ?>" class="m-0 w-100">
                                            <input type="hidden" name="action_type" value="update_status">
                                            <select name="new_status" class="form-select form-select-sm fw-bold border-secondary-subtle" onchange="this.form.submit()">
                                                <?php
                                                $statuses = ['Nowe', 'W diagnozie', 'Czeka na części', 'W naprawie', 'Gotowe', 'Wydane', 'Reklamacja', 'Anulowane'];
                                                foreach ($statuses as $st) {
                                                    $selected = ($rma['status'] === $st) ? 'selected' : '';
                                                    echo "<option value=\"$st\" $selected>$st</option>";
                                                }
                                                ?>
                                            </select>
                                        </form>
                                        <button id="btnOpenTimeline" class="btn btn-sm btn-outline-secondary px-2" title="Pokaż oś czasu"><i class="bi bi-clock-history"></i></button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                    <span class="text-muted small fw-semibold">Data zakończenia:</span>
                                    <span class="text-body fw-medium">
                                        <?= !empty($rma['ended_at']) ? date('d.m.Y H:i', strtotime($rma['ended_at'])) : '<span class="text-muted">---</span>' ?>
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                    <span class="text-muted small fw-semibold">Data odbioru:</span>
                                    <span class="text-success fw-bold">
                                        <?= !empty($rma['picked_up_at']) ? date('d.m.Y H:i', strtotime($rma['picked_up_at'])) : '<span class="text-muted fw-normal">---</span>' ?>
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                    <span class="text-muted small fw-semibold">Priorytet:</span>
                                    <span><?= $rma['is_express'] ? '<span class="badge bg-danger">🔥 Ekspres</span>' : '<span class="badge bg-secondary">Standard</span>' ?></span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small fw-semibold">Oddział:</span>
                                    <span class="text-body fw-medium"><?= htmlspecialchars($rma['localization_name'] ?? 'Brak') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <div class="card h-100 bg-body-tertiary border-light-subtle shadow-sm">
                            <div class="card-body p-4 d-flex flex-column">
                                <h3 class="h6 text-muted fw-bold text-uppercase border-bottom pb-2 mb-3"><i class="bi bi-person-badge me-2"></i> Klient</h3>
                                
                                <?php if ($rma['client_type'] === 'Partner'): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                        <span class="text-muted small fw-semibold">Partner (B2B):</span>
                                        <a href="<?= BASE_URL ?>partner/<?= $rma['partner_id'] ?>" class="text-body fw-bold text-decoration-none" title="Otwórz profil partnera">
                                            <?= htmlspecialchars($rma['partner_company_name']) ?> <i class="bi bi-box-arrow-up-right ms-1 small text-muted"></i>
                                        </a>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                        <span class="text-muted small fw-semibold">Telefon:</span>
                                        <span class="text-body fw-medium"><?= htmlspecialchars($rma['partner_phone']) ?></span>
                                    </div>
                                    <?php if(!empty($rma['partner_email'])): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                        <span class="text-muted small fw-semibold">E-mail:</span>
                                        <span class="text-body fw-medium"><?= htmlspecialchars($rma['partner_email']) ?></span>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (!empty($rma['client_id'])): ?>
                                        <div class="mt-3 mb-2 text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Klient końcowy</div>
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                            <span class="text-muted small fw-semibold">Imię i Nazwisko:</span>
                                            <a href="<?= BASE_URL ?>client/<?= $rma['client_id'] ?>" class="text-body fw-bold text-decoration-none" title="Otwórz profil klienta">
                                                <?= htmlspecialchars($rma['client_first_name'] . ' ' . ($rma['client_last_name'] ?? '')) ?> <i class="bi bi-box-arrow-up-right ms-1 small text-muted"></i>
                                            </a>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                            <span class="text-muted small fw-semibold">Telefon:</span>
                                            <span class="text-body fw-medium"><?= htmlspecialchars($rma['primary_phone']) ?></span>
                                        </div>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <?php if ($rma['client_type'] === 'Company'): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                            <span class="text-muted small fw-semibold">Firma:</span>
                                            <a href="<?= BASE_URL ?>client/<?= $rma['client_id'] ?>" class="text-body fw-bold text-decoration-none" title="Otwórz profil klienta">
                                                <?= htmlspecialchars($rma['client_first_name']) ?> <i class="bi bi-box-arrow-up-right ms-1 small text-muted"></i>
                                            </a>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                            <span class="text-muted small fw-semibold">NIP:</span>
                                            <span class="text-body fw-medium"><?= htmlspecialchars($rma['client_nip'] ?: 'Brak') ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                            <span class="text-muted small fw-semibold">Reprezentant:</span>
                                            <span class="text-body fw-medium"><?= htmlspecialchars($rma['client_last_name'] ?: '---') ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                            <span class="text-muted small fw-semibold">Imię:</span>
                                            <a href="<?= BASE_URL ?>client/<?= $rma['client_id'] ?>" class="text-body fw-bold text-decoration-none" title="Otwórz profil klienta">
                                                <?= htmlspecialchars($rma['client_first_name']) ?> <i class="bi bi-box-arrow-up-right ms-1 small text-muted"></i>
                                            </a>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                            <span class="text-muted small fw-semibold">Nazwisko:</span>
                                            <a href="<?= BASE_URL ?>client/<?= $rma['client_id'] ?>" class="text-body fw-bold text-decoration-none" title="Otwórz profil klienta">
                                                <?= htmlspecialchars($rma['client_last_name'] ?: '---') ?> <i class="bi bi-box-arrow-up-right ms-1 small text-muted"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                        <span class="text-muted small fw-semibold">Telefon:</span>
                                        <span class="text-body fw-medium"><?= htmlspecialchars($rma['primary_phone']) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small fw-semibold">E-mail:</span>
                                        <span class="text-body fw-medium"><?= htmlspecialchars($rma['email'] ?: 'Brak') ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <div class="card h-100 bg-body-tertiary border-light-subtle shadow-sm position-relative">
                            
                            <div class="position-absolute top-0 end-0 p-3 z-3 d-flex gap-2">
                                <button type="submit" form="device_edit_mode" class="btn btn-sm btn-success fw-bold px-3" id="btnSaveDevice" style="display: none;"><i class="bi bi-check-lg"></i> Zapisz</button>
                                <button type="button" class="btn btn-sm btn-secondary fw-bold px-3" id="btnCancelDevice" style="display: none;"><i class="bi bi-x-lg"></i> Anuluj</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnToggleDevice" title="Edytuj sprzęt"><i class="bi bi-pencil-square"></i></button>
                            </div>

                            <div class="card-body p-4 d-flex flex-column">
                                <h3 class="h6 text-muted fw-bold text-uppercase border-bottom pb-2 mb-3"><i class="bi bi-phone me-2"></i> Urządzenie</h3>

                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10 mt-2">
                                    <span class="text-muted small fw-semibold">Producent:</span>
                                    <span class="text-body fw-medium"><?= htmlspecialchars($rma['manufacturer_name'] ?? '---') ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                    <span class="text-muted small fw-semibold">Model:</span>
                                    <span class="text-body fw-bold"><?= htmlspecialchars($rma['model_name'] ?? '---') ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                    <span class="text-muted small fw-semibold">Nazwa kodowa:</span>
                                    <span class="text-body fw-medium"><?= htmlspecialchars($rma['code_name'] ?? '---') ?></span>
                                </div>

                                <div id="device_view_mode" class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                        <span class="text-muted small fw-semibold">SN / IMEI:</span>
                                        <span class="text-body fw-medium"><?= htmlspecialchars($rma['serial_number'] ?: '---') ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                        <span class="text-muted small fw-semibold">Kod blokady:</span>
                                        <?php $lockCode = htmlspecialchars($rma['device_lock_code'] ?: 'Brak / Nie podano'); ?>

                                        <?php if (strpos($lockCode, 'PATTERN:') === 0): ?>
                                            <div class="pattern-container bg-transparent">
                                                <svg class="pattern-svg"><g id="viewPatternLinesG_<?= $rma['id'] ?>"></g></svg>
                                                <div class="pattern-grid">
                                                    <?php for ($i = 1; $i <= 9; $i++): ?><div class="pattern-node view-node-<?= $i ?>"></div><?php endfor; ?>
                                                </div>
                                            </div>
                                            
                                            <script>
                                                setTimeout(() => {
                                                    const rawPattern = "<?= str_replace('PATTERN:', '', $lockCode) ?>";
                                                    const container = document.querySelector('#viewPatternLinesG_<?= $rma['id'] ?>').closest('.pattern-container');
                                                    const nodes = container.querySelectorAll('.pattern-node');
                                                    const linesG = container.querySelector('#viewPatternLinesG_<?= $rma['id'] ?>');

                                                    if (rawPattern.length === 0) return;

                                                    for (let i = 0; i < rawPattern.length; i++) {
                                                        const char = rawPattern[i];
                                                        const node = container.querySelector('.view-node-' + char);
                                                        if(node) {
                                                            node.classList.add('active');
                                                            node.textContent = i + 1;
                                                            if (i === 0) node.classList.add('start-node');
                                                        }
                                                    }

                                                    const totalSegments = rawPattern.length - 1;
                                                    const minOpacity = 0.35;

                                                    for (let i = 0; i < totalSegments; i++) {
                                                        const node1 = container.querySelector('.view-node-' + rawPattern[i]);
                                                        const node2 = container.querySelector('.view-node-' + rawPattern[i + 1]);
                                                        
                                                        if(!node1 || !node2) continue;

                                                        const rect1 = node1.getBoundingClientRect();
                                                        const rect2 = node2.getBoundingClientRect();
                                                        const gridRect = container.getBoundingClientRect();

                                                        const p1 = { x: rect1.left - gridRect.left + rect1.width / 2, y: rect1.top - gridRect.top + rect1.height / 2 };
                                                        const p2 = { x: rect2.left - gridRect.left + rect2.width / 2, y: rect2.top - gridRect.top + rect2.height / 2 };

                                                        const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                                                        line.setAttribute('x1', p1.x); line.setAttribute('y1', p1.y);
                                                        line.setAttribute('x2', p2.x); line.setAttribute('y2', p2.y);
                                                        line.classList.add('pattern-line');

                                                        const opacity = totalSegments > 1 ? minOpacity + (i / (totalSegments - 1)) * (1 - minOpacity) : 1.0;
                                                        line.style.opacity = opacity;
                                                        linesG.appendChild(line);
                                                    }
                                                }, 100);
                                            </script>
                                        <?php else: ?>
                                            <strong class="text-body"><?= $lockCode ?></strong>
                                        <?php endif; ?>
                                    </div>

                                    <?php
                                    $ldStatus = $rma['liquid_damage_status'];
                                    $ldTranslation = ['None' => '---', 'Reported_At_Intake' => 'Zgłoszono przy przyjęciu', 'Reported_Old_Unrelated' => 'Stare / Niezwiązane', 'Found_During_Diag_Relevant' => 'Wykryte (Ma wpływ)', 'Found_During_Diag_Irrelevant' => 'Wykryte (Bez wpływu)'];
                                    $ldText = $ldTranslation[$ldStatus] ?? $ldStatus;
                                    ?>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small fw-semibold">Zalanie cieczą:</span>
                                        <span>
                                            <?= ($ldStatus !== 'None') ? '<span class="badge bg-danger">💧 ' . htmlspecialchars($ldText) . '</span>' : '<span class="text-muted">Brak</span>' ?>
                                        </span>
                                    </div>
                                </div>

                                <form id="device_edit_mode" method="POST" action="<?= BASE_URL ?>rma/<?= $rma['id'] ?>" class="m-0 bg-body p-3 rounded border border-secondary-subtle" style="display: none;">
                                    <input type="hidden" name="action_type" value="update_device">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">SN / IMEI</label>
                                        <input type="text" class="form-control form-control-sm" name="serial_number" value="<?= htmlspecialchars($rma['serial_number'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Kod blokady</label>
                                        <input type="text" class="form-control form-control-sm" name="device_lock_code" value="<?= htmlspecialchars($rma['device_lock_code'] ?? '') ?>">
                                    </div>
                                    
                                    <div class="mb-2 p-2 border border-warning rounded bg-warning-subtle">
                                        <div class="form-check form-switch mb-1">
                                            <input class="form-check-input" type="checkbox" role="switch" id="edit_is_liquid" name="is_liquid_damage" value="1" <?= $ldStatus !== 'None' ? 'checked' : '' ?>>
                                            <label class="form-check-label text-warning-emphasis fw-bold small" for="edit_is_liquid">💧 Sprzęt po zalaniu</label>
                                        </div>
                                        <select name="liquid_damage_status" id="edit_liquid_status" class="form-select form-select-sm mt-2 border-warning-subtle" style="<?= $ldStatus === 'None' ? 'display: none;' : '' ?>">
                                            <option value="None" hidden <?= $ldStatus === 'None' ? 'selected' : '' ?>>Brak (wyczyszczenie statusu)</option>
                                            <option value="Reported_At_Intake" <?= $ldStatus === 'Reported_At_Intake' ? 'selected' : '' ?>>Zgłoszono przy przyjęciu</option>
                                            <option value="Reported_Old_Unrelated" <?= $ldStatus === 'Reported_Old_Unrelated' ? 'selected' : '' ?>>Stare / Niezwiązane</option>
                                            <option value="Found_During_Diag_Relevant" <?= $ldStatus === 'Found_During_Diag_Relevant' ? 'selected' : '' ?>>Wykryte (Ma wpływ)</option>
                                            <option value="Found_During_Diag_Irrelevant" <?= $ldStatus === 'Found_During_Diag_Irrelevant' ? 'selected' : '' ?>>Wykryte (Bez wpływu)</option>
                                        </select>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

                </div>

                <div id="lower_section">
                    
                    <div class="card bg-body-tertiary border-light-subtle shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h3 class="h6 text-muted fw-bold text-uppercase border-bottom pb-2 mb-3"><i class="bi bi-chat-square-text me-2 text-primary"></i> Zgłaszana usterka</h3>
                            <div class="fs-6 text-body" style="white-space: pre-wrap;"><?= htmlspecialchars($rma['issue_description']) ?></div>
                        </div>
                    </div>

                    <div class="card bg-body-tertiary border-light-subtle shadow-sm position-relative">
                        <div class="position-absolute top-0 end-0 p-3 z-3 d-flex gap-2">
                            <button type="submit" form="costs_edit_mode" id="btnSavePayment" class="btn btn-sm btn-success fw-bold px-3" style="display: none;"><i class="bi bi-check-lg"></i> Zapisz</button>
                            <button type="button" id="btnCancelPayment" class="btn btn-sm btn-secondary fw-bold px-3" style="display: none;"><i class="bi bi-x-lg"></i> Anuluj</button>
                            <button type="button" id="btnTogglePayment" class="btn btn-sm btn-outline-secondary" title="Edytuj finanse"><i class="bi bi-pencil-square"></i></button>
                        </div>

                        <div class="card-body p-4">
                            <h3 class="h6 text-muted fw-bold text-uppercase border-bottom pb-2 mb-3"><i class="bi bi-cash-coin me-2 text-success"></i> Rozliczenie finansowe</h3>

                            <div id="costs_view_mode">
                                <div class="row g-4 justify-content-center align-items-center">
                                    <div class="col-6 col-md-4 col-lg-2 finance-item">
                                        <div class="small text-muted fw-semibold mb-1">Szacowany koszt</div>
                                        <div class="fs-5 fw-bold text-body"><?= !empty((float)$rma['estimated_cost']) ? number_format($rma['estimated_cost'], 2) . ' zł' : '---' ?></div>
                                    </div>
                                    <div class="col-6 col-md-4 col-lg-2 finance-item border-md-start">
                                        <div class="small text-muted fw-semibold mb-1">Max budżet</div>
                                        <div class="fs-5 fw-bold text-body"><?= !empty((float)$rma['max_approved_cost']) ? number_format($rma['max_approved_cost'], 2) . ' zł' : '---' ?></div>
                                    </div>
                                    <div class="col-6 col-md-4 col-lg-2 finance-item border-md-start">
                                        <div class="small text-muted fw-semibold mb-1">Wp. Zaliczka</div>
                                        <div class="fs-5 fw-bold text-body"><?= !empty((float)$rma['advance_payment']) ? number_format($rma['advance_payment'], 2) . ' zł' : '---' ?></div>
                                    </div>
                                    <div class="col-6 col-md-4 col-lg-2 finance-item border-md-start">
                                        <div class="small text-muted fw-semibold mb-1">Koszt części</div>
                                        <div class="fs-5 fw-bold text-body"><?= !empty((float)$rma['parts_cost']) ? number_format($rma['parts_cost'], 2) . ' zł' : '---' ?></div>
                                    </div>
                                    <div class="col-6 col-md-4 col-lg-2 finance-item border-md-start">
                                        <div class="small text-muted fw-semibold mb-1">Koszta własne</div>
                                        <div class="fs-5 fw-bold text-body"><?= !empty((float)$rma['internal_cost']) ? number_format($rma['internal_cost'], 2) . ' zł' : '---' ?></div>
                                    </div>
                                    <div class="col-6 col-md-4 col-lg-2 finance-item border-md-start">
                                        <div class="small text-muted fw-bold text-uppercase mb-1">Cena końcowa</div>
                                        <div class="fs-4 fw-bold text-body"><?= !empty((float)$rma['final_cost']) ? number_format($rma['final_cost'], 2) . ' zł' : '---' ?></div>
                                    </div>
                                </div>
                                
                                <?php
                                $paymentPl = ['Cash' => 'Gotówka', 'Card' => 'Karta', 'Blik' => 'BLIK'];
                                $hasPayment = !empty($rma['payment_method']);
                                ?>
                                <div class="mt-4 pt-3 border-top">
                                    <div class="w-100 text-center p-2 rounded <?= $hasPayment ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle' ?> fw-medium">
                                        <?= $hasPayment ? '<i class="bi bi-check-circle-fill me-1"></i> Zlecenie opłacone (<strong>' . $paymentPl[$rma['payment_method']] . '</strong>)' : '<i class="bi bi-dash-circle me-1"></i> Zlecenie nieopłacone' ?>
                                    </div>
                                </div>
                            </div>

                            <form id="costs_edit_mode" method="POST" action="<?= BASE_URL ?>rma/<?= $rma['id'] ?>" class="m-0 bg-body p-4 rounded border border-secondary-subtle" style="display: none;">
                                <input type="hidden" name="action_type" value="update_costs">

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-muted">Szacowany koszt</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" class="form-control" step="0.01" name="estimated_cost" value="<?= !empty((float)$rma['estimated_cost']) ? htmlspecialchars($rma['estimated_cost']) : '' ?>">
                                            <span class="input-group-text">PLN</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-muted">Max budżet</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" class="form-control" step="0.01" name="max_approved_cost" value="<?= !empty((float)$rma['max_approved_cost']) ? htmlspecialchars($rma['max_approved_cost']) : '' ?>">
                                            <span class="input-group-text">PLN</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-muted">Zaliczka</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" class="form-control" step="0.01" name="advance_payment" value="<?= !empty((float)$rma['advance_payment']) ? htmlspecialchars($rma['advance_payment']) : '' ?>">
                                            <span class="input-group-text">PLN</span>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12"><hr class="my-1 border-secondary-subtle"></div>
                                    
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold text-muted">Koszt części</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" class="form-control" step="0.01" name="parts_cost" value="<?= !empty((float)$rma['parts_cost']) ? htmlspecialchars($rma['parts_cost']) : '' ?>">
                                            <span class="input-group-text">PLN</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold text-muted">Koszta własne</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" class="form-control" step="0.01" name="internal_cost" value="<?= !empty((float)$rma['internal_cost']) ? htmlspecialchars($rma['internal_cost']) : '' ?>">
                                            <span class="input-group-text">PLN</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-body">Cena końcowa</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" class="form-control fw-bold" step="0.01" name="final_cost" value="<?= !empty((float)$rma['final_cost']) ? htmlspecialchars($rma['final_cost']) : '' ?>">
                                            <span class="input-group-text">PLN</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-primary">Forma płatności</label>
                                        <select name="payment_method" class="form-select form-select-sm border-primary">
                                            <option value="" <?= empty($rma['payment_method']) ? 'selected' : '' ?>>Brak (Nieopłacono)</option>
                                            <option value="Cash" <?= $rma['payment_method'] === 'Cash' ? 'selected' : '' ?>>Gotówka</option>
                                            <option value="Card" <?= $rma['payment_method'] === 'Card' ? 'selected' : '' ?>>Karta</option>
                                            <option value="Blik" <?= $rma['payment_method'] === 'Blik' ? 'selected' : '' ?>>BLIK</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div id="warranty_section" class="card bg-body-tertiary border-primary mt-4 shadow" style="display: none;">
                    <div class="card-body p-4 p-lg-5">
                        <h4 class="h4 mb-4 text-body fw-bold border-bottom border-primary-subtle pb-3"><i class="bi bi-shield-check me-2 text-primary"></i> Zarządzanie Gwarancją</h4>

                        <?php
                        $hasWarranty = !empty($rma['warranty_issued_at']) && !empty($rma['warranty_months']);
                        $isExpired = false;
                        if ($hasWarranty) {
                            $issued = new DateTime($rma['warranty_issued_at']);
                            $expires = clone $issued;
                            $expires->modify('+' . $rma['warranty_months'] . ' months');
                            $now = new DateTime();
                            $diff = $now->diff($expires);
                            $isExpired = $now > $expires;
                        }
                        ?>

                        <div class="text-center bg-body p-4 rounded border mb-4">
                            <?php if ($hasWarranty): ?>
                                <?php
                                    $totalMonthsLeft = ($diff->y * 12) + $diff->m;
                                    $daysLeft = $diff->d;
                                    $timeString = $totalMonthsLeft > 0 ? "{$totalMonthsLeft} mies. i {$daysLeft} dni" : "{$daysLeft} dni";
                                ?>
                                <h5 class="fw-bold mb-2">Status: <span class="<?= $isExpired ? 'text-danger' : 'text-success' ?>"><?= $isExpired ? 'Wygasła' : 'Aktywna' ?></span></h5>
                                <p class="m-0 text-muted fs-5">
                                    <?= $isExpired ? 'Okres ochronny minął.' : 'Pozostało: <strong class="text-body">' . $timeString . '</strong> (Ważna do ' . $expires->format('d.m.Y') . ')' ?>
                                </p>
                                <p class="m-0 text-muted mt-2 small">Dokument wydano: <?= $issued->format('d.m.Y H:i') ?></p>
                            <?php else: ?>
                                <h5 class="fw-bold mb-0 text-muted"><i class="bi bi-info-circle me-2"></i> Brak wydanej gwarancji do tego zlecenia</h5>
                            <?php endif; ?>
                        </div>

                        <form id="warranty_form" method="POST" action="<?= BASE_URL ?>warranty_pdf/<?= $rma['id'] ?>" target="_blank" class="m-0">
                            <input type="hidden" name="action_type" value="generate_warranty">

                            <?php $hasDetailedWarranty = !empty($rma['warranty_scope']) || !empty($rma['warranty_covered']); ?>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-body">Okres gwarancji (w miesiącach)</label>
                                    <div class="input-group input-group-lg">
                                        <input type="number" class="form-control" name="warranty_months" value="<?= htmlspecialchars($rma['warranty_months'] ?? 3) ?>" min="1" <?= $hasWarranty ? 'readonly style="background-color: var(--bs-secondary-bg);"' : 'required' ?>>
                                        <span class="input-group-text">Mies.</span>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 d-flex align-items-center">
                                    <div class="form-check form-switch mt-md-4 pt-md-2 ms-md-4">
                                        <input class="form-check-input fs-4" type="checkbox" role="switch" id="use_detailed_warranty" name="use_detailed_warranty" value="1" <?= $hasDetailedWarranty ? 'checked' : '' ?> <?= $hasWarranty ? 'disabled' : '' ?>>
                                        <label class="form-check-label fs-5 ms-2 fw-semibold <?= $hasWarranty ? 'text-muted' : 'text-primary' ?>" for="use_detailed_warranty">
                                            Szczegółowa karta gwarancyjna
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12" id="custom_warranty_fields" style="display: <?= $hasDetailedWarranty ? 'block' : 'none' ?>;">
                                    <div class="row g-3 mt-2 bg-body p-3 rounded border border-primary-subtle">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-muted">Zakres wykonanej naprawy:</label>
                                            <textarea class="form-control" name="warranty_scope" rows="3" placeholder="np. Wymiana wyświetlacza i czyszczenie płyty głównej..." <?= $hasWarranty ? 'readonly style="background-color: var(--bs-secondary-bg);"' : '' ?>><?= htmlspecialchars($rma['warranty_scope'] ?? '') ?></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-muted">Części objęte ochroną:</label>
                                            <textarea class="form-control" name="warranty_covered" rows="3" placeholder="np. Wyłącznie nowy moduł ekranowy LCD..." <?= $hasWarranty ? 'readonly style="background-color: var(--bs-secondary-bg);"' : '' ?>><?= htmlspecialchars($rma['warranty_covered'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 text-center">
                                <?php if ($hasWarranty): ?>
                                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                                        <a href="<?= BASE_URL ?>warranty_pdf/<?= $rma['id'] ?>" target="_blank" class="btn btn-lg btn-success fw-bold px-5"><i class="bi bi-printer me-2"></i> Drukuj ponownie</a>
                                        <button type="button" class="btn btn-lg btn-outline-danger fw-bold px-4" onclick="if(confirm('Czy na pewno chcesz permanentnie usunąć tę gwarancję z systemu?')) { document.getElementById('delete_warranty_form').submit(); }"><i class="bi bi-trash me-2"></i> Usuń gwarancję</button>
                                    </div>
                                <?php else: ?>
                                    <button type="submit" class="btn btn-lg btn-primary fw-bold px-5"><i class="bi bi-shield-plus me-2"></i> Generuj nową kartę gwarancyjną</button>
                                <?php endif; ?>
                            </div>
                        </form>

                        <?php if ($hasWarranty): ?>
                            <form id="delete_warranty_form" method="POST" action="<?= BASE_URL ?>rma/<?= $rma['id'] ?>" style="display: none;">
                                <input type="hidden" name="action_type" value="delete_warranty">
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Modal Osi Czasu ---
        const modal = document.getElementById('timelineModal');
        const btnOpenTimeline = document.getElementById('btnOpenTimeline');
        const btnCloseTimeline = document.getElementById('btnCloseTimeline');

        if (btnOpenTimeline) {
            btnOpenTimeline.addEventListener('click', (e) => {
                e.preventDefault();
                if(modal) modal.style.display = 'flex';
            });
        }
        if (btnCloseTimeline) {
            btnCloseTimeline.addEventListener('click', () => {
                if(modal) modal.style.display = 'none';
            });
        }

        // --- Edycja Urządzenia ---
        const viewDevice = document.getElementById('device_view_mode');
        const editDevice = document.getElementById('device_edit_mode');
        const btnToggleDevice = document.getElementById('btnToggleDevice');
        const btnSaveDevice = document.getElementById('btnSaveDevice');
        const btnCancelDevice = document.getElementById('btnCancelDevice');
        
        if (btnToggleDevice) {
            btnToggleDevice.addEventListener('click', function () {
                viewDevice.style.display = 'none';
                editDevice.style.display = 'block';
                if (btnSaveDevice) btnSaveDevice.style.display = 'block';
                if (btnCancelDevice) btnCancelDevice.style.display = 'block';
                this.style.display = 'none';
            });
        }
        
        if (btnCancelDevice) {
            btnCancelDevice.addEventListener('click', function () {
                editDevice.style.display = 'none';
                viewDevice.style.display = 'flex';
                viewDevice.style.flexDirection = 'column';
                if (btnSaveDevice) btnSaveDevice.style.display = 'none';
                if (btnToggleDevice) btnToggleDevice.style.display = 'inline-flex';
                this.style.display = 'none';
            });
        }

        // Obsługa zalania
        const liqCheckbox = document.getElementById('edit_is_liquid');
        const liqSelect = document.getElementById('edit_liquid_status');
        if (liqCheckbox) {
            liqCheckbox.addEventListener('change', function () {
                if (liqSelect) {
                    liqSelect.style.display = this.checked ? 'block' : 'none';
                    if(!this.checked) liqSelect.value = 'None';
                }
            });
        }

        // --- Edycja Kosztów i Płatności ---
        const viewCosts = document.getElementById('costs_view_mode');
        const editCosts = document.getElementById('costs_edit_mode');
        const btnTogglePayment = document.getElementById('btnTogglePayment');
        const btnCancelPayment = document.getElementById('btnCancelPayment');
        const btnSavePayment = document.getElementById('btnSavePayment');

        if (btnTogglePayment) {
            btnTogglePayment.addEventListener('click', () => {
                viewCosts.style.display = 'none';
                editCosts.style.display = 'block';
                btnTogglePayment.style.display = 'none';
                if (btnCancelPayment) btnCancelPayment.style.display = 'block';
                if (btnSavePayment) btnSavePayment.style.display = 'block';
            });
        }

        if (btnCancelPayment) {
            btnCancelPayment.addEventListener('click', () => {
                editCosts.style.display = 'none';
                viewCosts.style.display = 'block';
                btnTogglePayment.style.display = 'inline-flex';
                btnCancelPayment.style.display = 'none';
                if (btnSavePayment) btnSavePayment.style.display = 'none';
            });
        }

        // --- Gwarancja ---
        const btnToggleWarranty = document.getElementById('btnToggleWarranty');
        const lowerSection = document.getElementById('lower_section');
        const warrantySection = document.getElementById('warranty_section');
        const cbDetailedWarranty = document.getElementById('use_detailed_warranty'); 
        const customWarrantyFields = document.getElementById('custom_warranty_fields');
        const warrantyForm = document.getElementById('warranty_form');

        if (btnToggleWarranty) {
            btnToggleWarranty.addEventListener('click', function (e) {
                e.preventDefault();
                if (warrantySection.style.display === 'none' || warrantySection.style.display === '') {
                    lowerSection.style.display = 'none';
                    warrantySection.style.display = 'block';
                    btnToggleWarranty.classList.add('active');
                } else {
                    lowerSection.style.display = 'block';
                    warrantySection.style.display = 'none';
                    btnToggleWarranty.classList.remove('active');
                }
            });
        }

        if (cbDetailedWarranty) {
            cbDetailedWarranty.addEventListener('change', function () {
                if (customWarrantyFields) {
                    customWarrantyFields.style.display = this.checked ? 'block' : 'none';
                    if (!this.checked) {
                        const scopeInput = document.querySelector('[name="warranty_scope"]');
                        const coveredInput = document.querySelector('[name="warranty_covered"]');
                        if (scopeInput) scopeInput.value = '';
                        if (coveredInput) coveredInput.value = '';
                    }
                }
            });
        }

        if (warrantyForm) {
            warrantyForm.addEventListener('submit', () => {
                setTimeout(() => { window.location.reload(); }, 1500);
            });
        }
    });
</script>
<?php require_once 'app/Views/layout/footer.php'; ?>