@vite(['resources/js/tinymce.js'])
<div class="{{ $parentClass }}">
    <textarea
        {{ $attributes->merge(['class' => 'tinymceEditor']) }} {{ $attributes }} data-baseurl="{{ url('tinymce') }}"
        rows="5" name="{{ $name }}">{{$slot}}</textarea>
</div>
