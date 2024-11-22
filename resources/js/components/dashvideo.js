import dashjs from 'dashjs';

function dashvideo() {
    let dashs = document.querySelectorAll('.dashvideo');
    for (let dash of dashs) {
        if (!dash.classList.contains('dashvideoed')) {
            let src = dash.dataset.src;
            let autoplay = dash.dataset.autoplay === "true";
            let starttime = parseInt(dash.dataset.starttime) ?? 0;
            let mediaPlayerClass = dashjs.MediaPlayer().create();
            mediaPlayerClass.initialize(dash, src, autoplay, starttime);
            dash.classList.add('dashvideoed');
            dash.CGmediaPlayer = mediaPlayerClass;
            const bitrates = mediaPlayerClass.getBitrateInfoListFor('video') // 获取视频轨道的所有画质信息
            console.log(bitrates); // 可在控制台查看各质量层的详细信息
        }
    }
}

document.addEventListener('DOMContentLoaded', dashvideo);
