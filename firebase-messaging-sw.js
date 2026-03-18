importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');

firebase.initializeApp({
    apiKey: "AIzaSyBrjhNWFR7Np_lKcKANhW3wpn7tTr0_k48",
    authDomain: "talapy-4704f.firebaseapp.com",
    projectId: "talapy-4704f",
    storageBucket: "talapy-4704f.firebasestorage.app",
    messagingSenderId: "1006470237068",
    appId: "1:1006470237068:android:086070c06162ba339be4a9",
    measurementId: ""
});

const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function (payload) {
    return self.registration.showNotification(payload.data.title, {
        body: payload.data.body ? payload.data.body : '',
        icon: payload.data.icon ? payload.data.icon : ''
    });
});