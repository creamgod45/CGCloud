@php
    /***
     * @var string[] $urlParams 當前 URL 參數
     * @var array $moreParams 更多參數
     * @var \App\Lib\I18N\I18N $i18N I18N 本地化語言系統
     * @var Request $request 請求
     * @var string $fingerprint 客戶端指紋
     * @var string $theme 主題
     * @var \App\Models\ShopConfig[] $styleConfig 系統設定
     */
@endphp
<style>
:root{
    --color7: {{ \App\Lib\Utils\Utils::default($styleConfig["ShopMainColor"]["value"], "rgb(251, 191, 36)") }};
    --color7-50: {{ \App\Lib\Utils\Utils::default($styleConfig["ShopSecondaryColor"]["value"], "rgb(245, 158, 11)") }};
    --menu-bar: {{ \App\Lib\Utils\Utils::default($styleConfig["ShopMenuColor"]["value"], "rgb(245, 158, 11)") }};
}
</style>
