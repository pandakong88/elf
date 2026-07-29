<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\PedomanController;

Route::get('/', \App\Livewire\Public\LandingPage::class)->name('landing-page');
Route::get('/pedoman-santri/stream', [PedomanController::class, 'stream'])->name('pedoman.stream');
Route::get('/pedoman-santri/download', [PedomanController::class, 'download'])->name('pedoman.download');

Route::get('/login',  [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Public Portal Wali (Tanpa Login)
Route::get('/portal-wali', \App\Livewire\WaliPortal\SantriSearch::class)->name('portal-wali.search');
Route::get('/portal-wali/{personId}', \App\Livewire\WaliPortal\DashboardTagihan::class)->name('portal-wali.dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [WebAuthController::class, 'dashboard'])->name('dashboard');
    
    // System Access Control & CMS Pages
    Route::get('/system/roles-permissions', \App\Livewire\System\RolePermissionManager::class)->name('system.roles-permissions');
    Route::get('/system/cms', \App\Livewire\System\LandingPageCMS::class)->name('system.cms');
    Route::get('/system/wali-cms', \App\Livewire\System\WaliPortalCMS::class)->name('system.wali-cms');
    Route::get('/system/santri/import', \App\Livewire\System\SantriImportManager::class)->name('system.santri.import');
    Route::get('/setup/santri', \App\Livewire\System\SantriImportManager::class)->name('setup.santri');
    Route::get('/system/users/download-template', [\App\Http\Controllers\SystemController::class, 'downloadUserImportTemplate'])->name('system.users.download-template');
    Route::get('/system/santri/download-template', [\App\Http\Controllers\SystemController::class, 'downloadSantriImportTemplate'])->name('system.santri.download-template');
    Route::get('/system/asrama/download-template', [\App\Http\Controllers\SystemController::class, 'downloadAsramaImportTemplate'])->name('system.asrama.download-template');
    Route::get('/system/kelas/download-template', [\App\Http\Controllers\SystemController::class, 'downloadKelasImportTemplate'])->name('system.kelas.download-template');
    
    // Livewire Kepengasuhan Pages
    Route::get('/kepengasuhan/asrama-kelas', \App\Livewire\Kepengasuhan\PusatKendaliAsramaKelas::class)->name('kepengasuhan.asrama-kelas');
    Route::get('/kepengasuhan/peta-santri', \App\Livewire\Kepengasuhan\PetaSantriManager::class)->name('kepengasuhan.peta-santri');
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



    // Printable Checklists (No Auth Layout)
    Route::get('/print/checklist-komplek', [\App\Http\Controllers\KeuanganPrintController::class, 'checklistKomplek'])->name('print.checklist-komplek');
    Route::get('/print/checklist-kelas', [\App\Http\Controllers\KeuanganPrintController::class, 'checklistKelas'])->name('print.checklist-kelas');
    Route::get('/print/checklist-config/{id}', [\App\Http\Controllers\KeuanganPrintController::class, 'checklistConfig'])->name('print.checklist-config');
});
