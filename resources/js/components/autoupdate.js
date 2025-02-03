import axios from "axios";

function autoupdate(){
    let autoupdates = document.querySelectorAll('.autoupdate:not(.autoupdated)');
    for (let _autoupdate of autoupdates) {
        _autoupdate.classList.add('autoupdated');
        let mseconds = _autoupdate.dataset.mseconds ?? 1000;
        let fn = _autoupdate.dataset.fn ?? "";
        let timer = setInterval(function(){
            switch (fn) {
                case 'get_dash_progress':
                    let id = _autoupdate.dataset.id ?? 0;
                    if(id === 0) break;
                    axios.post('/sharetable/get_dash_progress', { id: id }).then(async function(response){
                        let data = await response.data;
                        console.log(data);
                        if(data.message === "success"){
                            _autoupdate.innerText = "浮水印影片 生成中... " + data.value + "%";
                        }
                        if(data.message === "success2"){
                            _autoupdate.innerText = "轉換 Dash 生成中... " + data.value + "%";
                        }
                        if(data.message === "success3"){
                            _autoupdate.innerText = "轉換完成... 100%";
                            _autoupdate.destroy();
                        }
                        if(data.message === "stop"){
                            _autoupdate.innerText = "已轉換";
                            _autoupdate.destroy();
                        }
                    });
                    break;
            }
        }, mseconds);
        _autoupdate.mseconds = mseconds;
        _autoupdate.fn = fn;
        _autoupdate.timer = timer;
        _autoupdate.destroy = function(){
            clearInterval(this.timer);
        }
    }
}

document.addEventListener('CGAUTOUPDATE::init', autoupdate);
document.addEventListener('DOMContentLoaded', autoupdate);
