<?php $__env->startSection('content'); ?>
<div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between mb-4">
  <form method="GET" class="flex gap-2">
    <input name="q" value="<?php echo e($search); ?>" class="border rounded px-3 py-2 w-72" placeholder="Buscar producto">
    <button class="px-4 py-2 bg-gray-900 text-white rounded">Buscar</button>
  </form>
  <a href="<?php echo e(route('admin.productos.create')); ?>" class="px-4 py-2 rounded bg-black text-white text-center">Nuevo</a>
</div>

<div class="bg-white border rounded overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 border-b">
      <tr>
        <th class="text-left p-3">Nombre</th>
        <th class="text-left p-3">Categoría</th>
        <th class="text-right p-3">Precio</th>
        <th class="text-center p-3">Activo</th>
        <th class="p-3"></th>
      </tr>
    </thead>
    <tbody class="divide-y">
      <?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td class="p-3 font-medium"><?php echo e($p->nombre); ?></td>
          <td class="p-3 text-gray-600"><?php echo e($p->categoria?->nombre); ?></td>
          <td class="p-3 text-right">$<?php echo e(number_format($p->precio,2)); ?></td>
          <td class="p-3 text-center"><?php echo e($p->activo ? 'Sí' : 'No'); ?></td>
          <td class="p-3 text-right">
            <a class="text-blue-700 hover:underline" href="<?php echo e(route('admin.productos.edit',$p->id)); ?>">Editar</a>
            <form method="POST" action="<?php echo e(route('admin.productos.destroy',$p->id)); ?>" class="inline">
              <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
              <button class="text-red-700 hover:underline ml-2" onclick="return confirm('¿Eliminar?')">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
</div>

<div class="mt-4"><?php echo e($productos->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title'=>'Productos','header'=>'Productos'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\sites\emc_abastos\current\resources\views/admin/productos/index.blade.php ENDPATH**/ ?>