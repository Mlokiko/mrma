<?php require_once 'app/Views/layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-12">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4 p-lg-5">

                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom flex-wrap gap-3">
                    <h2 class="h3 mb-0 text-body d-flex align-items-center">
                        <i class="bi bi-box-seam me-2 text-primary"></i> Stan Magazynowy Części
                    </h2>
                    <a href="<?= BASE_URL ?>warehouse_add"
                        class="btn btn-primary d-inline-flex align-items-center fw-semibold">
                        <i class="bi bi-plus-lg me-2"></i> Dodaj część
                    </a>
                </div>

                <div class="table-responsive border rounded shadow-sm bg-body">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="text-muted small text-uppercase align-middle user-select-none">
                            <tr>
                                <th class="ps-3 text-nowrap">ID</th>
                                <th class="text-nowrap">Kategoria</th>
                                <th class="text-nowrap">Producent części</th>
                                <th class="text-nowrap">Kod części / Model</th>
                                <th class="text-nowrap">Stan</th>
                                <th class="text-nowrap">Cena rynkowa</th>
                                <th class="text-nowrap">Ilość (Szt)</th>
                                <th class="pe-3 text-nowrap">Lokalizacja</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            <?php if (empty($parts)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="bi bi-box fs-1 d-block mb-3 opacity-50"></i>
                                        Magazyn jest aktualnie pusty.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($parts as $part): ?>
                                    <tr>
                                        <td class="px-3 text-muted fw-bold">#<?= htmlspecialchars($part['id']) ?></td>
                                        <td>
                                            <span
                                                class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle px-2 py-1">
                                                <?= htmlspecialchars($part['category_name']) ?>
                                            </span>
                                        </td>
                                        <td class="text-body small"><?= htmlspecialchars($part['manufacturer'] ?: '---') ?></td>
                                        <td><strong
                                                class="text-body"><?= htmlspecialchars($part['part_model_code'] ?: '---') ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1 fw-medium">
                                                <?= htmlspecialchars($part['condition_status']) ?>
                                            </span>
                                        </td>
                                        <td class="fw-medium text-success">
                                            <?= $part['market_price'] ? number_format($part['market_price'], 2) . ' zł' : '<span class="text-muted fw-normal">---</span>' ?>
                                        </td>
                                        <td>
                                            <?php if ($part['quantity'] < 2): ?>
                                                <span class="badge bg-danger px-2 py-1 shadow-sm"><i
                                                        class="bi bi-exclamation-triangle me-1"></i>
                                                    <?= htmlspecialchars($part['quantity']) ?> szt.</span>
                                            <?php else: ?>
                                                <strong class="text-body"><?= htmlspecialchars($part['quantity']) ?> szt.</strong>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-3 text-muted small">
                                            <?= htmlspecialchars($part['storage_location'] ?: 'Nie określono') ?>
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

<?php require_once 'app/Views/layout/footer.php'; ?>