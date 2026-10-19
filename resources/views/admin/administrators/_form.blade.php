@php
    $selected = old('permissions', $administrator->permissions ?? []);
    $isSuperAdmin = $administrator->exists && $administrator->isSuperAdmin();
@endphp

<div class="grid grid-2">
    <x-input
        name="name"
        label="Full name"
        :value="$administrator->name"
        :required="true"
        autocomplete="off"
    />

    <x-input
        name="email"
        label="Official email address"
        type="email"
        :value="$administrator->email"
        :required="true"
        autocomplete="off"
    />
</div>

<div class="grid grid-2">
    <x-input
        name="phone"
        label="Contact number"
        :value="$administrator->phone"
        autocomplete="off"
        hint="Optional."
    />

    <x-input
        name="password"
        :label="$administrator->exists ? 'Reset password' : 'Temporary password'"
        type="password"
        :required="! $administrator->exists"
        autocomplete="new-password"
        :hint="$administrator->exists ? 'Leave blank to keep the current password.' : 'Minimum 8 characters.'"
    />
</div>

<x-input
    name="password_confirmation"
    label="Confirm password"
    type="password"
    :required="! $administrator->exists"
    autocomplete="new-password"
/>

<fieldset class="permission-fieldset">
    <legend class="form-label">Access permissions</legend>

    @if ($isSuperAdmin)
        <p class="form-hint">This is a super administrator account with unrestricted access to every section. Permissions cannot be limited here.</p>
    @else
        <p class="form-hint">Grant only the access this administrator needs. Sections without a permission stay hidden from them.</p>
    @endif

    @error('permissions')
        <p class="form-error">{{ $message }}</p>
    @enderror

    @foreach ($permissionGroups as $group => $permissions)
        <div class="permission-group">
            <p class="permission-group-title">{{ $group }}</p>
            <div class="permission-grid">
                @foreach ($permissions as $key => $label)
                    <label class="checkbox-field">
                        <input
                            type="checkbox"
                            name="permissions[]"
                            value="{{ $key }}"
                            @checked(in_array($key, $selected, true))
                            @disabled($isSuperAdmin)
                        >
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    @endforeach
</fieldset>
