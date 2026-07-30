<?php require_once 'app/Views/layout/header.php'; ?>

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

<div class="row justify-content-center">
    <div class="col-12">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4 p-lg-5">

                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom flex-wrap gap-3">
                    <h2 class="h3 mb-0 text-body d-flex align-items-center">
                        <i class="bi bi-people me-2 text-primary"></i> Baza Klientów Serwisu
                    </h2>
                </div>

                <div class="mb-4 bg-body-tertiary p-4 rounded border border-light-subtle shadow-sm">
                    <label for="clientSearchInput" class="form-label fw-semibold text-muted mb-2">
                        Wyszukaj klienta w bazie:
                    </label>
                    <div class="input-group input-group-lg shadow-sm">
                        <span class="input-group-text bg-body border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="clientSearchInput" class="form-control border-start-0 ps-0"
                            placeholder="Wpisz imię, nazwisko, telefon lub e-mail...">
                    </div>
                </div>

                <div class="table-responsive border rounded shadow-sm bg-body">
                    <table id="clientsTable" class="table-striped table table-hover align-middle mb-0">
                        <thead class="text-muted small text-uppercase align-middle user-select-none">
                            <tr>
                                <th class="resizable-th ps-3 pe-4 text-nowrap" data-sort="int">ID <span
                                        class="d-inline-block text-center" style="min-width: 15px;"></span></th>
                                <th class="resizable-th pe-4 text-nowrap" data-sort="string">Imię i Nazwisko <span
                                        class="d-inline-block text-center" style="min-width: 15px;"></span></th>
                                <th class="resizable-th pe-4 text-nowrap" data-sort="string">Typ klienta <span
                                        class="d-inline-block text-center" style="min-width: 15px;"></span></th>
                                <th class="resizable-th pe-4 text-nowrap" data-sort="string">Telefon <span
                                        class="d-inline-block text-center" style="min-width: 15px;"></span></th>
                                <th class="resizable-th pe-4 text-nowrap" data-sort="string">Adres E-mail <span
                                        class="d-inline-block text-center" style="min-width: 15px;"></span></th>
                                <th class="resizable-th pe-4 text-nowrap" data-sort="string">Preferowany kontakt <span
                                        class="d-inline-block text-center" style="min-width: 15px;"></span></th>
                                <th class="ps-3 pe-4 text-nowrap" data-sort="int">Liczba RMA <span
                                        class="d-inline-block text-center" style="min-width: 15px;"></span></th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            <?php if (empty($clients)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5 table-empty">
                                        <i class="bi bi-people fs-1 d-block mb-3 opacity-50"></i>
                                        Brak klientów w bazie danych.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($clients as $client): ?>
                                    <tr>
                                        <td class="px-3 text-muted fw-bold">#<?= htmlspecialchars($client['id']) ?></td>

                                        <td>
                                            <a href="<?= BASE_URL ?>client/<?= $client['id'] ?>"
                                                class="text-decoration-none text-primary fw-bold"
                                                title="Otwórz pełny profil klienta">
                                                <?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?>
                                            </a>
                                        </td>

                                        <td class="small">
                                            <?php
                                            if (($client['client_type'] ?? '') === 'Company') {
                                                echo '<i class="bi bi-building text-muted me-1"></i> Firma';
                                            } else {
                                                echo '<i class="bi bi-person text-muted me-1"></i> Osoba fizyczna';
                                            }
                                            ?>
                                        </td>

                                        <td class="text-body fw-medium">
                                            <?= htmlspecialchars($client['primary_phone']) ?>
                                        </td>

                                        <td>
                                            <?php if (!empty($client['email'])): ?>
                                                <a href="mailto:<?= htmlspecialchars($client['email']) ?>"
                                                    class="text-decoration-none fw-medium d-inline-flex align-items-center"
                                                    title="Napisz wiadomość e-mail">
                                                    <?= htmlspecialchars($client['email']) ?> <i
                                                        class="bi bi-envelope ms-1 small"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">---</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if (!empty($client['preferred_contact']) && $client['preferred_contact'] !== 'None'): ?>
                                                <span
                                                    class="badge border border-secondary-subtle text-secondary-emphasis bg-secondary-subtle px-2 py-1">
                                                    <?= htmlspecialchars($client['preferred_contact']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">---</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="px-3 fw-semibold text-body">
                                            <?= isset($client['total_rma']) ? (int) $client['total_rma'] : 0 ?> szt.
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const table = document.getElementById('clientsTable');
        if (!table) return;

        // ==========================================
        // 1. WYSZUKIWARKA DANYCH W LOCIE (Live Search)
        // ==========================================
        const searchInput = document.getElementById('clientSearchInput');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        searchInput?.addEventListener('input', function () {
            const filterText = this.value.toLowerCase().trim();

            // Pomiń filtrowanie, jeśli tabela jest pusta
            if (rows.length === 1 && rows[0].querySelector('.table-empty')) return;

            rows.forEach(row => {
                // Przeszukujemy całą zawartość tekstową wiersza
                const rowText = row.innerText.toLowerCase();
                if (rowText.includes(filterText)) {
                    row.style.display = ''; // Pokaż
                } else {
                    row.style.display = 'none'; // Ukryj
                }
            });
        });

        // ==========================================
        // 2. MECHANIZM ZMIANY SZEROKOŚCI KOLUMN
        // ==========================================
        const ths = table.querySelectorAll('thead th.resizable-th');
        ths.forEach((th) => {
            const grip = document.createElement('div');
            grip.classList.add('column-resize-grip');
            th.appendChild(grip);

            grip.addEventListener('mousedown', (e) => {
                e.preventDefault();
                e.stopPropagation(); // POPRAWKA: Zapobiega wywołaniu sortowania podczas zmiany szerokości!

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

        // ==========================================
        // 3. INTERAKTYWNE SORTOWANIE PO KLIKNIĘCIU KOLUMNY
        // ==========================================
        const headers = table.querySelectorAll('thead th[data-sort]');
        let currentSortColumn = -1;
        let isAscending = true;

        headers.forEach((header, colIndex) => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', (e) => {
                // Blokada sortowania, gdy kliknięto idealnie w suwak rozszerzania
                if (e.target.classList.contains('column-resize-grip')) return;

                const rowsToSort = Array.from(tbody.querySelectorAll('tr'));
                if (rowsToSort.length === 1 && rowsToSort[0].querySelector('.table-empty')) return;

                const type = header.getAttribute('data-sort');

                if (currentSortColumn === colIndex) {
                    isAscending = !isAscending;
                } else {
                    isAscending = true;
                    currentSortColumn = colIndex;
                }

                headers.forEach(h => h.querySelector('span').textContent = '');
                header.querySelector('span').textContent = isAscending ? ' ▲' : ' ▼';

                rowsToSort.sort((rowA, rowB) => {
                    const cellA = rowA.children[colIndex];
                    const cellB = rowB.children[colIndex];

                    let valA = cellA.innerText.trim();
                    let valB = cellB.innerText.trim();

                    if (type === 'int') {
                        // Wyciągamy same cyfry (np. z "#15" lub "3 szt.")
                        let intA = parseFloat(valA.replace(/[^0-9.-]/g, '')) || 0;
                        let intB = parseFloat(valB.replace(/[^0-9.-]/g, '')) || 0;
                        return isAscending ? intA - intB : intB - intA;
                    }

                    return isAscending
                        ? valA.localeCompare(valB, 'pl', { sensitivity: 'base' })
                        : valB.localeCompare(valA, 'pl', { sensitivity: 'base' });
                });

                rowsToSort.forEach(row => tbody.appendChild(row));
            });
        });
    });
</script>

<?php require_once 'app/Views/layout/footer.php'; ?>