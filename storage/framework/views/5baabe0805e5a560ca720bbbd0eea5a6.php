<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
    'placeholder' => null,
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
    'options' => [],
    'value' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
    'placeholder' => null,
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
    $selected = (string) old($name, $value);

    $normalized = [];
    foreach ($options as $key => $option) {
        if (is_array($option)) {
            $optionValue = (string) ($option['value'] ?? $option['id'] ?? $key);
            $normalized[$optionValue] = $option['label'] ?? $option['name'] ?? $option['title'] ?? $optionValue;
        } elseif (is_object($option)) {
            $optionValue = (string) ($option->value ?? $option->id ?? $key);
            $normalized[$optionValue] = $option->label ?? $option->name ?? $option->title ?? $optionValue;
        } elseif (is_int($key)) {
            $normalized[(string) $option] = $option;
        } else {
            $normalized[(string) $key] = $option;
        }
    }
?>

<div class="form-field <?php echo e($hasError ? 'has-error' : ''); ?>">
    <?php if($label): ?>
        <label class="form-label" for="<?php echo e($id); ?>">
            <?php echo e($label); ?><?php if($required): ?><span class="req" aria-hidden="true">*</span><?php endif; ?>
        </label>
    <?php endif; ?>

    <select
        id="<?php echo e($id); ?>"
        name="<?php echo e($name); ?>"
        <?php if($required): ?> required aria-required="true" <?php endif; ?>
        <?php if($hasError): ?> aria-invalid="true" <?php endif; ?>
        <?php if($describedBy): ?> aria-describedby="<?php echo e($describedBy); ?>" <?php endif; ?>
        <?php echo e($attributes->merge(['class' => 'form-select'])); ?>

    >
        <?php if($placeholder): ?>
            <option value=""><?php echo e($placeholder); ?></option>
        <?php endif; ?>
        <?php $__currentLoopData = $normalized; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionValue => $optionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($optionValue); ?>" <?php if($selected === (string) $optionValue): echo 'selected'; endif; ?>><?php echo e($optionLabel); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>

    <?php if($hint): ?>
        <p class="form-hint" id="<?php echo e($id); ?>-hint"><?php echo e($hint); ?></p>
    <?php endif; ?>

    <?php if($hasError): ?>
        <p class="form-error" id="<?php echo e($id); ?>-error"><?php echo e($message); ?></p>
    <?php endif; ?>
</div>
<?php /**PATH C:\GYRS portal\resources\views/components/select.blade.php ENDPATH**/ ?>