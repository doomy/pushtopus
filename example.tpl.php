<html>
    <head>
        <title>Pushtopus Example</title>
        <script
            src="https://code.jquery.com/jquery-3.6.0.slim.min.js"
            integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI="
            crossorigin="anonymous"></script>
        <script src="js/pushtopus.js"></script>
    </head>
    <body>
        <div id="notSubscribedText" style="color: red;">
            You are <strong>not</strong> subscribed to notifications.
        </div>

        <div id="subscribedText" style="color: green;">
            You <strong>are</strong> subscribed to notifications.
        </div>

        <button id="subscribeBtn">Subscribe</button>
        <button id="unsubscribeBtn">Unsubscribe</button>
        <button id="pushNotificiationBtn">Push notification</button>

        <script>
            const APP_PUBLIC_KEY =  '<?php echo $config->getVapidPublicKey(); ?>';
            const SUBSCRIPTION_BACKEND_URL = 'example-actions.php';
        </script>
        <script src="js/example.js"></script>
    </body>
</html>