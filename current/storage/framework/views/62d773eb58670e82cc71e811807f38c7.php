<?php $__env->startSection('content'); ?>
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="<?php echo e(route('admin.usuarios.index')); ?>" class="text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a Usuarios
        </a>
    </div>

    <form method="POST" action="<?php echo e(route('admin.usuarios.update', $usuario->id)); ?>" class="space-y-6"
          x-data="{ empresasCount: <?php echo e(max(1, count($asignaciones))); ?> }">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="bg-white rounded-lg shadow p-6 space-y-4">
            <h3 class="font-semibold text-gray-800 border-b pb-2">Datos del Usuario</h3>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo *</label>
                    <input type="text" name="name" value="<?php echo e(old('name', $usuario->name)); ?>" required
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $usuario->email)); ?>" required
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nueva contrasena</label>
                    <input type="password" name="password"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"
                           placeholder="Dejar vacio para mantener">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contrasena</label>
                    <input type="password" name="password_confirmation"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                    <input type="tel" name="whatsapp" value="<?php echo e(old('whatsapp', $usuario->whatsapp)); ?>"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefono</label>
                    <input type="tel" name="telefono" value="<?php echo e(old('telefono', $usuario->telefono)); ?>"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="activo" value="1" <?php echo e(($usuario->activo ?? true) ? 'checked' : ''); ?> id="activo"
                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <label for="activo" class="ml-2 text-sm text-gray-700">Usuario activo</label>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 space-y-4">
            <div class="flex justify-between items-center border-b pb-2">
                <h3 class="font-semibold text-gray-800">Asignacion a Empresas</h3>
                <button type="button" @click="empresasCount++"
                        class="text-sm text-primary-600 hover:text-primary-700">
                    + Agregar empresa
                </button>
            </div>

            <?php $asignacionesArray = $asignaciones->values()->all(); ?>

            <template x-for="i in empresasCount" :key="i">
                <div class="grid md:grid-cols-3 gap-4 p-3 bg-gray-50 rounded-lg">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                        <select :name="'empresas[' + (i-1) + '][empresa_id]'"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                            <option value="">Seleccionar...</option>
                            <?php $__currentLoopData = $empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($empresa->id); ?>"><?php echo e($empresa->nombre); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                        <select :name="'empresas[' + (i-1) + '][rol_id]'"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($rol->id); ?>"><?php echo e($rol->nombre); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center">
                            <input type="checkbox" :name="'empresas[' + (i-1) + '][activo]'" value="1" checked
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Activo</span>
                        </label>
                    </div>
                </div>
            </template>
        </div>

        <div class="flex justify-end gap-3">
            <a href="<?php echo e(route('admin.usuarios.index')); ?>"
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Editar Usuario', 'header' => 'Editar Usuario'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\sites\emc_abastos\current\resources\views/admin/usuarios/edit.blade.php ENDPATH**/ ?>