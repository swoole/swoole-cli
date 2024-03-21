// import {init, send} from "./message.js"

const generateOpiton = (e) => {
    const os = document.querySelector('select[name="os"]')
    const with_docker = document.querySelector('select[name="without-docker"]')
    const with_global_prefix = document.querySelector('input[name="with-global-prefix"]')
    const with_http_proxy = document.querySelector('input[name="with-http-proxy"]')
    const with_downloader = document.querySelector('select[name="with-downloader"]')
    const with_skip_download = document.querySelector('select[name="skip-download"]')
    const with_download_mirror_url = document.querySelector('select[name="with-download-mirror-url"]')
    const with_conf_path = document.querySelector('input[name="conf-path"]')
    const with_dependecny_graph = document.querySelector('select[name="with-dependency-graph"]')
    const with_web_ui = document.querySelector('select[name="with-web-ui"]')
    const with_swoole_pgsql = document.querySelector('select[name="with-swoole-pgsql"]')
    let cmd = 'php prepare.php'

    if (os.value === 'macos') {
        cmd += ' @os=' + os.value
    }

    if (with_docker.value === '1') {
        cmd += '  --without-docker=' + with_docker.value
    } else {
        if (os.value === 'macos') {
            cmd += '  --without-docker=1'
        }
    }

    if (with_global_prefix.value !== '/usr/local/swoole-cli') {
        cmd += '  --with-global-prefix=' + with_global_prefix.value
    }

    if (with_http_proxy.value.length > 0) {
        cmd += '  --with-http-proxy=' + with_http_proxy.value
    }

    if (with_downloader.value === 'wget') {
        cmd += '  --with-downloader=' + with_downloader.value
    }

    if (with_skip_download.value === '1') {
        cmd += '  --skip-download=' + with_skip_download.value
    }

    if (with_download_mirror_url.value !== '0') {
        cmd += '  --with-download-mirror-url=' + with_download_mirror_url.value
    }

    if (with_conf_path.value.length > 0) {
        cmd += '  --conf-path=' + with_conf_path.value
    }
    if (with_dependecny_graph.value === '1') {
        cmd += '  --with-dependency-graph=' + with_dependecny_graph.value
    }
    if (with_web_ui.value === '1') {
        cmd += '  --with-web-ui=' + with_web_ui.value
        if (with_skip_download.value === '0') {
            cmd += '  --with-skip-download=1'
        }
    }
    if (with_swoole_pgsql.value === '1') {
        cmd += '  --with-swoole-pgsql=' + with_swoole_pgsql.value
    }

    const extenion_list_obj = document.querySelectorAll('#all_extentions input[type=checkbox]')
    if (extenion_list_obj.length > 0) {
        const extension_list = []
        extenion_list_obj.forEach((value, key, parent) => {
            if (value.checked === true) {
                extension_list.push(value.value)
            }
        })
        const default_ready_extension_list = JSON.parse(
            document.querySelector('#all_extentions')
                .getAttribute('data-default-ready-extension-list')
        )

        // 交集
        const intersect = extension_list.filter(x => default_ready_extension_list.includes(x))

        // 差集
        const minus = extension_list.filter(x => !default_ready_extension_list.includes(x))
        if (minus.length > 0) {
            minus.map((value, index, array) => {
                cmd += ' +' + value
            })
        }
        console.log(minus)
        // 补集
        const complement = default_ready_extension_list.filter(x => !extension_list.includes(x))
        if (complement.length > 0) {
            complement.map((value, index, array) => {
                cmd += ' -' + value
            })
        }
        console.log(complement)
    }

    const codeBox = document.querySelector('.preview-code .pre-code.preprocessor')
    codeBox.innerText = cmd
    document.querySelector('.exec-button').setAttribute('data-cmd', cmd)
}

const bind_event = () => {
    const option_list = document.querySelector('.options-list')
    if (option_list) {
        option_list.addEventListener('click', (event) => {
            if (event.target.nodeName === 'SELECT') {
                console.log(event.target)
                generateOpiton()
            } else {
                event.stopPropagation()
                event.preventDefault()
            }
        }, false)
    }
    document.querySelector('input[name="with-global-prefix"]').onchange = (event) => {
        generateOpiton()
    }
    document.querySelector('input[name="with-http-proxy"]').onchange = (event) => {
        generateOpiton()
    }
    document.querySelector('input[name="conf-path"]').onchange = (event) => {
        generateOpiton()
    }
    document.querySelector('.generate-cmd-button').addEventListener('click', (e) => {
        generateOpiton()
    })
    document.querySelector('.build-workflow').addEventListener('click', (event) => {
        const cmd = event.target.getAttribute('data-cmd')
        console.log(cmd)
        const message = {
            action: 'preprocessor',
            data: cmd
        }
        // send(JSON.stringify(message));
        window.open('/workflow.html')
    })
}

const show_controller = () => {
    bind_event()
    generateOpiton()
}
export {show_controller}
