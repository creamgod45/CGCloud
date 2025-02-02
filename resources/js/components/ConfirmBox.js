import * as utils from './utils.js';
import axios from "axios";

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
                case "shareable_conversion_file":
                    let parent1 = dataset.parent;
                    if(parent1 === undefined) return;
                    let element1 = document.querySelector(parent1);
                    if(element1 === null) return;
                    element1.hidePopover();
                    break;
            }
            utils.confirmDialog(title, content, type, ()=>{
                switch(fn){
                    case "shareable_conversion_file":
                        let href3 = dataset.href;
                        let id = dataset.id;
                        axios.post(href3, {}, {
                            adapter: 'fetch',
                            method: "POST",
                        }).then(async response => {
                            let data = response.data;
                            console.log(data);
                            let tElement = document.querySelector(id);
                            tElement.innerText = data.message;
                        });
                        break;
                    case "shareable_delete_file":
                        let href1 = dataset.href;
                        window.location.href = href1;
                        break;
                    case "popover_shareable_delete_file":
                        let href2 = dataset.href;
                        window.location.href = href2;
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
                    case "shareable_conversion_file":
                        let parent1 = dataset.parent;
                        if(parent1 === undefined) return;
                        let element2 = document.querySelector(parent1);
                        if(element2 === null) return;
                        element2.showPopover();
                        break;
                }
            });
        };
    }
}
setInterval(ConfirmBox, 100);
document.addEventListener('CGCONFIRMBOX::init', ConfirmBox);
document.addEventListener('DOMContentLoaded', ConfirmBox);
