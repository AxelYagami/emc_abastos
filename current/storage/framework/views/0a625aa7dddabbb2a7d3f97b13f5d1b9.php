<?php $__env->startSection('content'); ?>
<div class="bg-white border rounded p-4 mb-4">
  <form class="flex gap-2" method="GET">
    <input name="q" value="<?php echo e($search); ?>" class="border rounded px-3 py-2 w-72" placeholder="Nombre / WhatsApp / Email">
    <button class="px-4 py-2 bg-gray-900 text-white rounded">Buscar</button>
  </form>
</div>

<div class="bg-white border rounded overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 border-b">
      <tr>
        <th class="text-left p-3">Cliente</th>
        <th class="text-left p-3">WhatsApp</th>
        <th class="text-left p-3">Email</th>
        <th class="text-center p-3">Enviar estatus</th>
        <th class="p-3"></th>
      </tr>
    </thead>
    <tbody class="divide-y">
      <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td class="p-3 font-medium"><?php echo e($c->nombre); ?></td>
          <td class="p-3"><?php echo e($c->whatsapp); ?></td>
          <td class="p-3"><?php echo e($c->email); ?></td>
          <td class="p-3 text-center">
            <form method="POST" action="<?php echo e(route('admin.clientes.toggle', $c->id)); ?>">
              <?php echo csrf_field(); ?>
              <button class="px-3 py-1 rounded border <?php echo e($c->enviar_estatus ? 'bg-green-50' : 'bg-gray-50'); ?>">
                <?php echo e($c->enviar_estatus ? 'Sí' : 'No'); ?>

              </button>
            </form>
          </td>
          <td class="p-3 text-right">
            <a class="text-blue-700 hover:underline" href="<?php echo e(route('admin.clientes.show',$c->id)); ?>">Ver</a>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
</div>

<div class="mt-4"><?php echo e($clientes->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title'=>'Clientes','header'=>'Clientes'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\sites\emc_abastos\current\resources\views/admin/clientes/index.blade.php ENDPATH**/ ?>