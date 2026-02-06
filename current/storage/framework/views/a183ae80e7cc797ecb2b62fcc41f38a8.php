<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Usuarios</h1>
            <p class="text-gray-500">Gestion de usuarios del sistema</p>
        </div>
        <a href="<?php echo e(route('admin.usuarios.create')); ?>"
           class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Nuevo Usuario
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold">
                                <?php echo e(strtoupper(substr($usuario->name, 0, 1))); ?>

                            </div>
                            <div class="ml-4">
                                <div class="font-medium text-gray-900"><?php echo e($usuario->name); ?></div>
                                <?php if($usuario->whatsapp): ?>
                                    <div class="text-sm text-gray-500"><?php echo e($usuario->whatsapp); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-600"><?php echo e($usuario->email); ?></td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            <?php $__currentLoopData = $usuario->empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-700">
                                    <?php echo e($emp->nombre); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if($usuario->activo ?? true): ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                        <?php else: ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="<?php echo e(route('admin.usuarios.edit', $usuario->id)); ?>" class="text-primary-600 hover:text-primary-900 mr-3">Editar</a>
                        <form method="POST" action="<?php echo e(route('admin.usuarios.toggle', $usuario->id)); ?>" class="inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="text-gray-600 hover:text-gray-900 mr-3">
                                <?php echo e(($usuario->activo ?? true) ? 'Desactivar' : 'Activar'); ?>

                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        No hay usuarios registrados
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if($usuarios->hasPages()): ?>
            <div class="px-6 py-4 border-t">
                <?php echo e($usuarios->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Usuarios', 'header' => 'Usuarios'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\sites\emc_abastos\current\resources\views/admin/usuarios/index.blade.php ENDPATH**/ ?>