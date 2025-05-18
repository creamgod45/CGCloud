<div class="alert alert-{{ $type }} {{$customClass}}">
    <ul>
        @foreach ($messages as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
