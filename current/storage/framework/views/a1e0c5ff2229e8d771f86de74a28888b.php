<?php $__env->startSection('content'); ?>
<div class="flex items-center justify-between mb-4">
  <div class="text-sm text-gray-600">Organiza tu catálogo.</div>
  <a class="px-4 py-2 rounded bg-black text-white" href="<?php echo e(route('admin.categorias.create')); ?>">Nueva</a>
</div>

<div class="bg-white border rounded overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 border-b">
      <tr>
        <th class="text-left p-3">Nombre</th>
        <th class="text-left p-3">Slug</th>
        <th class="text-center p-3">Orden</th>
        <th class="text-center p-3">Activa</th>
        <th class="p-3"></th>
      </tr>
    </thead>
    <tbody class="divide-y">
      <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td class="p-3 font-medium"><?php echo e($c->nombre); ?></td>
          <td class="p-3 text-gray-600"><?php echo e($c->slug); ?></td>
          <td class="p-3 text-center"><?php echo e($c->orden); ?></td>
          <td class="p-3 text-center"><?php echo e($c->activa ? 'Sí' : 'No'); ?></td>
          <td class="p-3 text-right">
            <a class="text-blue-700 hover:underline" href="<?php echo e(route('admin.categorias.edit',$c->id)); ?>">Editar</a>
            <form method="POST" action="<?php echo e(route('admin.categorias.destroy',$c->id)); ?>" class="inline">
              <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
              <button class="text-red-700 hover:underline ml-2" onclick="return confirm('¿Eliminar?')">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title'=>'Categorías','header'=>'Categorías'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\sites\emc_abastos\current\resources\views/admin/categorias/index.blade.php ENDPATH**/ ?>