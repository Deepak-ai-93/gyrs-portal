<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['status' => null]));

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

foreach (array_filter((['status' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $key = strtolower(str_replace([' ', '-'], '_', (string) $status));

    $map = [
        'draft' => ['neutral', 'Draft'],
        'published' => ['success', 'Published'],
        'open' => ['success', 'Open'],
        'closed' => ['neutral', 'Closed'],
        'archived' => ['neutral', 'Archived'],
        'expired' => ['danger', 'Expired'],
        'applied' => ['info', 'Applied'],
        'under_review' => ['warning', 'Under review'],
        'shortlisted' => ['success', 'Shortlisted'],
        'interview_call' => ['success', 'Interview call'],
        'placed' => ['success', 'Placed'],
        'rejected' => ['danger', 'Rejected'],
        'pending' => ['warning', 'Pending'],
        'verified' => ['success', 'Verified'],
    ];

    [$variant, $label] = $map[$key] ?? ['neutral', $status ? \Illuminate\Support\Str::headline((string) $status) : 'Unknown'];
?>

<span class="badge badge-<?php echo e($variant); ?> status-badge" data-status="<?php echo e($key); ?>"><?php echo e($label); ?></span>
<?php /**PATH C:\GYRS portal\resources\views/components/status-badge.blade.php ENDPATH**/ ?>