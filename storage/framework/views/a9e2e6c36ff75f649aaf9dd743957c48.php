<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'meta' => null,
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
    'title' => null,
    'meta' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div <?php echo e($attributes->merge(['class' => 'card'])); ?>>
    <?php if($title || $meta || isset($actions)): ?>
        <div class="card-head">
            <div class="card-head-text">
                <?php if($title): ?>
                    <h3 class="card-title"><?php echo e($title); ?></h3>
                <?php endif; ?>
                <?php if($meta): ?>
                    <p class="card-meta"><?php echo e($meta); ?></p>
                <?php endif; ?>
            </div>
            <?php if(isset($actions)): ?>
                <div class="card-actions"><?php echo e($actions); ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="card-body">
        <?php echo e($slot); ?>

    </div>

    <?php if(isset($footer)): ?>
        <div class="card-foot"><?php echo e($footer); ?></div>
    <?php endif; ?>
</div>
<?php /**PATH C:\GYRS portal\resources\views/components/card.blade.php ENDPATH**/ ?>