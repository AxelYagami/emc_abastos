<?php $__env->startSection('content'); ?>
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="<?php echo e(route('admin.empresas.index')); ?>" class="text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a Empresas
        </a>
    </div>

    <form method="POST" action="<?php echo e(route('admin.empresas.update', $empresa->id)); ?>" enctype="multipart/form-data" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <!-- Tabs -->
        <div x-data="{ tab: 'branding' }" class="bg-white rounded-lg shadow">
            <div class="border-b">
                <nav class="flex -mb-px">
                    <button type="button" @click="tab = 'branding'"
                            :class="tab === 'branding' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="py-4 px-6 border-b-2 font-medium text-sm">
                        Branding
                    </button>
                    <button type="button" @click="tab = 'pagos'"
                            :class="tab === 'pagos' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="py-4 px-6 border-b-2 font-medium text-sm">
                        Pagos
                    </button>
                    <button type="button" @click="tab = 'catalogo'"
                            :class="tab === 'catalogo' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="py-4 px-6 border-b-2 font-medium text-sm">
                        Catalogo
                    </button>
                </nav>
            </div>

            <!-- Branding Tab -->
            <div x-show="tab === 'branding'" class="p-6 space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre interno *</label>
                        <input type="text" name="nombre" value="<?php echo e(old('nombre', $empresa->nombre)); ?>" required
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug (URL)</label>
                        <input type="text" name="slug" value="<?php echo e(old('slug', $empresa->slug)); ?>"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre comercial (display)</label>
                        <input type="text" name="brand_nombre_publico" value="<?php echo e(old('brand_nombre_publico', $empresa->brand_nombre_publico)); ?>"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del sistema (app_name)</label>
                        <input type="text" name="app_name" value="<?php echo e(old('app_name', $empresa->getSetting('app_name'))); ?>"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                    <?php if($empresa->logo_path): ?>
                        <div class="mb-2 flex items-center gap-3">
                            <img src="<?php echo e(asset('storage/' . $empresa->logo_path)); ?>" class="h-16 w-auto rounded">
                            <label class="flex items-center text-sm text-gray-600">
                                <input type="checkbox" name="remove_logo" value="1" class="mr-2">
                                Eliminar logo actual
                            </label>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="logo" accept="image/*"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color primario</label>
                        <input type="color" name="primary_color" value="<?php echo e(old('primary_color', $empresa->getSetting('primary_color') ?? '#16a34a')); ?>"
                               class="w-full h-10 border rounded-lg cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color secundario</label>
                        <input type="color" name="secondary_color" value="<?php echo e(old('secondary_color', $empresa->getSetting('secondary_color') ?? '#6b7280')); ?>"
                               class="w-full h-10 border rounded-lg cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color acento</label>
                        <input type="color" name="accent_color" value="<?php echo e(old('accent_color', $empresa->getSetting('accent_color') ?? '#3b82f6')); ?>"
                               class="w-full h-10 border rounded-lg cursor-pointer">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tema</label>
                    <select name="theme_id" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Sin tema</option>
                        <?php $__currentLoopData = $themes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $theme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($theme->id); ?>" <?php echo e($empresa->theme_id == $theme->id ? 'selected' : ''); ?>>
                                <?php echo e($theme->nombre); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="activa" value="1" <?php echo e($empresa->activa ? 'checked' : ''); ?> id="activa"
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="activa" class="ml-2 text-sm text-gray-700">Empresa activa</label>
                </div>
            </div>

            <!-- Pagos Tab -->
            <div x-show="tab === 'pagos'" x-cloak class="p-6 space-y-4">
                <?php if($empresa->hasMercadoPago()): ?>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center gap-2 text-green-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="font-medium">MercadoPago configurado</span>
                        </div>
                    </div>
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Access Token</label>
                    <input type="password" name="mp_access_token" value="<?php echo e(old('mp_access_token', $empresa->getSetting('mp_access_token'))); ?>"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 font-mono text-sm"
                           placeholder="APP_USR-...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Public Key</label>
                    <input type="text" name="mp_public_key" value="<?php echo e(old('mp_public_key', $empresa->getSetting('mp_public_key'))); ?>"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 font-mono text-sm"
                           placeholder="APP_USR-...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Webhook Secret (opcional)</label>
                    <input type="text" name="mp_webhook_secret" value="<?php echo e(old('mp_webhook_secret', $empresa->getSetting('mp_webhook_secret'))); ?>"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 font-mono text-sm">
                </div>
            </div>

            <!-- Catalogo Tab -->
            <div x-show="tab === 'catalogo'" x-cloak class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL imagen default para productos</label>
                    <input type="url" name="default_product_image_url"
                           value="<?php echo e(old('default_product_image_url', $empresa->getSetting('default_product_image_url'))); ?>"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"
                           placeholder="https://...">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="<?php echo e(route('admin.empresas.index')); ?>"
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

<?php echo $__env->make('layouts.admin', ['title' => 'Editar Empresa', 'header' => 'Editar Empresa'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\sites\emc_abastos\current\resources\views/admin/empresas/edit.blade.php ENDPATH**/ ?>