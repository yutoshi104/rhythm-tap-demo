
// ファイル入力時
function inputFile(inputElem){
    // 画像プレビュー
    var previewElem = document.getElementById("preview_image");
    if (typeof window.default_image === 'undefined') {
        window.default_image = previewElem.src;
    }
    if(previewElem){
        var fileList = inputElem.files;
        for(let i=0; i<fileList.length; i++){
            var fileReader = new FileReader();
            var file = fileList[i];
            fileReader.onload = function() {
                previewElem.src = this.result;
            };
            fileReader.readAsDataURL(file);
            // ファイル名更新
            document.getElementById('input_detail').innerHTML = file.name;
        }
    }
    // 取消ボタン
    document.getElementById('del_input_button').style.display = 'block';
    // デフォルトフラグ変更
    let elem = document.querySelector('input[name="default_icon"]');
    if(elem) elem.value = 0;
}
// ファイル選択取消
function deleteFile(){
    // 画像プレビュー
    var previewElem = document.getElementById("preview_image");
    if (typeof window.default_image !== 'undefined') {
        previewElem.src = window.default_image;
    }
    // ファイル選択状態解除
    document.getElementById('input_file').value = "";
    // ファイル名リセット
    document.getElementById('input_detail').innerHTML = "選択されていません";
    // 取消ボタン
    document.getElementById('del_input_button').style.display = 'none';
}

// ドラッグ&ドロップ
window.addEventListener('load',function(){
    let dropArea = document.getElementById('drop_area');
    if(dropArea){
        dropArea.addEventListener('dragover', function(e){
            e.preventDefault();
            dropArea.classList.add('dragover');
        });
        dropArea.addEventListener('dragleave', function(e){
            e.preventDefault();
            dropArea.classList.remove('dragover');
        });
        dropArea.addEventListener('drop', function(e){
            let inputElem = document.getElementById('input_file');
            const multiple = inputElem.multiple;
            const accept = inputElem.accept;
            e.preventDefault();
            dropArea.classList.remove('dragover');
            let files = e.dataTransfer.files;
            inputElem.files = files;
            if(typeof files[0] !== 'undefined') {
                inputFile(inputElem);
            }
        });
    }
});


// デフォルトに戻す
function setDefaultIcon(){
    // ファイル選択状態を解除
    deleteFile();
    // デフォルトフラグ変更
    let elem = document.querySelector('input[name="default_icon"]');
    if(elem) elem.value = 1;
    // プレビューを変更
    var previewElem = document.getElementById("preview_image");
    if(previewElem){
        previewElem.src = window.default_icon_url;
    }
}
