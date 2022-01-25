let pushtopus = new Pushtopus(APP_PUBLIC_KEY, updateUi, SUBSCRIPTION_BACKEND_URL);

$('#subscribeBtn').click(pushtopus.subscribe);
$('#unsubscribeBtn').click(pushtopus.unsubscribe);
$('#pushNotificiationBtn').click(function() {
    pushtopus.push('Sample Pushtopus notification', 'Something requires your attention...', 'https://github.com/doomy/pushtopus')
});

function updateUi(isSubscribed) {
    if (isSubscribed) {
        $('#notSubscribedText, #subscribeBtn').hide();
        $('#subscribedText, #unsubscribeBtn, #pushNotificiationBtn').show();
    } else {
        $('#notSubscribedText, #subscribeBtn').show();
        $('#subscribedText, #unsubscribeBtn, #pushNotificiationBtn').hide();
    }
}
