

////////////////////////////////////////////////////////////////////////////////////////////////////
// ハンバーガー
////////////////////////////////////////////////////////////////////////////////////////////////////

window.addEventListener('load', function(){
    if(document.getElementById('humberger_button') && document.querySelector('.humberger-menu')){
        document.getElementById('humberger_button').addEventListener('click', switchHumberger);
        document.querySelector('.humberger-menu').addEventListener('click', switchHumberger);
    }
});
function switchHumberger(){
    if(document.getElementById('humberger_button').getAttribute('aria-expanded')==='false'){
        // ハンバーガーマーク
        document.getElementById('humberger_button').classList.add('humberger-active');
        // ハンバーガーメニュー
        document.querySelector('.humberger-menu').classList.add('humberger-active');
        // スクロール禁止
        window.past_overflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        // 状態切り替え
        document.getElementById('humberger_button').setAttribute('aria-expanded','true');
    }else{
        // ハンバーガーマーク
        document.getElementById('humberger_button').classList.remove('humberger-active');
        // ハンバーガーメニュー
        document.querySelector('.humberger-menu').classList.remove('humberger-active');
        // スクロール解除
        document.body.style.overflow = window.past_overflow;
        // 状態切り替え
        document.getElementById('humberger_button').setAttribute('aria-expanded','false');
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////



////////////////////////////////////////////////////////////////////////////////////////////////////
// ローディング
////////////////////////////////////////////////////////////////////////////////////////////////////

window.addEventListener("load", endLoading);
setTimeout(endLoading, 10*1000);
function endLoading(){
    var elem = document.getElementById('loading');
    if(elem!==null){
        elem.classList.add('loaded');
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////




////////////////////////////////////////////////////////////////////////////////////////////////////
// ヘッダーユーザーアイコンを押下時のドロップダウンメニュー
////////////////////////////////////////////////////////////////////////////////////////////////////

window.addEventListener('load',function(){
    let icon_elem = document.querySelector('header .login-status img');
    if(icon_elem){
        icon_elem.addEventListener('click',function(){
            let dropdown_elem = document.querySelector('header .login-status .dropdown');
            if(dropdown_elem){
                if(dropdown_elem.style.display==='none'){
                    dropdown_elem.style.display = 'block';
                }else if(dropdown_elem.style.display==='block'){
                    dropdown_elem.style.display = 'none';
                }else{
                    dropdown_elem.style.display = 'block';
                }
            }
        });
    }
});


////////////////////////////////////////////////////////////////////////////////////////////////////






////////////////////////////////////////////////////////////////////////////////////////////////////
// 確認画面からの戻るボタン押下時
////////////////////////////////////////////////////////////////////////////////////////////////////

function setModeBack(){
    var elem = document.querySelector('input[name="mode"]');
    if(elem!==null){
        elem.value = "back";
        return true;
    }else{
        return false;
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////








////////////////////////////////////////////////////////////////////////////////////////////////////
// リンクをコピー
////////////////////////////////////////////////////////////////////////////////////////////////////

function copyUrl() {
    const element = document.createElement('input');
    element.value = location.href;
    document.body.appendChild(element);
    element.select();
    document.execCommand('copy');
    document.body.removeChild(element);
    alert("リンクをクリップボードにコピーしました。");
}

////////////////////////////////////////////////////////////////////////////////////////////////////






////////////////////////////////////////////////////////////////////////////////////////////////////
// 要素保存
////////////////////////////////////////////////////////////////////////////////////////////////////

// canvas要素から
function saveImage(canvas_elem,name="image"){
    let downloadEle = document.createElement("a");
    downloadEle.href = canvas_elem.toDataURL("image/jpg");
    downloadEle.download = name+".jpg";
    downloadEle.click();
}

// html要素から (html2canvas必要)
function saveElemImage(elem,name="image"){
    html2canvas(elem).then(function(canvas) {
        let downloadEle = document.createElement("a");
        downloadEle.href = canvas.toDataURL("image/png");
        downloadEle.download = name+".png";
        downloadEle.click();
        var cdata = canvas.toDataURL();
        var encdata= atob(cdata.replace(/^.*,/, ''));
        var outdata = new Uint8Array(encdata.length);
        for (var i = 0; i < encdata.length; i++) {
            outdata[i] = encdata.charCodeAt(i);
        }
        var blob = new Blob([outdata], ["image/png"]);
        if (window.navigator.msSaveBlob) {
            window.navigator.msSaveOrOpenBlob(blob, fname);
        } else {
            var elem = document.createElement("a");
            elem.href = cdata;
            elem.download = name+".png";
            elem.click();
        }
    });
}

////////////////////////////////////////////////////////////////////////////////////////////////////
















// 加速度センサー
window.addEventListener("devicemotion", (dat) => {
    document.getElementById("result").innerHTML = "加速度センサーが検知されました。";
    console.log(dat);
    console.log(dat.accelerationIncludingGravity);
    const aX = dat.accelerationIncludingGravity.x;    // x軸の重力加速度（Android と iOSでは正負が逆）
    const aY = dat.accelerationIncludingGravity.y;    // y軸の重力加速度（Android と iOSでは正負が逆）
    const aZ = dat.accelerationIncludingGravity.z;    // z軸の重力加速度（Android と iOSでは正負が逆）

    let ax_elem = document.getElementById('ax');
    let ay_elem = document.getElementById('ay');
    let az_elem = document.getElementById('az');
    if(ax_elem && ay_elem && az_elem){
        ax_elem.innerHTML = ax;
        ay_elem.innerHTML = ay;
        az_elem.innerHTML = az;
    }else{
        console.log('x: '+String(ax));
        console.log('y: '+String(ay));
        console.log('z: '+String(az));
    }
});



// document.addEventListener("load", function() { DeviceMotionEvent.requestPermission(); });
// window.addEventListener("deviceorientation", function(e){
//     document.getElementById("result").innerHTML = "加速度センサーが検知されました。";
//     const absolute = e.absolute;
//     const alpha = e.alpha;
//     const beta = e.beta;
//     const gamma = e.gamma;

//     if(!location.pathname.match(/^\/acceleration\/?$/)){
//         return false;
//     }

//     let absolute_elem = document.getElementById('absolute');
//     let alpha_elem = document.getElementById('alpha');
//     let beta_elem = document.getElementById('beta');
//     let gamma_elem = document.getElementById('gamma');
//     if(absolute_elem && alpha_elem && beta_elem && gamma_elem){
//         absolute_elem.innerHTML = absolute;
//         alpha_elem.innerHTML = alpha;
//         beta_elem.innerHTML = beta;
//         gamma_elem.innerHTML = gamma;
//     }else{
//         console.log('absolute'+String(absolute));
//         console.log('alpha'+String(alpha));
//         console.log('beta'+String(beta));
//         console.log('gamma'+String(gamma));
//     }
// }, false);









