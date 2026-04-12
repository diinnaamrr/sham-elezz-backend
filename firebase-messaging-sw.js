importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');

firebase.initializeApp({
    apiKey: "AIzaSyBltRu5AqVDtcT88wGELucoREZPOLpPMrg",
    authDomain: "sham-el-ezz.firebaseapp.com",
    projectId: "sham-el-ezz",
    storageBucket: "sham-el-ezz.firebasestorage.app",
    messagingSenderId: "171384549270",
    appId: "1:171384549270:android:aef5e1ce6169ee1498b7b7",
    measurementId: "G-9LX5GW4L5W"
});

const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function (payload) {
    return self.registration.showNotification(payload.data.title, {
        body: payload.data.body ? payload.data.body : '',
        icon: payload.data.icon ? payload.data.icon : ''
    });
});