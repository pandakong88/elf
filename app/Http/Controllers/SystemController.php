<?php

namespace App\Http\Controllers;

use App\Exports\UserImportTemplateExport;
use App\Exports\SantriImportTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SystemController extends Controller
{
    public function downloadUserImportTemplate(): BinaryFileResponse
    {
        if (!auth()->check() || (!auth()->user()->hasRole('super-admin') && !auth()->user()->can('manage-roles'))) {
            abort(403, 'Anda tidak memiliki wewenang untuk mengakses halaman ini.');
        }

        return Excel::download(new UserImportTemplateExport(), 'Template_Import_User.xlsx');
    }

    public function downloadSantriImportTemplate(): BinaryFileResponse
    {
        if (!auth()->check()) {
            abort(403, 'Anda harus login untuk mengunduh template.');
        }

        return Excel::download(new SantriImportTemplateExport(), 'Template_Setup_Santri_dan_Wali.xlsx');
    }

    public function downloadAsramaImportTemplate(): BinaryFileResponse
    {
        if (!auth()->check()) {
            abort(403, 'Anda harus login untuk mengunduh template.');
        }

        return Excel::download(new \App\Exports\AsramaImportTemplateExport(), 'Template_Setup_Asrama_dan_Kamar.xlsx');
    }

    public function downloadKelasImportTemplate(): BinaryFileResponse
    {
        if (!auth()->check()) {
            abort(403, 'Anda harus login untuk mengunduh template.');
        }

        return Excel::download(new \App\Exports\KelasImportTemplateExport(), 'Template_Setup_Kelas_Madrasah.xlsx');
    }

    public function downloadTunggakanImportTemplate(\Illuminate\Http\Request $request): BinaryFileResponse
    {
        if (!auth()->check()) {
            abort(403, 'Anda harus login untuk mengunduh template.');
        }

        $dormitoryId = $request->query('dormitory_id') ?: null;
        $kelasId     = $request->query('kelas_id') ?: null;
        $gender      = $request->query('gender') ?: null;
        $prefill     = $request->boolean('prefill', true);
        $billType    = $request->query('bill_type', 'kebersihan');
        $year        = (int)$request->query('year', 2025);

        $parts = ['Template_Tunggakan'];
        if ($gender) {
            $parts[] = $gender === 'L' ? 'Putra' : 'Putri';
        }
        if ($dormitoryId) {
            $dorm = \App\Modules\Kepengasuhan\Models\Dormitory::find($dormitoryId);
            if ($dorm) $parts[] = \Illuminate\Support\Str::slug($dorm->name, '_');
        }
        if ($kelasId) {
            $k = \App\Modules\Madrasah\Models\MadrasahKelas::find($kelasId);
            if ($k) $parts[] = \Illuminate\Support\Str::slug($k->name, '_');
        }
        $parts[] = (string)$year;
        $filename = implode('_', $parts) . '.xlsx';

        return Excel::download(new \App\Exports\TunggakanImportTemplateExport(
            $dormitoryId,
            $kelasId,
            $gender,
            $prefill,
            $billType,
            $year
        ), $filename);
    }
}
