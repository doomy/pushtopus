function Pushtopus (
    vapidPublicAppKey,
    subscriptionUpdateCallback,
    subscriptionBackendUrl
) {
    let serviceWorkerRegistration;
    let publicAppKeyArray = urlB64ToUint8Array(vapidPublicAppKey);
    let subscribed = false;

    registerServiceWorker(detectSubscription);

    function registerServiceWorker(callback) {
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            console.log('Service Worker and Push is supported');
            return navigator.serviceWorker.register('serviceWorker.js') //this MUST be in the same directory as index.php
                .then(function (swReg) {
                    serviceWorkerRegistration = swReg;
                    callback();
                })
                .catch(function (error) {
                    console.error('Service Worker Error', error);
                });
        } else {
            console.warn('Push messaging is not supported');
        }
    }

    function detectSubscription() {
        serviceWorkerRegistration.pushManager.getSubscription()
            .then(function(subscription) {
                console.log('detecting', subscription);
                if (subscription) {
                    subscribed = true;
                    subscriptionUpdateCallback(subscribed);
                }
            });
    }

    this.subscribe = function() {
        serviceWorkerRegistration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: publicAppKeyArray
        }).then(function(subscription) {
            console.debug('aaa', subscription);
            const key = subscription.getKey('p256dh');
            const token = subscription.getKey('auth');

            fetch(subscriptionBackendUrl, {
                method: 'post',
                headers: new Headers({
                    'Content-Type': 'application/json'
                }),
                body: JSON.stringify({
                    action: 'subscribe',
                    endpoint: subscription.endpoint,
                    key: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('p256dh')))) : null,
                    token: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('auth')))) : null
                })
            }).then(function () {
                subscribed = true;
                subscriptionUpdateCallback(subscribed);
            });
        }).catch(function(err) {
            console.log('Failed to subscribe the user: ', err);
        });
    }

    this.unsubscribe = function() {
        serviceWorkerRegistration.pushManager.getSubscription()
            .then(function(subscription) {
                if (subscription) {
                    const key = subscription.getKey('p256dh');
                    const token = subscription.getKey('auth');
                    fetch(subscriptionBackendUrl, {
                        method: 'post',
                        headers: new Headers({
                            'Content-Type': 'application/json'
                        }),
                        body: JSON.stringify({
                            action: 'unsubscribe',
                            endpoint: subscription.endpoint,
                            key: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('p256dh')))) : null,
                            token: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('auth')))) : null
                        })
                    }).then(function(response) {
                        return response.text();
                    }).then(function(response) {
                        console.log(response);
                    }).catch(function(err) {
                        console.log('error removing from db');
                        throw new error('error removing from db');
                    });
                    return subscription.unsubscribe();
                }
            }).catch(function(error) {
            console.log('Error unsubscribing', error);
        }).then(function() {
            console.log('User is unsubscribed.');
            subscribed = false;
            subscriptionUpdateCallback(subscribed);
        });
    }

    this.push = function (title, message, url) {
        navigator.serviceWorker.ready
            .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
            .then(subscription => {
                if (!subscription) {
                    alert('Please enable push notifications');
                    return;
                }

                const key = subscription.getKey('p256dh');
                const token = subscription.getKey('auth');
                fetch(subscriptionBackendUrl, {
                    method: 'POST',
                    body: JSON.stringify({
                        action: 'push',
                        title: title,
                        message: message,
                        url: url
                    })
                })
            });
    }

    this.isSubscribed = function () {
        return subscribed;
    };

    function urlB64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }


}