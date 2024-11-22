import Pusher from 'pusher-js';
import axios from "axios";
import * as Utils from './utils.js';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Enable pusher logging - don't include this in production
Pusher.logToConsole = true;
let pusher = new Pusher('faa244d58f2ef2333cd9', {
    cluster: 'ap1'
});

const HTMLTemplateNotificationLoadedEvent = new CustomEvent('HTMLTemplateNotificationLoadedEvent', {
    detail: {
        message: "API Call HTMLTemplateNotificationLoadedEvent is ready"
    },
    cancelable: false
});
const BrowserIDLoadedEvent = new CustomEvent('BrowserIDLoadedEvent', {
    detail: {
        message: "API Call userIDLoadedEvent is ready"
    },
    cancelable: false
});
const PusherRenderNotificationEvent = new CustomEvent('PusherRenderNotificationEvent', {
    detail: {
        message: "API Call PusherRenderNotificationEvent is render done"
    },
    cancelable: false
});
let HTMLTemplateNotificationData = "";
let id = "";

axios.post('/HTMLTemplate/Notification', {}, {
    adapter: "fetch",

}).then(async (res) => {
    //console.log(res);
    HTMLTemplateNotificationData = res.data;
    document.dispatchEvent(HTMLTemplateNotificationLoadedEvent);
});

axios.post('/browser', {}, {
    adapter: "fetch",
}).then(async (res) => {
    //console.log(res);
    if (res.status === 200) {
        id = res.data.id;
        //console.log(id);
    }
    document.dispatchEvent(BrowserIDLoadedEvent);
});

document.addEventListener('BrowserIDLoadedEvent', () => {
    //console.log(event);
    //console.log(2);
    let userChannel = pusher.subscribe('Notification.user.' + id);
    userChannel.bind('Notification', function (msg) {
        //console.log(msg);
        insertItemToParentDOM(msg)
        document.dispatchEvent(PusherRenderNotificationEvent);
    });
});

document.addEventListener('HTMLTemplateNotificationLoadedEvent', () => {
    //console.log(event);
    console.log('publicChannel');
    let publicchannel = pusher.subscribe('Notification');
    publicchannel.bind('Notification', function (msg) {
        //console.log(msg);
        insertItemToParentDOM(msg)
        document.dispatchEvent(PusherRenderNotificationEvent);
    });
});

function HTMLFormater(msg) {
    let temp = HTMLTemplateNotificationData;
    temp = temp.replaceAll("%id%", "N" + Utils.generateRandomString(10));
    temp = temp.replaceAll("%type%", msg.type);
    temp = temp.replaceAll("%title%", msg.title);
    temp = temp.replaceAll("%description%", msg.description);
    return temp.replaceAll("%second%", msg.second);
}

function insertItemToParentDOM(msg) {
    let notificationParents = document.querySelectorAll('.notification .item');
    if (notificationParents.length <= 0) {
        let notificationParent = document.querySelector('.notification');
        notificationParent.innerHTML = HTMLFormater(msg);
    } else {
        let notificationParent = notificationParents[notificationParents.length - 1];
        if (notificationParent !== null) {
            notificationParent.insertAdjacentHTML("afterend", HTMLFormater(msg));
        }
    }
}
