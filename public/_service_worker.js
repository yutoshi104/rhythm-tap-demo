// キャッシュファイルの指定
var CACHE_NAME = 'battle-pwa-caches';
var urlsToCache = [
    // キャッシュ化したいコンテンツ
    '/',
    '/about/admin/',
    // '/contact/',
    '/terms/',
    '/privacy/',
    // '/faq/',
    // '/search/',
    // '/report/',


    // root dir
    '/manifest.json',
    '/robots.txt',
    // '/service_worker.js',
    '/sitemap.xml',

    // css
    // '/assets/css/admin_common.css',
    '/assets/css/common.css',
    '/assets/css/signin.css',

    // font
    '/assets/font/YuseiMagic-Regular.ttf',

    // img

    // js
    '/assets/js/common.js',
    '/assets/js/file_input.js',
    '/assets/js/JSValidation.js',

    // src
    '/assets/src/iphone_icon_user.png',
    '/assets/src/twitter_card.png',
    '/assets/src/y_user.png',
    '/assets/src/user_icon_default.png',
    '/assets/src/group_default.png',
    '/assets/src/room_default.png',
    '/assets/src/logo/logo.png',
    '/assets/src/sns/amazon.png',
    '/assets/src/sns/facebook.png',
    '/assets/src/sns/github.png',
    '/assets/src/sns/instagram.png',
    '/assets/src/sns/LINE_Android.png',
    '/assets/src/sns/line.png',
    '/assets/src/sns/twitter.png',
    '/assets/src/sns/youtube_icon.png',
    '/assets/src/sns/youtube_logo.png',


];

// インストール処理
// 前述のファイルパスをすべてキャッシュに登録する
self.addEventListener('install', function(event) {
    console.log('sw event: install called');
    event.waitUntil(
        caches
            .open(CACHE_NAME)
            .then(function(cache) {
                return cache.addAll(urlsToCache);
            })
    );
});

// リソースフェッチ時のキャッシュロード処理
// ウェブサイトへのアクセスが成功すれば取得できた内容をキャッシュに保存した上でアプリで表示する。
// ウェブサイトへのアクセスが失敗すれば保存されているキャッシュをアプリで表示する。
self.addEventListener('fetch', function(event) {
    console.log('sw event: fetch called');
    event.respondWith(async function() {
        try{
            var res = await fetch(event.request);
            var cache = await caches.open(CACHE_NAME);
            cache.put(event.request.url, res.clone());
            return res;
        }catch(error){
            console.log('Using cache');
            return caches.match(event.request);
        }
}());
});

// プッシュ通知を受信した時の処理
// self.addEventListener('push', function(event){
//     console.log('sw event: push called');
//     var notificationDataObj = event.data.json();
//     var content = {
//         body: notificationDataObj.body,
//         icon: '/assets/src/icon/icon.png',
//         tag: 'decider-push'
//     };
//     event.waitUntil(
//         self.registration.showNotification(notificationDataObj.title, content)
//     );
// });
// プッシュ通知を開いた時の処理
// self.addEventListener('notificationclick', function (event) {
//     console.log('sw event: push opened');
//     event.notification.close();
//     clients.openWindow("/");
// }, false);

// キャッシュ削除
function deleteServiceWorker(){
    console.log('sw event: cache deleted');
    caches.keys().then(function(keys) {
        let promises = [];
        keys.forEach(function(CACHE_NAME) {
            if (CACHE_NAME) {
                promises.push(caches.delete(CACHE_NAME));
            }
        });
    });
}

// service worker登録解除
function releaseServiceWorker(){
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistrations().then((registrations) => {
            if (registrations.length != 0) {
                for (let i=0; i<registrations.length; i++) {
                    registrations[i].unregister();
                    //console.log('ServiceWorker unregister.');
                }
                caches.keys().then((keys) => {
                    Promise.all(keys.map((key) => { caches.delete(key); })).then(() => {
                        //console.log('caches delete.');
                    });
                });
            }
        });
    }
}