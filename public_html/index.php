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
                        <a class="pseudoshop" href="/"><strong>INDEX</strong></a>
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
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" class="form-control" id="email" placeholder="you@example.com">
                        <span id="help-email" class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="billing-phone">Billing phone number</label>
                        <input type="text" class="form-control" id="billing-phone" placeholder="123-456-7890">
                        <span id="help-billing-phone" class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="billing-phone">Billing phone number</label>
                        <input type="text" class="form-control" id="billing-phone" placeholder="123-456-7890">
                        <span id="help-billing-phone" class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="billing-given-name">Billing given name</label>
                        <input type="text" class="form-control" id="billing-given-name" placeholder="First">
                        <span id="help-billing-given-name" class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="billing-surname">Billing surname</label>
                        <input type="text" class="form-control" id="billing-surname" placeholder="Last">
                        <span id="help-billing-surname" class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="billing-street-address">Billing street address</label>
                        <input type="text" class="form-control" id="billing-street-address" placeholder="123 Street">
                        <span id="help-billing-street-address" class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="billing-extended-address">Billing extended address</label>
                        <input type="text" class="form-control" id="billing-extended-address" placeholder="Unit 1">
                        <span id="help-billing-extended-address" class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="billing-locality">Billing locality</label>
                        <input type="text" class="form-control" id="billing-locality" placeholder="City">
                        <span id="help-billing-locality" class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="billing-region">Billing region</label>
                        <input type="text" class="form-control" id="billing-region" placeholder="State">
                        <span id="help-billing-region" class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="billing-postal-code">Billing postal code</label>
                        <input type="text" class="form-control" id="billing-postal-code" placeholder="12345">
                        <span id="help-billing-postal-code" class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="billing-country-code">Billing country code (Alpha 2)</label>
                        <input type="text" class="form-control" id="billing-country-code" placeholder="XX">
                        <span id="help-billing-country-code" class="help-block"></span>
                    </div>

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
          locale: 'en_US', // 加这个可以让paypal的“结账”文字变成英文
          threeDSecure: true, // 开启这个可以验证用户输入的信用卡地址
          card: {
            cardholderName: {
              required: true
            },
          },
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
              var threeDSecureParameters = {
                  amount: document.querySelector('#amount').value,
                  email: document.querySelector('#email').value,
                  billingAddress: {
                      givenName: document.querySelector('#billing-given-name').value, // ASCII-printable characters required, else will throw a validation error
                      surname: document.querySelector('#billing-surname').value, // ASCII-printable characters required, else will throw a validation error
                      phoneNumber: document.querySelector('#billing-phone').value,
                      streetAddress: document.querySelector('#billing-street-address').value,
                      extendedAddress: document.querySelector('#billing-extended-address').value,
                      locality: document.querySelector('#billing-locality').value,
                      region: document.querySelector('#billing-region').value,
                      postalCode: document.querySelector('#billing-postal-code').value,
                      countryCodeAlpha2: document.querySelector('#billing-country-code').value
                  },
              };

              instance.requestPaymentMethod({
                threeDSecure: threeDSecureParameters
            }, function (err, payload) {
              if (err) {
                console.log('Request Payment Method Error', err);
                alert(err)
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
    <script>
        'use strict';
        (function () {
            var amount = document.querySelector('#amount');
            var amountLabel = document.querySelector('label[for="amount"]');

            amount.addEventListener('focus', function () {
                amountLabel.className = 'has-focus';
            }, false);
            amount.addEventListener('blur', function () {
                amountLabel.className = '';
            }, false);
        })();
</script>
</body>
</html>
