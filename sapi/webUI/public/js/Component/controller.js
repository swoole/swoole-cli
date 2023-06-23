// import {init, send} from "./message.js"


let gen = (e) => {

    let os = document.querySelector('select[name="os"]')
    let with_docker = document.querySelector('select[name="without-docker"]')
    let with_global_prefix = document.querySelector('input[name="with-global-prefix"]')
    let with_http_proxy = document.querySelector('input[name="with-http-proxy"]')
    let with_downloader = document.querySelector('select[name="with-downloader"]')
    let skip_download = document.querySelector('select[name="skip-download"]')
    let with_download_mirror_url = document.querySelector('select[name="with-download-mirror-url"]')
    let conf_path = document.querySelector('input[name="conf-path"]')
    let with_dependecny_graph = document.querySelector('select[name="with-dependency-graph"]')
    let with_web_ui = document.querySelector('select[name="with-web-ui"]')
    let with_swoole_pgsql = document.querySelector('select[name="with-swoole-pgsql"]')
    let cmd = "php prepare.php"

    if (os.value === 'macos') {
        cmd += " @os=" + os.value
    }

    if (with_docker.value === "1") {
        cmd += "  --without-docker=" + with_docker.value
    } else {
        if (os.value === 'macos') {
            cmd += "  --without-docker=1"
        }
    }

    if (with_global_prefix.value !== '/usr/local/swoole-cli') {
        cmd += "  --with-global-prefix=" + with_global_prefix.value
    }

    if (with_http_proxy.value.length > 0) {
        cmd += "  --with-http-proxy=" + with_http_proxy.value
    }

    if (with_downloader.value === 'wget') {
        cmd += "  --with-downloader=" + with_downloader.value
    }

    if (skip_download.value === "1") {
        cmd += "  --skip-download=" + skip_download.value
    }

    if (with_download_mirror_url.value !== "0") {
        cmd += "  --with-download-mirror-url=" + with_download_mirror_url.value
    }

    if (conf_path.value.length > 0) {
        cmd += "  --conf-path=" + conf_path.value
    }
    if (with_dependecny_graph.value === "1") {
        cmd += "  --with-dependency-graph=" + with_dependecny_graph.value
    }
    if (with_web_ui.value === "1") {
        cmd += "  --with-web-ui=" + with_web_ui.value
        if (skip_download.value === "0") {
            cmd += "  --skip-download=1"
        }
    }
    if (with_swoole_pgsql.value === "1") {
        cmd += "  --with-swoole-pgsql=" + with_swoole_pgsql.value
    }

    let extenion_list_obj = document.querySelectorAll('#all_extentions input[type=checkbox]')
    if (extenion_list_obj.length > 0) {
        let extension_list = []
        extenion_list_obj.forEach((value, key, parent) => {
            if (value.checked === true) {
                extension_list.push(value.value)
            }

        })
        let default_ready_extension_list = JSON.parse(
            document.querySelector('#all_extentions')
                .getAttribute('data-default-ready-extension-list')
        )

        //交集
        let intersect = extension_list.filter(x => default_ready_extension_list.includes(x))

        //差集
        let minus = extension_list.filter(x => !default_ready_extension_list.includes(x))
        if (minus.length > 0) {
            minus.map((value, index, array) => {
                cmd += " +" + value;
            })
        }
        console.log(minus)
        //补集
        let complement = default_ready_extension_list.filter(x => !extension_list.includes(x))
        if (complement.length > 0) {
            complement.map((value, index, array) => {
                cmd += " -" + value;
            })
        }
        console.log(complement)
    }

    let codeBox = document.querySelector('.preview-code .pre-code.preprocessor')
    codeBox.innerText = cmd;
    document.querySelector('.exec-button').setAttribute('data-cmd', cmd)

}

let bindEvent = () => {
    let option_list = document.querySelector('.options-list')
    if (option_list) {
        option_list.addEventListener('click', (event) => {
            if (event.target.nodeName === 'SELECT') {
                console.log(event.target)
                gen()
            } else {
                event.stopPropagation()
                event.preventDefault()
            }

        }, false)
    }
    document.querySelector('input[name="with-global-prefix"]').onchange = (event) => {
        gen()
    }
    document.querySelector('input[name="with-http-proxy"]').onchange = (event) => {
        gen()
    }
    document.querySelector('input[name="conf-path"]').onchange = (event) => {
        gen()
    }
    document.querySelector('.generate-cmd-button').addEventListener('click', (e) => {
        gen()
    })
    document.querySelector('.exec-button').addEventListener('click', (event) => {
        let cmd = event.target.getAttribute('data-cmd');
        console.log(cmd)
        let message = {
            "action": "preprocessor",
            "data": cmd
        }
        //send(JSON.stringify(message));

    })

}

let show_controller = () => {
    bindEvent()
    gen()
    //init()
}
export {show_controller}
