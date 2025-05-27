function copyer(){
    let elementNodeListOf = document.querySelectorAll(".copyer:not(.copied)");
    for (let copyer of elementNodeListOf) {
        copyer.classList.add("copied");
        let url = copyer.dataset.url;
        if(url === undefined) return false;
        copyer.onclick = () => {
            navigator.clipboard.writeText(url).then(() => {
                console.log('Text copied to clipboard:', url);
            }).catch(err => {
                console.error('Could not copy text: ', err);
            });
            document.dispatchEvent(new CustomEvent("CGTOASTIFY::notice", { detail: { message: "已複製分享網址", destination: url, avatar: "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2NDAgNTEyIj48IS0tIUZvbnQgQXdlc29tZSBGcmVlIDYuNy4yIGJ5IEBmb250YXdlc29tZSAtIGh0dHBzOi8vZm9udGF3ZXNvbWUuY29tIExpY2Vuc2UgLSBodHRwczovL2ZvbnRhd2Vzb21lLmNvbS9saWNlbnNlL2ZyZWUgQ29weXJpZ2h0IDIwMjUgRm9udGljb25zLCBJbmMuLS0+PHBhdGggZmlsbD0iIzYzRTZCRSIgZD0iTTU3OS44IDI2Ny43YzU2LjUtNTYuNSA1Ni41LTE0OCAwLTIwNC41Yy01MC01MC0xMjguOC01Ni41LTE4Ni4zLTE1LjRsLTEuNiAxLjFjLTE0LjQgMTAuMy0xNy43IDMwLjMtNy40IDQ0LjZzMzAuMyAxNy43IDQ0LjYgNy40bDEuNi0xLjFjMzIuMS0yMi45IDc2LTE5LjMgMTAzLjggOC42YzMxLjUgMzEuNSAzMS41IDgyLjUgMCAxMTRMNDIyLjMgMzM0LjhjLTMxLjUgMzEuNS04Mi41IDMxLjUtMTE0IDBjLTI3LjktMjcuOS0zMS41LTcxLjgtOC42LTEwMy44bDEuMS0xLjZjMTAuMy0xNC40IDYuOS0zNC40LTcuNC00NC42cy0zNC40LTYuOS00NC42IDcuNGwtMS4xIDEuNkMyMDYuNSAyNTEuMiAyMTMgMzMwIDI2MyAzODBjNTYuNSA1Ni41IDE0OCA1Ni41IDIwNC41IDBMNTc5LjggMjY3Ljd6TTYwLjIgMjQ0LjNjLTU2LjUgNTYuNS01Ni41IDE0OCAwIDIwNC41YzUwIDUwIDEyOC44IDU2LjUgMTg2LjMgMTUuNGwxLjYtMS4xYzE0LjQtMTAuMyAxNy43LTMwLjMgNy40LTQ0LjZzLTMwLjMtMTcuNy00NC42LTcuNGwtMS42IDEuMWMtMzIuMSAyMi45LTc2IDE5LjMtMTAzLjgtOC42Qzc0IDM3MiA3NCAzMjEgMTA1LjUgMjg5LjVMMjE3LjcgMTc3LjJjMzEuNS0zMS41IDgyLjUtMzEuNSAxMTQgMGMyNy45IDI3LjkgMzEuNSA3MS44IDguNiAxMDMuOWwtMS4xIDEuNmMtMTAuMyAxNC40LTYuOSAzNC40IDcuNCA0NC42czM0LjQgNi45IDQ0LjYtNy40bDEuMS0xLjZDNDMzLjUgMjYwLjggNDI3IDE4MiAzNzcgMTMyYy01Ni41LTU2LjUtMTQ4LTU2LjUtMjA0LjUgMEw2MC4yIDI0NC4zeiIvPjwvc3ZnPg==" } }));
        }
    }
}

document.addEventListener('CGCP::init', copyer);
document.addEventListener('DOMContentLoaded', copyer);
