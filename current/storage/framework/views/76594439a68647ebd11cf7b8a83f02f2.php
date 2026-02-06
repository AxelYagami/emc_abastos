<?php $__env->startSection('content'); ?>
<div class="grid md:grid-cols-3 gap-4">
  <a class="bg-white border rounded p-4 hover:bg-gray-50" href="<?php echo e(route('ops.ordenes.hoy')); ?>">
    <div class="font-semibold">Lista del día</div>
    <div class="text-xs text-gray-500 mt-1">Órdenes de hoy, acciones rápidas.</div>
  </a>
  <a class="bg-white border rounded p-4 hover:bg-gray-50" href="<?php echo e(route('ops.ordenes.index')); ?>">
    <div class="font-semibold">Órdenes</div>
    <div class="text-xs text-gray-500 mt-1">Búsqueda y filtros.</div>
  </a>
  <a class="bg-white border rounded p-4 hover:bg-gray-50" href="<?php echo e(route('ops.whatsapp.index')); ?>">
    <div class="font-semibold">WhatsApp</div>
    <div class="text-xs text-gray-500 mt-1">Reintentar fallidos / revisar logs.</div>
  </a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.ops', ['title'=>'Ops Hub'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\sites\emc_abastos\current\resources\views/operaciones/hub/index.blade.php ENDPATH**/ ?>