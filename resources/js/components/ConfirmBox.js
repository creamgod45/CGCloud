import * as utils from './utils.js';

function ConfirmBox(){
    let confirmBoxes = document.querySelectorAll('.confirm-box:not(.confirm-box-rendered)');
    for (let confirmBox of confirmBoxes) {
        //console.log(confirmBox);
        //console.log("init");
        confirmBox.classList.add('confirm-box-rendered');
        let dataset = confirmBox.dataset;
        let title = dataset.title;
        let content = dataset.confirmboxcontent;
        let type = dataset.type;
        let fn = dataset.fn;
        confirmBox.onclick = () => {
            console.log("onclick");
            switch(fn){
                case "shareable_delete_file":
                    let parent = dataset.parent;
                    if(parent === undefined) return;
                    let element = document.querySelector(parent);
                    if(element === null) return;
                    element.hidePopover();
                    break;
            }
            utils.confirmDialog(title, content, type, ()=>{
                switch(fn){
                    case "shareable_delete_file":
                        let href1 = dataset.href;
                        window.location.href = href1;
                        break;
                    case "shareable_delete":
                        let href = dataset.href;
                        window.location.href = href;
                        break;
                }
            }, ()=> {
                switch(fn){
                    case "shareable_delete_file":
                        let parent = dataset.parent;
                        if(parent === undefined) return;
                        let element = document.querySelector(parent);
                        if(element === null) return;
                        element.showPopover();
                        break;
                }
            });
        };
    }
}
setInterval(ConfirmBox, 100);
document.addEventListener('CGCONFIRMBOX::init', ConfirmBox);
document.addEventListener('DOMContentLoaded', ConfirmBox);
