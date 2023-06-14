"use strict";
var element = document.createElement("script");
element.type = "text/javascript";
element.src = "https://ajaxzip3.github.io/ajaxzip3.js";
document.head.appendChild(element);
class JSValidation{

    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // クラス変数
    ////////////////////////////////////////////////////////////////////////////////////////////////////

    static type_list = [
        "int",
        "float",
        "bool",
        // "array",
        "text",
        "email",
        "password",
        "phone_number",
        "postal_code",
        "postal_code_0",
        "postal_code_3",
        "postal_code_4",
        "address",
        "datetime",
        "date",
        "time",
        "file",
        "image",
        "url",
        "domain",
        "ip",
        "mac",
        "radio",
        "checkbox",
        "select",
        "none",
    ];
    static ngword_list = [
        /バカ/,
        /カス/,
    ];
    static next_id = 1;

    static getTypeList(){
        return this.type_list;
    }
    static getNgWordList(){
        return this.ngword_list;
    }
    static setNgWordList(ngword){
        if(typeof ngword === 'string'){
            ngword = new RegExp(ngword);
        }
        this.ngword_list.push(ngword);
        return true;
    }
    static clearNgWordList(){
        this.ngword_list = [];
        return true;
    }
    static isNgWord(word){
        this.ngword_list.forEach((ngword)=>{
            if(ngword.test(word)){
                return true;
            }
        });
        return false;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////



    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // コンストラクタ
    ////////////////////////////////////////////////////////////////////////////////////////////////////

    #identify;
    constructor(type=undefined,input_id=undefined,result_id=undefined){
        if(type!==undefined){
            this.checkType(type);
        }
        if(input_id!==undefined){
            this.checkElement(input_id);
        }
        if(result_id!==undefined){
            this.checkElement(result_id);
        }
        this.#identify = this.constructor.next_id;
        this.constructor.next_id++;
        this.input_id = input_id;
        this.result_id = result_id;
        this.type = type;
        this.valid_params = {};
        this.valid_messages = {};
        this.valid_func = undefined;
        this.success_callback = undefined;
        this.failure_callback = undefined;
        this.#__apply();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////



    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // チェック
    ////////////////////////////////////////////////////////////////////////////////////////////////////

    checkElement(id){
        if(document.getElementById(id) === null){
            throw new Error("Element Id "+'"'+id+'"'+" does not exist.");
        }
    }
    checkType(type){
        if(!this.constructor.type_list.includes(type)){
            throw new Error('"'+type+'"'+" is not a type. Please watch readme.");
        }
    }
    checkFunction(func){
        if(!func || typeof func !== 'function'){
            throw new TypeError('"'+func+'"'+" is not a function.");
        }
    }
    checkValidation(key,value){
        if( !key || typeof key!=="string" ){
            throw new TypeError('"'+key+'"'+" is not a string.");
        }
        if( (key==="required") && (typeof value!=="boolean") ){
            throw new TypeError('"'+value+'"'+" is not a boolean. " + '"required" property should be boolean value.');
        }
        if( (key==="regexp") && (Object.prototype.toString.call(value).slice(8,-1).toLowerCase()!=="regexp") ){
            throw new TypeError('"'+value+'"'+" is not a RegExp. " + '"regexp" property should be RegExp value.');
        }
        if( (key==="maxlength") && (typeof value!=="number") ){
            throw new TypeError('"'+value+'"'+" is not a number. " + '"maxlength" property should be number.');
        }
        if( (key==="minlength") && (typeof value!=="number") ){
            throw new TypeError('"'+value+'"'+" is not a number. " + '"minlength" property should be number.');
        }
        if( (key==="max") && (typeof value!=="number") ){
            throw new TypeError('"'+value+'"'+" is not a number. " + '"max" property should be number.');
        }
        if( (key==="min") && (typeof value!=="number") ){
            throw new TypeError('"'+value+'"'+" is not a number. " + '"min" property should be number.');
        }
        if( (key==="maxselect") && (typeof value!=="number") ){
            throw new TypeError('"'+value+'"'+" is not a number. " + '"maxselect" property should be number.');
        }
        if( (key==="minselect") && (typeof value!=="number") ){
            throw new TypeError('"'+value+'"'+" is not a number. " + '"minselect" property should be number.');
        }
        if( (key==="maxsize") && (typeof value!=="number") ){
            throw new TypeError('"'+value+'"'+" is not a number. " + '"maxsize" property should be number.');
        }
        if( (key==="minsize") && (typeof value!=="number") ){
            throw new TypeError('"'+value+'"'+" is not a number. " + '"minsize" property should be number.');
        }
        if( (key==="maxdatetime") && (typeof value!=="string") ){
            throw new TypeError('"'+value+'"'+" is not a string. " + '"maxdatetime" property should be number.');
        }
        if( (key==="maxdatetime") && isNaN(Date.parse(value)) ){
            throw new TypeError('"'+value+'"'+" is not a datetime format.");
        }
        if( (key==="mindatetime") && (typeof value!=="string") ){
            throw new TypeError('"'+value+'"'+" is not a string. " + '"mindatetime" property should be number.');
        }
        if( (key==="mindatetime") && isNaN(Date.parse(value)) ){
            throw new TypeError('"'+value+'"'+" is not a datetime format.");
        }
        if( (key==="maxdate") && (typeof value!=="string") ){
            throw new TypeError('"'+value+'"'+" is not a string. " + '"maxdate" property should be number.');
        }
        if( (key==="maxdate") && isNaN(Date.parse(value+" 00:00:00")) ){
            throw new TypeError('"'+value+'"'+" is not a date format.");
        }
        if( (key==="mindate") && (typeof value!=="string") ){
            throw new TypeError('"'+value+'"'+" is not a string. " + '"mindate" property should be number.');
        }
        if( (key==="mindate") && isNaN(Date.parse(value+" 00:00:00")) ){
            throw new TypeError('"'+value+'"'+" is not a date format.");
        }
        if( (key==="maxtime") && (typeof value!=="string") ){
            throw new TypeError('"'+value+'"'+" is not a string. " + '"maxtime" property should be number.');
        }
        if( (key==="maxtime") && isNaN(Date.parse(value)) ){
            throw new TypeError('"'+value+'"'+" is not a time format.");
        }
        if( (key==="mintime") && (typeof value!=="string") ){
            throw new TypeError('"'+value+'"'+" is not a string. " + '"mintime" property should be number.');
        }
        if( (key==="mintime") && isNaN(Date.parse(value)) ){
            throw new TypeError('"'+value+'"'+" is not a time format.");
        }
        if( (key==="ng_word") && !Array.isArray(value) ){
            throw new TypeError('"'+value+'"'+" is not array. " + '"ng_word" property should be array.');
        }
        if( (key==="file_extension") && !Array.isArray(value) ){
            throw new TypeError('"'+value+'"'+" is not array. " + '"file_extension" property should be array.');
        }
        if( (key==="accepted_values") && !Array.isArray(value) ){
            throw new TypeError('"'+value+'"'+" is not array. " + '"accepted_values" property should be array.');
        }
        if( (key==="address_auto") && !( (value instanceof Object) && !(value instanceof Array) ) ){
            throw new TypeError('"'+value+'"'+" is not array. " + '"address_auto" property should be array.');
        }
        if( (key==="name") && (typeof value!=="string") ){
            throw new TypeError('"'+value+'"'+" is not a string. " + '"name" property should be string.');
        }
        if( (key==="custom_func") && (typeof value!=="function") ){
            throw new TypeError('"'+value+'"'+" is not a function. " + '"custom_func" property should be function.');
        }
        if( (key==="name") && (document.getElementsByName(value).length <= 0) ){
            console.warn('warining: Element named "'+value+'"'+" does not exist.");
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////



    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // 変数操作
    ////////////////////////////////////////////////////////////////////////////////////////////////////

    // 入力エリアID
    getInputId(){
        return this.input_id;
    }
    setInputId(input_id){
        this.checkElement(input_id);
        this.input_id = input_id;
        this.#__apply();
        return true;
    }
    delInputId(){
        this.input_id = undefined;
        this.#__apply();
        return true;
    }
    // 結果表示ID
    getResultId(){
        return this.result_id;
    }
    setResultId(result_id){
        this.checkElement(result_id);
        this.result_id = result_id;
        this.#__apply();
        return true;
    }
    delResultId(){
        this.result_id = undefined;
        this.#__apply();
        return true;
    }
    // タイプ
    getType(){
        return this.type;
    }
    setType(type){
        this.checkType(type);
        this.type = type;
        this.#__apply();
        return true;
    }
    delType(){
        this.type = undefined;
        this.#__apply();
        return true;
    }
    // バリデーション詳細
    getValidation(key=undefined){
        if(key===undefined){
            return this.valid_params;
        }else{
            return this.valid_params[key];
        }
    }
    setValidation(key,value){
        this.checkValidation(key,value);
        this.valid_params[key] = value;
        this.#__apply();
        return true;
    }
    delValidation(key){
        delete this.valid_params[key];
        this.#__apply();
        return true;
    }
    setValidations(valid_list){
        for(const key in valid_list){
            this.checkValidation(key,valid_list[key]);
            this.valid_params[key] = valid_list[key];
        }
        this.#__apply();
        return true;
    }
    delValidations(){
        this.valid_params = {};
        this.#__apply();
        return true;
    }
    // メッセージ
    getMessage(key=undefined){
        if(key===undefined){
            return this.valid_messages;
        }else{
            return this.valid_messages[key];
        }
    }
    setMessage(key,value){
        this.valid_messages[key] = value;
        this.#__apply();
        return true;
    }
    delMessage(key){
        delete this.valid_params[key];
        this.#__apply();
        return true;
    }
    setMessages(valid_list){
        for(const key in valid_list){
            this.valid_messages[key] = valid_list[key];
        }
        this.#__apply();
        return true;
    }
    delMessages(){
        this.valid_messages = {};
        this.#__apply();
        return true;
    }
    // 成功した時のコールバック関数
    getSuccessCallback(){
        return this.success_callback;
    }
    setSuccessCallback(func){
        this.checkFunction(func);
        this.success_callback = func;
        this.#__apply();
        return true;
    }
    delSuccessCallback(){
        this.success_callback = undefined;
        this.#__apply();
        return true;
    }
    // 失敗した時のコールバック関数
    getFailureCallback(){
        return this.failure_callback;
    }
    setFailureCallback(func){
        this.checkFunction(func);
        this.failure_callback = func;
        this.#__apply();
        return true;
    }
    delFailureCallback(){
        this.failure_callback = undefined;
        this.#__apply();
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////



    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // バリデーション反映
    ////////////////////////////////////////////////////////////////////////////////////////////////////

    #__apply(){
        // 必須項目チェック(input_id, result_id or callbacks, type)
        if( (document.getElementById(this.input_id)===null) && (document.getElementById(this.input_id+"-0")===null) ){
            return false;
        }
        if(this.type===undefined){
            return false;
        }
        if( (this.success_callback!==undefined) && (this.failure_callback!==undefined) ){
            var sc = this.success_callback;
            var fc = this.failure_callback;
        }else if(document.getElementById(this.result_id)){
            var sc = new Function("value,message", 'document.getElementById("'+this.result_id+'").innerHTML = \'<font style="color:green;">\'+message+\'</font>\';')
            var fc = new Function("value,message", 'document.getElementById("'+this.result_id+'").innerHTML = \'<font style="color:red;">\'+message+\'</font>\';')
        }else{
            return false;
        }

        // valid_func作成
        var valid_sentence = `
            const value = event.srcElement.value;
            var message = "";
            var sc = `+String(sc)+`;
            var fc = `+String(fc)+`;
        `;

        // valid_sentenceに処理を貯めていく
        // 必須かどうか
        if( (this.valid_params.required!==undefined) && (this.valid_params.required===true) ){
            if(this.valid_messages.required!==undefined){
                var _msg = this.valid_messages.required;
            }else{
                var _msg = "必須の項目です。";
            }
            valid_sentence += `
                if( (value==="") || (value===null) ){
                    fc(value,"`+_msg+`");
                    return;
                }
            `;
        }
        // 正規表現に合っているかどうか
        if( (this.valid_params.regexp!==undefined) && (Object.prototype.toString.call(this.valid_params.regexp).slice(8,-1).toLowerCase()==="regexp") ){
            if(this.valid_messages.regexp!==undefined){
                var _msg = this.valid_messages.regexp;
            }else{
                var _msg = "指定された形式ではありません。";
            }
            valid_sentence += `
                if(`+this.valid_params.regexp+`.test(value)===false){
                    message += "`+_msg+`";
                }
            `;
        }
        // タイプごとの処理
        if(this.type==="int"){
            // 整数かどうか
            if(this.valid_messages.int!==undefined){
                var _msg = this.valid_messages.int;
            }else{
                var _msg = "整数を入力してください。";
            }
            valid_sentence += `
                if(!Number.isInteger(Number(value))){
                    message += "`+_msg+`";
                }
            `;
            // 最小値
            if(this.valid_params.min!==undefined){
                if(this.valid_messages.min!==undefined){
                    var _msg = this.valid_messages.min;
                }else{
                    var _msg = this.valid_params.min+"以上で回答してください。";
                }
                valid_sentence += `
                    if(parseInt(value) < `+this.valid_params.min+`){
                        message += "`+_msg+`";
                    }
                `;
            }
            // 最大値
            if(this.valid_params.max!==undefined){
                if(this.valid_messages.max!==undefined){
                    var _msg = this.valid_messages.max;
                }else{
                    var _msg = this.valid_params.max+"以内で回答してください。";
                }
                valid_sentence += `
                    if(parseInt(value) > `+this.valid_params.max+`){
                        message += "`+_msg+`";
                    }
                `;
            }
        }else if(this.type==="float"){
            // 実数かどうか
            if(this.valid_messages.float!==undefined){
                var _msg = this.valid_messages.float;
            }else{
                var _msg = "実数を入力してください。";
            }
            valid_sentence += `
                if(!Number.isFloat(Number(value))){
                    message += "`+_msg+`";
                }
            `;
            // 最小値
            if(this.valid_params.min!==undefined){
                if(this.valid_messages.min!==undefined){
                    var _msg = this.valid_messages.min;
                }else{
                    var _msg = this.valid_params.min+"以上で回答してください。";
                }
                valid_sentence += `
                    if(parseFloat(value) < `+this.valid_params.min+`){
                        message += "`+_msg+`";
                    }
                `;
            }
            // 最大値
            if(this.valid_params.max!==undefined){
                if(this.valid_messages.max!==undefined){
                    var _msg = this.valid_messages.max;
                }else{
                    var _msg = this.valid_params.max+"以内で回答してください。";
                }
                valid_sentence += `
                    if(parseFloat(value) > `+this.valid_params.max+`){
                        message += "`+_msg+`";
                    }
                `;
            }
        }else if(this.type==="bool"){
            // ブール値かどうか
            if(this.valid_messages.bool!==undefined){
                var _msg = this.valid_messages.bool;
            }else{
                var _msg = "ブール値を入力してください。";
            }
            valid_sentence += `
                if(typeof value!=="boolean"){
                    message += "`+_msg+`";
                }
            `;
        // }else if(type==="array"){

        }else if(this.type==="text"){
            // 文字列かどうか
            if(this.valid_messages.text!==undefined){
                var _msg = this.valid_messages.text;
            }else{
                var _msg = "テキストを入力してください。";
            }
            valid_sentence += `
                if(typeof value !== "string"){
                    message += "`+_msg+`";
                }
            `;
            // 最小文字数
            if(this.valid_params.minlength!==undefined){
                if(this.valid_messages.minlength!==undefined){
                    var _msg = this.valid_messages.minlength;
                }else{
                    var _msg = this.valid_params.minlength+"文字以上で回答してください。";
                }
                valid_sentence += `
                    if(value.length < `+this.valid_params.minlength+`){
                        message += "`+_msg+`";
                    }
                `;
            }
            // 最大文字数
            if(this.valid_params.maxlength!==undefined){
                if(this.valid_messages.maxlength!==undefined){
                    var _msg = this.valid_messages.maxlength;
                }else{
                    var _msg = this.valid_params.maxlength+"文字以内で回答してください。";
                }
                valid_sentence += `
                    if(value.length > `+this.valid_params.maxlength+`){
                        message += "`+_msg+`";
                    }
                `;
            }
            // ngword
            if(this.valid_messages.ngword!==undefined){
                var _msg = this.valid_messages.ngword;
            }else{
                var _msg = "「${value}」に不適切なワードが含まれています。";
            }
            valid_sentence += `
                if(JSValidation.isNgWord(value)){
                    message += "`+_msg+`";
                }
            `;
        }else if(this.type==="email"){
            // メールアドレスかどうか
            var email_reg = /^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]{1,}\.[A-Za-z0-9]{1,}$/;
            if(this.valid_messages.email!==undefined){
                var _msg = this.valid_messages.email;
            }else{
                var _msg = "正しいメールアドレスの形式ではありません。";
            }
            valid_sentence += `
                if(`+email_reg+`.test(value)===false){
                    message += "`+_msg+`";
                }
            `;
        }else if(this.type==="password"){
            // 最小文字数
            if(this.valid_params.minlength!==undefined){
                if(this.valid_messages.minlength!==undefined){
                    var _msg = this.valid_messages.minlength;
                }else{
                    var _msg = this.valid_params.minlength+"文字以上で回答してください。";
                }
                valid_sentence += `
                    if(value.length < `+this.valid_params.minlength+`){
                        message += "`+_msg+`";
                    }
                `;
            }
            // 最大文字数
            if(this.valid_params.maxlength!==undefined){
                if(this.valid_messages.maxlength!==undefined){
                    var _msg = this.valid_messages.maxlength;
                }else{
                    var _msg = this.valid_params.maxlength+"文字以内で回答してください。";
                }
                valid_sentence += `
                    if(value.length > `+this.valid_params.maxlength+`){
                        message += "`+_msg+`";
                    }
                `;
            }
        }else if(this.type==="phone_number"){
            // 電話番号かどうか(ハイフンあり)
            var phone_reg = /^[0-9]{2,4}-[0-9]{2,4}-[0-9]{3,4}$/;
            if(this.valid_messages.phone_number!==undefined){
                var _msg = this.valid_messages.phone_number;
            }else{
                var _msg = "正しい電話番号の形式ではありません。";
            }
            valid_sentence += `
                if(`+phone_reg+`.test(value)===false){
                    message += "`+_msg+`";
                }
            `;
        }else if(this.type==="postal_code_0"){
            // 郵便番号かどうか(ハイフンなし)
            var postal_reg = /^[0-9]{7}$/;
            if(this.valid_messages.postal_code_0!==undefined){
                var _msg = this.valid_messages.postal_code_0;
            }else{
                var _msg = "正しい郵便番号(ハイフンなし)の形式ではありません。";
            }
            valid_sentence += `
                if(`+postal_reg+`.test(value)===false){
                    message += "`+_msg+`";
                }
            `;
            // 自動入力
            if(this.valid_params.address_auto!==undefined){
                var dst = "";
                if(this.valid_params.address_auto.address!==undefined){
                    dst = (",'"+this.valid_params.address_auto.address+"'").repeat(2);
                }else{
                    if(this.valid_params.address_auto.prefecture!==undefined){
                        dst += ",'"+this.valid_params.address_auto.prefecture+"'";
                    }else{
                        dst += ",''";
                    }
                    if(this.valid_params.address_auto.municipality!==undefined){
                        dst += ",'"+this.valid_params.address_auto.municipality+"'";
                    }else{
                        dst += ",''";
                    }
                    if(this.valid_params.address_auto.town!==undefined){
                        dst += ",'"+this.valid_params.address_auto.town+"'";
                    }else{
                        dst += ",''";
                    }
                }
                valid_sentence += `
                    AjaxZip3.zip2addr(event.srcElement.name,''`+dst+`);
                `;
            }
        }else if(this.type==="postal_code"){
            // 郵便番号かどうか(ハイフンあり)
            var postal_reg = /^[0-9]{3}-[0-9]{4}$/;
            if(this.valid_messages.postal_code!==undefined){
                var _msg = this.valid_messages.postal_code;
            }else{
                var _msg = "正しい郵便番号(ハイフンあり)の形式ではありません。";
            }
            valid_sentence += `
                if(`+postal_reg+`.test(value)===false){
                    message += "`+_msg+`";
                }
            `;
            // 自動入力
            if(this.valid_params.address_auto!==undefined){
                var dst = "";
                if(this.valid_params.address_auto.address!==undefined){
                    dst = (",'"+this.valid_params.address_auto.address+"'").repeat(2);
                }else{
                    if(this.valid_params.address_auto.prefecture!==undefined){
                        dst += ",'"+this.valid_params.address_auto.prefecture+"'";
                    }else{
                        dst += ",''";
                    }
                    if(this.valid_params.address_auto.municipality!==undefined){
                        dst += ",'"+this.valid_params.address_auto.municipality+"'";
                    }else{
                        dst += ",''";
                    }
                    if(this.valid_params.address_auto.town!==undefined){
                        dst += ",'"+this.valid_params.address_auto.town+"'";
                    }else{
                        dst += ",''";
                    }
                }
                valid_sentence += `
                    AjaxZip3.zip2addr(event.srcElement.name,''`+dst+`);
                `;
            }
        }else if(this.type==="postal_code_3"){
            // 郵便番号(上3桁)かどうか
            var postal_reg = /^[0-9]{3}$/;
            if(this.valid_messages.postal_code_3!==undefined){
                var _msg = this.valid_messages.postal_code_3;
            }else{
                var _msg = "正しい郵便番号(上3桁)の形式ではありません。";
            }
            valid_sentence += `
                if(`+postal_reg+`.test(value)===false){
                    message += "`+_msg+`";
                }
            `;
            // 自動入力
            if( (this.valid_params.address_auto!==undefined) && (this.valid_params.address_auto.postal_code_3!==undefined) && (this.valid_params.address_auto.postal_code_4!==undefined) ){
                var src = "'"+this.valid_params.address_auto.postal_code_3+"','"+this.valid_params.address_auto.postal_code_4+"'";
                var dst = "";
                if(this.valid_params.address_auto.address!==undefined){
                    dst = (",'"+this.valid_params.address_auto.address+"'").repeat(2);
                }else{
                    if(this.valid_params.address_auto.prefecture!==undefined){
                        dst += ",'"+this.valid_params.address_auto.prefecture+"'";
                    }else{
                        dst += ",''";
                    }
                    if(this.valid_params.address_auto.municipality!==undefined){
                        dst += ",'"+this.valid_params.address_auto.municipality+"'";
                    }else{
                        dst += ",''";
                    }
                    if(this.valid_params.address_auto.town!==undefined){
                        dst += ",'"+this.valid_params.address_auto.town+"'";
                    }else{
                        dst += ",''";
                    }
                }
                valid_sentence += `
                    AjaxZip3.zip2addr(`+src+dst+`);
                `;
            }
        }else if(this.type==="postal_code_4"){
            // 郵便番号(下4桁)かどうか
            var postal_reg = /^[0-9]{4}$/;
            if(this.valid_messages.postal_code_4!==undefined){
                var _msg = this.valid_messages.postal_code_4;
            }else{
                var _msg = "正しい郵便番号(下4桁)の形式ではありません。";
            }
            valid_sentence += `
                if(`+postal_reg+`.test(value)===false){
                    message += "`+_msg+`";
                }
            `;
            // 自動入力
            if( (this.valid_params.address_auto!==undefined) && (this.valid_params.address_auto.postal_code_3!==undefined) && (this.valid_params.address_auto.postal_code_4!==undefined) ){
                var src = "'"+this.valid_params.address_auto.postal_code_3+"','"+this.valid_params.address_auto.postal_code_4+"'";
                var dst = "";
                if(this.valid_params.address_auto.address!==undefined){
                    dst = (",'"+this.valid_params.address_auto.address+"'").repeat(2);
                }else{
                    if(this.valid_params.address_auto.prefecture!==undefined){
                        dst += ",'"+this.valid_params.address_auto.prefecture+"'";
                    }else{
                        dst += ",''";
                    }
                    if(this.valid_params.address_auto.municipality!==undefined){
                        dst += ",'"+this.valid_params.address_auto.municipality+"'";
                    }else{
                        dst += ",''";
                    }
                    if(this.valid_params.address_auto.town!==undefined){
                        dst += ",'"+this.valid_params.address_auto.town+"'";
                    }else{
                        dst += ",''";
                    }
                }
                valid_sentence += `
                    AjaxZip3.zip2addr(`+src+dst+`);
                `;
            }
        }else if(this.type==="address"){

        }else if(this.type==="datetime"){
            // datetime形式であるか
            if(this.valid_messages.datetime!==undefined){
                var _msg = this.valid_messages.datetime;
            }else{
                var _msg = "正しい日時の形式ではありません。";
            }
            valid_sentence += `
                if(isNaN(Date.parse(value))){
                    message += "`+_msg+`";
                }
            `;
            // 最大日時
            if(this.valid_params.maxdatetime!==undefined){
                if(this.valid_messages.maxdatetime!==undefined){
                    var _msg = this.valid_messages.maxdatetime;
                }else{
                    var _msg = this.valid_params.maxdatetime+"より前の日時を入力してください。";
                }
                valid_sentence += `
                    if(Date.parse(value).getTime() > Date.parse("`+this.valid_params.maxdatetime+`").getTime()){
                        message += "`+_msg+`";
                    }
                `;
            }
            // 最小日時
            if(this.valid_params.mindatetime!==undefined){
                if(this.valid_messages.mindatetime!==undefined){
                    var _msg = this.valid_messages.mindatetime;
                }else{
                    var _msg = this.valid_params.mindatetime+"より後の日時を入力してください。";
                }
                valid_sentence += `
                    if(Date.parse(value).getTime() < Date.parse("`+this.valid_params.mindatetime+`").getTime()){
                        message += "`+_msg+`";
                    }
                `;
            }
        }else if(this.type==="date"){
            // date形式であるか
            if(this.valid_messages.date!==undefined){
                var _msg = this.valid_messages.date;
            }else{
                var _msg = "正しい日付の形式ではありません。";
            }
            valid_sentence += `
                if(isNaN(Date.parse(value))){
                    message += "`+_msg+`";
                }
            `;
            // 最大日時
            if(this.valid_params.maxdate!==undefined){
                if(this.valid_messages.maxdate!==undefined){
                    var _msg = this.valid_messages.maxdate;
                }else{
                    var _msg = this.valid_params.maxdate+"より前の日付を入力してください。";
                }
                valid_sentence += `
                    if(Date.parse(value).getTime() > Date.parse("`+this.valid_params.maxdate+`").getTime()){
                        message += "`+_msg+`";
                    }
                `;
            }
            // 最小日時
            if(this.valid_params.mindate!==undefined){
                if(this.valid_messages.mindate!==undefined){
                    var _msg = this.valid_messages.mindate;
                }else{
                    var _msg = this.valid_params.mindate+"より後の日付を入力してください。";
                }
                valid_sentence += `
                    if(Date.parse(value).getTime() < Date.parse("`+this.valid_params.mindate+`").getTime()){
                        message += "`+_msg+`";
                    }
                `;
            }
        }else if(this.type==="time"){
            // time形式であるか
            if(this.valid_messages.time!==undefined){
                var _msg = this.valid_messages.time;
            }else{
                var _msg = "正しい時間の形式ではありません。";
            }
            valid_sentence += `
                if(isNaN(Date.parse("1970/01/01 "+value))){
                    message += "`+_msg+`";
                }
            `;
            // 最大日時
            if(this.valid_params.maxtime!==undefined){
                if(this.valid_messages.maxtime!==undefined){
                    var _msg = this.valid_messages.maxtime;
                }else{
                    var _msg = this.valid_params.maxtime+"より前の時間を入力してください。";
                }
                valid_sentence += `
                    if(Date.parse("1970/01/01 "+value).getTime() > Date.parse("1970/01/01 `+this.valid_params.maxtime+`").getTime()){
                        message += "`+_msg+`";
                    }
                `;
            }
            // 最小日時
            if(this.valid_params.mintime!==undefined){
                if(this.valid_messages.mintime!==undefined){
                    var _msg = this.valid_messages.mintime;
                }else{
                    var _msg = this.valid_params.mintime+"より後の時間を入力してください。";
                }
                valid_sentence += `
                    if(Date.parse("1970/01/01 "+value).getTime() < Date.parse("1970/01/01 `+this.valid_params.mintime+`").getTime()){
                        message += "`+_msg+`";
                    }
                `;
            }
        }else if(this.type==="file"){
            // ファイルが選択されているか
            if( (this.valid_params.required!==undefined) && (this.valid_params.required===true) ){
                if(this.valid_messages.file!==undefined){
                    var _msg = this.valid_messages.file;
                }else{
                    var _msg = "ファイルが選択されていません。";
                }
                valid_sentence += `
                    if(event.srcElement.files.length > 0){
                        message += "`+_msg+`";
                    }
                `;
            }
            // 許可された拡張子であるか
            if( (this.valid_params.required!==undefined) && (this.valid_params.required===true) && (this.valid_params.file_extension!==undefined) ){
                if(this.valid_messages.file!==undefined){
                    var _msg = this.valid_messages.file;
                }else{
                    var _msg = "「${files[i].name}」は許可されていない拡張子のファイルです。${this.valid_params.file_extension}のファイルが許可されています。";
                }
                valid_sentence += `
                    var files = event.srcElement.files;
                    for(let i=0; i<files.length; i++){
                        var pos = files[i].name.lastIndexOf('.');
                        if(pos === -1){
                            message += "`+_msg+`";
                            break;
                        }
                        var ext = files[i].name.slice(pos + 1).toLowerCase();
                        if(["`+this.valid_params.file_extension.join('","')+`"].indexOf(ext)){
                            message += "`+_msg+`";
                            break;
                        }
                    }
                `;
            }
        }else if(this.type==="image"){
            // ファイルが選択されているか
            if( (this.valid_params.required!==undefined) && (this.valid_params.required===true) ){
                if(this.valid_messages.file!==undefined){
                    var _msg = this.valid_messages.file;
                }else{
                    var _msg = "ファイルが選択されていません。";
                }
                valid_sentence += `
                    if(event.srcElement.files.length > 0){
                        message += "`+_msg+`";
                    }
                `;
            }
            // 画像であるか
            if( (this.valid_params.required!==undefined) && (this.valid_params.required===true) ){
                if(this.valid_messages.image!==undefined){
                    var _msg = this.valid_messages.image;
                }else{
                    var _msg = "画像ファイルを選択してください。";
                }
                valid_sentence += `
                    var files = event.srcElement.files;
                    var image_reg = /^image\/[a-zA-Z0-9]{2,}$/;
                    for(let i=0; i<files.length; i++){
                        if(!image_reg.test(files[i].type)){
                            message += "`+_msg+`";
                            break;
                        }
                    }
                `;
            }
            // 許可された拡張子であるか
            if( (this.valid_params.required!==undefined) && (this.valid_params.required===true) && (this.valid_params.file_extension!==undefined) ){
                if(this.valid_messages.file!==undefined){
                    var _msg = this.valid_messages.file;
                }else{
                    var _msg = "「${files[i].name}」は許可されていない拡張子のファイルです。${this.valid_params.file_extension}のファイルが許可されています。";
                }
                valid_sentence += `
                    var files = event.srcElement.files;
                    for(let i=0; i<files.length; i++){
                        var pos = files[i].name.lastIndexOf('.');
                        if(pos === -1){
                            message += "`+_msg+`";
                            break;
                        }
                        var ext = files[i].name.slice(pos + 1).toLowerCase();
                        if(["`+this.valid_params.file_extension.join('","')+`"].indexOf(ext)){
                            message += "`+_msg+`";
                            break;
                        }
                    }
                `;
            }
        }else if(this.type==="url"){
            // URLであるか
            var url_reg = /^https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#\u3000-\u30FE\u4E00-\u9FA0\uFF01-\uFFE3]+$/;
            if(this.valid_messages.url!==undefined){
                var _msg = this.valid_messages.url;
            }else{
                var _msg = "正しいURLの形式ではありません。";
            }
            valid_sentence += `
                if(`+url_reg+`.test(value)===false){
                    message += "`+_msg+`";
                }
            `;
        }else if(this.type==="domain"){
            // ドメインであるか
            var domain_reg = /^([a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9]*\.)+[a-zA-Z]{2,}$/;
            if(this.valid_messages.domain!==undefined){
                var _msg = this.valid_messages.domain;
            }else{
                var _msg = "正しいドメインの形式ではありません。";
            }
            valid_sentence += `
                if(`+domain_reg+`.test(value)===false){
                    message += "`+_msg+`";
                }
            `;
        }else if(this.type==="ip"){
            // IPアドレスであるか
            var ip_reg = /^\d{1,3}(\.\d{1,3}){3}$/;
            if(this.valid_messages.ip!==undefined){
                var _msg = this.valid_messages.ip;
            }else{
                var _msg = "正しいIPアドレスの形式ではありません。";
            }
            valid_sentence += `
                if(`+ip_reg+`.test(value)===false){
                    message += "`+_msg+`";
                }
            `;
        }else if(this.type==="mac"){
            // MACアドレスであるか
            var mac_reg = /^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$/;
            if(this.valid_messages.mac!==undefined){
                var _msg = this.valid_messages.mac;
            }else{
                var _msg = "正しいMACアドレスの形式ではありません。";
            }
            valid_sentence += `
                if(`+mac_reg+`.test(value)===false){
                    message += "`+_msg+`";
                }
            `;
        }else if(this.type==="radio"){
            // 許可された値かどうか
            if( (this.valid_params.accepted_values!==undefined) && (this.valid_params.name!==undefined) ){
                if(this.valid_messages.select!==undefined){
                    var _msg = this.valid_messages.select;
                }else{
                    var _msg = "正しい選択肢を選んでください。";
                }
                valid_sentence += `
                    var values = document.getElementsByName("`+this.valid_params.name+`");
                    var accepted_list = ["`+this.valid_params.accepted_values.join('","')+`"];
                    for(let i=0; i<values.length; i++){
                        if( (typeof values[i].value!=="string" || !accepted_list.includes(values[i].value)) && values[i].checked ){
                            message += "`+_msg+`";
                            break;
                        }
                    }
                `;
            }
            // 最大選択数
            if( this.valid_params.name!==undefined ){
                if(this.valid_messages.maxselect!==undefined){
                    var _msg = this.valid_messages.maxselect;
                }else{
                    var _msg = "最大選択数は1つです。";
                }
                valid_sentence += `
                    var count = 0;
                    var elem = document.getElementsByName("`+this.valid_params.name+`");
                    for(let i=0; i<elem.length; i++){
                        if(elem[i].checked){
                            count++;
                        }
                    }
                    if(count > 1){
                        message += "`+_msg+`";
                    }
                `;
            }
            // 最低選択数
            if( this.valid_params.name!==undefined ){
                if(this.valid_messages.minselect!==undefined){
                    var _msg = this.valid_messages.minselect;
                }else{
                    var _msg = "最低1つは選択してください。";
                }
                valid_sentence += `
                    var count = 0;
                    var elem = document.getElementsByName("`+this.valid_params.name+`");
                    for(let i=0; i<elem.length; i++){
                        if(elem[i].checked){
                            count++;
                        }
                    }
                    if(count < 1){
                        message += "`+_msg+`";
                    }
                `;
            }
        }else if(this.type==="checkbox"){
            // 許可された値かどうか
            if( (this.valid_params.accepted_values!==undefined) && (this.valid_params.name!==undefined) ){
                if(this.valid_messages.select!==undefined){
                    var _msg = this.valid_messages.select;
                }else{
                    var _msg = "正しい選択肢を選んでください。";
                }
                valid_sentence += `
                    var values = document.getElementsByName("`+this.valid_params.name+`");
                    var accepted_list = ["`+this.valid_params.accepted_values.join('","')+`"];
                    for(let i=0; i<values.length; i++){
                        if( (typeof values[i].value!=="string" || !accepted_list.includes(values[i].value)) && values[i].checked ){
                            message += "`+_msg+`";
                            break;
                        }
                    }
                `;
            }
            // 最大選択数
            if( (this.valid_params.maxselect!==undefined) && (this.valid_params.name!==undefined) ){
                if(this.valid_messages.maxselect!==undefined){
                    var _msg = this.valid_messages.maxselect;
                }else{
                    var _msg = "最大選択数は"+this.valid_params.maxselect+"つです。";
                }
                valid_sentence += `
                    var count = 0;
                    var elem = document.getElementsByName("`+this.valid_params.name+`");
                    for(let i=0; i<elem.length; i++){
                        if(elem[i].checked){
                            count++;
                        }
                    }
                    if(count > `+this.valid_params.maxselect+`){
                        message += "`+_msg+`";
                    }
                `;
            }
            // 最低選択数
            if( (this.valid_params.minselect!==undefined) && (this.valid_params.name!==undefined) ){
                if(this.valid_messages.minselect!==undefined){
                    var _msg = this.valid_messages.minselect;
                }else{
                    var _msg = "最低"+this.valid_params.minselect+"つは選択してください。";
                }
                valid_sentence += `
                    var count = 0;
                    var elem = document.getElementsByName("`+this.valid_params.name+`");
                    for(let i=0; i<elem.length; i++){
                        if(elem[i].checked){
                            count++;
                        }
                    }
                    if(count < `+this.valid_params.minselect+`){
                        message += "`+_msg+`";
                    }
                `;
            }
        }else if(this.type==="select"){
            // 許可された値かどうか
            if(this.valid_params.accepted_values!==undefined){
                if(this.valid_messages.select!==undefined){
                    var _msg = this.valid_messages.select;
                }else{
                    var _msg = "正しい選択肢を選んでください。";
                }
                valid_sentence += `
                    var accepted_list = ["`+this.valid_params.accepted_values.join('","')+`"];
                    if(typeof value!=="string" || !accepted_list.includes(value)){
                        message += "`+_msg+`";
                    }
                `;
            }
        }else if(this.type==="agree"){
            // チェックがついているかどうか
            if(this.valid_messages.agree!==undefined){
                var _msg = this.valid_messages.agree;
            }else{
                var _msg = "チェックをつけてください。";
            }
            valid_sentence += `
                if(typeof event.srcElement.checked!=="boolean" || !event.srcElement.checked){
                    message += "`+_msg+`";
                }
            `;
        }else{
            return false;
        }
        if(this.valid_params.custom_func!==undefined){
            if(this.valid_messages.custom_func!==undefined){
                var _msg = this.valid_messages.custom_func;
            }else{
                var _msg = "不正な回答を検知しました。";
            }
            valid_sentence += `
                var res = `+this.valid_params.custom_func+`(value);
                if(res!==true){
                    if(res===null){
                        message += "";
                    }else if(typeof res==="string"){
                        message += res;
                    }else{
                        message += "`+_msg+`";
                    }
                }
            `;
        }

        var success_message = this.valid_messages.success!==undefined ? this.valid_messages.success : "OK";

        valid_sentence += `
            if(message===""){
                sc(value,"`+success_message+`");
            }else{
                fc(value,message);
            }
        `;


        var f = new Function("event", valid_sentence);

        // valid_func配置
        this.valid_func = f;
        if( (this.type==="radio") || (this.type==="checkbox") ){
            for(let i=0; ; i++){
                var s_elem = document.getElementById(this.input_id+"-"+i);
                if(s_elem!==null){
                    s_elem.onchange = f;
                }else{
                    break;
                }
            }
        }else if( this.type==="select" ){
            document.getElementById(this.input_id).onfocus = f;
        }else{
            document.getElementById(this.input_id).oninput = f;
        }



    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////

}
