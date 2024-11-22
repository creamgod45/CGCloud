import axios from "axios";

function setDark(type) {
    axios.post('/clientconfig', {
        theme: type,
    }, {
        adapter: 'fetch',
        method: "POST",
    }).then((response) => {
        console.log(response.data);
    });
}

function getOnclick() {
    console.log('click');
    if (localStorage.getItem('theme') === 'light') {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
        setDark('dark');
    } else {
        localStorage.setItem('theme', 'light');
        setDark('light');
        document.documentElement.classList.remove('dark');
    }
}

function dark() {
    axios.post('/clientconfig', {}, {
        adapter: 'fetch',
        method: "POST",
    }).then((response) => {
        console.log(response.data);
    });
    if (localStorage.theme === null && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else if (localStorage.theme === null) {
        localStorage.setItem('theme', 'light');
    }

    // On page load or when changing themes, best to add inline in `head` to avoid FOUC
    if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    let darkModeTrigger = document.querySelectorAll(".dark-mode-trigger");
    for (let darkModeTriggerElement of darkModeTrigger) {
        console.log(darkModeTriggerElement);
        darkModeTriggerElement.onclick = getOnclick;
    }
}
document.addEventListener("CG::Dark", getOnclick);
document.addEventListener("DOMContentLoaded", function () {
    dark();
});
