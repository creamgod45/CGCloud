import videojs from 'video.js';
import 'videojs-playlist';
import 'videojs-playlist-ui';
import airplay from '@silvermine/videojs-airplay';
import 'videojs-contrib-quality-menu';
import '@theonlyducks/videojs-zoom';
import 'videojs-contrib-dash';
import chromecast from '@silvermine/videojs-chromecast';
import './chromecast.js';
import 'videojs-hls-quality-selector';
import 'videojs-persist';
import 'videojs-landscape-fullscreen';

chromecast(videojs, {
    preloadWebComponents: true,
});
airplay(videojs);

window.SILVERMINE_VIDEOJS_CHROMECAST_CONFIG = {
    preloadWebComponents: true,
};

function vjs() {
    let vjss = document.querySelectorAll('.vjs');
    for (let vjs of vjss) {
        let src = vjs.dataset.src;
        let width = vjs.dataset.width;
        let controls = vjs.dataset.controls;
        if(controls === undefined)
            controls = true;
        else
            controls = controls === 'true';
        let autoplay = vjs.dataset.autoplay === 'true';
        let preload = vjs.dataset.preload ?? 'auto';
        let persist = vjs.dataset.persist === 'true';
        let landscapeFullscreen = vjs.dataset.landscapeFullscreen === 'true';
        let chromecast = vjs.dataset.chromecast === 'true';
        let zoom = vjs.dataset.zoom === 'true';
        let airplay = vjs.dataset.airplay === 'true';
        let playerlist = vjs.dataset.playerlist === 'true';
        let playerlistjson = vjs.dataset.playerlistjson === 'true';
        if(src === undefined) continue;
        let player = videojs(vjs, {
            controls: controls,
            autoplay: autoplay,
            preload: preload,
            language: 'zh-TW',
            playbackRates: [0.5, 1, 2, 5, 10],
            techOrder: [ 'chromecast', 'html5' ],
            width: width ?? '300px',
        });
        player.ready(function() {
            player.src({
                src: src,
                type: 'application/dash+xml'
            });
        });
        player.qualityMenu();

        if(persist) {
            player.persist();
        }
        if(landscapeFullscreen) {
            player.landscapeFullscreen();
        }
        if(chromecast) {
            player.chromecast();
        }
        if(zoom) {
            player.zoomPlugin();
        }
        if(airplay)
            player.airPlay();
        if(playerlist) {
            player.playlist();
            player.playlistUi();
        }
        vjs.cgplayer = player;
        vjs.classList.add('vjsed');
    }
}
document.addEventListener('CG::Video', function (e) {
    /**
     * @var {HTMLVideoElement}
     */
    let detail = e.detail;
    detail.onmouseenter = function () {};
    detail.setAttribute('onmouseenter', null);
    vjs();

})
document.addEventListener('DOMContentLoaded', function () {
    vjs();
});