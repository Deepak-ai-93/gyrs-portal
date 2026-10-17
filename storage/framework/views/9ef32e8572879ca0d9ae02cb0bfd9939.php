<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'label' => null,
    'value' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
    'placeholder' => null,
    'rows' => 4,
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
    'label' => null,
    'value' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
    'placeholder' => null,
    'rows' => 4,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $id = $attributes->get('id') ?: 'field-' . str_replace(['[', ']', '.'], ['-', '', '-'], $name);
    $message = $error ?? (isset($errors) ? $errors->first($name) : null);
    $hasError = filled($message);
    $describedBy = $hasError ? $id . '-error' : (filled($hint) ? $id . '-hint' : null);
?>

<div class="form-field <?php echo e($hasError ? 'has-error' : ''); ?>">
    <?php if($label): ?>
        <label class="form-label" for="<?php echo e($id); ?>">
            <?php echo e($label); ?><?php if($required): ?><span class="req" aria-hidden="true">*</span><?php endif; ?>
        </label>
    <?php endif; ?>

    <textarea
        id="<?php echo e($id); ?>"
        name="<?php echo e($name); ?>"
        rows="<?php echo e($rows); ?>"
        placeholder="<?php echo e($placeholder); ?>"
        <?php if($required): ?> required aria-required="true" <?php endif; ?>
        <?php if($hasError): ?> aria-invalid="true" <?php endif; ?>
        <?php if($describedBy): ?> aria-describedby="<?php echo e($describedBy); ?>" <?php endif; ?>
        <?php echo e($attributes->merge(['class' => 'form-textarea'])); ?>

    ><?php echo e(old($name, $value)); ?></textarea>

    <?php if($hint): ?>
        <p class="form-hint" id="<?php echo e($id); ?>-hint"><?php echo e($hint); ?></p>
    <?php endif; ?>

    <?php if($hasError): ?>
        <p class="form-error" id="<?php echo e($id); ?>-error"><?php echo e($message); ?></p>
    <?php endif; ?>
</div>
<?php /**PATH C:\GYRS portal\resources\views/components/textarea.blade.php ENDPATH**/ ?>