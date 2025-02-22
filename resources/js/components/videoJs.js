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
        if(width !== undefined){
            if (width.indexOf('%') !== -1) {
                width = window.innerWidth * parseInt(width.replace('%', '')) / 100;
            }
        }
        let height = vjs.dataset.height;
        if(height !== undefined){
            if (height.indexOf('%') !== -1) {
                height = window.innerHeight * parseInt(height.replace('%', '')) / 100;
            }
        }
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
        let type = vjs.dataset.type ?? 'normal';
        let minetype = vjs.dataset.minetype ?? 'video/mp4';
        if(src === undefined) continue;
        let options = {
            controls: controls,
            autoplay: autoplay,
            preload: preload,
            language: 'zh-TW',
            playbackRates: [0.5, 1, 2, 5, 10],
            techOrder: [ 'chromecast', 'html5' ],
            width: width ?? '300px',
        };
        if(height !== undefined) {
            options.height = height;
        }
        let player = videojs(vjs, options);
        player.ready(function() {
            if(type === "dash") {
                player.src({
                    src: src,
                    type: 'application/dash+xml'
                });
            } else {
                player.src({
                    src: src,
                    type: minetype,
                });
            }
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
