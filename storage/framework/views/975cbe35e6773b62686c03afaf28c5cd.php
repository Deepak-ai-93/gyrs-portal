<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'title' => null,
    'wide' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'name',
    'title' => null,
    'wide' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="modal" data-modal="<?php echo e($name); ?>" hidden>
    <div class="modal-overlay" data-modal-close></div>

    <div
        class="modal-panel <?php echo e($wide ? 'is-wide' : ''); ?>"
        role="dialog"
        aria-modal="true"
        <?php if($title): ?> aria-labelledby="modal-<?php echo e($name); ?>-title" <?php endif; ?>
        tabindex="-1"
    >
        <div class="modal-head">
            <?php if($title): ?>
                <h2 class="modal-title" id="modal-<?php echo e($name); ?>-title"><?php echo e($title); ?></h2>
            <?php endif; ?>
            <button type="button" class="modal-close" data-modal-close aria-label="Close dialog">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <?php echo e($slot); ?>

        </div>

        <?php if(isset($footer)): ?>
            <div class="modal-foot"><?php echo e($footer); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\GYRS portal\resources\views/components/modal.blade.php ENDPATH**/ ?>