<?php require_once 'app/Views/layout/header.php'; ?>

<div class="view-wrapper-lg">
    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom flex-wrap gap-2">
                <h2 class="h4 mb-0 text-body d-flex align-items-center">
                    <i class="bi bi-geo-alt me-2"></i> Zarządzanie Lokalizacjami (Oddziały)
                </h2>
                <a href="<?= BASE_URL ?>admin_panel"
                    class="text-muted small text-decoration-none d-inline-flex align-items-center">
                    <i class="bi bi-arrow-left me-1"></i> Powrót do Panelu
                </a>
            </div>

            <div class="row g-4">
                
                <div class="col-lg-4">
                    <div class="card bg-body-tertiary border-light-subtle shadow-sm sticky-lg-top"
                        style="top: 1.5rem; z-index: 1;">
                        <div class="card-body p-4">
                            <h3 class="h5 card-title border-bottom pb-2 mb-3 text-body d-flex align-items-center"
                                id="formTitle">
                                <i class="bi bi-building-add me-2 text-primary" id="formIcon"></i>
                                <span id="formTitleText">Dodaj nową lokalizację</span>
                            </h3>

                            <form method="POST" action="<?= BASE_URL ?>admin_localizations" id="locForm">
                                <input type="hidden" name="action_type" value="add" id="locAction">
                                <input type="hidden" name="id" id="locId">

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-muted">Nazwa (np. Oddział Poznań)</label>
                                    <input type="text" class="form-control" name="name" id="locName" required>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-md-5">
                                        <label class="form-label small fw-semibold text-muted">Kod pocztowy</label>
                                        <input type="text" class="form-control" name="postal_code" id="locZip">
                                    </div>
                                    <div class="col-md-7">
                                        <label class="form-label small fw-semibold text-muted">Miasto</label>
                                        <input type="text" class="form-control" name="city" id="locCity">
                                    </div>
                                </div>

                                <div class="row g-2 mb-4">
                                    <div class="col-md-8">
                                        <label class="form-label small fw-semibold text-muted">Ulica</label>
                                        <input type="text" class="form-control" name="street" id="locStreet">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-muted">Numer</label>
                                        <input type="text" class="form-control" name="building_number" id="locBuilding">
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit"
                                        class="btn btn-primary w-100 fw-semibold d-flex justify-content-center align-items-center"
                                        id="btnSubmit">
                                        <i class="bi bi-plus-lg me-2" id="btnIcon"></i> Zapisz
                                    </button>
                                    <button type="button" class="btn btn-secondary w-100 fw-semibold"
                                        id="btnCancelEdit" style="display:none;">
                                        <i class="bi bi-x-lg me-1"></i> Anuluj
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="table-responsive border rounded bg-body shadow-sm">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="text-muted small text-uppercase align-middle user-select-none">
                                <tr>
                                    <th scope="col" class="ps-3 text-nowrap" style="width: 10%;">ID</th>
                                    <th scope="col" class="text-nowrap" style="width: 35%;">Nazwa Oddziału</th>
                                    <th scope="col" class="text-nowrap" style="width: 40%;">Adres</th>
                                    <th scope="col" class="text-end pe-3 text-nowrap" style="width: 15%;">Akcje</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                <?php if (empty($localizations)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <i class="bi bi-info-circle fs-4 d-block mb-2"></i> Brak zdefiniowanych lokalizacji.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($localizations as $loc): ?>
                                        <tr>
                                            <td class="text-muted fw-bold px-3">#<?= $loc['id'] ?></td>
                                            <td><span class="text-body fw-semibold"><?= htmlspecialchars($loc['name']) ?></span></td>
                                            <td class="text-muted small">
                                                <?= htmlspecialchars($loc['street'] . ' ' . $loc['building_number']) ?><br>
                                                <?= htmlspecialchars($loc['postal_code'] . ' ' . $loc['city']) ?>
                                            </td>
                                            <td class="text-end px-3">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-secondary btn-edit-loc d-inline-flex align-items-center"
                                                    data-id="<?= $loc['id'] ?>"
                                                    data-name="<?= htmlspecialchars($loc['name']) ?>"
                                                    data-zip="<?= htmlspecialchars($loc['postal_code'] ?? '') ?>"
                                                    data-city="<?= htmlspecialchars($loc['city'] ?? '') ?>"
                                                    data-street="<?= htmlspecialchars($loc['street'] ?? '') ?>"
                                                    data-building="<?= htmlspecialchars($loc['building_number'] ?? '') ?>">
                                                    <i class="bi bi-pencil me-1"></i> Edytuj
                                                </button>
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnCancel = document.getElementById('btnCancelEdit');
        const locForm = document.getElementById('locForm');

        const formTitleText = document.getElementById('formTitleText');
        const formIcon = document.getElementById('formIcon');
        const btnSubmit = document.getElementById('btnSubmit');
        const btnIcon = document.getElementById('btnIcon');

        document.querySelectorAll('.btn-edit-loc').forEach(btn => {
            btn.addEventListener('click', function () {
                document.getElementById('locAction').value = 'edit';
                document.getElementById('locId').value = this.dataset.id;
                document.getElementById('locName').value = this.dataset.name;
                document.getElementById('locZip').value = this.dataset.zip;
                document.getElementById('locCity').value = this.dataset.city;
                document.getElementById('locStreet').value = this.dataset.street;
                document.getElementById('locBuilding').value = this.dataset.building;

                formTitleText.innerText = 'Edycja: ' + this.dataset.name;
                formIcon.className = 'bi bi-pencil-square me-2 text-warning';

                btnSubmit.innerHTML = '<i class="bi bi-check2-circle me-2"></i> Zapisz zmiany';
                btnSubmit.className = 'btn btn-warning w-100 fw-bold d-flex justify-content-center align-items-center';

                btnCancel.style.display = 'block';

                if (window.innerWidth < 992) {
                    document.getElementById('formTitle').scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        btnCancel.addEventListener('click', () => {
            locForm.reset();
            document.getElementById('locAction').value = 'add';
            document.getElementById('locId').value = '';

            formTitleText.innerText = 'Dodaj nową lokalizację';
            formIcon.className = 'bi bi-building-add me-2 text-primary';

            btnSubmit.innerHTML = '<i class="bi bi-plus-lg me-2"></i> Zapisz';
            btnSubmit.className = 'btn btn-primary w-100 fw-semibold d-flex justify-content-center align-items-center';

            btnCancel.style.display = 'none';
        });
    });
</script>

<?php require_once 'app/Views/layout/footer.php'; ?>