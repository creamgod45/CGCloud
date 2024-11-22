@use(App\Lib\Utils\ENotificationType)
@php
    if(isset($id) && $id === "placeholder"){
        $id = "%id%";
    }else{
        $id = "N".Str::random(7);
    }
@endphp
@if($millisecond===-1)
    @php
        $s = "%second%";
    @endphp
@else
    @php
        $s = $millisecond;
    @endphp
@endif

<div id="{{$id}}" data-seconds="{!! $s !!}"
     class="item notification-@if($type instanceof ENotificationType){{$type->name}}@else%type%@endif">
    @if($millisecond !== 4900)
        <style>
            .notification

            #
            {{$id}}::after {
                animation: notificationtimeline @if($millisecond!==-1) {{$millisecond-200}}ms @else {{$s}}ms @endif ease-in-out !important;
            }
        </style>
    @endif
    <div class="icon">
        @if($type === "info" || $type === ENotificationType::info)
            <i class="fa-solid fa-info"></i>
        @elseif($type === "warning" || $type === ENotificationType::warning)
            <i class="fa-solid fa-triangle-exclamation"></i>
        @elseif($type === "error" || $type === ENotificationType::error)
            <i class="fa-solid fa-xmark"></i>
        @elseif($type === "success" || $type === ENotificationType::success)
            <i class="fa-solid fa-check"></i>
        @endif
    </div>
    <div class="context">
        <div class="title">{!! $title !!}</div>
        <div class="description">{!! $description !!}</div>
    </div>
    <div class="close-btn">&times;</div>
</div>
