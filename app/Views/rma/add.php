<?php require_once 'app/Views/layout/header.php'; ?>

<style>
    /* Wyszukiwarka AJAX */
    .search-results { position: absolute; top: 100%; left: 0; right: 0; z-index: 1050; display: none; max-height: 250px; overflow-y: auto; }
    .search-result-item { cursor: pointer; border-bottom: 1px solid var(--bs-border-color); }
    .search-result-item.active, .search-result-item:hover { background-color: var(--bs-primary-bg-subtle); color: var(--bs-primary-text-emphasis); }

    /* Android Pattern Lock */
    .pattern-wrapper { display: none; width: 240px; margin: 15px auto; text-align: center; }
    .pattern-container { position: relative; width: 240px; height: 240px; background: var(--bs-secondary-bg); border: 1px solid var(--bs-border-color); border-radius: .5rem; touch-action: none; }
    .pattern-grid { display: grid; grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(3, 1fr); width: 100%; height: 100%; position: absolute; top: 0; left: 0; z-index: 2; }
    .pattern-node { display: flex; justify-content: center; align-items: center; cursor: crosshair; font-size: 11px; font-weight: bold; color: transparent; user-select: none; position: relative; }
    .pattern-node::after { content: ''; position: absolute; width: 14px; height: 14px; background: var(--bs-secondary); border-radius: 50%; z-index: -1; transition: all 0.15s ease; }
    .pattern-node.active { color: #ffffff !important; }
    .pattern-node.active::after { background: var(--bs-primary); width: 24px; height: 24px; box-shadow: 0 0 0 6px rgba(13, 110, 253, 0.25); }
    .pattern-node.start-node::after { background: var(--bs-success) !important; box-shadow: 0 0 0 6px rgba(25, 135, 84, 0.25) !important; }
    .pattern-svg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none; }
    .pattern-line { fill: none; stroke: var(--bs-primary); stroke-width: 6; stroke-linecap: round; stroke-linejoin: round; transition: opacity 0.2s; }
</style>

<div class="row justify-content-center">
    <div class="view-wrapper-md">
        <div class="card shadow-sm mb-5">
            <div class="card-body p-4 p-lg-5">
                
                <h2 class="h3 mb-4 pb-3 border-bottom d-flex align-items-center text-body">
                    <i class="bi bi-file-earmark-plus me-2 text-primary"></i> Nowe zgłoszenie RMA
                </h2>

                <form method="POST" action="<?= BASE_URL ?>rma_add" id="rmaForm">

                    <h3 class="h5 mt-4 mb-3 text-muted fw-bold text-uppercase fs-6">1. Informacje organizacyjne</h3>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="loc_input" class="form-label small fw-semibold text-muted">Lokalizacja serwisu</label>
                            <input type="text" id="loc_input" class="form-control" list="loc_list" autocomplete="off" placeholder="-- Zacznij wpisywać --" required>
                            <datalist id="loc_list">
                                <?php foreach ($localizations as $loc): ?>
                                    <option data-value="<?= $loc['id'] ?>" value="<?= htmlspecialchars($loc['name']) ?>"></option>
                                <?php endforeach; ?>
                            </datalist>
                            <input type="hidden" name="localization_id" id="localization_id" required>
                        </div>

                        <div class="col-md-6">
                            <label for="received_by_user_id" class="form-label small fw-semibold text-muted">Przyjmujący zgłoszenie</label>
                            <select name="received_by_user_id" id="received_by_user_id" class="form-select" required>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= $u['id'] ?>" <?= $u['id'] == $_SESSION['user_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name'] . ' (' . $u['username'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <h3 class="h5 mt-5 mb-3 text-muted fw-bold text-uppercase fs-6">2. Dane klienta</h3>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <div class="btn-group w-100 shadow-sm" role="group">
                                <input type="radio" class="btn-check" name="base_client_type" value="Individual" id="type_ind" autocomplete="off" checked>
                                <label class="btn btn-outline-primary py-2 fw-medium" for="type_ind">
                                    <i class="bi bi-person me-2"></i>Osoba Fizyczna
                                </label>

                                <input type="radio" class="btn-check" name="base_client_type" value="Company" id="type_com" autocomplete="off">
                                <label class="btn btn-outline-primary py-2 fw-medium" for="type_com">
                                    <i class="bi bi-building me-2"></i>Firma
                                </label>

                                <input type="radio" class="btn-check" name="base_client_type" value="Partner" id="type_par" autocomplete="off">
                                <label class="btn btn-outline-primary py-2 fw-medium" for="type_par">
                                    <i class="bi bi-briefcase me-2"></i>Partner (B2B)
                                </label>
                            </div>
                        </div>

                        <div class="col-12" id="client_status_toggle_container">
                            <div class="btn-group w-100" role="group" id="client_status_toggle">
                                <input type="radio" class="btn-check" name="client_status" value="new" id="radioNewClient" autocomplete="off" checked>
                                <label class="btn btn-outline-secondary btn-sm fw-medium" for="radioNewClient">Nowy Klient</label>

                                <input type="radio" class="btn-check" name="client_status" value="old" id="radioOldClient" autocomplete="off">
                                <label class="btn btn-outline-secondary btn-sm fw-medium" for="radioOldClient">Stary Klient (Wyszukaj)</label>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="existing_client_id" id="existing_client_id" value="">

                    <div class="row g-3 position-relative" id="clientFields">
                        
                        <div class="search-results list-group shadow" id="clientSearchResults"></div>

                        <div class="col-md-6">
                            <label for="c_name" id="c_name_label" class="form-label small fw-semibold text-muted">Imię</label>
                            <input type="text" class="form-control" name="client_first_name" id="c_name" autocomplete="off" required>
                        </div>
                        
                        <div class="col-md-6" id="nipContainer" style="display: none;">
                            <label for="c_nip" class="form-label small fw-semibold text-muted">NIP</label>
                            <input type="text" class="form-control" name="client_nip" id="c_nip" autocomplete="off">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="c_surname" id="c_surname_label" class="form-label small fw-semibold text-muted">Nazwisko</label>
                            <input type="text" class="form-control" name="client_last_name" id="c_surname" autocomplete="off">
                        </div>
                        
                        <div class="col-md-6" id="phoneContainer">
                            <label for="c_phone" class="form-label small fw-semibold text-muted">Numer telefonu</label>
                            <input type="text" class="form-control" name="client_phone" id="c_phone" placeholder="+48..." autocomplete="off" required>
                            <div id="additionalPhonesList"></div>
                            <button type="button" class="btn btn-sm btn-link text-decoration-none mt-1 p-0" id="btnAddPhone">
                                <i class="bi bi-plus-circle me-1"></i>Dodaj kolejny numer
                            </button>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="c_email" class="form-label small fw-semibold text-muted">Adres e-mail</label>
                            <input type="email" class="form-control" name="client_email" id="c_email" autocomplete="off">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="c_pref" class="form-label small fw-semibold text-muted">Preferowana forma kontaktu</label>
                            <select class="form-select" name="preferred_contact" id="c_pref">
                                <option value="" selected>-- Nie podano --</option>
                                <option value="Phone">Telefon</option>
                                <option value="SMS">Wiadomość SMS</option>
                                <option value="Email">E-mail</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 bg-body-tertiary p-3 rounded mt-2 border border-secondary border-opacity-25" id="partnerFields" style="display: none;">
                        <div class="col-md-12">
                            <label for="partner_id" class="form-label small fw-semibold text-muted">Nazwa firmy partnerskiej</label>
                            <select class="form-select" name="partner_id" id="partner_id">
                                <option value="" disabled selected>-- Wybierz partnera --</option>
                                <?php foreach ($partners as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['company_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="add_end_client" name="add_end_client" value="1">
                                <label class="form-check-label fw-bold text-primary" for="add_end_client">
                                    Dodaj dane klienta końcowego (Serwis będzie kontaktował się bezpośrednio z klientem)
                                </label>
                            </div>
                        </div>
                    </div>

                    <h3 class="h5 mt-5 mb-3 text-muted fw-bold text-uppercase fs-6">3. Dane urządzenia</h3>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="type_input" class="form-label small fw-semibold text-muted">Typ urządzenia</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="type_input" list="type_list" autocomplete="off" placeholder="-- Wpisz typ --" required>
                                <button type="button" class="btn btn-outline-secondary btn-inne" data-target-input="type_input" data-target-list="type_list">Inne</button>
                                <button type="button" class="btn btn-outline-primary quick-add-btn" data-type="type" title="Dodaj nowy do bazy"><i class="bi bi-plus-lg"></i></button>
                            </div>
                            <datalist id="type_list">
                                <?php foreach ($deviceTypes as $type): ?>
                                    <option data-value="<?= $type['id'] ?>" value="<?= htmlspecialchars($type['name']) ?>"></option>
                                <?php endforeach; ?>
                            </datalist>
                            <input type="hidden" name="device_type_id" id="device_type_id" required>
                        </div>

                        <div class="col-md-6">
                            <label for="man_input" class="form-label small fw-semibold text-muted">Producent</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="man_input" list="man_list" autocomplete="off" placeholder="-- Najpierw typ --" disabled required>
                                <button type="button" class="btn btn-outline-secondary btn-inne" data-target-input="man_input" data-target-list="man_list">Inne</button>
                                <button type="button" class="btn btn-outline-primary quick-add-btn" data-type="man" title="Dodaj nowy do bazy" disabled id="qa_man"><i class="bi bi-plus-lg"></i></button>
                            </div>
                            <datalist id="man_list"></datalist>
                            <input type="hidden" name="device_manufacturer_id" id="device_manufacturer_id" required>
                        </div>

                        <div class="col-md-6">
                            <label for="model_input" class="form-label small fw-semibold text-muted">Model urządzenia</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="model_input" list="model_list" autocomplete="off" placeholder="-- Najpierw producent --" disabled required>
                                <button type="button" class="btn btn-outline-secondary btn-inne" data-target-input="model_input" data-target-list="model_list">Inne</button>
                                <button type="button" class="btn btn-outline-primary quick-add-btn" data-type="model" title="Dodaj nowy do bazy" disabled id="qa_model"><i class="bi bi-plus-lg"></i></button>
                            </div>
                            <datalist id="model_list"></datalist>
                            <input type="hidden" name="device_model_id" id="device_model_id" required>
                        </div>

                        <div class="col-md-6">
                            <label for="code_input" class="form-label small fw-semibold text-muted">Nazwa kodowa (Opcjonalnie)</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="code_input" autocomplete="off" placeholder="np. SM-S928B" disabled>
                                <button type="button" class="btn btn-outline-primary quick-add-btn" data-type="code" disabled id="qa_code" title="Po dodaniu, nazwa kodowa zostaje złączona z modelem w bazie."><i class="bi bi-plus-lg"></i></button>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="serial_number" class="form-label small fw-semibold text-muted">Numer seryjny (SN) / IMEI</label>
                            <input type="text" class="form-control" id="serial_number" name="serial_number" placeholder="np. 1234567890ABCDEF" autocomplete="off">
                        </div>

                        <div class="col-12 mt-4 bg-body-tertiary p-3 rounded border border-secondary border-opacity-25">
                            <label class="form-label small fw-semibold text-muted d-block mb-2">
                                <i class="bi bi-lock text-body me-1"></i> Kod blokady urządzenia
                            </label>

                            <div class="mb-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input lock-type-radio" type="radio" name="lock_type" id="lockTypePin" value="text" checked>
                                    <label class="form-check-label" for="lockTypePin">Kod PIN / Hasło</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input lock-type-radio" type="radio" name="lock_type" id="lockTypePattern" value="pattern">
                                    <label class="form-check-label" for="lockTypePattern">Wzór (Pattern)</label>
                                </div>
                            </div>

                            <input type="text" id="device_lock_code" class="form-control" name="device_lock_code" value="<?= htmlspecialchars($rma['device_lock_code'] ?? '') ?>">

                            <div id="pattern_ui" class="pattern-wrapper">
                                <div class="pattern-container" id="patternContainer">
                                    <svg class="pattern-svg"><g id="patternLinesG"></g></svg>
                                    <div class="pattern-grid" id="patternGrid">
                                        <div class="pattern-node" data-id="1"></div>
                                        <div class="pattern-node" data-id="2"></div>
                                        <div class="pattern-node" data-id="3"></div>
                                        <div class="pattern-node" data-id="4"></div>
                                        <div class="pattern-node" data-id="5"></div>
                                        <div class="pattern-node" data-id="6"></div>
                                        <div class="pattern-node" data-id="7"></div>
                                        <div class="pattern-node" data-id="8"></div>
                                        <div class="pattern-node" data-id="9"></div>
                                    </div>
                                </div>
                                <button type="button" id="btnClearPattern" class="btn btn-sm btn-outline-danger mt-3 w-100">
                                    <i class="bi bi-eraser-fill me-1"></i> Wyczyść wzór
                                </button>
                            </div>
                        </div>
                    </div>

                    <h3 class="h5 mt-5 mb-3 text-muted fw-bold text-uppercase fs-6">4. Szczegóły naprawy</h3>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label for="issue_description" class="form-label small fw-semibold text-muted">Opis usterki (zgłoszony przez klienta)</label>
                            <textarea class="form-control" id="issue_description" name="issue_description" rows="4" placeholder="Opisz dokładnie problem..." required></textarea>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_liquid_damage" name="is_liquid_damage" value="1">
                                <label class="form-check-label text-warning-emphasis fw-bold" for="is_liquid_damage">
                                    <i class="bi bi-droplet-half me-1"></i> Urządzenie po kontakcie z cieczą (Zalane)
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-12" id="liquid_status_wrapper" style="display: none;">
                            <label for="liquid_damage_status" class="form-label small fw-semibold text-muted">Szczegóły zalania</label>
                            <select class="form-select border-warning-subtle" id="liquid_damage_status" name="liquid_damage_status">
                                <option value="Reported_At_Intake">Zgłoszono przy przyjmowaniu</option>
                                <option value="Reported_Old_Unrelated">Stare zalanie / Niezwiązane z obecną usterką</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_express" name="is_express" value="1">
                                <label class="form-check-label text-danger fw-bold" for="is_express">
                                    <i class="bi bi-fire me-1"></i> Zgłoszenie ekspresowe (Priorytet)
                                </label>
                            </div>
                        </div>
                    </div>

                    <h3 class="h5 mt-5 mb-3 text-muted fw-bold text-uppercase fs-6">5. Kosztorys</h3>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="estimated_cost" class="form-label small fw-semibold text-muted">Szacowany koszt</label>
                            <div class="input-group">
                                <input type="number" class="form-control" step="1" id="estimated_cost" name="estimated_cost">
                                <span class="input-group-text text-muted">PLN</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="max_approved_cost" class="form-label small fw-semibold text-muted">Budżet klienta</label>
                            <div class="input-group">
                                <input type="number" class="form-control" step="1" id="max_approved_cost" name="max_approved_cost">
                                <span class="input-group-text text-muted">PLN</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="advance_payment" class="form-label small fw-semibold text-muted">Wpłacona zaliczka</label>
                            <div class="input-group">
                                <input type="number" class="form-control" step="1" id="advance_payment" name="advance_payment">
                                <span class="input-group-text text-muted">PLN</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 text-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5 w-100 w-sm-auto fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i> Zarejestruj zgłoszenie
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- 1. ANDROID PATTERN LOCK LOGIC ---
        const lockInput = document.getElementById('device_lock_code');
        const patternUI = document.getElementById('pattern_ui');
        const grid = document.getElementById('patternGrid');
        const linesG = document.getElementById('patternLinesG');
        const btnClear = document.getElementById('btnClearPattern');

        if (lockInput && patternUI && grid && linesG) {
            const nodes = Array.from(grid.querySelectorAll('.pattern-node'));
            let isDrawing = false;
            let patternPath = [];
            const TRIGGER_RADIUS = 35; 

            function toggleLockType(type) {
                if (type === 'pattern') {
                    lockInput.style.display = 'none';
                    patternUI.style.display = 'block';
                    if (!lockInput.value.startsWith('PATTERN:')) lockInput.value = 'PATTERN:';
                } else {
                    lockInput.style.display = 'block';
                    patternUI.style.display = 'none';
                    if (lockInput.value.startsWith('PATTERN:')) lockInput.value = '';
                }
            }

            document.querySelectorAll('.lock-type-radio').forEach(radio => {
                radio.addEventListener('change', (e) => toggleLockType(e.target.value));
            });

            if (lockInput.value.startsWith('PATTERN:')) {
                const patternRadio = document.querySelector('.lock-type-radio[value="pattern"]');
                if (patternRadio) patternRadio.checked = true;
                toggleLockType('pattern');
            }

            function getNodeCenterLocal(node) {
                const rect = node.getBoundingClientRect();
                const gridRect = grid.getBoundingClientRect();
                return { x: rect.left - gridRect.left + rect.width / 2, y: rect.top - gridRect.top + rect.height / 2 };
            }
            function getNodeCenterGlobal(node) {
                const rect = node.getBoundingClientRect();
                return { x: rect.left + rect.width / 2, y: rect.top + rect.height / 2 };
            }

            function updateSvgLine() {
                linesG.innerHTML = '';
                if (patternPath.length < 2) return;
                const totalSegments = patternPath.length - 1;
                const minOpacity = 0.35;
                for (let i = 0; i < totalSegments; i++) {
                    const p1 = getNodeCenterLocal(nodes[patternPath[i] - 1]);
                    const p2 = getNodeCenterLocal(nodes[patternPath[i + 1] - 1]);
                    const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                    line.setAttribute('x1', p1.x); line.setAttribute('y1', p1.y);
                    line.setAttribute('x2', p2.x); line.setAttribute('y2', p2.y);
                    line.classList.add('pattern-line');
                    line.style.opacity = totalSegments > 1 ? minOpacity + (i / (totalSegments - 1)) * (1 - minOpacity) : 1.0;
                    linesG.appendChild(line);
                }
            }

            function updateStartNodeVisuals() {
                nodes.forEach(n => n.classList.remove('start-node'));
                if (patternPath.length > 0) nodes[patternPath[0] - 1].classList.add('start-node');
            }

            function getDistance(x1, y1, x2, y2) { return Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2)); }

            function processInput(e) {
                if (!isDrawing) return;
                const clientX = e.clientX || (e.touches && e.touches[0] && e.touches[0].clientX);
                const clientY = e.clientY || (e.touches && e.touches[0] && e.touches[0].clientY);
                if (clientX === undefined || clientY === undefined) return;

                nodes.forEach(node => {
                    const center = getNodeCenterGlobal(node);
                    if (getDistance(clientX, clientY, center.x, center.y) <= TRIGGER_RADIUS) {
                        const id = node.getAttribute('data-id');
                        if (!patternPath.includes(id)) {
                            patternPath.push(id);
                            node.classList.add('active');
                            node.textContent = patternPath.length;
                            updateSvgLine(); updateStartNodeVisuals();
                            lockInput.value = 'PATTERN:' + patternPath.join('');
                        } else if (patternPath.length > 1 && patternPath[patternPath.length - 2] === id) {
                            const removedId = patternPath.pop();
                            const removedNode = nodes[removedId - 1];
                            removedNode.classList.remove('active');
                            removedNode.textContent = '';
                            updateSvgLine(); updateStartNodeVisuals();
                            lockInput.value = 'PATTERN:' + patternPath.join('');
                        }
                    }
                });
            }

            function startDrawing(e) {
                if (e.target.closest('.pattern-grid')) {
                    if (patternPath.length > 0) clearPattern();
                    isDrawing = true; processInput(e);
                }
            }
            function drawMove(e) { processInput(e); }
            function stopDrawing() { isDrawing = false; }
            function clearPattern() {
                patternPath = [];
                nodes.forEach(n => { n.classList.remove('active', 'start-node'); n.textContent = ''; });
                linesG.innerHTML = ''; lockInput.value = 'PATTERN:';
            }

            grid.addEventListener('mousedown', startDrawing);
            grid.addEventListener('touchstart', startDrawing, { passive: false });
            document.addEventListener('mousemove', drawMove);
            document.addEventListener('touchmove', drawMove, { passive: false });
            document.addEventListener('mouseup', stopDrawing);
            document.addEventListener('touchend', stopDrawing);
            btnClear?.addEventListener('click', clearPattern);
        }

        // --- 2. LOGIKA KLIENTÓW ---
        const typeRadios = document.querySelectorAll('input[name="base_client_type"]');
        const clientStatusContainer = document.getElementById('client_status_toggle_container');
        const clientFields = document.getElementById('clientFields');
        const partnerFields = document.getElementById('partnerFields');
        const partnerSelect = document.getElementById('partner_id');
        const labelName = document.getElementById('c_name_label');
        const labelSurname = document.getElementById('c_surname_label');
        const inputSurname = document.getElementById('c_surname');
        const nipContainer = document.getElementById('nipContainer');
        const inputNip = document.getElementById('c_nip');
        const checkboxEndClient = document.getElementById('add_end_client');

        function toggleClientFields(show) {
            if (show) {
                clientFields.style.display = 'flex'; // Zmiana z grid na flex/row w Bootstrapie
                clientStatusContainer.style.display = 'block';
                document.getElementById('c_name').required = true;
                document.getElementById('c_phone').required = true;
            } else {
                clientFields.style.display = 'none';
                clientStatusContainer.style.display = 'none';
                document.getElementById('c_name').required = false;
                document.getElementById('c_phone').required = false;
            }
        }

        checkboxEndClient.addEventListener('change', function() { toggleClientFields(this.checked); });

        typeRadios.forEach(r => r.addEventListener('change', function () {
            if (this.value === 'Partner') {
                partnerFields.style.display = 'flex';
                partnerSelect.required = true;
                toggleClientFields(checkboxEndClient.checked);
                nipContainer.style.display = 'none';
                labelName.textContent = 'Imię'; labelSurname.textContent = 'Nazwisko'; inputSurname.required = false;
            } else {
                partnerFields.style.display = 'none';
                partnerSelect.required = false;
                toggleClientFields(true);
                if (this.value === 'Company') {
                    labelName.textContent = 'Nazwa Firmy'; labelSurname.textContent = 'Osoba kontaktowa (Opcjonalnie)';
                    inputSurname.required = false; nipContainer.style.display = 'block';
                } else {
                    labelName.textContent = 'Imię'; labelSurname.textContent = 'Nazwisko';
                    inputSurname.required = false; nipContainer.style.display = 'none'; inputNip.value = '';
                }
            }
        }));

        // --- 3. WYSZUKIWARKA KLIENTÓW AJAX ---
        const searchResults = document.getElementById('clientSearchResults');
        const cInputs = [document.getElementById('c_name'), document.getElementById('c_surname'), document.getElementById('c_phone'), document.getElementById('c_email')];
        let currentFocus = -1;

        function addActive(x) {
            if (!x) return false; removeActive(x);
            if (currentFocus >= x.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = (x.length - 1);
            x[currentFocus].classList.add("active");
            x[currentFocus].scrollIntoView({ block: "nearest", behavior: "smooth" });
        }
        function removeActive(x) { for (let i = 0; i < x.length; i++) x[i].classList.remove("active"); }

        cInputs.forEach(input => {
            input.addEventListener("keydown", function (e) {
                let x = document.getElementById("clientSearchResults");
                if (x) x = x.getElementsByTagName("div");
                if (e.keyCode == 40) { currentFocus++; addActive(x); }
                else if (e.keyCode == 38) { currentFocus--; addActive(x); }
                else if (e.keyCode == 13) { e.preventDefault(); if (currentFocus > -1 && x) x[currentFocus].click(); }
            });

            input.addEventListener('blur', (e) => {
                setTimeout(() => {
                    if (!document.activeElement.closest('.search-results') && !document.activeElement.closest('#clientFields')) {
                        searchResults.style.display = 'none';
                    }
                }, 150);
            });

            let searchTimeout;
            input.addEventListener('input', () => {
                if (document.getElementById('radioNewClient').checked) return;
                clearTimeout(searchTimeout);
                const query = input.value;
                if (query.length < 2) { searchResults.style.display = 'none'; return; }

                searchTimeout = setTimeout(async () => {
                    const response = await fetch(`<?= BASE_URL ?>api_search_clients?q=${encodeURIComponent(query)}`);
                    const clients = await response.json();
                    searchResults.innerHTML = ''; currentFocus = -1;

                    if (clients.length > 0) {
                        clients.forEach(client => {
                            const div = document.createElement('div');
                            // Aktualizacja do klas Bootstrapa
                            div.className = 'list-group-item list-group-item-action search-result-item py-2';
                            div.innerHTML = `<strong class="text-body">${client.first_name} ${client.last_name || ''}</strong> <br><small class="text-muted"><i class="bi bi-telephone"></i> ${client.primary_phone}</small>`;
                            div.addEventListener('click', () => {
                                document.getElementById('existing_client_id').value = client.id;
                                cInputs[0].value = client.first_name; cInputs[1].value = client.last_name; cInputs[3].value = client.email;
                                cInputs.forEach(i => i.readOnly = true);
                                document.getElementById('c_phone').value = client.primary_phone;
                                document.getElementById('c_pref').value = client.preferred_contact || '';
                                searchResults.style.display = 'none';
                            });
                            searchResults.appendChild(div);
                        });
                        searchResults.style.display = 'block';
                    }
                }, 300);
            });
        });

        document.addEventListener('click', (e) => { if (!e.target.closest('#clientFields')) searchResults.style.display = 'none'; });

        document.getElementById('radioNewClient').addEventListener('change', () => {
            document.getElementById('existing_client_id').value = '';
            cInputs.forEach(input => { input.value = ''; input.readOnly = false; });
            document.getElementById('c_phone').value = ''; document.getElementById('c_pref').value = '';
        });
        document.getElementById('radioOldClient').addEventListener('change', () => {
            cInputs.forEach(input => { input.value = ''; input.readOnly = false; });
            document.getElementById('c_phone').value = '';
        });

        document.getElementById('btnAddPhone').addEventListener('click', () => {
            const wrapper = document.createElement('div');
            wrapper.className = 'd-flex gap-2 mt-2';
            wrapper.innerHTML = `<input type="text" class="form-control" name="add_phone_number[]" placeholder="Dodatkowy numer"> <input type="text" class="form-control" name="add_phone_desc[]" placeholder="Opis (np. do żony)"> <button type="button" class="btn btn-outline-danger px-3" onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>`;
            document.getElementById('additionalPhonesList').appendChild(wrapper);
        });

        // --- 4. AUTOCOMPLETE SPRZĘTU (Kaskada) ---
        function setupAutocomplete(inputId, hiddenId, onChangeCallback) {
            const input = document.getElementById(inputId);
            const hidden = document.getElementById(hiddenId);

            const checkMatch = function () {
                const list = document.getElementById(input.getAttribute('list'));
                if (!list) return;
                let matchedId = null;
                const currentVal = input.value.trim().toLowerCase();

                for (let option of list.options) {
                    if (option.value.toLowerCase() === currentVal) { matchedId = option.getAttribute('data-value'); break; }
                }

                if (matchedId) {
                    if (hidden.value !== matchedId) {
                        hidden.value = matchedId;
                        if (onChangeCallback) onChangeCallback(matchedId);
                    }
                } else {
                    if (hidden.value !== '') {
                        hidden.value = '';
                        if (onChangeCallback) onChangeCallback(null);
                    }
                }
            };
            input.addEventListener('input', checkMatch);
            input.addEventListener('change', checkMatch);
            input.addEventListener('blur', function () {
                if (!hidden.value && input.value !== '') { input.value = ''; input.placeholder = 'Wybierz z listy...'; checkMatch(); }
            });
        }

        async function loadDatalistOptions(url, inputId, listId, hiddenId, placeholderText) {
            const input = document.getElementById(inputId); const list = document.getElementById(listId); const hidden = document.getElementById(hiddenId);
            input.value = ''; hidden.value = ''; input.disabled = true; list.innerHTML = '';
            try {
                const response = await fetch(url);
                const data = await response.json();
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.setAttribute('data-value', item.id); option.value = item.name;
                    list.appendChild(option);
                });
                input.disabled = false; input.placeholder = placeholderText; input.focus();
            } catch (error) { console.error(error); }
        }

        setupAutocomplete('loc_input', 'localization_id');

        setupAutocomplete('type_input', 'device_type_id', (typeId) => {
            const manInput = document.getElementById('man_input'); const qaMan = document.getElementById('qa_man');
            if (typeId) {
                qaMan.disabled = false;
                loadDatalistOptions(`<?= BASE_URL ?>api_get_manufacturers?type_id=${typeId}`, 'man_input', 'man_list', 'device_manufacturer_id', '-- Wpisz producenta --');
            } else {
                manInput.value = ''; manInput.disabled = true; qaMan.disabled = true;
                document.getElementById('device_manufacturer_id').value = '';
                manInput.dispatchEvent(new Event('input'));
            }
        });

        setupAutocomplete('man_input', 'device_manufacturer_id', (manId) => {
            const modelInput = document.getElementById('model_input'); const qaModel = document.getElementById('qa_model');
            if (manId) {
                const typeId = document.getElementById('device_type_id').value;
                qaModel.disabled = false;
                loadDatalistOptions(`<?= BASE_URL ?>api_get_models?type_id=${typeId}&manufacturer_id=${manId}`, 'model_input', 'model_list', 'device_model_id', '-- Wpisz model --');
            } else {
                modelInput.value = ''; modelInput.disabled = true; qaModel.disabled = true;
                document.getElementById('device_model_id').value = '';
                modelInput.dispatchEvent(new Event('input'));
            }
        });

        setupAutocomplete('model_input', 'device_model_id', (modelId) => {
            const codeInput = document.getElementById('code_input'); const qaCode = document.getElementById('qa_code');
            if (modelId) { codeInput.disabled = false; qaCode.disabled = false; } 
            else { codeInput.value = ''; codeInput.disabled = true; qaCode.disabled = true; }
        });

        document.querySelectorAll('.btn-inne').forEach(btn => {
            btn.addEventListener('click', function () {
                const inputId = this.getAttribute('data-target-input');
                const listId = this.getAttribute('data-target-list');
                const input = document.getElementById(inputId);
                const list = document.getElementById(listId);
                let found = false;
                Array.from(list.options).forEach(opt => {
                    if (opt.value.toLowerCase().includes('inne')) {
                        input.value = opt.value; input.dispatchEvent(new Event('change')); found = true;
                    }
                });
                if (!found) alert('W bazie nie znaleziono opcji "Inne". Użyj przycisku [+] obok, aby ją dodać!');
            });
        });

        document.querySelectorAll('.quick-add-btn').forEach(btn => {
            btn.addEventListener('click', async function () {
                const type = this.getAttribute('data-type');
                let name = prompt(`Wpisz nową wartość do bazy (np. nowa nazwa producenta):`);
                if (!name) return;

                let payload = { name: name }; let actionUrl = '';
                if (type === 'type') { actionUrl = 'api_quick_add_type'; }
                if (type === 'man') { actionUrl = 'api_quick_add_man'; payload.type_id = document.getElementById('device_type_id').value; }
                if (type === 'model') { actionUrl = 'api_quick_add_model'; payload.type_id = document.getElementById('device_type_id').value; payload.manufacturer_id = document.getElementById('device_manufacturer_id').value; }
                if (type === 'code') { actionUrl = 'api_quick_add_code'; payload.model_id = document.getElementById('device_model_id').value; }

                try {
                    const res = await fetch(`<?= BASE_URL ?>${actionUrl}`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
                    const data = await res.json();

                    if (data.success) {
                        if (type === 'code') {
                            document.getElementById('code_input').value = data.name;
                            alert('Zapisano kod w bazie dla tego modelu!');
                        } else {
                            const inputId = type + '_input'; const listId = type + '_list';
                            const option = document.createElement('option');
                            option.setAttribute('data-value', data.id); option.value = data.name;
                            document.getElementById(listId).appendChild(option);
                            const input = document.getElementById(inputId);
                            input.value = data.name; input.dispatchEvent(new Event('change'));
                        }
                    }
                } catch (err) { alert('Błąd przy dodawaniu do bazy.'); }
            });
        });

        document.getElementById('is_liquid_damage').addEventListener('change', function () {
            document.getElementById('liquid_status_wrapper').style.display = this.checked ? 'block' : 'none';
        });
    });
</script>

<?php require_once 'app/Views/layout/footer.php'; ?>