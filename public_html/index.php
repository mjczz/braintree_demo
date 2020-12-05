<?php require_once("../includes/braintree_init.php"); ?>

<html>
<head>
    <meta charset="UTF-8">
    <title>BraintreePHPExample</title>
    <link rel=stylesheet type=text/css href="css/app.css">
    <link rel=stylesheet type=text/css href="css/overrides.css">
</head>
<body>
    <header class="main">
        <div class="container wide">
            <div class="content slim">
                <div class="set">
                    <div class="fill">
                        <a class="pseudoshop" href="/">PSEUDO<strong>SHOP</strong></a>
                    </div>

                    <div class="fit">
                        <a class="braintree" href="https://developers.braintreepayments.com/guides/drop-in" target="_blank">Braintree</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="notice-wrapper">
            <?php if(isset($_SESSION["errors"])) : ?>
                <div class="show notice error notice-error">
                <span class="notice-message">
                    <?php
                    echo($_SESSION["errors"]);
                    unset($_SESSION["errors"]);
                    ?>
                <span>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <div class="wrapper">
        <div class="checkout container">
            <header>
                <h1>Hi, <br>Let's test a transaction</h1>
                <p>沙箱账号,用这个做信用卡账号才能支付 4111111111111111</p>
                <p>沙箱账号,Expiration Date: 11/23</p>
            </header>

            <form method="post" id="payment-form" action="<?php echo $baseUrl;?>checkout.php">
                <section>
                    <label for="amount">
                        <span class="input-label">Amount</span>
                        <div class="input-wrapper amount-wrapper">
                            <input id="amount" name="amount" type="tel" min="1" placeholder="Amount" value="10">
                        </div>
                    </label>

                    <div class="bt-drop-in-wrapper">
                        <div id="bt-dropin"></div>
                    </div>
                </section>

                <input id="nonce" name="payment_method_nonce" type="hidden" />
                <input id="payload" name="payment_method_payload" type="hidden" />
                <button class="button" type="submit"><span>Test Transaction</span></button>
            </form>
        </div>
    </div>

    <script src="https://js.braintreegateway.com/web/dropin/1.25.0/js/dropin.min.js"></script>
    <script>
        var form = document.querySelector('#payment-form');
        var client_token = "<?php echo($gateway->ClientToken()->generate()); ?>"; // 请求接口获得

        braintree.dropin.create({
          authorization: client_token,
          selector: '#bt-dropin',
          paypal: {
            flow: 'vault'
          }
        }, function (createErr, instance) {
          if (createErr) {
            console.log('Create Error', createErr);
            return;
          }
          form.addEventListener('submit', function (event) {
            event.preventDefault();

            instance.requestPaymentMethod(function (err, payload) {
              if (err) {
                console.log('Request Payment Method Error', err);
                return;
              }

              // Add the nonce to the form and submit
              document.querySelector('#nonce').value = payload.nonce;
              document.querySelector('#payload').value = payload;

                // 提交表单，实际前后端分离前端自行去写请求
              form.submit();
            });
          });
        });
    </script>
    <script src="javascript/demo.js"></script>
</body>
</html>
