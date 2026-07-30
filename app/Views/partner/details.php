<?php require_once 'app/Views/layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="view-wrapper-lg">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4 p-lg-5">

                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom flex-wrap gap-3">
                    <h2 class="h3 mb-0 text-body d-flex align-items-center">
                        <i class="bi bi-briefcase me-2 text-primary"></i> Profil Partnera B2B
                        #<?= htmlspecialchars($partner['id']) ?>
                    </h2>
                    <a href="<?= BASE_URL ?>partner_list"
                        class="btn btn-outline-secondary d-inline-flex align-items-center fw-semibold">
                        <i class="bi bi-arrow-left me-2"></i> Wróć do listy
                    </a>
                </div>

                <div class="row g-4">

                    <div class="col-lg-4 d-flex flex-column gap-4">

                        <div class="card bg-body-tertiary border-light-subtle shadow-sm">
                            <div class="card-body p-4">

                                <div id="partnerViewBox">
                                    <div
                                        class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                        <h3 class="h6 text-body fw-bold text-uppercase m-0"><i
                                                class="bi bi-card-text me-2 text-primary"></i> Dane rejestrowe</h3>
                                        <button type="button" id="btnToggleEdit"
                                            class="btn btn-sm btn-outline-secondary py-0" title="Edytuj dane">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </div>

                                    <div class="d-flex flex-column gap-2">
                                        <div
                                            class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10">
                                            <span class="text-muted small fw-semibold">Nazwa firmy:</span>
                                            <strong
                                                class="text-body text-end"><?= htmlspecialchars($partner['company_name']) ?></strong>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10">
                                            <span class="text-muted small fw-semibold">Reprezentant:</span>
                                            <span
                                                class="text-body text-end"><?= htmlspecialchars($partner['representative_first_name'] . ' ' . $partner['representative_last_name'] ?: 'Nie wskazano') ?></span>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10">
                                            <span class="text-muted small fw-semibold">Główny telefon:</span>
                                            <a href="tel:<?= htmlspecialchars($partner['primary_phone']) ?>"
                                                class="text-decoration-none fw-bold"><?= htmlspecialchars($partner['primary_phone']) ?></a>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10">
                                            <span class="text-muted small fw-semibold">Adres E-mail:</span>
                                            <span
                                                class="text-end"><?= $partner['email'] ? '<a href="mailto:' . htmlspecialchars($partner['email']) . '" class="text-decoration-none">' . htmlspecialchars($partner['email']) . '</a>' : '<span class="text-muted">---</span>' ?></span>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10">
                                            <span class="text-muted small fw-semibold">Adres siedziby:</span>
                                            <span
                                                class="text-body small text-end"><?= htmlspecialchars($partner['address_location'] ?: 'Brak adresu') ?></span>
                                        </div>

                                        <div class="mt-2">
                                            <span class="text-muted small fw-semibold d-block mb-1">Dodatkowe
                                                telefony:</span>
                                            <?php
                                            $extraPhones = json_decode($partner['additional_phones'], true);
                                            if (!empty($extraPhones) && is_array($extraPhones)):
                                                foreach ($extraPhones as $p): ?>
                                                    <div
                                                        class="d-flex justify-content-between align-items-center bg-body p-2 rounded border border-light-subtle mb-1">
                                                        <strong
                                                            class="text-body small"><?= htmlspecialchars($p['number']) ?></strong>
                                                        <span
                                                            class="text-muted small fst-italic"><?= htmlspecialchars($p['description'] ?: 'brak opisu') ?></span>
                                                    </div>
                                                <?php endforeach;
                                            else: ?>
                                                <span class="text-muted small">Brak innych numerów</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div id="partnerEditBox" style="display: none;">
                                    <div
                                        class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                        <h3 class="h6 text-body fw-bold text-uppercase m-0"><i
                                                class="bi bi-pencil-square me-2 text-warning"></i> Modyfikacja danych
                                        </h3>
                                    </div>

                                    <form method="POST" action="<?= BASE_URL ?>partner/<?= $partner['id'] ?>"
                                        class="m-0">
                                        <input type="hidden" name="action_type" value="edit_partner_core">

                                        <div class="mb-3">
                                            <label for="company_name"
                                                class="form-label small fw-semibold text-muted">Pełna nazwa
                                                firmy</label>
                                            <input type="text" id="company_name" name="company_name"
                                                class="form-control form-control-sm"
                                                value="<?= htmlspecialchars($partner['company_name']) ?>" required>
                                        </div>

                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label for="representative_first_name"
                                                    class="form-label small fw-semibold text-muted">Imię</label>
                                                <input type="text" id="representative_first_name"
                                                    name="representative_first_name"
                                                    class="form-control form-control-sm"
                                                    value="<?= htmlspecialchars($partner['representative_first_name'] ?? '') ?>">
                                            </div>
                                            <div class="col-6">
                                                <label for="representative_last_name"
                                                    class="form-label small fw-semibold text-muted">Nazwisko</label>
                                                <input type="text" id="representative_last_name"
                                                    name="representative_last_name" class="form-control form-control-sm"
                                                    value="<?= htmlspecialchars($partner['representative_last_name'] ?? '') ?>">
                                            </div>
                                        </div>

                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label for="primary_phone"
                                                    class="form-label small fw-semibold text-muted">Główny
                                                    telefon</label>
                                                <input type="text" id="primary_phone" name="primary_phone"
                                                    class="form-control form-control-sm"
                                                    value="<?= htmlspecialchars($partner['primary_phone']) ?>" required>
                                            </div>
                                            <div class="col-6">
                                                <label for="email" class="form-label small fw-semibold text-muted">Adres
                                                    e-mail</label>
                                                <input type="email" id="email" name="email"
                                                    class="form-control form-control-sm"
                                                    value="<?= htmlspecialchars($partner['email'] ?? '') ?>">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="address_location"
                                                class="form-label small fw-semibold text-muted">Adres siedziby</label>
                                            <input type="text" id="address_location" name="address_location"
                                                class="form-control form-control-sm"
                                                value="<?= htmlspecialchars($partner['address_location'] ?? '') ?>">
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label small fw-semibold text-muted">Dodatkowe numery
                                                kontaktowe</label>
                                            <div id="additionalPhonesListEdit" class="d-flex flex-column gap-2 mb-2">
                                                <?php
                                                if (!empty($extraPhones) && is_array($extraPhones)):
                                                    foreach ($extraPhones as $p): ?>
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" name="add_phone_number[]" class="form-control"
                                                                value="<?= htmlspecialchars($p['number']) ?>"
                                                                placeholder="Numer">
                                                            <input type="text" name="add_phone_desc[]" class="form-control"
                                                                value="<?= htmlspecialchars($p['description'] ?? '') ?>"
                                                                placeholder="Opis">
                                                            <button type="button" class="btn btn-outline-danger"
                                                                onclick="this.parentElement.remove()"><i
                                                                    class="bi bi-trash"></i></button>
                                                        </div>
                                                    <?php endforeach;
                                                endif; ?>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary w-100"
                                                id="btnAddPhoneEdit"><i class="bi bi-plus-lg me-1"></i> Dodaj
                                                numer</button>
                                        </div>

                                        <div class="d-flex gap-2 mt-4 pt-3 border-top">
                                            <button type="submit" class="btn btn-primary w-100 fw-semibold"><i
                                                    class="bi bi-check-lg me-1"></i> Zapisz</button>
                                            <button type="button" id="btnCancelEdit"
                                                class="btn btn-secondary w-100 fw-semibold"><i
                                                    class="bi bi-x-lg me-1"></i> Anuluj</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-body-tertiary border-light-subtle shadow-sm">
                            <div class="card-body p-4">
                                <h3 class="h6 text-body fw-bold text-uppercase border-bottom pb-2 mb-3"><i
                                        class="bi bi-journal-lock me-2 text-warning"></i> Notatki dedykowane (Wew.)</h3>
                                <form method="POST" action="<?= BASE_URL ?>partner/<?= $partner['id'] ?>" class="m-0">
                                    <input type="hidden" name="action_type" value="update_note">
                                    <div class="mb-3">
                                        <textarea name="internal_note" class="form-control bg-body" rows="6"
                                            placeholder="Wpisz specyficzne ustalenia z partnerem, warunki rozliczeń lub kody rabatowe..."><?= htmlspecialchars($partner['internal_note'] ?? '') ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 fw-semibold"><i
                                            class="bi bi-save me-1"></i> Zapisz notatkę</button>
                                </form>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-8">
                        <h3 class="h5 text-body fw-bold mb-3 d-flex align-items-center">
                            <i class="bi bi-clock-history me-2 text-primary"></i> Historia zleceń serwisowych partnera
                        </h3>

                        <div class="table-responsive border rounded bg-body shadow-sm">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead
                                    class="text-muted small text-uppercase align-middle user-select-none">
                                    <tr>
                                        <th class="ps-3 text-nowrap">ID RMA</th>
                                        <th class="text-nowrap">Data przyjęcia</th>
                                        <th class="text-nowrap">Urządzenie</th>
                                        <th class="text-nowrap">Status</th>
                                        <th class="text-nowrap">Koszt</th>
                                        <th class="text-nowrap">Data wydania</th>
                                        <th class="text-end pe-3 text-nowrap">Akcja</th>
                                    </tr>
                                </thead>
                                <tbody class="table-group-divider">
                                    <?php if (empty($rmaHistory)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-5">
                                                <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i> Ten partner nie
                                                przekazał jeszcze żadnego sprzętu do serwisu.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($rmaHistory as $rma): ?>
                                            <tr>
                                                <td class="px-3"><strong class="text-primary">#<?= $rma['id'] ?></strong></td>
                                                <td class="small text-body">
                                                    <?= date('d.m.Y H:i', strtotime($rma['created_at'])) ?></td>
                                                <td class="small">
                                                    <span
                                                        class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle mb-1"><?= htmlspecialchars($rma['type_name']) ?></span><br>
                                                    <strong
                                                        class="text-body"><?= htmlspecialchars($rma['manufacturer_name'] . ' ' . $rma['model_name']) ?></strong>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge border border-secondary-subtle text-secondary-emphasis bg-secondary-subtle px-2 py-1 fw-medium">
                                                        <?= htmlspecialchars($rma['status']) ?>
                                                    </span>
                                                </td>
                                                <td class="fw-bold text-success">
                                                    <?= $rma['final_cost'] ? number_format($rma['final_cost'], 2) . ' zł' : '<span class="text-muted fw-normal">---</span>' ?>
                                                </td>
                                                <td class="small text-muted">
                                                    <?= $rma['picked_up_at'] ? date('d.m.Y', strtotime($rma['picked_up_at'])) : '<span class="badge bg-warning-subtle text-warning-emphasis">W serwisie</span>' ?>
                                                </td>
                                                <td class="text-end px-3">
                                                    <a href="<?= BASE_URL ?>rma/<?= $rma['id'] ?>"
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const viewBox = document.getElementById('partnerViewBox');
        const editBox = document.getElementById('partnerEditBox');
        const btnToggleEdit = document.getElementById('btnToggleEdit');
        const btnCancelEdit = document.getElementById('btnCancelEdit');
        const additionalPhonesListEdit = document.getElementById('additionalPhonesListEdit');

        // Przełączanie stanów wyświetlania podgląd <=> edycja
        btnToggleEdit.addEventListener('click', () => {
            viewBox.style.display = 'none';
            editBox.style.display = 'block';
        });

        btnCancelEdit.addEventListener('click', () => {
            editBox.style.display = 'none';
            viewBox.style.display = 'block';
        });

        // Dynamiczny skrypt JS wstrzykujący nowe wiersze dla numerów w formularzu edycji
        document.getElementById('btnAddPhoneEdit').addEventListener('click', () => {
            const wrapper = document.createElement('div');
            wrapper.className = 'input-group input-group-sm';
            wrapper.innerHTML = `
            <input type="text" name="add_phone_number[]" class="form-control" placeholder="Numer"> 
            <input type="text" name="add_phone_desc[]" class="form-control" placeholder="Opis (np. kierownik)"> 
            <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
        `;
            additionalPhonesListEdit.appendChild(wrapper);
        });
    });
</script>

<?php require_once 'app/Views/layout/footer.php'; ?>