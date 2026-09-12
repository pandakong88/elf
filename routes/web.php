<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\PedomanController;
use App\Http\Controllers\MediaStreamController;

Route::get('/', \App\Livewire\Public\LandingPage::class)->name('landing-page');
Route::get('/pedoman-santri/stream', [PedomanController::class, 'stream'])->name('pedoman.stream');
Route::get('/pedoman-santri/download', [PedomanController::class, 'download'])->name('pedoman.download');
Route::get('/media-stream/{id}', [MediaStreamController::class, 'stream'])->name('media.stream');

Route::get('/login',  [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Public Portal Wali (Tanpa Login)
Route::get('/portal-wali', \App\Livewire\WaliPortal\SantriSearch::class)->name('portal-wali.search');
Route::get('/portal-wali/{personId}', \App\Livewire\WaliPortal\DashboardTagihan::class)->name('portal-wali.dashboard');
Route::get('/portal-wali/payment/return', \App\Livewire\WaliPortal\StatusPembayaran::class)->name('portal-wali.payment.return');

// ─── Payment Gateway Webhooks ────────────────────────────────────────────────
// Webhook callback dari DOKU — publik, tanpa auth, tanpa CSRF
Route::post('/payment/doku/notification', [\App\Http\Controllers\DokuNotificationController::class, 'handle'])
    ->name('doku.notification')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Webhook callback dari Duitku — publik, tanpa auth, tanpa CSRF
Route::post('/duitku/callback', [\App\Http\Controllers\DuitkuCallbackController::class, 'handle'])
    ->name('duitku.callback')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// ─── Bukti Pembayaran Web Preview & PDF ──────────────────────────────────────────
// Wali portal & Bendahara — Preview Kuitansi Gateway & Unduh PDF
Route::get('/portal-wali/bukti-bayar/gateway/{trxId}', [\App\Http\Controllers\BuktiBayarController::class, 'gateway'])
    ->name('bukti-bayar.gateway');
Route::get('/portal-wali/bukti-bayar/gateway/{trxId}/pdf', [\App\Http\Controllers\BuktiBayarController::class, 'gatewayPdf'])
    ->name('bukti-bayar.gateway.pdf');

// Admin/Bendahara & Portal Wali — Preview Kuitansi & Unduh PDF
Route::get('/keuangan/kuitansi/{receiptNo}', [\App\Http\Controllers\BuktiBayarController::class, 'kuitansi'])
    ->name('bukti-bayar.kuitansi');
Route::get('/keuangan/kuitansi/{receiptNo}/pdf', [\App\Http\Controllers\BuktiBayarController::class, 'kuitansiPdf'])
    ->name('bukti-bayar.kuitansi.pdf');
Route::get('/keuangan/bukti-bayar/kasir/{paymentId}', [\App\Http\Controllers\BuktiBayarController::class, 'kasir'])
    ->name('bukti-bayar.kasir');

// Stream bukti transfer manual langsung (Bypass web server symlink requirement)
Route::get('/transfer-proof/{id}/view', [\App\Http\Controllers\BuktiBayarController::class, 'viewProofImage'])
    ->name('transfer-proof.view');
// ─── Storage Static Fallback (Jika symlink public/storage hosting belum aktif) ──
Route::get('/storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        $altJpg  = preg_replace('/\.(webp|png)$/i', '.jpg', $fullPath);
        $altWebp = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $fullPath);
        $altPng  = preg_replace('/\.(jpg|jpeg|webp)$/i', '.png', $fullPath);

        if (file_exists($altJpg)) {
            $fullPath = $altJpg;
        } elseif (file_exists($altWebp)) {
            $fullPath = $altWebp;
        } elseif (file_exists($altPng)) {
            $fullPath = $altPng;
        } else {
            abort(404, 'File bukti/media tidak ditemukan.');
        }
    }
    return response()->file($fullPath, [
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*')->name('storage.fallback');
// ─────────────────────────────────────────────────────────────────────────────



Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [WebAuthController::class, 'dashboard'])->name('dashboard');
    
    // System Access Control & CMS Pages
    Route::get('/system/roles-permissions', \App\Livewire\System\RolePermissionManager::class)->name('system.roles-permissions');
    Route::get('/system/cms', \App\Livewire\System\LandingPageCMS::class)->name('system.cms');
    Route::get('/system/wali-cms', \App\Livewire\System\WaliPortalCMS::class)->name('system.wali-cms');
    Route::get('/system/dev-settings', \App\Livewire\System\DeveloperSettings::class)->name('system.dev-settings');
    Route::get('/system/santri/import', \App\Livewire\System\SantriImportManager::class)->name('system.santri.import');
    Route::get('/setup/santri', \App\Livewire\System\SantriImportManager::class)->name('setup.santri');
    Route::get('/system/users/download-template', [\App\Http\Controllers\SystemController::class, 'downloadUserImportTemplate'])->name('system.users.download-template');
    Route::get('/system/santri/download-template', [\App\Http\Controllers\SystemController::class, 'downloadSantriImportTemplate'])->name('system.santri.download-template');
    Route::get('/system/asrama/download-template', [\App\Http\Controllers\SystemController::class, 'downloadAsramaImportTemplate'])->name('system.asrama.download-template');
    Route::get('/system/kelas/download-template', [\App\Http\Controllers\SystemController::class, 'downloadKelasImportTemplate'])->name('system.kelas.download-template');
    
    // Livewire Kepengasuhan Pages
    Route::get('/kepengasuhan/asrama-kelas', \App\Livewire\Kepengasuhan\PusatKendaliAsramaKelas::class)->name('kepengasuhan.asrama-kelas');
    Route::get('/kepengasuhan/peta-santri', \App\Livewire\Kepengasuhan\PetaSantriManager::class)->name('kepengasuhan.peta-santri');
    Route::get('/kepengasuhan/santri/{personId}/edit', \App\Livewire\Kepengasuhan\SantriEditor::class)->name('kepengasuhan.santri.edit');
    Route::get('/kepengasuhan/dormitories', \App\Livewire\Kepengasuhan\DormitoryList::class)->name('kepengasuhan.dormitories');
    Route::get('/kepengasuhan/perizinan',   \App\Livewire\Kepengasuhan\PerizinanList::class)->name('kepengasuhan.perizinan');
    Route::get('/kepengasuhan/violations',  \App\Livewire\Kepengasuhan\ViolationList::class)->name('kepengasuhan.violations');
    Route::get('/kepengasuhan/activities',  \App\Livewire\Kepengasuhan\ActivityAttendanceSheet::class)->name('kepengasuhan.activities');
    Route::get('/kepengasuhan/wali-saudara', \App\Livewire\Kepengasuhan\GuardianSiblingManager::class)->name('kepengasuhan.wali-saudara');

    // Livewire Madrasah Pages
    Route::get('/madrasah/kenaikan-kelas', \App\Livewire\Madrasah\PromotionWizard::class)->name('madrasah.kenaikan-kelas');

    // Sensus v3 — Flexible Census System
    Route::get('/sensus/templates',          \App\Livewire\Kepengasuhan\CensusTemplateManager::class)->name('sensus.templates');
    Route::get('/sensus/campaigns',          \App\Livewire\Kepengasuhan\CensusV3Dashboard::class)->name('sensus.campaigns');
    Route::get('/sensus/campaigns/create',   \App\Livewire\Kepengasuhan\CensusV3CampaignWizard::class)->name('sensus.campaigns.create');
    Route::get('/sensus/campaigns/{campaign}/input/{dormitory}', \App\Livewire\Kepengasuhan\CensusV3InputSheet::class)->name('sensus.input');
    Route::get('/sensus/campaigns/{campaign}/review/{dormitory}', \App\Livewire\Kepengasuhan\CensusV3Review::class)->name('sensus.review');

    // Keuangan Pages
    Route::get('/keuangan/billing',     \App\Livewire\Keuangan\BillingManager::class)->name('keuangan.billing');
    Route::get('/keuangan/billing/create', \App\Livewire\Keuangan\BillingConfigurationCreate::class)->name('keuangan.billing.create');
    Route::get('/keuangan/billing/exceptions/create', \App\Livewire\Keuangan\BillingExceptionCreate::class)->name('keuangan.billing.exceptions.create');
    Route::get('/keuangan/billing/exceptions/edit', \App\Livewire\Keuangan\BillingExceptionEdit::class)->name('keuangan.billing.exceptions.edit');
    Route::get('/keuangan/billing/{id}/edit', \App\Livewire\Keuangan\BillingConfigurationEdit::class)->name('keuangan.billing.edit');
    Route::get('/keuangan/billing/{id}/print-setup', \App\Livewire\Keuangan\BillingConfigurationPrintSetup::class)->name('keuangan.billing.print-setup');
    Route::get('/keuangan/lembar-setoran', \App\Livewire\Keuangan\LembarSetoranKolektif::class)->name('keuangan.lembar-setoran');
    Route::get('/keuangan/majek', \App\Livewire\Keuangan\MajekManager::class)->name('keuangan.majek');
    Route::get('/keuangan/tarif-pendaftaran', \App\Livewire\Keuangan\RegistrationTariffManager::class)->name('keuangan.tarif-pendaftaran');

    // Rekonsiliasi & Settlement Reports (PDF)
    Route::get('/keuangan/settlement/pdf', [\App\Http\Controllers\SettlementReportController::class, 'downloadSettlementPdf'])->name('keuangan.settlement.pdf');
    Route::get('/keuangan/settlement/batch-slips', [\App\Http\Controllers\SettlementReportController::class, 'downloadBatchSlipsPdf'])->name('keuangan.settlement.batch-slips');
    Route::get('/keuangan/settlement/slip-komplek/{dormitoryId}', [\App\Http\Controllers\SettlementReportController::class, 'downloadSlipKomplekPdf'])->name('keuangan.settlement.slip-komplek');
    Route::get('/keuangan/settlement/slip-kategori/{categoryKey}', [\App\Http\Controllers\SettlementReportController::class, 'downloadSlipKategoriPdf'])->name('keuangan.settlement.slip-kategori');
    Route::get('/keuangan/settlement/snapshot/{id}/pdf', [\App\Http\Controllers\SettlementReportController::class, 'downloadSnapshotPdf'])->name('keuangan.settlement.snapshot-pdf');



    // Printable Checklists (No Auth Layout)
    Route::get('/print/checklist-komplek', [\App\Http\Controllers\KeuanganPrintController::class, 'checklistKomplek'])->name('print.checklist-komplek');
    Route::get('/print/checklist-kelas', [\App\Http\Controllers\KeuanganPrintController::class, 'checklistKelas'])->name('print.checklist-kelas');
    Route::get('/print/checklist-config/{id}', [\App\Http\Controllers\KeuanganPrintController::class, 'checklistConfig'])->name('print.checklist-config');
});
