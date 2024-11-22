<button class="{{$btnClassList}}" popovertarget="{{$id}}">{{$popoverBtnMessage}}</button>
<style>
    #{{$id}}::backdrop {
        @if ($popoverOptions->blurBackground)
          backdrop-filter: blur(10px);
        @endif
        @if ($popoverOptions->blackBackground)
          background: rgba(60, 57, 51, 0.61);
    @endif


    }
</style>
<div id="{{$id}}" {{ $attributes->merge(['class' => 'popover']) }} popover>
    <div class="popover-closebtn btn btn-circle btn-ripple"><i class="fa-solid fa-x"></i></div>
    <div class="popover-title">{{$popoverTitle}}</div>
    {{$slot}}
</div>
