<?php
require_once 'app/Views/layout/header.php';
?>

<style>
    /* Niezbędne style wyłącznie dla mechanizmu rozszerzania kolumn (Resizer) */
    .resizable-th {
        position: relative;
        border-right: 1px solid var(--bs-border-color) !important;
        background-clip: padding-box;
    }

    .resizable-th:last-child {
        border-right: none !important;
    }

    .column-resize-grip {
        position: absolute;
        top: 0;
        right: 0;
        width: 6px;
        height: 100%;
        cursor: col-resize;
        background-color: transparent;
        transition: background-color 0.2s;
        z-index: 2;
    }

    .column-resize-grip:hover,
    .column-resize-grip:active {
        background-color: var(--bs-primary);
    }
</style>

<div class="card shadow-sm mb-4">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h2 class="h3 mb-0 text-body d-flex align-items-center">
                    <i class="bi bi-card-list me-2 text-primary"></i> Lista RMA
                </h2>
                <div class="text-muted small mt-1" style="cursor: help;" id="countTooltip"
                    title="Wyświetlono (po filtracji) 0 z <?= count($rmasList) ?> zgłoszeń pobranych z bazy danych.">
                    Wyświetlono: <strong id="displayedCount" class="text-body">0</strong> z <strong id="totalCount"
                        class="text-body"><?= count($rmasList) ?></strong>
                </div>
            </div>
            <a href="<?= BASE_URL ?>rma_add" class="btn btn-primary d-inline-flex align-items-center fw-semibold">
                <i class="bi bi-plus-lg me-2"></i> Nowe RMA
            </a>
        </div>

        <form method="GET" action="<?= BASE_URL ?>rma_list" id="rmaSearchForm" class="mb-4">
            <?php if (!empty($month)): ?>
                <input type="hidden" name="month" value="<?= htmlspecialchars($month) ?>" id="hiddenMonthInput">
            <?php endif; ?>
            <?php if (isset($_GET['all']) && $_GET['all'] == '1'): ?>
                <input type="hidden" name="all" value="1">
            <?php endif; ?>

            <div class="input-group input-group-lg mb-3 shadow-sm">
                <input type="text" id="mainSearchInput" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                    placeholder=" Wpisz RMA lub dowolną frazę (filtrowanie na żywo)..." autofocus
                    class="form-control border-start-0 ps-0">

                <button type="button" class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center px-4"
                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <i class="bi bi-funnel me-2"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-end p-4 shadow-lg border-light-subtle"
                    style="width: 380px; max-width: 100vw;">
                    <h4 class="h6 mb-3 pb-2 border-bottom text-body fw-bold d-flex align-items-center">
                        <i class="bi bi-sliders2 me-2 text-primary"></i>Filtry
                    </h4>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Data przyjęcia (Od - Do)</label>
                        <div class="input-group input-group-sm">
                            <input type="date" name="f_date_from"
                                value="<?= htmlspecialchars($_GET['f_date_from'] ?? '') ?>"
                                class="form-control filter-input">
                            <span class="input-group-text bg-body-tertiary">-</span>
                            <input type="date" name="f_date_to"
                                value="<?= htmlspecialchars($_GET['f_date_to'] ?? '') ?>"
                                class="form-control filter-input">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Typ Klienta</label>
                        <div class="d-flex flex-wrap gap-2">
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input filter-checkbox" type="checkbox" name="f_client_type[]"
                                    value="Individual" id="f_ind" <?= in_array('Individual', $_GET['f_client_type'] ?? []) ? 'checked' : '' ?> data-filter="client-type">
                                <label class="form-check-label small" for="f_ind"><i
                                        class="bi bi-person me-1 text-muted"></i>Osoba</label>
                            </div>
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input filter-checkbox" type="checkbox" name="f_client_type[]"
                                    value="Company" id="f_com" <?= in_array('Company', $_GET['f_client_type'] ?? []) ? 'checked' : '' ?> data-filter="client-type">
                                <label class="form-check-label small" for="f_com"><i
                                        class="bi bi-building me-1 text-muted"></i>Firma</label>
                            </div>
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input filter-checkbox" type="checkbox" name="f_client_type[]"
                                    value="Partner" id="f_par" <?= in_array('Partner', $_GET['f_client_type'] ?? []) ? 'checked' : '' ?> data-filter="client-type">
                                <label class="form-check-label small" for="f_par"><i
                                        class="bi bi-briefcase me-1 text-muted"></i>Partner</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Status</label>
                        <div class="border rounded p-2 overflow-auto bg-body-tertiary" style="max-height: 130px;">
                            <?php foreach (['Nowe', 'W diagnozie', 'Czeka na części', 'W naprawie', 'Gotowe', 'Wydane', 'Reklamacja', 'Anulowane'] as $st): ?>
                                <div class="form-check mb-1">
                                    <input class="form-check-input filter-checkbox" type="checkbox" name="f_status[]"
                                        value="<?= $st ?>" id="st_<?= md5($st) ?>" <?= in_array($st, $_GET['f_status'] ?? []) ? 'checked' : '' ?> data-filter="status">
                                    <label class="form-check-label small" for="st_<?= md5($st) ?>"><?= $st ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Typ sprzętu</label>
                        <input type="text" name="f_type" value="<?= htmlspecialchars($_GET['f_type'] ?? '') ?>"
                            class="form-control form-control-sm filter-input" data-filter="type"
                            placeholder="Wpisz typ sprzętu...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Producent</label>
                        <input type="text" name="f_manufacturer"
                            value="<?= htmlspecialchars($_GET['f_manufacturer'] ?? '') ?>"
                            class="form-control form-control-sm filter-input" data-filter="manufacturer"
                            placeholder="Wpisz producenta...">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-muted mb-1">Model</label>
                        <input type="text" name="f_model" value="<?= htmlspecialchars($_GET['f_model'] ?? '') ?>"
                            class="form-control form-control-sm filter-input" data-filter="model"
                            placeholder="Wpisz model...">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2 pt-3 border-top">
                        <a href="<?= BASE_URL ?>rma_list?all=1" class="btn btn-sm btn-outline-primary">Wszystkie z
                            bazy</a>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-cloud-arrow-down me-1"></i>
                            Pobierz z bazy</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary px-4 fw-semibold"><i
                        class="bi bi-search"></i></button>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-2 flex-wrap gap-3">
                <?php
                $currentMonth = $month ?? date('Y-m');
                $thisMonth = date('Y-m');
                $prevMonth = date('Y-m', strtotime($currentMonth . ' -1 month'));
                $nextMonth = date('Y-m', strtotime($currentMonth . ' +1 month'));
                $isNextDisabled = ($currentMonth >= $thisMonth);
                ?>
                <div class="input-group input-group-sm" style="width: auto;">
                    <a href="<?= BASE_URL ?>rma_list?month=<?= $prevMonth ?>"
                        class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <input type="month" value="<?= $currentMonth ?>" class="form-control text-center fw-bold text-body"
                        style="width: 140px; cursor: pointer;"
                        onchange="window.location.href='<?= BASE_URL ?>rma_list?month='+this.value">
                    <a href="<?= $isNextDisabled ? '#' : BASE_URL . 'rma_list?month=' . $nextMonth ?>"
                        class="btn btn-outline-secondary d-flex align-items-center <?= $isNextDisabled ? 'disabled' : '' ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <label for="limit_input" class="mb-0 small fw-semibold text-muted text-nowrap">Pobierz
                        najnowsze:</label>
                    <div class="input-group input-group-sm" style="width: 150px;">
                        <input type="number" id="limit_input" class="form-control" name="limit"
                            value="<?= htmlspecialchars($_GET['limit'] ?? '') ?>" placeholder="np. 50">
                        <button type="submit" class="btn btn-outline-secondary">Ustaw</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="table-responsive border rounded">
            <table id="rmaTable" class="table-striped table table-hover align-middle mb-0">
                <thead class="text-muted small text-uppercase align-middle user-select-none">
                    <tr>
                        <th class="resizable-th ps-3 pe-4 text-nowrap" data-sort="int">RMA <span
                                class="d-inline-block text-center" style="min-width: 15px;"></span></th>
                        <th class="resizable-th pe-4 text-nowrap" data-sort="string">Status <span
                                class="d-inline-block text-center" style="min-width: 15px;"></span></th>
                        <th class="resizable-th pe-4 text-nowrap" data-sort="date">Data przyjęcia <span
                                class="d-inline-block text-center" style="min-width: 15px;"></span></th>
                        <th class="resizable-th pe-4 text-nowrap" data-sort="date">Data odebrania <span
                                class="d-inline-block text-center" style="min-width: 15px;"></span></th>
                        <th class="resizable-th pe-4 text-nowrap" data-sort="string">Typ klienta <span
                                class="d-inline-block text-center" style="min-width: 15px;"></span></th>
                        <th class="resizable-th pe-4 text-nowrap" data-sort="string">Klient <span
                                class="d-inline-block text-center" style="min-width: 15px;"></span></th>
                        <th class="resizable-th pe-4 text-nowrap" data-sort="string">Typ sprzętu <span
                                class="d-inline-block text-center" style="min-width: 15px;"></span></th>
                        <th class="resizable-th pe-4 text-nowrap" data-sort="string">Producent <span
                                class="d-inline-block text-center" style="min-width: 15px;"></span></th>
                        <th class="resizable-th pe-4 text-nowrap" data-sort="string">Model <span
                                class="d-inline-block text-center" style="min-width: 15px;"></span></th>
                        <th class="pe-3 text-nowrap">Opis Usterki</th>
                    </tr>
                </thead>
<tbody class="table-group-divider">
                        <?php if (empty($rmasList)): ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5 table-empty">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i> Brak wyników wyszukiwania.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rmasList as $rma): ?>
                            <tr>
                                <td data-order="<?= $rma['id'] ?>" class="px-3">
                                    <a href="<?= BASE_URL ?>rma/<?= $rma['id'] ?>"
                                        class="text-decoration-none text-primary fw-bold d-block">
                                        #<?= htmlspecialchars($rma['id']) ?>
                                    </a>
                                </td>

                                <td>
                                    <span
                                        class="badge border border-secondary-subtle text-secondary-emphasis bg-secondary-subtle px-2 py-1">
                                        <?= htmlspecialchars($rma['status']) ?>
                                    </span>
                                </td>

                                <td data-order="<?= strtotime($rma['created_at']) ?>" class="small">
                                    <?= htmlspecialchars(date('d.m.Y H:i', strtotime($rma['created_at']))) ?>
                                </td>

                                <td data-order="<?= !empty($rma['picked_up_at']) ? strtotime($rma['picked_up_at']) : 0 ?>"
                                    class="small text-muted">
                                    <?= !empty($rma['picked_up_at']) ? htmlspecialchars(date('d.m.Y H:i', strtotime($rma['picked_up_at']))) : '---' ?>
                                </td>

                                <td class="small">
                                    <?php
                                    if (($rma['client_type'] ?? '') === 'Partner') {
                                        echo '<i class="bi bi-briefcase text-muted me-1"></i> Partner';
                                    } elseif (($rma['client_type'] ?? '') === 'Company') {
                                        echo '<i class="bi bi-building text-muted me-1"></i> Firma';
                                    } else {
                                        echo '<i class="bi bi-person text-muted me-1"></i> Osoba';
                                    }
                                    ?>
                                </td>

                                <td>
                                    <?php if (($rma['client_type'] ?? '') === 'Partner'): ?>
                                        <a href="<?= BASE_URL ?>partner/<?= $rma['partner_id'] ?>"
                                            class="text-decoration-none text-body d-block" title="Zobacz profil partnera">
                                            <strong class="d-block text-truncate"
                                                style="max-width: 150px;"><?= htmlspecialchars($rma['company_name'] ?? '---') ?></strong>
                                            <small class="text-muted"><i
                                                    class="bi bi-telephone me-1"></i><?= htmlspecialchars($rma['partner_phone'] ?? '') ?></small>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>client/<?= $rma['client_id'] ?>"
                                            class="text-decoration-none text-body d-block" title="Otwórz profil klienta">
                                            <strong class="d-block text-truncate"
                                                style="max-width: 150px;"><?= htmlspecialchars(($rma['client_first_name'] ?? '') . ' ' . ($rma['client_last_name'] ?? '')) ?></strong>
                                            <small class="text-muted"><i
                                                    class="bi bi-telephone me-1"></i><?= htmlspecialchars($rma['primary_phone'] ?? '') ?></small>
                                        </a>
                                    <?php endif; ?>
                                </td>

                                <td class="small text-muted"><?= htmlspecialchars($rma['type_name'] ?? '---') ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($rma['manufacturer_name'] ?? '---') ?></td>
                                <td><strong
                                        class="text-body small"><?= htmlspecialchars($rma['model_name'] ?? '---') ?></strong>
                                </td>

                                <td title="<?= htmlspecialchars($rma['issue_description']) ?>" class="small px-3">
                                    <div class="text-truncate" style="max-width: 180px; cursor: help;">
                                        <?= htmlspecialchars($rma['issue_description']) ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const table = document.getElementById('rmaTable');
        if (!table) return;

        const mainSearch = document.getElementById('mainSearchInput');
        const displayedCountEl = document.getElementById('displayedCount');

        // --- 1. Silnik błyskawicznego dopasowywania na żywo (Live Client-Side Filter) ---
        const rows = Array.from(table.querySelectorAll('tbody tr'));
        const isTableEmpty = rows.length === 1 && rows[0].querySelector('.table-empty');

        const checkboxes = document.querySelectorAll('.filter-checkbox');
        const filterInputs = document.querySelectorAll('.filter-input');

        function executeLiveFiltering() {
            if (isTableEmpty) return;

            const searchText = mainSearch.value.toLowerCase().trim();
            const checkedClientTypes = Array.from(document.querySelectorAll('.filter-checkbox[data-filter="client-type"]:checked')).map(cb => cb.value);
            const checkedStatuses = Array.from(document.querySelectorAll('.filter-checkbox[data-filter="status"]:checked')).map(cb => cb.value);

            const filterType = document.querySelector('.filter-input[data-filter="type"]').value.toLowerCase().trim();
            const filterMan = document.querySelector('.filter-input[data-filter="manufacturer"]').value.toLowerCase().trim();
            const filterMod = document.querySelector('.filter-input[data-filter="model"]').value.toLowerCase().trim();

            let matchesCounter = 0;

            rows.forEach(row => {
                const rmaId = row.children[0].innerText.toLowerCase();
                const status = row.children[1].innerText.trim();
                const clientTypeText = row.children[4].innerText.trim();
                const clientName = row.children[5].innerText.toLowerCase();
                const deviceType = row.children[6].innerText.toLowerCase();
                const manufacturer = row.children[7].innerText.toLowerCase();
                const model = row.children[8].innerText.toLowerCase();
                const description = row.children[9].innerText.toLowerCase();

                // Mapowanie dla checkboxów
                let dbClientTypeKey = 'Individual';
                if (clientTypeText === 'Partner') dbClientTypeKey = 'Partner';
                else if (clientTypeText === 'Firma') dbClientTypeKey = 'Company';

                const matchesSearch = searchText === '' ||
                    rmaId.includes(searchText) ||
                    clientName.includes(searchText) ||
                    deviceType.includes(searchText) ||
                    manufacturer.includes(searchText) ||
                    model.includes(searchText) ||
                    status.toLowerCase().includes(searchText) ||
                    description.includes(searchText);

                const matchesClientType = checkedClientTypes.length === 0 || checkedClientTypes.includes(dbClientTypeKey);
                const matchesStatus = checkedStatuses.length === 0 || checkedStatuses.includes(status);
                const matchesType = filterType === '' || deviceType.includes(filterType);
                const matchesMan = filterMan === '' || manufacturer.includes(filterMan);
                const matchesMod = filterMod === '' || model.includes(filterMod);

                if (matchesSearch && matchesClientType && matchesStatus && matchesType && matchesMan && matchesMod) {
                    row.style.display = '';
                    matchesCounter++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (displayedCountEl) {
                displayedCountEl.textContent = matchesCounter;
                document.getElementById('countTooltip').setAttribute('title', `Wyświetlono (po filtracji) ${matchesCounter} z <?= count($rmasList) ?> zgłoszeń pobranych z bazy danych.`);
            }
        }

        mainSearch.addEventListener('input', executeLiveFiltering);
        checkboxes.forEach(cb => cb.addEventListener('change', executeLiveFiltering));
        filterInputs.forEach(input => input.addEventListener('input', executeLiveFiltering));
        executeLiveFiltering();

        // --- 2. Mechanizm zmiany szerokości kolumn (Resizing) ---
        const ths = table.querySelectorAll('thead th.resizable-th');
        ths.forEach((th) => {
            const grip = document.createElement('div');
            grip.classList.add('column-resize-grip');
            th.appendChild(grip);

            grip.addEventListener('mousedown', (e) => {
                e.preventDefault();
                e.stopPropagation(); // Zapobiega triggerowaniu sortowania kolumny przy próbie zmiany szerokości
                const startX = e.pageX;
                const startWidth = th.offsetWidth;

                const onMouseMove = (moveEvent) => {
                    const currentWidth = startWidth + (moveEvent.pageX - startX);
                    if (currentWidth > 60) {
                        th.style.width = currentWidth + 'px';
                    }
                };

                const onMouseUp = () => {
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);
                };

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            });
        });

        // --- 3. Interaktywne sortowanie kolumn tabeli ---
        const headers = table.querySelectorAll('thead th[data-sort]');
        let currentSortColumn = -1;
        let isAscending = true;

        headers.forEach((header) => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', (e) => {
                // Jeśli kliknięto idealnie w uchwyt (grip), nie sortuj
                if (e.target.classList.contains('column-resize-grip')) return;

                const tbody = table.querySelector('tbody');
                const rowsArray = Array.from(tbody.querySelectorAll('tr'));
                if (isTableEmpty) return;

                const colIndex = Array.from(header.parentElement.children).indexOf(header);
                const type = header.getAttribute('data-sort');

                if (currentSortColumn === colIndex) {
                    isAscending = !isAscending;
                } else {
                    isAscending = true;
                    currentSortColumn = colIndex;
                }

                headers.forEach(h => h.querySelector('span').textContent = '');
                header.querySelector('span').textContent = isAscending ? ' ▲' : ' ▼';

                rowsArray.sort((rowA, rowB) => {
                    const cellA = rowA.children[colIndex];
                    const cellB = rowB.children[colIndex];

                    let valA = cellA.hasAttribute('data-order') ? cellA.getAttribute('data-order') : cellA.innerText.trim();
                    let valB = cellB.hasAttribute('data-order') ? cellB.getAttribute('data-order') : cellB.innerText.trim();

                    if (type === 'int' || type === 'date') {
                        return isAscending ? parseFloat(valA) - parseFloat(valB) : parseFloat(valB) - parseFloat(valA);
                    }

                    return isAscending
                        ? valA.localeCompare(valB, 'pl', { sensitivity: 'base' })
                        : valB.localeCompare(valA, 'pl', { sensitivity: 'base' });
                });

                rowsArray.forEach(row => tbody.appendChild(row));
            });
        });
    });
</script>

<?php require_once 'app/Views/layout/footer.php'; ?>