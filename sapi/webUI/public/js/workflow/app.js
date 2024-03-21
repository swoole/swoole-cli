const app = () => {
    fetch('/api/workflow').then((resolve, reject)=>{
        return resolve.json()
    }).then((resolve, reject)=>{
        console.log(resolve)
    })
    document.querySelector('.exec-workflow').addEventListener('click', () => {
        window.open('/runner.html')
    })
}

export default app
