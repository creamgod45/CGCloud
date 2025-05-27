<div class="panel-field-list">
    @if(!empty($shareTables))
        @foreach($shareTables as $shareTable)
            @if($shareTable instanceof \App\Models\ShareTable)
                <x-panel-field-card :shareTable="$shareTable" :popoverid="$popoverid" :i18-n="$i18N" />
            @endif
        @endforeach
    @endif
</div>
