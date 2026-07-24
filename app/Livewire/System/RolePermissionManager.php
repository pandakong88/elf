<?php

namespace App\Livewire\System;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use App\Livewire\Concerns\SendsToast;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class RolePermissionManager extends Component
{
    use SendsToast, WithPagination, WithFileUploads;

    public $activeTab = 'roles';
    
    // --- Create User Form Properties ---
    public bool $showCreateUserModal = false;
    public string $newUserName = '';
    public string $newUserEmail = '';
    public string $newUserUsername = '';
    public string $newUserPassword = '';
    public string $newUserPasswordConfirm = '';
    public array $newUserRoles = [];

    // --- Edit User Form Properties ---
    public bool $showEditUserModal = false;
    public ?string $editingUserDataId = null;
    public string $editName = '';
    public string $editEmail = '';
    public string $editUsername = '';
    public string $editPassword = '';
    public string $editPasswordConfirm = '';
    public bool $editIsActive = true;

    // --- Delete User Properties ---
    public bool $showDeleteUserModal = false;
    public ?string $deletingUserId = null;
    public string $deletingUserName = '';

    // --- Import Excel Properties ---
    public $importFile = null;
    public bool $showImportModal = false;
    public array $importResults = [];
    public bool $importDone = false;
    public array $tempValidUsers = [];
    public array $tempInvalidUsers = [];
    public $searchUser = '';
    public $searchPermission = '';
    public $selectedRoleId = null;
    public $newRoleName = '';
    public $showUserModal = false;
    public $editingUserId = null;
    public $editingUserRoles = [];

    // Copy permissions modal properties
    public $showCopyModal = false;
    public $copyFromRoleId = null;

    // Active sub-tab permission group filtering
    public $selectedGroup = 'all';

    // Filters for users list
    public $filterRole = '';
    public $filterStatus = '';

    // Confirmation modal properties
    public $showConfirmModal = false;
    public $confirmTitle = '';
    public $confirmDescription = '';
    public $confirmAction = '';
    public $confirmData = null;

    protected $queryString = [
        'activeTab' => ['except' => 'roles'],
        'searchUser' => ['except' => ''],
        'selectedGroup' => ['except' => 'all'],
        'filterRole' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function mount()
    {
        // Enforce Spatie Authorization checks
        if (!auth()->check() || (!auth()->user()->hasRole('super-admin') && !auth()->user()->can('manage-roles'))) {
            abort(403, 'Anda tidak memiliki wewenang untuk mengakses halaman ini.');
        }

        // Set default selected role (first role in the list)
        $firstRole = Role::where('guard_name', 'web')->first();
        if ($firstRole) {
            $this->selectedRoleId = $firstRole->id;
        }
    }

    public function updatingSearchUser()
    {
        $this->resetPage();
    }

    public function updatingFilterRole()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function selectRole($roleId)
    {
        $this->selectedRoleId = $roleId;
    }

    public function createRole()
    {
        $this->validate([
            'newRoleName' => 'required|string|min:3|max:50|unique:roles,name',
        ], [
            'newRoleName.required' => 'Nama peran harus diisi.',
            'newRoleName.min' => 'Nama peran minimal 3 karakter.',
            'newRoleName.unique' => 'Nama peran ini sudah terdaftar.',
        ]);

        $role = Role::create([
            'name' => strtolower(str_replace(' ', '-', trim($this->newRoleName))),
            'guard_name' => 'web',
        ]);

        $this->selectedRoleId = $role->id;
        $this->newRoleName = '';

        activity('security')
            ->performedOn($role)
            ->causedBy(auth()->user())
            ->log("Telah membuat peran baru '{$role->name}'.");

        $this->toastSuccess("Peran '{$role->name}' berhasil ditambahkan.");
    }

    public function togglePermission($permissionName)
    {
        if ($this->selectedRoleId === null) {
            return;
        }

        $role = Role::findById($this->selectedRoleId, 'web');

        // Prevent modifying super-admin permissions directly to protect system security
        if ($role->name === 'super-admin') {
            $this->toastError('Izin untuk peran Super-Admin tidak dapat diubah (dikelola penuh otomatis).');
            return;
        }

        if ($role->hasPermissionTo($permissionName)) {
            $role->revokePermissionTo($permissionName);
            activity('security')
                ->performedOn($role)
                ->causedBy(auth()->user())
                ->log("Telah mencabut wewenang '{$permissionName}' dari peran '{$role->name}'.");
            $this->toastSuccess("Izin '{$permissionName}' telah dicabut dari peran '{$role->name}'.");
        } else {
            $role->givePermissionTo($permissionName);
            activity('security')
                ->performedOn($role)
                ->causedBy(auth()->user())
                ->log("Telah memberikan wewenang '{$permissionName}' ke peran '{$role->name}'.");
            $this->toastSuccess("Izin '{$permissionName}' telah diberikan ke peran '{$role->name}'.");
        }

        // Forget cached permissions to apply updates instantly
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function toggleUserStatus($userId)
    {
        $user = User::findOrFail($userId);

        // Security check: cannot suspend self
        if ($user->id === auth()->id()) {
            $this->toastError('Anda tidak dapat menonaktifkan akun Anda sendiri.');
            return;
        }

        // Security check: cannot deactivate the last active super-admin
        if ($user->hasRole('super-admin') && $user->is_active) {
            $activeSuperAdminCount = User::role('super-admin')->where('is_active', true)->count();
            if ($activeSuperAdminCount <= 1) {
                $this->toastError('Tidak dapat menonaktifkan akun Super-Admin ini karena ini adalah satu-satunya akun administrator utama yang aktif.');
                return;
            }
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusStr = $user->is_active ? 'mengaktifkan' : 'menonaktifkan';
        activity('security')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log("Telah {$statusStr} akun pengurus '{$user->name}' ({$user->email}).");

        $this->toastSuccess("Status pengurus '{$user->name}' berhasil diperbarui.");
    }

    public function confirmResetPermissions()
    {
        if ($this->selectedRoleId === null) {
            return;
        }

        $role = Role::findOrFail($this->selectedRoleId);

        // Prevent resetting super-admin permissions
        if ($role->name === 'super-admin') {
            $this->toastError('Wewenang peran Super-Admin dilindungi penuh dan tidak dapat direset.');
            return;
        }

        $this->confirmTitle = 'Reset Wewenang Peran';
        $this->confirmDescription = "Apakah Anda yakin ingin menghapus semua wewenang dari peran '" . str_replace('-', ' ', $role->name) . "'? Tindakan ini tidak dapat dibatalkan.";
        $this->confirmAction = 'resetPermissions';
        $this->confirmData = null;
        $this->showConfirmModal = true;
    }

    public function confirmToggleUserStatus($userId)
    {
        $user = User::findOrFail($userId);

        // Security check: cannot suspend self
        if ($user->id === auth()->id()) {
            $this->toastError('Anda tidak dapat menonaktifkan akun Anda sendiri.');
            return;
        }

        // Security check: cannot deactivate the last active super-admin
        if ($user->hasRole('super-admin') && $user->is_active) {
            $activeSuperAdminCount = User::role('super-admin')->where('is_active', true)->count();
            if ($activeSuperAdminCount <= 1) {
                $this->toastError('Tidak dapat menonaktifkan akun Super-Admin ini karena ini adalah satu-satunya akun administrator utama yang aktif.');
                return;
            }
        }

        $statusStr = $user->is_active ? 'menonaktifkan' : 'mengaktifkan';
        $this->confirmTitle = ucfirst($statusStr) . ' Akun Pengurus';
        $this->confirmDescription = "Apakah Anda yakin ingin {$statusStr} akun pengurus '{$user->name}' ({$user->email})?";
        $this->confirmAction = 'toggleUserStatus';
        $this->confirmData = $userId;
        $this->showConfirmModal = true;
    }

    public function confirmCopyPermissions()
    {
        if ($this->selectedRoleId === null) {
            return;
        }

        $targetRole = Role::findOrFail($this->selectedRoleId);

        // Prevent copying to super-admin
        if ($targetRole->name === 'super-admin') {
            $this->toastError('Wewenang peran Super-Admin dilindungi penuh dan tidak dapat ditimpa.');
            return;
        }

        $this->validate([
            'copyFromRoleId' => 'required|exists:roles,id',
        ], [
            'copyFromRoleId.required' => 'Pilih peran asal terlebih dahulu.',
            'copyFromRoleId.exists' => 'Peran asal tidak valid.',
        ]);

        $sourceRole = Role::findOrFail($this->copyFromRoleId);

        $this->confirmTitle = 'Salin & Timpa Wewenang';
        $this->confirmDescription = "Tindakan ini akan menimpa seluruh wewenang peran '" . str_replace('-', ' ', $targetRole->name) . "' dengan wewenang dari peran '" . str_replace('-', ' ', $sourceRole->name) . "'. Apakah Anda yakin?";
        $this->confirmAction = 'copyPermissions';
        $this->confirmData = null;
        $this->showCopyModal = false;
        $this->showConfirmModal = true;
    }

    public function cancelConfirm()
    {
        if ($this->confirmAction === 'copyPermissions') {
            $this->showCopyModal = true;
        }
        $this->showConfirmModal = false;
        $this->confirmAction = '';
        $this->confirmData = null;
        $this->confirmTitle = '';
        $this->confirmDescription = '';
    }

    public function executeConfirmedAction()
    {
        if (!$this->confirmAction) {
            return;
        }

        $action = $this->confirmAction;
        $data = $this->confirmData;

        // Reset state
        $this->showConfirmModal = false;
        $this->confirmAction = '';
        $this->confirmData = null;
        $this->confirmTitle = '';
        $this->confirmDescription = '';

        if ($action === 'resetPermissions') {
            $this->resetPermissions();
        } elseif ($action === 'toggleUserStatus') {
            $this->toggleUserStatus($data);
        } elseif ($action === 'copyPermissions') {
            $this->copyPermissions();
        }
    }

    public function resetPermissions()
    {
        if ($this->selectedRoleId === null) {
            return;
        }

        $role = Role::findOrFail($this->selectedRoleId);

        // Prevent resetting super-admin permissions
        if ($role->name === 'super-admin') {
            $this->toastError('Wewenang peran Super-Admin dilindungi penuh dan tidak dapat direset.');
            return;
        }

        $role->syncPermissions([]);

        activity('security')
            ->performedOn($role)
            ->causedBy(auth()->user())
            ->log("Telah mereset (menghapus) semua wewenang untuk peran '{$role->name}'.");

        // Forget cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->toastSuccess("Semua wewenang untuk peran '{$role->name}' berhasil dikosongkan.");
    }

    public function openCopyModal()
    {
        $this->showCopyModal = true;
        $this->copyFromRoleId = null;
    }

    public function copyPermissions()
    {
        if ($this->selectedRoleId === null) {
            return;
        }

        $targetRole = Role::findOrFail($this->selectedRoleId);

        // Prevent copying to super-admin
        if ($targetRole->name === 'super-admin') {
            $this->toastError('Wewenang peran Super-Admin dilindungi penuh dan tidak dapat ditimpa.');
            $this->showCopyModal = false;
            return;
        }

        $this->validate([
            'copyFromRoleId' => 'required|exists:roles,id',
        ], [
            'copyFromRoleId.required' => 'Pilih peran asal terlebih dahulu.',
            'copyFromRoleId.exists' => 'Peran asal tidak valid.',
        ]);

        $sourceRole = Role::findOrFail($this->copyFromRoleId);

        $permissions = $sourceRole->permissions()->pluck('name')->toArray();
        $targetRole->syncPermissions($permissions);

        activity('security')
            ->performedOn($targetRole)
            ->causedBy(auth()->user())
            ->log("Telah menyalin wewenang dari peran '{$sourceRole->name}' ke peran '{$targetRole->name}'.");

        // Forget cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->showCopyModal = false;
        $this->copyFromRoleId = null;

        $this->toastSuccess("Wewenang berhasil disalin dari peran '{$sourceRole->name}' ke peran '{$targetRole->name}'.");
    }

    public function openUserModal($userId)
    {
        $user = User::findOrFail($userId);
        $this->editingUserId = $userId;
        $this->editingUserRoles = $user->roles->pluck('name')->toArray();
        $this->showUserModal = true;
    }

    public function closeUserModal()
    {
        $this->showUserModal = false;
        $this->editingUserId = null;
        $this->editingUserRoles = [];
    }

    public function toggleUserRole($roleName)
    {
        if (in_array($roleName, $this->editingUserRoles)) {
            $this->editingUserRoles = array_diff($this->editingUserRoles, [$roleName]);
        } else {
            $this->editingUserRoles[] = $roleName;
        }
    }

    public function saveUserRoles()
    {
        if (!$this->editingUserId) {
            return;
        }

        $user = User::findOrFail($this->editingUserId);
        
        // Prevent removing super-admin from active user if they are the only super-admin
        if ($user->hasRole('super-admin') && !in_array('super-admin', $this->editingUserRoles)) {
            $superAdminCount = User::role('super-admin')->count();
            if ($superAdminCount <= 1) {
                $this->toastError('Tidak dapat mencabut wewenang Super-Admin karena ini adalah satu-satunya akun administrator utama.');
                $this->closeUserModal();
                return;
            }
        }

        $oldRoles = $user->roles->pluck('name')->toArray();
        $user->syncRoles($this->editingUserRoles);
        $newRoles = $user->fresh()->roles->pluck('name')->toArray();

        activity('security')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log("Telah memperbarui peran pengurus '{$user->name}' dari [" . implode(', ', $oldRoles) . "] menjadi [" . implode(', ', $newRoles) . "].");

        $this->toastSuccess("Peran untuk pengurus '{$user->name}' berhasil diperbarui.");
        $this->closeUserModal();
    }

    // --- Create User Manual ---
    public function openCreateUserModal()
    {
        $this->resetCreateForm();
        $this->showCreateUserModal = true;
    }

    public function closeCreateUserModal()
    {
        $this->showCreateUserModal = false;
        $this->resetCreateForm();
    }

    private function resetCreateForm()
    {
        $this->newUserName = '';
        $this->newUserEmail = '';
        $this->newUserUsername = '';
        $this->newUserPassword = '';
        $this->newUserPasswordConfirm = '';
        $this->newUserRoles = [];
        $this->resetValidation();
    }

    public function createUser()
    {
        $this->validate([
            'newUserName' => 'required|string|max:255',
            'newUserEmail' => 'required|email|max:255|unique:users,email',
            'newUserUsername' => 'nullable|string|max:50|unique:users,username',
            'newUserPassword' => 'required|string|min:8',
            'newUserPasswordConfirm' => 'required|same:newUserPassword',
            'newUserRoles' => 'required|array|min:1',
        ], [
            'newUserName.required' => 'Nama lengkap wajib diisi.',
            'newUserEmail.required' => 'Email wajib diisi.',
            'newUserEmail.email' => 'Format email tidak valid.',
            'newUserEmail.unique' => 'Email sudah terdaftar.',
            'newUserUsername.unique' => 'Username sudah terdaftar.',
            'newUserPassword.required' => 'Password wajib diisi.',
            'newUserPassword.min' => 'Password minimal 8 karakter.',
            'newUserPasswordConfirm.required' => 'Konfirmasi password wajib diisi.',
            'newUserPasswordConfirm.same' => 'Konfirmasi password harus sama dengan password.',
            'newUserRoles.required' => 'Pilih minimal satu peran (role).',
        ]);

        $user = User::create([
            'name' => $this->newUserName,
            'email' => $this->newUserEmail,
            'username' => $this->newUserUsername ?: null,
            'password' => Hash::make($this->newUserPassword),
            'is_active' => true,
        ]);

        $user->syncRoles($this->newUserRoles);

        activity('security')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log("Telah membuat user baru '{$user->name}' ({$user->email}) dengan peran: [" . implode(', ', $this->newUserRoles) . "].");

        $this->showCreateUserModal = false;
        $this->resetCreateForm();
        $this->toastSuccess("User '{$user->name}' berhasil ditambahkan.");
    }

    // --- Edit User ---
    public function openEditUserModal($userId)
    {
        $user = User::findOrFail($userId);
        $this->editingUserDataId = $userId;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editUsername = $user->username ?? '';
        $this->editPassword = '';
        $this->editPasswordConfirm = '';
        $this->editIsActive = (bool)$user->is_active;
        $this->showEditUserModal = true;
    }

    public function closeEditUserModal()
    {
        $this->showEditUserModal = false;
        $this->editingUserDataId = null;
        $this->resetValidation();
    }

    public function updateUser()
    {
        if (!$this->editingUserDataId) return;

        $user = User::findOrFail($this->editingUserDataId);

        // Security check: cannot suspend self
        if ($user->id === auth()->id() && !$this->editIsActive) {
            $this->toastError('Anda tidak dapat menonaktifkan akun Anda sendiri.');
            return;
        }

        // Security check: cannot deactivate the last active super-admin
        if ($user->hasRole('super-admin') && $user->is_active && !$this->editIsActive) {
            $activeSuperAdminCount = User::role('super-admin')->where('is_active', true)->count();
            if ($activeSuperAdminCount <= 1) {
                $this->toastError('Tidak dapat menonaktifkan akun Super-Admin ini karena ini adalah satu-satunya akun administrator utama yang aktif.');
                return;
            }
        }

        $rules = [
            'editName' => 'required|string|max:255',
            'editEmail' => 'required|email|max:255|unique:users,email,' . $user->id,
            'editUsername' => 'nullable|string|max:50|unique:users,username,' . $user->id,
            'editPassword' => 'nullable|string|min:8',
            'editPasswordConfirm' => 'nullable|same:editPassword',
        ];

        $this->validate($rules, [
            'editName.required' => 'Nama lengkap wajib diisi.',
            'editEmail.required' => 'Email wajib diisi.',
            'editEmail.email' => 'Format email tidak valid.',
            'editEmail.unique' => 'Email sudah terdaftar.',
            'editUsername.unique' => 'Username sudah terdaftar.',
            'editPassword.min' => 'Password baru minimal 8 karakter.',
            'editPasswordConfirm.same' => 'Konfirmasi password baru harus sama.',
        ]);

        $user->name = $this->editName;
        $user->email = $this->editEmail;
        $user->username = $this->editUsername ?: null;
        $user->is_active = $this->editIsActive;

        if ($this->editPassword) {
            $user->password = Hash::make($this->editPassword);
        }

        $user->save();

        activity('security')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log("Telah memperbarui profil user '{$user->name}' ({$user->email}).");

        $this->showEditUserModal = false;
        $this->editingUserDataId = null;
        $this->toastSuccess("Profil user '{$user->name}' berhasil diperbarui.");
    }

    // --- Delete User ---
    public function confirmDeleteUser($userId)
    {
        $user = User::findOrFail($userId);

        // Security check: cannot delete self
        if ($user->id === auth()->id()) {
            $this->toastError('Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        // Security check: cannot delete the last active super-admin
        if ($user->hasRole('super-admin')) {
            $superAdminCount = User::role('super-admin')->count();
            if ($superAdminCount <= 1) {
                $this->toastError('Tidak dapat menghapus akun Super-Admin ini karena ini adalah satu-satunya akun administrator utama.');
                return;
            }
        }

        $this->deletingUserId = $userId;
        $this->deletingUserName = $user->name;
        $this->showDeleteUserModal = true;
    }

    public function deleteUser()
    {
        if (!$this->deletingUserId) return;

        $user = User::findOrFail($this->deletingUserId);

        // Re-run checks
        if ($user->id === auth()->id() || ($user->hasRole('super-admin') && User::role('super-admin')->count() <= 1)) {
            $this->toastError('Aksi ditolak karena melanggar aturan keamanan.');
            $this->showDeleteUserModal = false;
            return;
        }

        $name = $user->name;
        $email = $user->email;

        $user->syncRoles([]);
        $user->delete();

        activity('security')
            ->causedBy(auth()->user())
            ->log("Telah menghapus permanen akun pengurus '{$name}' ({$email}).");

        $this->showDeleteUserModal = false;
        $this->deletingUserId = null;
        $this->deletingUserName = '';
        $this->toastSuccess("User '{$name}' berhasil dihapus secara permanen.");
    }

    // --- Import Excel ---
    public function openImportModal()
    {
        $this->resetImport();
        $this->showImportModal = true;
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->resetImport();
    }

    public function resetImport()
    {
        $this->importFile = null;
        $this->importResults = [];
        $this->tempValidUsers = [];
        $this->tempInvalidUsers = [];
        $this->importDone = false;
        $this->resetValidation();
    }

    public function updatedImportFile()
    {
        $this->processImport();
    }

    public function processImport()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls|max:2048',
        ], [
            'importFile.required' => 'Pilih file Excel terlebih dahulu.',
            'importFile.mimes' => 'Format file harus berupa .xlsx atau .xls.',
            'importFile.max' => 'Ukuran file maksimal adalah 2MB.',
        ]);

        try {
            $path = $this->importFile->getRealPath();
            $data = \Maatwebsite\Excel\Facades\Excel::toArray(new \stdClass(), $path);

            if (empty($data) || empty($data[0])) {
                $this->toastError('File Excel kosong atau tidak terbaca.');
                return;
            }

            $rows = $data[0];
            unset($rows[0]); // remove header row

            $validUsers = [];
            $invalidUsers = [];

            // Get valid roles for verification
            $validRoles = Role::where('guard_name', 'web')->pluck('name')->toArray();

            foreach ($rows as $index => $row) {
                $rowNum = $index + 1; // Row key matches Excel row index (1-based index)
                
                // Trim all values
                $name = isset($row[0]) ? trim((string)$row[0]) : '';
                $email = isset($row[1]) ? trim((string)$row[1]) : '';
                $username = isset($row[2]) ? trim((string)$row[2]) : '';
                $password = isset($row[3]) ? trim((string)$row[3]) : '';
                $role1 = isset($row[4]) ? trim((string)$row[4]) : '';
                $role2 = isset($row[5]) ? trim((string)$row[5]) : '';

                // Skip completely empty rows
                if (empty($name) && empty($email) && empty($password) && empty($role1)) {
                    continue;
                }

                // Row-level validation
                $rowErrors = [];

                if (empty($name)) {
                    $rowErrors[] = 'Nama Lengkap kosong.';
                }
                if (empty($email)) {
                    $rowErrors[] = 'Email kosong.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $rowErrors[] = 'Format email tidak valid.';
                } elseif (User::where('email', $email)->exists()) {
                    $rowErrors[] = 'Email sudah terdaftar.';
                }

                if (!empty($username) && User::where('username', $username)->exists()) {
                    $rowErrors[] = 'Username sudah terdaftar.';
                }

                // Check inside current batch to prevent duplicates
                foreach ($validUsers as $vu) {
                    if ($vu['email'] === $email) {
                        $rowErrors[] = 'Email ganda dalam berkas Excel.';
                    }
                    if (!empty($username) && !empty($vu['username']) && $vu['username'] === $username) {
                        $rowErrors[] = 'Username ganda dalam berkas Excel.';
                    }
                }

                if (empty($password)) {
                    $rowErrors[] = 'Password kosong.';
                } elseif (strlen($password) < 8) {
                    $rowErrors[] = 'Password minimal 8 karakter.';
                }

                $rolesToAssign = [];
                if (empty($role1)) {
                    $rowErrors[] = 'Role 1 wajib diisi.';
                } elseif (!in_array($role1, $validRoles)) {
                    $rowErrors[] = "Role '{$role1}' tidak terdaftar.";
                } else {
                    $rolesToAssign[] = $role1;
                }

                if (!empty($role2)) {
                    if (!in_array($role2, $validRoles)) {
                        $rowErrors[] = "Role '{$role2}' tidak terdaftar.";
                    } else {
                        $rolesToAssign[] = $role2;
                    }
                }

                if (!empty($rowErrors)) {
                    $invalidUsers[] = [
                        'row' => $rowNum,
                        'name' => $name ?: '(Tanpa Nama)',
                        'email' => $email ?: '(Tanpa Email)',
                        'errors' => $rowErrors
                    ];
                } else {
                    $validUsers[] = [
                        'row' => $rowNum,
                        'name' => $name,
                        'email' => $email,
                        'username' => $username ?: null,
                        'password' => $password,
                        'roles' => $rolesToAssign
                    ];
                }
            }

            $this->tempValidUsers = $validUsers;
            $this->tempInvalidUsers = $invalidUsers;
            $this->importDone = true;

            if (count($validUsers) > 0) {
                $this->toastInfo("Pratinjau data siap ditampilkan. Silakan tinjau kembali sebelum menyimpan.");
            } else {
                $this->toastError("Semua baris berkas Excel gagal divalidasi.");
            }

        } catch (\Exception $e) {
            $this->toastError('Gagal memproses file Excel: ' . $e->getMessage());
        }
    }

    public function confirmAndSaveImport()
    {
        if (empty($this->tempValidUsers)) {
            $this->toastError('Tidak ada data user valid untuk disimpan.');
            return;
        }

        try {
            $successCount = 0;
            foreach ($this->tempValidUsers as $vu) {
                // Prevent duplicate save
                if (User::where('email', $vu['email'])->exists()) {
                    continue;
                }

                $user = User::create([
                    'name' => $vu['name'],
                    'email' => $vu['email'],
                    'username' => $vu['username'] ?: null,
                    'password' => \Illuminate\Support\Facades\Hash::make($vu['password']),
                    'is_active' => true,
                ]);

                $user->syncRoles($vu['roles']);
                $successCount++;
            }

            activity('security')
                ->causedBy(auth()->user())
                ->log("Telah melakukan import bulk user pengurus via Excel. Sukses tersimpan: {$successCount} user.");

            $this->toastSuccess("Berhasil mengimpor {$successCount} user baru ke database.");
            $this->closeImportModal();

        } catch (\Exception $e) {
            $this->toastError('Gagal menyimpan data import: ' . $e->getMessage());
        }
    }

    public function getGroupedPermissions()
    {
        return [
            'Core & Person' => [
                'view-any-person' => ['label' => 'Melihat Semua Person', 'desc' => 'Melihat daftar ustadz, pengurus, santri, dan wali.'],
                'view-person' => ['label' => 'Melihat Detail Person', 'desc' => 'Melihat kontak, alamat, dan catatan pribadi per orang.'],
                'create-person' => ['label' => 'Mendaftarkan Person Baru', 'desc' => 'Membuat biodata pengurus / santri baru di sistem.'],
                'update-person' => ['label' => 'Mengubah Data Person', 'desc' => 'Mengedit biodata profil pengurus / santri.'],
                'delete-person' => ['label' => 'Menghapus Data Person', 'desc' => 'Menghapus biodata orang dari database sistem.'],
            ],
            'Kepengurusan' => [
                'view-any-santri' => ['label' => 'Melihat Data Santri', 'desc' => 'Melihat penempatan asrama, total poin, dan absensi santri.'],
                'manage-asrama' => ['label' => 'Mengelola Gedung Asrama', 'desc' => 'Menambah, mengedit, atau menghapus gedung asrama.'],
                'manage-kamar' => ['label' => 'Mengelola Kamar Asrama', 'desc' => 'Mengatur isi kamar, kapasitas, dan menempatkan santri.'],
                'view-perizinan' => ['label' => 'Melihat Buku Perizinan', 'desc' => 'Melihat status checkout / checkin perizinan santri.'],
                'create-perizinan' => ['label' => 'Membuat / Mengajukan Izin', 'desc' => 'Membantu mengisikan form izin keluar santri.'],
                'approve-perizinan' => ['label' => 'Menyetujui Izin Santri', 'desc' => 'Melakukan persetujuan / penolakan izin santri.'],
                'view-pelanggaran' => ['label' => 'Melihat Buku Pelanggaran', 'desc' => 'Melihat daftar pelanggaran dan akumulasi sanksi santri.'],
                'create-pelanggaran' => ['label' => 'Melaporkan Pelanggaran', 'desc' => 'Mencatat pelanggaran tata tertib santri baru.'],
                'manage-kegiatan' => ['label' => 'Mengelola Absensi Kegiatan', 'desc' => 'Mencatat absensi bulk kajian/kegiatan pondok.'],
                'manage-sensus' => ['label' => 'Mengelola Sensus Bulanan', 'desc' => 'Membuka periode, memverifikasi, dan menyetujui sensus santri.'],
            ],
            'Madrasah (Sekolah)' => [
                'view-any-kelas' => ['label' => 'Melihat Daftar Kelas', 'desc' => 'Meninjau pembagian kelas dan jadwal pelajaran.'],
                'manage-kelas' => ['label' => 'Mengelola Rombel Kelas', 'desc' => 'Mengatur kenaikan kelas dan pembagian siswa.'],
                'input-absensi' => ['label' => 'Input Absensi Sekolah', 'desc' => 'Mencatat absensi harian KBM sekolah.'],
                'input-nilai' => ['label' => 'Input Nilai Pelajaran', 'desc' => 'Memasukkan nilai rapor, UTS, dan UAS siswa.'],
                'view-raport' => ['label' => 'Melihat Rapor Santri', 'desc' => 'Melihat dan mengunduh capaian rapor belajar.'],
                'manage-raport' => ['label' => 'Mengelola Nilai Rapor', 'desc' => 'Menyusun nilai deskripsi, KKM, dan status naik kelas.'],
            ],
            'Keuangan (Syahriah)' => [
                'view-tagihan' => ['label' => 'Melihat Tagihan Santri', 'desc' => 'Memonitor riwayat iuran SPP/Syahriah santri.'],
                'create-tagihan' => ['label' => 'Membuat Tagihan Syahriah', 'desc' => 'Membuat tagihan bulanan baru secara bulk.'],
                'record-pembayaran' => ['label' => 'Mencatat Pembayaran Kasir', 'desc' => 'Melayani transaksi cicilan / syahriah lunas.'],
                'void-pembayaran' => ['label' => 'Pembatalan Transaksi Kasir', 'desc' => 'Membatalkan / memvoid kuitansi transaksi salah di kasir.'],
                'manage-billing-config' => ['label' => 'Kelola Master Tarif & Biaya', 'desc' => 'Membuat & mengedit komponen iuran, nominal tarif, dan pengelola.'],
                'manage-setoran-kolektif' => ['label' => 'Kelola Setoran Kolektif', 'desc' => 'Menerima & memverifikasi setoran kasir harian ke bendahara pusat.'],
                'view-laporan-keuangan' => ['label' => 'Melihat Laporan Keuangan', 'desc' => 'Mengakses kas masuk syahriah & laporan tunggakan.'],
                'manage-adjustment' => ['label' => 'Keringanan Biaya (Diskon)', 'desc' => 'Memberikan dispensasi / diskon khusus yatim piatu.'],
            ],
            'Koperasi & Toko' => [
                'view-produk' => ['label' => 'Melihat Daftar Produk', 'desc' => 'Melihat daftar stok barang di koperasi.'],
                'manage-produk' => ['label' => 'Mengelola Katalog Produk', 'desc' => 'Menambah atau mengubah harga barang koperasi.'],
                'manage-stok' => ['label' => 'Mengatur Stok & Opname', 'desc' => 'Melakukan opname fisik dan mencatat stok masuk.'],
                'view-penjualan' => ['label' => 'Melihat Laporan Toko', 'desc' => 'Membaca rekapitulasi penjualan harian koperasi.'],
                'create-penjualan' => ['label' => 'Kasir Penjualan Koperasi', 'desc' => 'Melayani pembelanjaan barang santri di toko.'],
                'manage-supplier' => ['label' => 'Mengelola Supplier', 'desc' => 'Mengatur daftar agen / pemasok barang koperasi.'],
            ],
            'Alur Workflow' => [
                'initiate-workflow' => ['label' => 'Memulai Alur Workflow', 'desc' => 'Memicu pengajuan approval step baru.'],
                'approve-workflow' => ['label' => 'Menyetujui Langkah Workflow', 'desc' => 'Menyetujui status langkah approval aktif.'],
                'reject-workflow' => ['label' => 'Menolak Langkah Workflow', 'desc' => 'Menolak / membatalkan status langkah approval.'],
            ],
            'Keamanan & Sistem' => [
                'manage-master-data' => ['label' => 'Mengelola Data Master', 'desc' => 'Mengatur data wilayah, struktur sekolah, dll.'],
                'manage-users' => ['label' => 'Mengelola Akun Users', 'desc' => 'Membuat akun pengurus, ustadz, dan menonaktifkan akun.'],
                'manage-roles' => ['label' => 'Mengatur Peran & Hak Akses', 'desc' => 'Menyusun permission per role peran (Halaman Ini).'],
                'view-audit-log' => ['label' => 'Melihat Log Audit Sistem', 'desc' => 'Memonitor jejak digital / history klik staf.'],
            ]
        ];
    }

    public function render()
    {
        // 1. Fetch all Roles
        $roles = Role::where('guard_name', 'web')->withCount('users')->get();

        // 2. Fetch Active Role Details
        $activeRole = null;
        if ($this->selectedRoleId) {
            $activeRole = Role::where('id', $this->selectedRoleId)->where('guard_name', 'web')->first();
        }

        // 3. Fetch Users list with pagination, search, and filters
        $usersQuery = User::query()
            ->with('roles')
            ->where(function($q) {
                $q->where('name', 'like', "%{$this->searchUser}%")
                  ->orWhere('email', 'like', "%{$this->searchUser}%")
                  ->orWhere('username', 'like', "%{$this->searchUser}%");
            });

        if ($this->filterRole !== '') {
            $usersQuery->role($this->filterRole);
        }

        if ($this->filterStatus !== '') {
            $usersQuery->where('is_active', $this->filterStatus === 'active');
        }

        $users = $usersQuery->paginate(10);

        // 4. Filter grouped permissions by selected group and/or search query
        $groupedPermissions = $this->getGroupedPermissions();
        if ($this->selectedGroup !== 'all') {
            $groupedPermissions = array_intersect_key($groupedPermissions, [$this->selectedGroup => true]);
        }

        if (!empty($this->searchPermission)) {
            $search = strtolower($this->searchPermission);
            foreach ($groupedPermissions as $groupName => $perms) {
                $filteredPerms = [];
                foreach ($perms as $permName => $details) {
                    if (
                        str_contains(strtolower($permName), $search) ||
                        str_contains(strtolower($details['label']), $search) ||
                        str_contains(strtolower($details['desc']), $search)
                    ) {
                        $filteredPerms[$permName] = $details;
                    }
                }
                if (empty($filteredPerms)) {
                    unset($groupedPermissions[$groupName]);
                } else {
                    $groupedPermissions[$groupName] = $filteredPerms;
                }
            }
        }

        // 5. Fetch 5 most recent security activity logs
        $activityLogs = \Spatie\Activitylog\Models\Activity::where('log_name', 'security')
            ->with('causer')
            ->latest()
            ->limit(5)
            ->get();

        return view('livewire.system.role-permission-manager', [
            'roles' => $roles,
            'activeRole' => $activeRole,
            'users' => $users,
            'groupedPermissions' => $groupedPermissions,
            'activityLogs' => $activityLogs,
        ])->layout('layouts.app');
    }
}
