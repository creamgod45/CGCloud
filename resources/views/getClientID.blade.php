@vite(['resources/css/index.css', 'resources/js/index.js', 'resources/js/index_.js',])
@use (App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Utils\Htmlv2;use App\Lib\Utils\Utilsv2;use Illuminate\Http\Request;use Illuminate\Support\Facades\Config)
@php
    /***
     * @var string[] $urlParams 當前 URL 參數
     * @var array $moreParams 更多參數
     * @var I18N $i18N I18N 本地化語言系統
     * @var Request $request 請求
     * @var string $fingerprint 客戶端指紋
     * @var string $theme 主題
     * @var \App\Models\ShopConfig[] $styleConfig 系統設定
     */
    $menu=true;
    $footer=true;
    $ref = false;
    if(!empty($moreParams)){
        if(isset($moreParams[0]['ref'])){
            $ref = $moreParams[0]['ref'];
        }
    }
@endphp
@include("index")
@if($ref)
    @php
        try{
            $ref = Utilsv2::decodeContext($ref);
            echo '<input name="ref" value="'.$ref.'">';
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
        }
    @endphp
@endif
