<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto p-4">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">Crear producto</h1>
    <a class="text-sm underline" href="<?php echo e(route('admin.productos.index')); ?>">Volver</a>
  </div>

  <?php if($errors->any()): ?>
    <div class="mb-4 rounded border p-3">
      <ul class="list-disc pl-5 text-sm">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($e); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" action="<?php echo e(route('admin.productos.store')); ?>" class="space-y-4">
    <?php echo csrf_field(); ?>

    <div>
      <label class="block text-sm font-medium mb-1">Nombre</label>
      <input class="w-full border rounded p-2" name="nombre" value="<?php echo e(old('nombre')); ?>" required>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">SKU</label>
      <input class="w-full border rounded p-2" name="sku" value="<?php echo e(old('sku')); ?>">
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Categoría</label>
      <select class="w-full border rounded p-2" name="categoria_id">
        <option value="">—</option>
        <?php $__currentLoopData = ($categorias ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($c->id); ?>" <?php if(old('categoria_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->nombre); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>

    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-medium mb-1">Precio</label>
        <input class="w-full border rounded p-2" name="precio" value="<?php echo e(old('precio')); ?>" inputmode="decimal" required>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Stock (opcional)</label>
        <input class="w-full border rounded p-2" name="stock" value="<?php echo e(old('stock')); ?>" inputmode="numeric">
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Descripción</label>
      <textarea class="w-full border rounded p-2" rows="4" name="descripcion"><?php echo e(old('descripcion')); ?></textarea>
    </div>

    <div class="flex items-center gap-2">
      <!-- Fix: checkbox unchecked sends nothing -->
      <input type="hidden" name="activo" value="0">
      <input type="checkbox" name="activo" value="1" id="activo" <?php if(old('activo',1)==1): echo 'checked'; endif; ?>>
      <label for="activo" class="text-sm">Activo</label>
    </div>

    <div class="pt-2">
      <button class="px-4 py-2 rounded bg-black text-white">Guardar</button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\sites\emc_abastos\current\resources\views/admin/productos/create.blade.php ENDPATH**/ ?>