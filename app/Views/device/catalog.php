<?php require_once 'app/Views/layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="view-wrapper-lg">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4 p-lg-5">

                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom flex-wrap gap-3">
                    <h2 class="h3 mb-0 text-body d-flex align-items-center">
                        <i class="bi bi-pc-display-horizontal me-2 text-primary"></i> Katalog Urządzeń
                    </h2>
                </div>

                <div class="row g-4">

                    <div class="col-12 col-xl-6">
                        <div class="card h-100 bg-body-tertiary border-light-subtle shadow-sm">
                            <div class="card-body p-4 d-flex flex-column">
                                <h3 class="h5 text-body fw-bold border-bottom pb-2 mb-3 d-flex align-items-center">
                                    <span class="badge bg-primary rounded-pill me-2">1</span> Nowy Typ Urządzenia
                                </h3>
                                <form method="POST" action="<?= BASE_URL ?>device_catalog" class="flex-grow-1 d-flex flex-column">
                                    <input type="hidden" name="form_action" value="add_type">
                                    <div class="mb-3">
                                        <label for="type_name" class="form-label small fw-semibold text-muted">Nazwa typu</label>
                                        <input type="text" id="type_name" name="type_name" class="form-control" placeholder="np. Laptop, Smartfon, Konsola..." required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 mt-auto fw-semibold">
                                        <i class="bi bi-plus-circle me-1"></i> Dodaj typ urządzenia
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-6">
                        <div class="card h-100 bg-body-tertiary border-light-subtle shadow-sm">
                            <div class="card-body p-4 d-flex flex-column">
                                <h3 class="h5 text-body fw-bold border-bottom pb-2 mb-3 d-flex align-items-center">
                                    <span class="badge bg-primary rounded-pill me-2">2</span> Nowy Producent
                                </h3>
                                <form method="POST" action="<?= BASE_URL ?>device_catalog" class="flex-grow-1 d-flex flex-column">
                                    <input type="hidden" name="form_action" value="add_manufacturer">
                                    <div class="mb-3">
                                        <label for="manufacturer_name" class="form-label small fw-semibold text-muted">Nazwa producenta</label>
                                        <input type="text" id="manufacturer_name" name="manufacturer_name" class="form-control" placeholder="np. Apple, Samsung, Lenovo..." required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label small fw-semibold text-muted mb-2">Jakie typy urządzeń produkuje ta firma?</label>
                                        <div class="row g-2 p-3 bg-body border border-secondary-subtle rounded">
                                            <?php foreach ($types as $type): ?>
                                                <div class="col-sm-6 col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="device_type_ids[]" value="<?= $type['id'] ?>" id="type_<?= $type['id'] ?>">
                                                        <label class="form-check-label small" for="type_<?= $type['id'] ?>">
                                                            <?= htmlspecialchars($type['name']) ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 mt-auto fw-semibold">
                                        <i class="bi bi-building-add me-1"></i> Dodaj producenta
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-6">
                        <div class="card h-100 bg-body-tertiary border-light-subtle shadow-sm">
                            <div class="card-body p-4 d-flex flex-column">
                                <h3 class="h5 text-body fw-bold border-bottom pb-2 mb-3 d-flex align-items-center">
                                    <span class="badge bg-primary rounded-pill me-2">3</span> Nowy Model Urządzenia
                                </h3>
                                <form method="POST" action="<?= BASE_URL ?>device_catalog" class="flex-grow-1 d-flex flex-column">
                                    <input type="hidden" name="form_action" value="add_model">
                                    
                                    <div class="row g-3 mb-3">
                                        <div class="col-sm-6">
                                            <label for="cat_type_id" class="form-label small fw-semibold text-muted">Typ Urządzenia</label>
                                            <select id="cat_type_id" name="type_id" class="form-select" required>
                                                <option value="" disabled selected>-- Wybierz typ --</option>
                                                <?php foreach ($types as $type): ?>
                                                    <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="cat_man_id" class="form-label small fw-semibold text-muted">Producent</label>
                                            <select id="cat_man_id" name="manufacturer_id" class="form-select" disabled required>
                                                <option value="" disabled selected>-- Najpierw wybierz typ --</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="model_name" class="form-label small fw-semibold text-muted">Nazwa Modelu</label>
                                        <input type="text" id="model_name" name="model_name" class="form-control" placeholder="np. Galaxy S24 Ultra, ThinkPad T480..." required>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100 mt-auto fw-semibold">
                                        <i class="bi bi-phone me-1"></i> Dodaj model
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-6">
                        <div class="card h-100 bg-body-tertiary border-light-subtle shadow-sm">
                            <div class="card-body p-4 d-flex flex-column">
                                <h3 class="h5 text-body fw-bold border-bottom pb-2 mb-3 d-flex align-items-center">
                                    <span class="badge bg-primary rounded-pill me-2">4</span> Nazwa kodowa modelu <span class="ms-1 fw-normal text-muted fs-6">(Z zewnątrz)</span>
                                </h3>
                                <form method="POST" action="<?= BASE_URL ?>device_catalog" class="flex-grow-1 d-flex flex-column">
                                    <input type="hidden" name="form_action" value="add_model_code">
                                    <div class="mb-3">
                                        <label for="model_id" class="form-label small fw-semibold text-muted">Wybierz model docelowy</label>
                                        <select id="model_id" name="model_id" class="form-select" required>
                                            <option value="" disabled selected>-- Wybierz model z bazy --</option>
                                            <?php foreach ($models as $model): ?>
                                                <option value="<?= $model['id'] ?>"><?= htmlspecialchars($model['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="code_name" class="form-label small fw-semibold text-muted">Oznaczenie kodowe / Model Number</label>
                                        <input type="text" id="code_name" name="code_name" class="form-control" placeholder="np. SM-S928B, A2894..." required>
                                    </div>
                                    <button type="submit" class="btn btn-secondary w-100 mt-auto fw-semibold">
                                        <i class="bi bi-upc-scan me-1"></i> Przypisz kod
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('cat_type_id');
        const manSelect = document.getElementById('cat_man_id');

        typeSelect.addEventListener('change', async function () {
            manSelect.innerHTML = '<option value="" disabled selected>Ładowanie...</option>';
            manSelect.disabled = true;

            try {
                // POPRAWKA: Zmiana znaku '&' na '?' w zapytaniu URL, aby spełniało standardy
                const response = await fetch(`<?= BASE_URL ?>api_get_manufacturers?type_id=${this.value}`);
                const data = await response.json();

                manSelect.innerHTML = '<option value="" disabled selected>-- Wybierz producenta --</option>';
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name;
                    manSelect.appendChild(option);
                });

                manSelect.disabled = false;
            } catch (error) {
                console.error('Błąd pobierania producentów:', error);
                manSelect.innerHTML = '<option value="" disabled selected>Błąd połączenia</option>';
            }
        });
    });
</script>

<?php require_once 'app/Views/layout/footer.php'; ?>