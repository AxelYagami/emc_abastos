<?php $__env->startSection('content'); ?>
<!-- Hero Slider with Flyers -->
<?php if(isset($flyers) && $flyers->count() > 0): ?>
<div x-data="flyerSlider()" @mouseenter="pause()" @mouseleave="resume()" class="relative overflow-hidden">
  <div class="relative">
    <?php $__currentLoopData = $flyers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $flyer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div x-show="currentSlide === <?php echo e($index); ?>"
           x-transition:enter="transition ease-out duration-500"
           x-transition:enter-start="opacity-0 transform translate-x-full"
           x-transition:enter-end="opacity-100 transform translate-x-0"
           x-transition:leave="transition ease-in duration-300"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="w-full">
        <?php if($flyer->link_url): ?>
          <a href="<?php echo e($flyer->link_url); ?>" class="block">
        <?php endif; ?>
          <div class="aspect-[21/9] md:aspect-[3/1] relative bg-gray-100">
            <img src="<?php echo e($flyer->imagen_url); ?>"
                 alt="<?php echo e($flyer->alt_text ?? $flyer->titulo ?? 'Promoción EMC Abastos'); ?>"
                 class="w-full h-full object-cover"
                 loading="<?php echo e($index === 0 ? 'eager' : 'lazy'); ?>">
            <?php if($flyer->titulo): ?>
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex items-end">
                <div class="max-w-7xl mx-auto px-4 pb-8 w-full">
                  <h1 class="text-2xl md:text-4xl font-bold text-white drop-shadow-lg"><?php echo e($flyer->titulo); ?></h1>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php if($flyer->link_url): ?>
          </a>
        <?php endif; ?>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  <!-- Navigation Arrows -->
  <?php if($flyers->count() > 1): ?>
    <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 hover:bg-white rounded-full flex items-center justify-center text-gray-800 shadow-lg transition z-10">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
    </button>
    <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 hover:bg-white rounded-full flex items-center justify-center text-gray-800 shadow-lg transition z-10">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>
    </button>

    <!-- Dots Indicators -->
    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
      <?php $__currentLoopData = $flyers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $flyer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <button @click="goTo(<?php echo e($index); ?>)"
                :class="currentSlide === <?php echo e($index); ?> ? 'bg-white w-8' : 'bg-white/50 w-2'"
                class="h-2 rounded-full transition-all duration-300"></button>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  <?php endif; ?>
</div>
<?php else: ?>
<!-- Default Hero (when no flyers) -->
<div class="relative bg-gradient-to-r from-primary-600 to-primary-800 text-white overflow-hidden">
  <div class="max-w-7xl mx-auto px-4 py-16 md:py-24">
    <div class="grid md:grid-cols-2 gap-8 items-center">
      <div>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Central de Abastos Digital</h1>
        <p class="text-xl text-primary-100 mb-6">Los mejores productos frescos al mejor precio. Compra fácil, entrega rápida.</p>
        <div class="flex gap-3">
          <a href="#productos" class="bg-white text-primary-700 px-6 py-3 rounded-lg font-semibold hover:bg-primary-50 transition">
            Ver Productos
          </a>
          <a href="<?php echo e(route('cart.index')); ?>" class="border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white/10 transition">
            Mi Carrito
          </a>
        </div>
      </div>
      <div class="hidden md:flex justify-center">
        <div class="w-64 h-64 bg-white/20 rounded-full flex items-center justify-center">
          <svg class="w-32 h-32 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
        </div>
      </div>
    </div>
  </div>
  <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
  <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
</div>
<?php endif; ?>

<!-- Search & Filters -->
<div id="productos" class="max-w-7xl mx-auto px-4 py-8">
  <div class="bg-white rounded-xl shadow-sm border p-4 mb-8">
    <form method="GET" class="flex flex-col md:flex-row gap-4">
      <div class="flex-1">
        <div class="relative">
          <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Buscar productos..."
            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
          <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>
      </div>
      <select name="categoria_id" class="px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
        <option value="">Todas las categorías</option>
        <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($cat->id); ?>" <?php echo e($categoriaId == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->nombre); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <button type="submit" class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition flex items-center justify-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        Buscar
      </button>
    </form>
  </div>

  <!-- Products Grid -->
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
    <?php $__empty_1 = true; $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="bg-white rounded-xl shadow-sm border overflow-hidden hover:shadow-lg transition group" x-data="{ qty: 1 }">
        <!-- Product Image -->
        <a href="<?php echo e(route('store.producto', $producto)); ?>" class="block aspect-square bg-gray-100 relative overflow-hidden">
          <?php if($producto->imagen_url): ?>
            <img src="<?php echo e($producto->imagen_url); ?>" alt="<?php echo e($producto->nombre); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
          <?php else: ?>
            <div class="w-full h-full flex items-center justify-center text-gray-300">
              <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
          <?php endif; ?>
          <?php if($producto->categoria): ?>
            <span class="absolute top-2 left-2 bg-primary-600 text-white text-xs px-2 py-1 rounded-full"><?php echo e($producto->categoria->nombre); ?></span>
          <?php endif; ?>
        </a>

        <!-- Product Info -->
        <div class="p-4">
          <a href="<?php echo e(route('store.producto', $producto)); ?>" class="block">
            <h3 class="font-semibold text-gray-800 group-hover:text-primary-600 transition line-clamp-2"><?php echo e($producto->nombre); ?></h3>
          </a>
          <div class="mt-2 flex items-baseline gap-2">
            <span class="text-2xl font-bold text-primary-600">$<?php echo e(number_format($producto->precio, 2)); ?></span>
          </div>

          <!-- Quantity Selector -->
          <div class="mt-3 flex items-center gap-2">
            <div class="flex items-center border rounded-lg">
              <button type="button" @click="qty = Math.max(1, qty - 1)" class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
              </button>
              <span class="px-3 py-2 font-semibold min-w-[40px] text-center" x-text="qty">1</span>
              <button type="button" @click="qty = Math.min(99, qty + 1)" class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
              </button>
            </div>
          </div>

          <!-- Add to Cart -->
          <form method="POST" action="<?php echo e(route('cart.add')); ?>" class="mt-3">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="producto_id" value="<?php echo e($producto->id); ?>">
            <input type="hidden" name="qty" :value="qty">
            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white py-2.5 rounded-lg font-medium transition flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              Agregar
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="col-span-full text-center py-16">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">No hay productos disponibles</h3>
        <p class="text-gray-500">Intenta con otra búsqueda o categoría</p>
      </div>
    <?php endif; ?>
  </div>

  <!-- Pagination -->
  <?php if($productos->hasPages()): ?>
    <div class="mt-8">
      <?php echo e($productos->links()); ?>

    </div>
  <?php endif; ?>
</div>

<script>
function flyerSlider() {
  return {
    currentSlide: 0,
    totalSlides: <?php echo e(isset($flyers) ? $flyers->count() : 0); ?>,
    interval: null,
    autoplayDelay: 5000,

    init() {
      if (this.totalSlides > 1) {
        this.startAutoplay();
      }
    },

    startAutoplay() {
      this.interval = setInterval(() => this.next(), this.autoplayDelay);
    },

    pause() {
      if (this.interval) {
        clearInterval(this.interval);
        this.interval = null;
      }
    },

    resume() {
      if (!this.interval && this.totalSlides > 1) {
        this.startAutoplay();
      }
    },

    next() {
      this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
    },

    prev() {
      this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
    },

    goTo(index) {
      this.currentSlide = index;
    }
  }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\sites\emc_abastos\current\resources\views/store/index.blade.php ENDPATH**/ ?>