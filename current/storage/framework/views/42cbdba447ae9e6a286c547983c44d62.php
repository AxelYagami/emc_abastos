<?php $__env->startSection('content'); ?>
<div class="bg-white border rounded p-4">
  <div class="flex items-center justify-between">
    <div>
      <div class="font-semibold">Números del vendedor</div>
      <div class="text-xs text-gray-500">Se usan para notificar al vendedor sobre cambios de estatus.</div>
    </div>
    <a class="px-4 py-2 rounded bg-black text-white" href="<?php echo e(route('admin.whatsapp.create')); ?>">Agregar número</a>
  </div>

  <div class="mt-4 overflow-hidden border rounded">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b">
        <tr>
          <th class="text-left p-3">WhatsApp</th>
          <th class="text-center p-3">Activo</th>
          <th class="p-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        <?php $__currentLoopData = $numeros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td class="p-3 font-medium"><?php echo e($n->whatsapp); ?></td>
            <td class="p-3 text-center"><?php echo e($n->activo ? 'Sí' : 'No'); ?></td>
            <td class="p-3 text-right">
              <form method="POST" action="<?php echo e(route('admin.whatsapp.toggle',$n->id)); ?>" class="inline">
                <?php echo csrf_field(); ?>
                <button class="text-blue-700 hover:underline"><?php echo e($n->activo ? 'Desactivar' : 'Activar'); ?></button>
              </form>
              <form method="POST" action="<?php echo e(route('admin.whatsapp.destroy',$n->id)); ?>" class="inline">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button class="text-red-700 hover:underline ml-2" onclick="return confirm('¿Eliminar?')">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>

  <div class="mt-6">
    <div class="font-semibold mb-2">Logs recientes</div>
    <div class="text-xs text-gray-600 mb-2">Usa Ops &gt; Reintentar para re-enviar fallidos.</div>
    <div class="border rounded overflow-hidden">
      <table class="w-full text-xs">
        <thead class="bg-gray-50 border-b">
          <tr>
            <th class="text-left p-2">Fecha</th>
            <th class="text-left p-2">Evento</th>
            <th class="text-left p-2">To</th>
            <th class="text-left p-2">Status</th>
            <th class="text-left p-2">Skipped</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td class="p-2"><?php echo e($l->created_at); ?></td>
              <td class="p-2"><?php echo e($l->evento); ?></td>
              <td class="p-2"><?php echo e($l->to_whatsapp); ?></td>
              <td class="p-2"><?php echo e($l->status); ?></td>
              <td class="p-2"><?php echo e($l->skipped_reason); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title'=>'WhatsApp','header'=>'WhatsApp'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\sites\emc_abastos\current\resources\views/admin/whatsapp/index.blade.php ENDPATH**/ ?>