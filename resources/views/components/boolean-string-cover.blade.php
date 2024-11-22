@if($value==="true")
    <div class="mx-1 my-3 btn btn-ripple btn-md-strip btn-pill btn-success"><i
            class="fa-solid fa-check"></i>&nbsp;{{$result}}</div>
@else
    <div class="mx-1 my-3 btn btn-ripple btn-md-strip btn-pill btn-error"><i class="fa-solid fa-x"></i>&nbsp;{{$result}}
    </div>
@endif
