var QUESTION_NUMBER_IN_ONE_NAV_COLUMN = 5;   //number of questions shown in one navigation bar column
var activityId = 0;
var examName = '';
var examQuestions = new Array();
var currentQuestionIndex = null;
var currentAnswers = {};
var feedbackNeeded = {};
var elapsedTime = 0;

jQuery(function () {
    if (jQuery('#exam-ui').length)
    {

        $.ajax({
            type: "POST",
            url: "../get_user_exam_data/",
            async: false,
            data: {"activityId": EXAM_REQUEST_ID},
            success: function (response)
            {
                var data = $.parseJSON(response);
                activityId = data.id;
                examName = data.name;
                examQuestions = data.questions;
                // Add answers to the current answers map if we have any
                if (data.answers && data.answers.length) {
                    for (var i = 0; i < data.answers.length; i++) {
                        var answer = data.answers[i];
                        currentAnswers[(answer.questionIndex - 1)] = answer.answerId;
                    }
                }
                loadQuestion(0);
                displayExamUI();
                updateQuestionStates();

                //re-active the first question
                jQuery('#question_nav_' + 0).addClass('active_question');
            }
        });
    }
    if (jQuery('#exam-time-left').length && EXAM_TIME_LEFT) {
        updateExamTimer();
    }
});

/**
 * Hide the loading message and display the actual exam UI
 */
function displayExamUI() {

    // Hide loading message
    jQuery('#loading').hide();

    // Set some info in the ui
    jQuery('#exam-name').text(examName);
    jQuery('#question-count').text(examQuestions.length);
    // build the navigation bar
    var navQuestions;
    for (var i = 0; i < examQuestions.length; i++) {
        if (i % QUESTION_NUMBER_IN_ONE_NAV_COLUMN == 0) {
            topic = examQuestions[i].topic;
            var navArea = jQuery('#navigation-area');
            var navUl = jQuery('<ul/>');
            var navBar = 'question-nav' + (i / QUESTION_NUMBER_IN_ONE_NAV_COLUMN + 1);
            navUl.attr('id', navBar);
            navUl.attr('class', 'pagination');
            navArea.append(navUl);
            var navTopic = jQuery('<li/>');
            navUl.append(navTopic);
            navQuestions = jQuery('<ul/>');
            navTopic.append(navQuestions);
        }

        var navLink = jQuery('<a/>');
        navLink.attr('id', 'question_nav_' + i);
        navLink.attr('href', 'javascript:void(0);');
        navLink.attr('style', 'text-align: center;');
        navLink.attr('class', 'question_unanswered');
        navLink.text((i + 1));          //generate list of question links in navigation bar
        navLink.click(function () {
            navigateToQuestion(jQuery(this));
        });

        var answeredText = jQuery('<span/>');
        answeredText.text(' (Answered)');
        answeredText.hide();
        navLink.append(answeredText);

        var navList = jQuery('<li/>');
        navList.attr('id', 'nav-list-' + i);
        navList.append(navLink);
        navList.append(answeredText);

        navQuestions.append(navList);
    }
    // Add click event to the buttons
    jQuery('#skip-button').click(function () {
        skipQuestion();
    });
    jQuery('#explanation').click(function () {
        activityExplanation();
    });
    jQuery('#record-answer-button').click(function () {
        recordAnswer(examQuestions.length);
    });
    jQuery('#finish-exam-button').click(function () {
        confirmAndFinishExam();
    });

    // Show the actual UI
    jQuery('#exam-ui').show();

}

/**
 * Set the proper state labels on all the questions
 */
function updateQuestionStates() {
    var topic_counter = -1;
    var topic = '';
    for (var i = 0; i < examQuestions.length; i++) {
        if (examQuestions[i].topic != topic) {
            topic_counter++;
            topic = examQuestions[i].topic;
        }
        if (currentAnswers[i]) {
            jQuery('#nav-list-' + i + ' a').attr('class', 'question_answered_question');
        }
    }
}

/**
 * Hide the exam UI
 */
function hideExamUI() {
    jQuery('#exam-ui').hide();
}

function deactiveQuestion(index) {
    // Color the active question
    // console.log("index", index);
    jQuery('#question_nav_' + index).removeClass('active_question');
}

/**
 * Load the specified question
 */
function loadQuestion(index) {

    if (index >= examQuestions.length)
    {
        index = 0;
    }
    currentQuestionIndex = index;

    var question = examQuestions[currentQuestionIndex];
    // Set some info in the ui
    jQuery('#question-index').text((currentQuestionIndex + 1));
    jQuery('#topic-name').html(question.topic);
    String.prototype.regex_question = function (regexp) {
        var matches = [];
        this.replace(regexp, function () {
            var arr = ([]).slice.call(arguments, 0);
            var extras = arr.splice(-2);
            arr.index = extras[0];
            arr.input = extras[1];
            matches.push(arr);
        });
        return matches.length ? matches : null;
    };
    var imgQues = question.text.regex_question(/[^\w\s]/gi);
    // console.log(question.text);
    var res = question.text.split(imgQues);
    jQuery('#question-text').html(question.text);
    // Displaying digits in box
    var digitContainer = jQuery(".digit1 .a");
    digitContainer.html(""); // Clear contents
    // For first digit
    var digit = jQuery("");
    digitContainer.html(res[0]);
    digitContainer.append(digitContainer);


    // For second digit
    var digitContainer1 = jQuery(".digit2 .c");
    digitContainer1.html(""); // Clear contents
    // For second digit
    var digit1 = jQuery("");
    digitContainer1.html(res[1]);
    digitContainer1.append(digitContainer1);

    var imgContainer = jQuery(".imgHolder-img1");
    imgContainer.html(""); // Clear contents

    var imgContainer1 = jQuery(".imgHolder-img2");
    imgContainer1.html(""); // Clear contents2

    if (question.image != '') {
        for (var i = 0; i < res[0]; i++) {
            var newImage = jQuery("<p class=\"a\"></p>");
            newImage.html(question.image);
            imgContainer.append(newImage);
        }
        for (var j = 0; j < res[1]; j++) {
            var newImage1 = jQuery("<p class=\"c\"></p>");
            newImage1.html(question.image);
            imgContainer1.append(newImage1);
        }
    }
    jQuery('#question-id').val(question.question_id);

    // Add the questions
    jQuery('#answers').empty();
    for (var i = 0; i < question.answers.length; i++) {

        var answer = question.answers[i];

        var li = jQuery('<li />');
        var radio = jQuery("<label class=\"btn\"><input type=\"radio\" name='answer' id='" + 'answer_' + i + "' /><i class=\"fa fa-circle-o fa-2x\"></i><i class=\"fa fa-check-circle-o fa-2x\"></i></label>");

        radio.val(answer.id);


        if (currentAnswers[currentQuestionIndex] && currentAnswers[currentQuestionIndex] == answer.id) {
            radio.attr('checked', 'checked');
        }

        var label = jQuery('<label />');
        label.attr('for', 'answer_' + i);
        label.html(answer.text);
        label.attr('class', 'question_choice');

        var span = jQuery('<label />');
        span.attr('for', 'answer_' + i);
        span.html(toWords(answer.text));
        span.attr('class', 'question_choice');

        li.append(radio);
        li.append(label);
        li.append(span);
        jQuery('#answers').append(li);
    }
    jQuery('#answers').append('</ul>');

    // Color the active question
    jQuery('#question_nav_' + index).addClass('active_question');

    // Handle the skip button
    if (currentQuestionIndex == examQuestions.length - 1) {
        jQuery('#skip-button').hide();
    } else {
        jQuery('#skip-button').show();
    }

}

// Clears the feedback checkbox
function clearFeedback() {
    if (jQuery('#register-feedback').is(':checked')) {
        jQuery('#register-feedback').removeAttr('checked');
    }
}

function skipQuestion() {
    clearFeedback();
    deactiveQuestion(currentQuestionIndex);
    loadQuestion(currentQuestionIndex + 1);
}

function navigateToQuestion(caller) {
    var callerId = caller.attr('id').replace('question_nav_', '');
    clearFeedback();
    deactiveQuestion(currentQuestionIndex);
    loadQuestion(parseInt(callerId));
}

/**
 * recordAnswer: if Submit button is shown, record answer button will only submit the answer of the question
 * if not, recordAnswer will submit the answer as well as the rating.
 */
function recordAnswer(len) {
    var question = jQuery('#question-text').text();
    String.prototype.regex_question = function (regexp) {
        var matches = [];
        this.replace(regexp, function () {
            var arr = ([]).slice.call(arguments, 0);
            var extras = arr.splice(-2);
            arr.index = extras[0];
            arr.input = extras[1];
            matches.push(arr);
        });
        return matches.length ? matches : null;
    };
    var imgQues = question.regex_question(/[^\w\s]/gi);
    var ques = imgQues[0].input,
            split = imgQues[0][0],
            arr = ques.split(split);

    if(split=='+'){
        var sum = parseInt(arr[0]) + parseInt(arr[1]);
    }if(split=='*'){
        var sum = parseInt(arr[0]) * parseInt(arr[1]);
    }

    // Find the checked element
    var checkedElement = jQuery("#answers input[type='radio']:checked");

    if (checkedElement.length) {
        //find the value of checked element
        var chkval = checkedElement.closest("label.btn.active").next("label.question_choice");
        var answerId;
        if (sum == chkval.text()) {
            JSalert('Congratulation! You are correct', null);
            answerId =1;
        } else {
            JSalert('You made a mistake', sum);
            answerId =0;
        }
        currentAnswers[currentQuestionIndex] = answerId;
        jQuery.ajax({
            type: 'POST',
            url: '../save_answer',
            async: false,
            data: {id: activityId, q: jQuery('#question-id').val(), a: answerId},
            success: function (data) {
                if (data != 'success') {
                    if (data == 'relogin') {
                        $('#login_link').click();
                    } else {
                        showError(data);
                    }
                } else {
                    clearFeedback();
                    deactiveQuestion(currentQuestionIndex);
                    if (answerId ==1){
                        if (currentQuestionIndex + 1 != len) {
                        // Navigate to the next question
                        loadQuestion(currentQuestionIndex + 1);
                        }
                    }
                    
                }
            }
        });
        updateQuestionStates();
    } else {
        JSalert('Please choose an answer first', null);
    }
}


function confirmAndFinishExam() {

    if (confirm('Are you sure you wish to finish this exam?')) {
        finishExam();
    }

}

function finishExam() {

    hideExamUI();
    jQuery('#submitting').show();

    var answers = new Array();
    for (var i = 0; i < examQuestions.length; i++) {

        var questionId = examQuestions[i].id;
        var answerId = null;

        if (currentAnswers[questionId]) {
            answerId = currentAnswers[questionId];
        }

        if (answerId != null) {
            answers.push({'index': (i + 1), 'answerId': answerId});
        }
    }

    jQuery.ajax({
        type: 'POST',
        url: '../finish_user_exam',
        async: false,
        data: {id: activityId},
        success: function (data) {
            if (data == 'success') {
                document.location.href = '../viewresults/' + activityId;
            } else {
                jQuery('#submitting').hide();
                if (data == 'relogin') {
                    window.location = '/users/login';
                } else {
                    showError(data);
                }
            }

        }
    });

}

function showError(m) {
    jQuery('#error-text').text(m);
    jQuery('#error-message').show();
}

function updateExamTimer() {
    var timeLeft = parseInt(EXAM_TIME_LEFT) + elapsedTime;
    elapsedTime += 1;

    var minutes = Math.floor(timeLeft / 60);
    var seconds = timeLeft % 60;
    var hours = Math.floor(minutes / 60);
    var minutes = minutes % 60;

    if (hours < 10) {
        hours = '0' + hours;
    }
    if (minutes < 10) {
        minutes = '0' + minutes;
    }
    if (seconds < 10) {
        seconds = '0' + seconds;
    }


    if (timeLeft <= 0) {

        alert('Your exam has timed out. You will now be redirected to the exam submission screen.');

        // If we're in ajax mode, submit via ajax - otherwise, redirect to the completion page
        if (jQuery('#exam-ui').length) {
            finishExam(false);
        } else {
            document.location.href = 'complete?id=' + EXAM_REQUEST_ID;
        }

    } else {
        jQuery('#exam-time-left').val(hours + ':' + minutes + ':' + seconds);
        setTimeout('updateExamTimer()', 1000);
    }

}

//form tags to omit in NS6+:
var omitformtags = ['input', 'textarea', 'select'];

omitformtags = omitformtags.join('|');

function disableselect(e) {
    if (omitformtags.indexOf(e.target.tagName.toLowerCase()) == -1)
        return false;
}

function reEnable() {
    return true;
}

if (typeof document.onselectstart != 'undefined')
    document.onselectstart = new Function('return false');
else {
    document.onmousedown = disableselect;
    document.onmouseup = reEnable;
}

function JSalert($msg, $response) {
    if ($response == null) {
        swal($msg);
    } else {
        $msg = "Sorry, Your answer is InCorrect. Please go to Explanation.";
        swal($msg);
    }
}

function activityExplanation(){
    var question_id = jQuery('#question-id').val();
    $.ajax({
            type: 'POST',
            url: '../explanation',
            async: false,
            data: {id: activityId, q: jQuery('#question-id').val()},
            success: function (data) {
                    document.location.href = "../explanation/" + question_id;
            }
        });
}
