<div class="container middlecontent">
    <div class="row">
        <form id="defaultForm" method="post" class="form-horizontal" action="">
            <div class="col-xs-6">
                <div class="form-group">
                    <label class="col-sm-3 control-label" id="captchaOperation"></label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="captcha" />
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    <label class="col-sm-3 control-label" id="captchaOperation1"></label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="captcha1" />
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    <label class="col-sm-3 control-label" id="captchaOperation2"></label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="captcha2" />
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    <label class="col-sm-3 control-label" id="captchaOperation3"></label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="captcha3" />
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    <label class="col-sm-3 control-label" id="captchaOperation4"></label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="captcha4" />
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    <label class="col-sm-3 control-label" id="captchaOperation5"></label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="captcha5" />
                    </div>
                </div>
            </div>
        </form>
    </div>
</div><!--end container-->
<script type="text/javascript">
    $(document).ready(function () {
        $('.c10').addClass('active');
        function randomNumber(min, max) {
            return Math.floor(Math.random() * (max - min + 1) + min);
        }
        ;
        $('#captchaOperation').html([randomNumber(1, 5), '+', randomNumber(1, 5), '='].join(' '));
        $('#captchaOperation1').html([randomNumber(1, 5), '+', randomNumber(1, 5), '='].join(' '));
        $('#captchaOperation2').html([randomNumber(1, 5), '+', randomNumber(1, 5), '='].join(' '));
        $('#captchaOperation3').html([randomNumber(1, 5), '+', randomNumber(1, 5), '='].join(' '));
        $('#captchaOperation4').html([randomNumber(1, 5), '+', randomNumber(1, 5), '='].join(' '));
        $('#captchaOperation5').html([randomNumber(1, 5), '+', randomNumber(1, 5), '='].join(' '));

        $('#defaultForm').formValidation({
            message: 'This value is not valid',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                captcha: {
                    validators: {
                        callback: {
                            message: 'Wrong answer',
                            callback: function (value, validator, $field) {
                                var items = $('#captchaOperation').html().split(' '), sum = parseInt(items[0]) + parseInt(items[2]);
                                return value == sum;
                            }
                        }
                    }
                },
                captcha1: {
                    validators: {
                        callback: {
                            message: 'Wrong answer',
                            callback: function (value, validator, $field) {
                                var items = $('#captchaOperation1').html().split(' '), sum = parseInt(items[0]) + parseInt(items[2]);
                                return value == sum;
                            }
                        }
                    }
                },
                captcha2: {
                    validators: {
                        callback: {
                            message: 'Wrong answer',
                            callback: function (value, validator, $field) {
                                var items = $('#captchaOperation2').html().split(' '), sum = parseInt(items[0]) + parseInt(items[2]);
                                return value == sum;
                            }
                        }
                    }
                },
                captcha3: {
                    validators: {
                        callback: {
                            message: 'Wrong answer',
                            callback: function (value, validator, $field) {
                                var items = $('#captchaOperation3').html().split(' '), sum = parseInt(items[0]) + parseInt(items[2]);
                                return value == sum;
                            }
                        }
                    }
                },
                captcha4: {
                    validators: {
                        callback: {
                            message: 'Wrong answer',
                            callback: function (value, validator, $field) {
                                var items = $('#captchaOperation4').html().split(' '), sum = parseInt(items[0]) + parseInt(items[2]);
                                return value == sum;
                            }
                        }
                    }
                },
                captcha5: {
                    validators: {
                        callback: {
                            message: 'Wrong answer',
                            callback: function (value, validator, $field) {
                                var items = $('#captchaOperation5').html().split(' '), sum = parseInt(items[0]) + parseInt(items[2]);
                                return value == sum;
                            }
                        }
                    }
                },
            }
        });
    });

    var _gaq = [['_setAccount', 'UA-3764464-3'], ['_setDomainName', '.farukat.es'], ['_trackPageview']];
    (function (d, t) {
        var g = d.createElement(t),
                s = d.getElementsByTagName(t)[0];
        g.async = true;
        g.src = ('https:' == location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        s.parentNode.insertBefore(g, s);
    })(document, 'script');
</script> 
