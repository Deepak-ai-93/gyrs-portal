@if ($errors->any())
    <div class="flash-region">
        <x-alert variant="danger">
            <strong>Please review the highlighted fields.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    </div>
@endif
