<?php require_once 'app/Views/layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="view-wrapper-md">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4 p-lg-5">
                
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom flex-wrap gap-3">
                    <h2 class="h3 mb-0 text-body d-flex align-items-center">
                        <i class="bi bi-box-seam me-2 text-primary"></i> Nowa Część
                    </h2>
                    <a href="<?= BASE_URL ?>warehouse_list" class="btn btn-outline-secondary d-inline-flex align-items-center fw-semibold">
                        <i class="bi bi-list-ul me-2"></i> Wróć do listy
                    </a>
                </div>

                <form method="POST" action="<?= BASE_URL ?>warehouse_add" id="partForm">

                    <h3 class="h5 mt-4 mb-3 text-muted fw-bold text-uppercase fs-6"><i class="bi bi-tags me-2"></i> 1. Identyfikacja i klasyfikacja części</h3>
                    <div class="row g-3 mb-4 bg-body-tertiary p-3 rounded border border-secondary-subtle">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label small fw-semibold text-muted">Kategoria części</label>
                            <select id="category_id" name="category_id" class="form-select" required>
                                <option value="" disabled selected>-- Wybierz kategorię --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="item_type" class="form-label small fw-semibold text-muted">Typ elementu (Rodzaj komponentu)</label>
                            <select id="item_type" name="item_type" class="form-select" required>
                                <option value="Part">Part (Pojedyncza część, np. złącze, układ)</option>
                                <option value="Assembly">Assembly (Kompletny moduł, np. ekran z ramką)</option>
                                <option value="Incomplete_Assembly">Incomplete Assembly (Niekompletny moduł)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="manufacturer" class="form-label small fw-semibold text-muted">Producent samej części</label>
                            <input type="text" id="manufacturer" name="manufacturer" class="form-control" list="part_man_list" autocomplete="off" placeholder="np. Samsung, Foxconn...">
                            <datalist id="part_man_list">
                                <option value="OEM (Oryginalny producent)"></option>
                                <option value="Samsung"></option>
                                <option value="LG Display"></option>
                                <option value="BOE"></option>
                                <option value="Foxconn"></option>
                            </datalist>
                        </div>

                        <div class="col-md-6">
                            <label for="part_model_code" class="form-label small fw-semibold text-muted">Oznaczenie kodowe / Model części</label>
                            <input type="text" id="part_model_code" name="part_model_code" class="form-control" placeholder="np. NT156FHM-N63, BM4Y" required>
                        </div>
                    </div>

                    <h3 class="h5 mt-5 mb-3 text-muted fw-bold text-uppercase fs-6"><i class="bi bi-search me-2"></i> 2. Stan wizualny i techniczny</h3>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="condition_status" class="form-label small fw-semibold text-muted">Status / Stan części</label>
                            <select id="condition_status" name="condition_status" class="form-select" required>
                                <option value="Nowy">Nowy</option>
                                <option value="Refabrykowany - Jak Nowy">Refabrykowany - Jak Nowy</option>
                                <option value="Refabrykowany - Normalne Ślady">Refabrykowany - Normalne Ślady</option>
                                <option value="Refabrykowany - Znaczne Ślady">Refabrykowany - Znaczne Ślady</option>
                                <option value="Używany - Jak Nowy">Używany - Jak Nowy</option>
                                <option value="Używany - Normalne Ślady">Używany - Normalne Ślady</option>
                                <option value="Używany - Znaczne Ślady">Używany - Znaczne Ślady</option>
                                <option value="Na Części">Na Części (Dawca)</option>
                                <option value="Testowy">Testowy</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="color" class="form-label small fw-semibold text-muted">Kolor elementu (Jeśli dotyczy)</label>
                            <input type="text" id="color" name="color" class="form-control" placeholder="np. Black, Space Gray, Brak">
                        </div>

                        <div class="col-12 mt-3">
                            <div class="form-check form-switch p-3 bg-body rounded border border-light-subtle d-inline-block w-100 shadow-sm">
                                <input class="form-check-input ms-0 me-3 fs-4" type="checkbox" role="switch" id="is_original" name="is_original" value="1" checked>
                                <label class="form-check-label fw-bold text-success mt-1" for="is_original" style="cursor: pointer;">
                                    <i class="bi bi-patch-check-fill me-1"></i> Część oryginalna <span class="fw-normal text-muted small ms-1">(Odznaczenie oznacza zamiennik)</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <h3 class="h5 mt-5 mb-3 text-muted fw-bold text-uppercase fs-6"><i class="bi bi-boxes me-2"></i> 3. Magazyn, logistyka i finanse</h3>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="quantity" class="form-label small fw-semibold text-muted">Ilość sztuk na stanie</label>
                            <input type="number" id="quantity" name="quantity" class="form-control" min="0" value="1" required>
                        </div>

                        <div class="col-md-6">
                            <label for="market_price" class="form-label small fw-semibold text-muted">Aktualna cena rynkowa</label>
                            <div class="input-group">
                                <input type="number" step="0.01" id="market_price" name="market_price" class="form-control" placeholder="np. 149.99">
                                <span class="input-group-text">PLN</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="storage_location" class="form-label small fw-semibold text-muted">Miejsce składowania (Lokalizacja w magazynie)</label>
                            <input type="text" id="storage_location" name="storage_location" class="form-control" list="location_list" autocomplete="off" placeholder="np. Regał A - Półka 3, Szuflada wyświetlacze iPhone...">
                            <datalist id="location_list">
                                <option value="Regał Główny - Piwnica"></option>
                                <option value="Pudełko Baterie Xiaomi"></option>
                                <option value="Szuflada Układy Scalone"></option>
                                <option value="Gablota Serwis"></option>
                            </datalist>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label small fw-semibold text-muted">Notatki / Opis dodatkowy części</label>
                            <textarea id="description" name="description" class="form-control" rows="3" placeholder="np. Nie pasuje do wersji chińskiej, wersja z wąską tasiemką..."></textarea>
                        </div>
                    </div>

                    <div class="mt-5 text-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold shadow-sm w-100 w-sm-auto">
                            <i class="bi bi-save me-2"></i> Zapisz w magazynie
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>