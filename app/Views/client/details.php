<?php require_once 'app/Views/layout/header.php'; ?>

<?php
// Pobranie istniejących relacji dla skryptu JS
$myInitiatedRelations = [];
if (!empty($relatedClients)) {
    foreach ($relatedClients as $rc) {
        if (!empty($rc['initiated_by_me'])) {
            $myInitiatedRelations[] = [
                'id' => $rc['id'],
                'relation' => $rc['relation_type']
            ];
        }
    }
}
$existingRelationsJson = json_encode($myInitiatedRelations);
?>

<div class="row justify-content-center">
    <div class="view-wrapper-lg">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4 p-lg-5">

                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom flex-wrap gap-3">
                    <h2 class="h3 mb-0 text-body d-flex align-items-center">
                        <i class="bi bi-person-vcard me-2 text-primary"></i> Profil Klienta
                        #<?= htmlspecialchars($client['id']) ?>
                    </h2>
                    <a href="<?= BASE_URL ?>client_list"
                        onclick="if(window.history.length > 1) { window.history.back(); return false; }"
                        class="btn btn-outline-secondary d-inline-flex align-items-center fw-semibold">
                        <i class="bi bi-arrow-left me-2"></i> Wróć
                    </a>
                </div>

                <div class="row g-4">

                    <div class="col-lg-4 d-flex flex-column gap-4">

                        <div class="card bg-body-tertiary border-light-subtle shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex flex-column gap-2">
                                    <div
                                        class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10">
                                        <span class="text-muted small fw-semibold">Imię / Nazwa:</span>
                                        <strong
                                            class="text-body"><?= htmlspecialchars($client['first_name']) ?></strong>
                                    </div>
                                    <?php if (!empty($client['last_name'])): ?>
                                        <div
                                            class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10">
                                            <span class="text-muted small fw-semibold">Nazwisko:</span>
                                            <strong class="text-body"><?= htmlspecialchars($client['last_name']) ?></strong>
                                        </div>
                                    <?php endif; ?>

                                    <div
                                        class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10">
                                        <span class="text-muted small fw-semibold">Typ klienta:</span>
                                        <span class="text-body fw-semibold">
                                            <?php
                                            if (($client['client_type'] ?? '') === 'Company') {
                                                echo '<i class="bi bi-building me-1 text-muted"></i> Firma';
                                            } else {
                                                echo '<i class="bi bi-person me-1 text-muted"></i> Osoba fizyczna';
                                            }
                                            ?>
                                        </span>
                                    </div>

                                    <div
                                        class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10">
                                        <span class="text-muted small fw-semibold">Telefon główny:</span>
                                        <strong
                                            class="text-body"><?= htmlspecialchars($client['primary_phone']) ?></strong>
                                    </div>

                                    <?php
                                    if (!empty($client['additional_phones'])):
                                        $extraPhones = json_decode($client['additional_phones'], true);
                                        if (is_array($extraPhones)):
                                            foreach ($extraPhones as $index => $phoneObj):
                                                if (is_array($phoneObj) && isset($phoneObj['number'])) {
                                                    $displayStr = $phoneObj['number'] . (!empty($phoneObj['description']) ? ' <span class="text-muted small">(' . $phoneObj['description'] . ')</span>' : '');
                                                } else {
                                                    $displayStr = is_array($phoneObj) ? implode(', ', $phoneObj) : $phoneObj;
                                                }
                                                ?>
                                                <div
                                                    class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10">
                                                    <span class="text-muted small fw-semibold">Tel. <?= $index + 2 ?>:</span>
                                                    <span class="text-body"><?= $displayStr ?></span>
                                                </div>
                                            <?php endforeach;
                                        endif;
                                    endif; ?>

                                    <div
                                        class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10">
                                        <span class="text-muted small fw-semibold">Adres E-mail:</span>
                                        <span>
                                            <?php if (!empty($client['email'])): ?>
                                                <a href="mailto:<?= htmlspecialchars($client['email']) ?>"
                                                    class="text-decoration-none text-primary fw-medium">
                                                    <?= htmlspecialchars($client['email']) ?> <i
                                                        class="bi bi-envelope ms-1"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">---</span>
                                            <?php endif; ?>
                                        </span>
                                    </div>

                                    <div
                                        class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10">
                                        <span class="text-muted small fw-semibold">Kontakt:</span>
                                        <span
                                            class="text-body"><?= htmlspecialchars($client['preferred_contact'] ?: '---') ?></span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="text-muted small fw-semibold">Wydane:</span>
                                        <strong
                                            class="text-success fs-6"><?= number_format($client['total_spent'] ?? 0, 2) ?>
                                            zł</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small fw-semibold">Liczba RMA:</span>
                                        <strong class="text-body"><?= (int) ($client['rma_count'] ?? 0) ?> szt.</strong>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-top">
                                    <button type="button" class="btn btn-outline-secondary w-100 mb-2 fw-semibold"
                                        data-bs-toggle="modal" data-bs-target="#editClientModal">
                                        <i class="bi bi-pencil-square me-2"></i> Edytuj dane klienta
                                    </button>

                                    <form method="POST" action="<?= BASE_URL ?>client/<?= $client['id'] ?>" class="m-0">
                                        <input type="hidden" name="action_type" value="recalculate_stats">
                                        <button type="submit" class="btn w-100 fw-semibold text-white"
                                            style="background-color: var(--bs-purple); border-color: var(--bs-purple);">
                                            <i class="bi bi-arrow-repeat me-2"></i> Przelicz statystyki
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card border-light-subtle shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="h6 m-0 text-body fw-bold"><i
                                            class="bi bi-people-fill me-2 text-primary"></i> Powiązane konta</h4>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal" data-bs-target="#relationsModal">
                                        <i class="bi bi-gear me-1"></i> Zarządzaj
                                    </button>
                                </div>

                                <?php if (empty($relatedClients)): ?>
                                    <p class="text-muted small m-0 text-center py-2">Brak powiązanych kont w bazie.</p>
                                <?php else: ?>
                                    <div class="d-flex flex-column gap-2">
                                        <?php foreach ($relatedClients as $rel): ?>
                                            <div class="p-2 border rounded bg-body-tertiary">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <strong
                                                        class="text-body small"><?= htmlspecialchars($rel['first_name'] . ' ' . $rel['last_name']) ?></strong>
                                                    <a href="<?= BASE_URL ?>client/<?= $rel['id'] ?>"
                                                        class="text-decoration-none text-primary small fw-bold">
                                                        #<?= $rel['id'] ?> <i class="bi bi-box-arrow-up-right ms-1"
                                                            style="font-size: 0.75rem;"></i>
                                                    </a>
                                                </div>
                                                <?php if (!empty($rel['initiated_by_me'])): ?>
                                                    <span
                                                        class="badge bg-primary-subtle text-primary border border-primary-subtle fw-medium d-inline-block">
                                                        Zainicjowano: <?= htmlspecialchars($rel['relation_type']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span
                                                        class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle fw-medium d-inline-block">
                                                        Połączono z #<?= $rel['id'] ?>:
                                                        <?= htmlspecialchars($rel['relation_type']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 d-flex flex-column gap-4">

                        <div>
                            <h4 class="h5 mb-3 text-body fw-bold border-bottom pb-2 d-flex align-items-center">
                                <i class="bi bi-journal-lock me-2 text-warning"></i> Notatki wewnętrzne serwisu
                            </h4>
                            <form method="POST" action="<?= BASE_URL ?>client/<?= $client['id'] ?>"
                                class="m-0 bg-body-tertiary p-3 rounded border border-light-subtle shadow-sm">
                                <input type="hidden" name="action_type" value="update_note">
                                <div class="mb-3">
                                    <textarea name="internal_note" rows="3" class="form-control"
                                        placeholder="Wpisz notatkę o kliencie (widoczną tylko dla pracowników)..."><?= htmlspecialchars($client['internal_note'] ?? '') ?></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-4 fw-semibold"><i
                                            class="bi bi-save me-2"></i> Zapisz notatkę</button>
                                </div>
                            </form>
                        </div>

                        <div>
                            <h4 class="h5 mb-3 text-body fw-bold border-bottom pb-2 mt-2 d-flex align-items-center">
                                <i class="bi bi-clock-history me-2 text-primary"></i> Historia zgłoszeń serwisowych
                            </h4>

                            <div class="table-responsive border rounded bg-body shadow-sm">
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead
                                        class="text-muted small text-uppercase align-middle user-select-none">
                                        <tr>
                                            <th class="ps-3 text-nowrap">RMA</th>
                                            <th class="text-nowrap">Data przyjęcia</th>
                                            <th class="text-nowrap">Data wydania</th>
                                            <th class="text-nowrap">Urządzenie</th>
                                            <th class="text-nowrap">Status</th>
                                            <th class="text-nowrap">Koszt</th>
                                            <th class="text-end pe-3 text-nowrap">Akcje</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php if (empty($rmaHistory)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-5">
                                                    <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i> Ten klient nie
                                                    posiada jeszcze historii zgłoszeń.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($rmaHistory as $history): ?>
                                                <tr>
                                                    <td class="px-3"><strong
                                                            class="text-primary">#<?= htmlspecialchars($history['id']) ?></strong>
                                                    </td>
                                                    <td class="small text-body">
                                                        <?= date('d.m.Y H:i', strtotime($history['created_at'])) ?></td>
                                                    <td class="small text-muted">
                                                        <?php
                                                        if (!empty($history['picked_up_at']) && $history['picked_up_at'] !== '0000-00-00 00:00:00') {
                                                            echo date('d.m.Y H:i', strtotime($history['picked_up_at']));
                                                        } else {
                                                            echo '---';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="small">
                                                        <div class="text-muted" style="font-size: 0.75rem;">
                                                            <?= htmlspecialchars($history['type_name'] ?? 'Inne') ?></div>
                                                        <strong
                                                            class="text-body"><?= htmlspecialchars(($history['manufacturer_name'] ?? '') . ' ' . ($history['model_name'] ?? '')) ?></strong>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge border border-secondary-subtle text-secondary-emphasis bg-secondary-subtle px-2 py-1 fw-medium">
                                                            <?= htmlspecialchars($history['status']) ?>
                                                        </span>
                                                    </td>

                                                    <td class="fw-bold text-success">
                                                        <?= isset($history['final_cost']) && $history['final_cost'] !== null && $history['final_cost'] !== '' ? number_format($history['final_cost'], 2) . ' zł' : '<span class="text-muted fw-normal">---</span>' ?>
                                                    </td>

                                                    <td class="text-end px-3">
                                                        <a href="<?= BASE_URL ?>rma/<?= $history['id'] ?>"
                                                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                                                            <i class="bi bi-folder2-open me-1"></i> Otwórz
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="editClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="editClientModalLabel"><i
                        class="bi bi-person-gear me-2 text-primary"></i> Edycja profilu klienta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= BASE_URL ?>client/<?= $client['id'] ?>">
                    <input type="hidden" name="action_type" value="edit_client_core">

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Imię / Nazwa firmy</label>
                        <input type="text" name="first_name" class="form-control"
                            value="<?= htmlspecialchars($client['first_name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nazwisko <span
                                class="fw-normal">(Pozostaw puste dla firm)</span></label>
                        <input type="text" name="last_name" class="form-control"
                            value="<?= htmlspecialchars($client['last_name'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Główny numer telefonu</label>
                        <input type="text" name="primary_phone" class="form-control"
                            value="<?= htmlspecialchars($client['primary_phone']) ?>" required>
                    </div>

                    <div class="mb-3 p-3 bg-body-tertiary rounded border border-light-subtle">
                        <label class="form-label small fw-semibold text-muted mb-2">Dodatkowe numery telefonów</label>
                        <div id="dynamicPhonesContainer"></div>
                        <button type="button" id="btnAddPhoneField" class="btn btn-sm btn-outline-secondary w-100 mt-1">
                            <i class="bi bi-plus-lg me-1"></i> Dodaj kolejny numer
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Adres E-mail</label>
                        <input type="email" name="email" class="form-control"
                            value="<?= htmlspecialchars($client['email'] ?? '') ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-muted">Preferowany kontakt</label>
                        <select name="preferred_contact" class="form-select">
                            <option value="" <?= empty($client['preferred_contact']) ? 'selected' : '' ?>>Nie wybrano /
                                Brak</option>
                            <option value="Phone" <?= ($client['preferred_contact'] === 'Phone') ? 'selected' : '' ?>>
                                Telefon</option>
                            <option value="SMS" <?= ($client['preferred_contact'] === 'SMS') ? 'selected' : '' ?>>Wiadomość
                                SMS</option>
                            <option value="Email" <?= ($client['preferred_contact'] === 'Email') ? 'selected' : '' ?>>
                                E-mail</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-semibold"><i class="bi bi-save me-2"></i>
                        Zapisz zmiany danych</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="relationsModal" tabindex="-1" aria-labelledby="relationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="relationsModalLabel"><i
                        class="bi bi-diagram-3 me-2 text-primary"></i> Powiązane konta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= BASE_URL ?>client/<?= $client['id'] ?>">
                    <input type="hidden" name="action_type" value="edit_relations">

                    <div class="alert alert-info border-info-subtle small py-2 mb-3 d-flex align-items-center">
                        <i class="bi bi-info-circle-fill fs-5 me-2"></i>
                        <div>Edytujesz powiązania zainicjowane przez ten profil. (Relacje odwrotne musisz usunąć na
                            kontach docelowych).</div>
                    </div>

                    <div id="relationsContainer" class="mb-3"></div>

                    <button type="button" id="btnAddRelationRow"
                        class="btn btn-outline-secondary w-100 mb-4 border-dashed">
                        <i class="bi bi-plus-circle me-2"></i> Dodaj powiązanie po ID klienta
                    </button>

                    <button type="submit" class="btn btn-primary w-100 fw-semibold"><i class="bi bi-diagram-2 me-2"></i>
                        Zapisz układ powiązań</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        // =====================================
        // 1. DYNAMICZNE NUMERY TELEFONÓW
        // =====================================
        const containerPhones = document.getElementById('dynamicPhonesContainer');
        const btnAddPhone = document.getElementById('btnAddPhoneField');
        const existingPhones = <?= !empty($client['additional_phones']) ? $client['additional_phones'] : '[]' ?>;

        function createPhoneRow(numberStr = '', descStr = '') {
            const row = document.createElement('div');
            row.className = 'd-flex gap-2 mb-2 align-items-center';

            const inputNumber = document.createElement('input');
            inputNumber.type = 'text';
            inputNumber.name = 'additional_phones_number[]';
            inputNumber.placeholder = 'Numer, np. 500 100 200';
            inputNumber.value = numberStr;
            inputNumber.className = 'form-control form-control-sm';

            const inputDesc = document.createElement('input');
            inputDesc.type = 'text';
            inputDesc.name = 'additional_phones_desc[]';
            inputDesc.placeholder = 'Opis (np. Do córki)';
            inputDesc.value = descStr;
            inputDesc.className = 'form-control form-control-sm';

            const btnRemove = document.createElement('button');
            btnRemove.type = 'button';
            btnRemove.innerHTML = '<i class="bi bi-trash"></i>';
            btnRemove.title = 'Usuń numer';
            btnRemove.className = 'btn btn-sm btn-outline-danger flex-shrink-0';

            btnRemove.addEventListener('click', () => row.remove());
            row.appendChild(inputNumber);
            row.appendChild(inputDesc);
            row.appendChild(btnRemove);
            containerPhones.appendChild(row);
        }

        if (Array.isArray(existingPhones)) {
            existingPhones.forEach(p => {
                if (typeof p === 'object' && p !== null) {
                    createPhoneRow(p.number || '', p.description || '');
                } else {
                    createPhoneRow(p, '');
                }
            });
        }
        btnAddPhone?.addEventListener('click', () => createPhoneRow());


        // =====================================
        // 2. DYNAMICZNE RELACJE (POWIĄZANIA)
        // =====================================
        const containerRel = document.getElementById('relationsContainer');
        const btnAddRowRel = document.getElementById('btnAddRelationRow');
        const existingRelations = <?= $existingRelationsJson ?>;

        function createRelationRow(id = '', relation = '') {
            const row = document.createElement('div');
            row.className = 'd-flex gap-2 mb-2 align-items-center bg-body p-2 border rounded';

            const inputId = document.createElement('input');
            inputId.type = 'number';
            inputId.name = 'related_id[]';
            inputId.value = id;
            inputId.placeholder = 'ID (np. 15)';
            inputId.required = true;
            inputId.className = 'form-control form-control-sm';
            inputId.style.width = '100px';

            const inputRelation = document.createElement('input');
            inputRelation.type = 'text';
            inputRelation.name = 'related_name[]';
            inputRelation.value = relation;
            inputRelation.placeholder = 'Relacja (np. Córka)';
            inputRelation.required = true;
            inputRelation.className = 'form-control form-control-sm';

            const btnRemove = document.createElement('button');
            btnRemove.type = 'button';
            btnRemove.innerHTML = '<i class="bi bi-trash"></i>';
            btnRemove.className = 'btn btn-sm btn-outline-danger flex-shrink-0';
            btnRemove.addEventListener('click', () => row.remove());

            row.appendChild(inputId);
            row.appendChild(inputRelation);
            row.appendChild(btnRemove);
            containerRel.appendChild(row);
        }

        if (Array.isArray(existingRelations)) {
            existingRelations.forEach(rel => createRelationRow(rel.id, rel.relation));
        }

        btnAddRowRel?.addEventListener('click', () => createRelationRow());

    });
</script>

<?php require_once 'app/Views/layout/footer.php'; ?>