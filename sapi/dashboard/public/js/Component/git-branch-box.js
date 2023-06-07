let git_branch_list = async() => {
    let response = await fetch('/api/branchList', {
        credentials: 'include',
        mode: 'cors',
        method: 'GET',
        headers: {
            //"Access-Control-Request-Method": "GET",
            // "Access-Control-Request-Credentials": true,
            //"Access-Control-Request-Private-Network": true
        },
        //https://developer.chrome.com/blog/private-network-access-preflight/
    })
    let res = await response.json();
if (response.status === 200 && res) {
    let branch_list = document.querySelector('select[name="branch_list"]')
    if (branch_list) {
        let children = ' <option value="" selected>请选择</option>'
        let patt = /\* /
        res['data'].map((value, index, array) => {
            let selected = '';
            if (patt.test(value)) {
                selected = "selected=selected";
            }
            children += `
            <option value="${value}" ${selected} >${value}</option>
            `
            });

        branch_list.innerHTML = children;

        branch_list.addEventListener('change', change_branch)
    }
}

}

let change_branch = async(event) => {
    console.log(event, event.target, event.target.value)
    let url = "/api/changeBranch"
    if (event.target.value) {
        let data = {
            "action": "change_branch",
            "data": {
                "branch_name": event.target.value
            }
        }
        let result = await postData(url, data)
        console.log(result)
    }

}

// Example POST method implementation:
async function postData(url = '', data = {})
{
    // Default options are marked with *
    const response = await fetch(url, {
        method: 'POST', // *GET, POST, PUT, DELETE, etc.
        mode: 'cors', // no-cors, *cors, same-origin
        cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
        credentials: 'same-origin', // include, *same-origin, omit
        headers: {
            'Content-Type': 'application/json'
            // 'Content-Type': 'application/x-www-form-urlencoded',
        },
        redirect: 'follow', // manual, *follow, error
        referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
        body: JSON.stringify(data) // body data type must match "Content-Type" header
    });
    return response.json(); // parses JSON response into native JavaScript objects
}


export {git_branch_list}
