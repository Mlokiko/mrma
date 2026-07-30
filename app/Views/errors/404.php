<?php
// Wymuszamy kod błędu 404 dla przeglądarek i wyszukiwarek
http_response_code(404);
require_once 'app/Views/layout/header.php';

// Określamy awaryjny link (jeśli przeglądarka nie ma historii, np. odpalono link w nowej karcie)
$fallbackUrl = isset($_SESSION['user_id']) ? BASE_URL . 'rma_list' : BASE_URL . 'login';
?>

<div class="row justify-content-center align-items-center text-center" style="min-height: 65vh;">
    <div class="col-11 col-md-8 col-lg-6 col-xl-5">
        
        <i class="bi bi-emoji-frown text-secondary opacity-25" style="font-size: 6rem; line-height: 1;"></i>
        <h1 class="display-1 fw-bolder text-secondary opacity-50 mb-2">404</h1>
        
        <h2 class="h3 text-body fw-bold mb-3">Strona nie znaleziona</h2>
        
        <p class="text-muted mb-5 fs-5">
            <?= isset($errorMessage) ? htmlspecialchars($errorMessage) : 'Przepraszamy, ale podany adres URL nie istnieje w systemie mRMA.' ?>
        </p>
        
        <a href="<?= $fallbackUrl ?>" onclick="if(window.history.length > 1) { window.history.back(); return false; }" class="btn btn-primary btn-lg fw-semibold px-4 shadow-sm">
            <i class="bi bi-arrow-left me-2"></i> Wróć do poprzedniej strony
        </a>

    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>