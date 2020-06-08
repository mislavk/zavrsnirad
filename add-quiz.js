var questionsArray = [];
var questionId = 0;
var quizInfo = {
    name: '',
    code: '',
    style: '',
    dialect: '',
    audio: '',
    questions: []
}

function Question(id) {
    this.id = id;
    this.desc = undefined;
    this.optionNum = 0;
    this.container = undefined;
    this.answers = [];
}

function Answer(id) {
    this.id = id;
    this.desc = undefined;
	this.checked = false;
}

Answer.prototype.appendHtml = function(parent) {
    this.checked =$('<input type="checkbox"/>').appendTo(parent);
    this.desc = $('<input type="text" required="required" placeholder="Option '+(this.id+1)+'"/>').appendTo(parent);
    $('<button type="button" class="btn btn-default removeAnswer"><span class="glyphicon glyphicon-minus"></span></button>').appendTo(parent);
};

function addAnswer(type, parent, questionId) {
    var answerId = questionsArray[questionId].optionNum;
    var container = $('<div data-qid="'+questionId+'" data-aid="'+answerId+'" class="answerContainer"></div>').appendTo(parent);
    questionsArray[questionId].container = container;
    
    var ans = new Answer(answerId, type);
    ans.appendHtml(container);
    questionsArray[questionId].answers.push(ans);
    questionsArray[questionId].optionNum++;
}

function addQuestion(parent) {
    var html = `
    <div class="questionContainer" data-qid="${questionId}">
        <div class="qDetailsContainer">
            <div class="form-group">
                <label for="quizName">Question No. ${questionId+1}</label>
                <input type="text" class="form-control" placeholder="Enter a question" id="question-${questionId}" required />
            </div>
            <div data-qid="${questionId}" id="answerSheet-${questionId}"></div>
            <button data-qid="${questionId}" id="addAnswer-${questionId}" type="button" class="addAnswerBtn btn btn-default"><span class="glyphicon glyphicon-plus"></button>
        </div>
    </div>
    `;
    $(html).appendTo(parent);
    questionsArray[questionId] = new Question(questionId, 'checkbox');
    questionsArray[questionId].desc = $('#question-'+questionId);
    questionId++;
}

$(document).ready(function() {
    var questionsContainer = $('#questionsContainer');
    $('#addQuestionBtn').on('click', function(e) {
        e.preventDefault();
        addQuestion(questionsContainer);
    });
    questionsContainer.on('click', '.removeAnswer', function(e) {
        e.preventDefault();
        var container = $(this).parent();
        var qid = container.attr('data-qid');
        var aid = container.attr('data-aid');
        questionsArray[qid].optionNum--;
        questionsArray[qid].answers.splice(aid,1);
        container.remove();
    });
    questionsContainer.on('click', '.addAnswerBtn', function(e) {
        e.preventDefault();
        var qid = $(this).attr('data-qid');
        var questionType = $('option:selected', $('#questionType-'+qid)).val();
        addAnswer(questionType, $('#answerSheet-'+qid), qid);
    });
    $('#quizForm').submit(function (e) {
        e.preventDefault();
        
        var actionurl = e.currentTarget.action;
        
        var i, j;
        for (i = 0; i < questionsArray.length; i++) {
            questionsArray[i].desc = questionsArray[i].desc.val();
            delete questionsArray[i].container;
            for (j = 0; j < questionsArray[i].answers.length; j++) {
                questionsArray[i].answers[j].desc = questionsArray[i].answers[j].desc.val(); 
                questionsArray[i].answers[j].checked = questionsArray[i].answers[j].checked.is(':checked');
            }
        }
		
        quizInfo.name = $('#quizName').val();
        quizInfo.code = $('#accessCode').val();
        quizInfo.style = $('option:selected', $('#style')).val();
        quizInfo.dialect = $('option:selected', $('#dialect')).val();
        quizInfo.audio = $('#audio').val();
        quizInfo.questions = questionsArray;
        
        var json = JSON.stringify(quizInfo);
        
        $.ajax({
                url: actionurl,
                type: 'post',
                dataType:'json',
                data: json,
                processData: false,
                contentType: "application/json",
                success: function(data) {
                    console.log(data);
                    window.location.href = "admin.php";
                }
        }).fail(function(data,txt,err) {
            console.log('Server: ' + data.responseText + '; Client: ' + txt + ')\n');
        });
    });
});