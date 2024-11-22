<div class="alert alert-{{ $type }}">
    <ul>
        @foreach ($messages as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
