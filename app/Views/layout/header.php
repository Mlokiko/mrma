<!DOCTYPE html>
<html lang="pl" data-bs-theme="light" data-nav-orientation="horizontal">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mRMA - System Zarządzania Serwisem</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Obejście cachowania CLoudflare - przydatne przy zmianach w css -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css?v=<?= time() ?>">
    <!-- <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css"> -->
</head>

<body>
    <script>

        const savedPageWidth = localStorage.getItem('pageWidth') || 'wide';
        document.documentElement.setAttribute('data-page-width', savedPageWidth);

        // Błyskawiczne załadowanie ustawień z localStorage (zapobiega mignięciu starych stylów)
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);

        const savedNavOrient = localStorage.getItem('navOrientation') || 'horizontal';
        document.documentElement.setAttribute('data-nav-orientation', savedNavOrient);
    </script>

    <nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>">mRMA</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>rma_add"><i class="bi bi-plus-circle me-1"></i>Nowe
                                RMA</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>rma_list"><i class="bi bi-card-list me-1"></i>Lista
                                RMA</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>client_list"><i
                                    class="bi bi-people me-1"></i>Klienci</a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i
                                    class="bi bi-briefcase me-1"></i>Partnerzy</a>
                            <ul class="dropdown-menu shadow-sm">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>partner_list">Lista partnerów</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>partner_add">Dodaj partnera</a></li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i
                                    class="bi bi-box-seam me-1"></i>Magazyn</a>
                            <ul class="dropdown-menu shadow-sm">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>warehouse_list">Stan magazynowy</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>warehouse_add">Dodaj część</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>device_catalog">Katalog Urządzeń</a></li>
                            </ul>
                        </li>

                        <?php if (isset($_SESSION['account_type']) && $_SESSION['account_type'] === 'Admin'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i
                                        class="bi bi-gear me-1"></i>Admin</a>
                                <ul class="dropdown-menu shadow-sm">
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>admin_panel">Panel Główny</a></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>admin_localizations">Lokalizacje
                                            Serwisu</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>account"><i
                                    class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['username'] ?? 'Konto') ?></a>
                        </li>
                        <li class="nav-item d-flex align-items-center ms-lg-2 mt-2 mt-lg-0">
                            <button class="btn btn-sm btn-outline-secondary w-100" data-bs-toggle="modal"
                                data-bs-target="#settingsModal">
                                <i class="bi bi-sliders"></i> Wygląd
                            </button>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="main-content">
        <div class="container-fluid page-container px-4 py-4">