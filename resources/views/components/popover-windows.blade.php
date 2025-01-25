<style>
    #{{$id}}:before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        @if ($popoverOptions->blurBackground)
          backdrop-filter: blur(10px);
        @endif
        @if ($popoverOptions->blackBackground)
          background: rgba(60, 57, 51, 0.61);
    @endif


    }
</style>
<div id="{{$id}}" {{ $attributes->merge(['class' => 'dialog-frame']) }}>
    <div class="dialog-vt">
        <div class="dialog-title">
            <span class="dialog-title-field">{{$popoverTitle}}</span>
            <div class="dialog-closebtn btn btn-circle btn-ripple"><i class="fa-solid fa-x"></i></div>
        </div>
        {{$slot}}
    </div>
</div>
