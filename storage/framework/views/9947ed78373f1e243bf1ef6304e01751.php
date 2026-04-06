<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($member ? 'Edit' : 'Tambah'); ?> Petugas — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6"><?php echo e($member ? 'Edit' : 'Tambah'); ?> Petugas Non-Dokter</h1>

    <?php if($errors->any()): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4 text-sm">
            <ul class="list-disc list-inside"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
    <?php endif; ?>

    <form method="POST"
          action="<?php echo e($member ? route('master.staff.update', $member->id) : route('master.staff.store')); ?>"
          class="bg-white rounded shadow p-6 space-y-4">
        <?php echo csrf_field(); ?>
        <?php if($member): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
            <input type="text" name="username" value="<?php echo e(old('username', $member?->username)); ?>"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Password <?php echo e($member ? '(kosongkan jika tidak diubah)' : ''); ?> <span class="text-red-500"><?php echo e($member ? '' : '*'); ?></span>
            </label>
            <input type="password" name="password"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                   <?php echo e($member ? '' : 'required'); ?> minlength="8">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Peran / Unit Kerja <span class="text-red-500">*</span></label>
            <select name="role" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                <option value="">-- Pilih Peran --</option>
                <?php $__currentLoopData = $staffRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($role); ?>" <?php echo e(old('role', $member?->role) === $role ? 'selected' : ''); ?>>
                        <?php echo e(ucfirst(str_replace('_', ' ', $role))); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active"
                   <?php echo e(old('is_active', $member?->is_active ?? true) ? 'checked' : ''); ?>>
            <label for="is_active" class="text-sm text-gray-700">Akun Aktif</label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 text-sm">Simpan</button>
            <a href="<?php echo e(route('master.staff.index')); ?>" class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300 text-sm">Batal</a>
        </div>
    </form>
</div>
</body>
</html>
<?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/master/staff/form.blade.php ENDPATH**/ ?>