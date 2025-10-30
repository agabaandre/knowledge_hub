@if(isset($questions) && count($questions) > 0 && (settings()->show_quiz ?? false))
<!-- Quiz Trigger Button -->
<div class="quiz-trigger-wrapper text-left mb-4">
    <button id="showQuizBtn" class="btn btn-sm btn-primary-theme btn-quiz-trigger">
        <i class="fa fa-question-circle me-2"></i> Start Health Quiz
    </button>
</div>

<div class="quiz-wrapper" id="quizContainer" style="display: none;">
    <div class="quiz-container">
        <div class="quiz-header">
            <div class="quiz-header-content">
                <h3><i class="fa fa-graduation-cap me-2"></i>Health Knowledge Quiz</h3>
                <p class="quiz-subtitle">Test your knowledge about Africa's health data</p>
            </div>
            <button class="quiz-close-btn" onclick="toggleQuiz()" title="Close Quiz">
                <i class="fa fa-times"></i>
					</button>
        </div>

        <div class="questions-slide">

            <!-- Welcome Slide -->
            <div class="quiz-slide welcome-slide">
                <div class="quiz-slide-content">
                    <div class="welcome-icon">
                        <i class="fa fa-brain"></i>
                    </div>
                    <h3>Would you love to test your knowledge about Africa's health data?</h3>
                    <p>If so, we are delighted to present this quiz!</p>
                    <button class="quiz-start-btn" onclick="$('.questions-slide').slick('slickNext')">
                        <i class="fa fa-play me-2"></i>Start Quiz
                    </button>
                    <div class="quiz-info">
                        <small><i class="fa fa-info-circle me-1"></i>{{ count($questions) }} {{ count($questions) === 1 ? 'question' : 'questions' }} available</small>
                    </div>
				</div>
			</div>

		@php
$count = 0;
		@endphp
		@foreach($questions as $qn)
		@php
	$count++;
		@endphp
                @if(count($qn->answers) > 0)
                    <div class="quiz-slide question-slide">
                        <div class="quiz-slide-content">
                            <div class="question-header">
                                <span class="question-number">Question {{ $count }} of {{ count($questions) }}</span>
                                <div class="progress-indicator">
                                    <div class="progress-bar-fill" style="width: {{ ($count / count($questions)) * 100 }}%"></div>
                                </div>
                            </div>
                            
                            <h4 class="question-text">{{ $qn->question_text }}</h4>
                            
                            <div class="answers-container">
			    @foreach($qn->answers as $ans)
                                    <button type="button" 
                                            class="answer-option answer answer_{{$qn->id}}{{$ans->id}}" 
                                            onclick="markQuestion({{$qn->id}},{{$ans->id}})">
                                        <span class="answer-text">{{ $ans->answer_text }}</span>
                                    </button>
				@endforeach
			</div>
			  
                            <div class="question-navigation">
			   @if($count > 1)
                                    <button class="nav-btn prev-btn" onclick="$('.question_stats').hide();$('.questions-slide').slick('slickPrev')">
                                        <i class="fa fa-arrow-left me-2"></i>Previous Question
					</button>
				@endif

				@if($count < count($questions))
                                    <button class="nav-btn next-btn" onclick="$('.question_stats').hide();$('.questions-slide').slick('slickNext')">
                                        Next Question<i class="fa fa-arrow-right ms-2"></i>
				</button>
				@endif
			</div>
			</div>
		   </div>
		   @endif
		   @endforeach
		</div>

        <div class="question_stats"></div>
    </div>
</div>

<style>
.quiz-wrapper {
    margin-bottom: 2rem;
}

.quiz-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    border: 2px solid var(--theme-color-primary, #119A48);
}

.quiz-header {
    background: linear-gradient(135deg, var(--theme-color-primary, #119A48) 0%, color-mix(in srgb, var(--theme-color-primary, #119A48) 85%, black) 100%);
    color: white;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.quiz-header-content h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
}

.quiz-subtitle {
    margin: 0.25rem 0 0 0;
    opacity: 0.9;
    font-size: 0.95rem;
}

.quiz-close-btn {
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    border-radius: 8px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.quiz-close-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    transform: rotate(90deg);
}

.questions-slide {
    position: relative;
    min-height: 400px;
    padding: 2rem;
    background: white !important;
    width: 100%;
}

.questions-slide .slick-list,
.questions-slide .slick-track {
    background: white;
}

.questions-slide .quiz-slide {
    background: white;
    min-height: 400px;
}

.quiz-slide {
    padding: 1rem;
    background: white;
}

.welcome-slide {
    text-align: center;
    color: #333;
    background: white;
    padding: 2rem;
    border-radius: 12px;
}

.welcome-icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    color: var(--theme-color-primary, #119A48);
}

.welcome-slide h3 {
    color: #333;
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.welcome-slide p {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

.quiz-start-btn {
    background: white;
    color: var(--theme-color-primary, #119A48);
    border: none;
    border-radius: 12px;
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.quiz-start-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
    background: #f8f9fa;
}

.quiz-info {
    margin-top: 1.5rem;
    color: #666;
    font-size: 0.9rem;
}

.question-slide {
    color: #333;
    background: white;
    padding: 2rem;
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.question-number {
    font-weight: 600;
    font-size: 0.9rem;
    color: #666;
    background: #f8f9fa;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
}

.progress-indicator {
    flex: 1;
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    margin-left: 1rem;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: var(--theme-color-primary, #119A48);
    border-radius: 3px;
    transition: width 0.3s ease;
}

.question-text {
    color: #333;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 2rem;
    line-height: 1.4;
}

.answers-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

.answer-option {
    background: #f8f9fa;
    border: 2px solid #e2e8f0;
    color: #333;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    text-align: left;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
    font-weight: 500;
    width: 100%;
}

.answer-option:hover {
    background: #e9ecef;
    border-color: var(--theme-color-primary, #119A48);
    transform: translateX(8px);
    color: #333;
}

.answer-option.bg-success {
    background: #28a745 !important;
    border-color: #28a745 !important;
}

.answer-option.bg-danger {
    background: #dc3545 !important;
    border-color: #dc3545 !important;
}

.answer-text {
    display: block;
}

.question-navigation {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    margin-top: 2rem;
}

.nav-btn {
    background: white;
    color: var(--theme-color-primary, #119A48);
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.nav-btn:hover {
    background: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.prev-btn {
    margin-right: auto;
}

.next-btn {
    margin-left: auto;
}

.question_stats {
    background: #f8f9fa;
    padding: 1.5rem;
    border-top: 2px solid #e2e8f0;
    display: none;
}

.question_stats h3,
.question_stats h4 {
    color: #1e293b;
    margin-bottom: 1rem;
}

.question_stats .progress {
    margin-bottom: 1rem;
    height: 20px;
    border-radius: 10px;
}

/* Quiz Trigger Button */
.btn-quiz-trigger {
    background: linear-gradient(135deg, var(--theme-color-primary, #119A48) 0%, color-mix(in srgb, var(--theme-color-primary, #119A48) 85%, black) 100%);
    color: white;
    border: none;
    border-radius: 10px;
    padding: 0.5rem 1rem;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(17, 154, 72, 0.3);
}

.btn-quiz-trigger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(17, 154, 72, 0.4);
    color: white;
}

@media (max-width: 768px) {
    .quiz-header {
        padding: 1rem 1.5rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .quiz-header-content h3 {
        font-size: 1.25rem;
    }

    .questions-slide {
        padding: 1.5rem;
    }

    .question-text {
        font-size: 1.25rem;
    }

    .question-navigation {
        flex-direction: column;
    }

    .nav-btn {
        width: 100%;
    }
}
</style>

		<script>
function toggleQuiz() {
    const container = document.getElementById('quizContainer');
    const btn = document.getElementById('showQuizBtn');
    
    if (container.style.display === 'none' || !container.style.display || container.style.display === '') {
        container.style.display = 'block';
        if (btn) btn.style.display = 'none';
        
        // Initialize Slick slider immediately when quiz is shown
        setTimeout(function() {
            if ($('.questions-slide').length && typeof $.fn.slick !== 'undefined') {
                // Destroy existing slider if it exists
                if ($('.questions-slide').hasClass('slick-initialized')) {
                    $('.questions-slide').slick('unslick');
                }
                // Initialize slider
                $('.questions-slide').slick({
                    dots: false,
                    arrows: false,
                    infinite: false,
                    speed: 300,
                    fade: true,
                    cssEase: 'linear',
                    adaptiveHeight: true
                });
            }
        }, 50);
        
        // Scroll to quiz
        setTimeout(() => {
            container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 200);
    } else {
        container.style.display = 'none';
        if (btn) btn.style.display = 'block';
        
        // Destroy slider when hiding
        if ($('.questions-slide').hasClass('slick-initialized')) {
            $('.questions-slide').slick('unslick');
        }
    }
}
</script>

<script>
			function getCookie(name) {
				function escape(s) { return s.replace(/([.*+?\^$(){}|\[\]\/\\])/g, '\\$1'); }
				var match = document.cookie.match(RegExp('(?:^|;\\s*)' + escape(name) + '=([^;]*)'));
				return match ? match[1] : null;
			}

    function saveStats(question_id, answer_id) {
				let formData = new FormData();
        formData.append('_token', '<?php echo csrf_token(); ?>');
        formData.append('ans_id', answer_id);
        formData.append('qn_id', question_id);

				$.ajax({
            method: 'POST',
					processData: false,
                    contentType: false,
            url: `{{ route('quiz.savestat') }}`,
            data: formData,
            success: function(data) {
                console.log(data);
					}
				});
			}

    function markQuestion(question_id, answer_id) {
			    $('.answer').removeClass('bg-success').removeClass('bg-danger').removeClass('text-white');

				var qns = '<?php echo json_encode($questions->toArray()); ?>';
        const questions = JSON.parse(qns);

        const current_qn = questions.find((item) => item.id === parseInt(question_id));

        let all_answers = current_qn.responses.length;
        let right_answers = current_qn.right_answers;
        let wrong_answers = current_qn.wrong_answers;
				let increment_value = 0;

				const already_answered = getCookie(`answered_${current_qn.id}`);

        if (!already_answered)
            increment_value = 1;

        const current_ans = current_qn.answers.find((item) => item.id === parseInt(answer_id));
        const correct_ans = current_qn.answers.find((item) => item.is_correct === 1);

        const elem_class = `.answer_${question_id}${answer_id}`;
				const correct_class = `.answer_${question_id}${correct_ans.id}`;

        all_answers += increment_value;

        $(elem_class).html('<span class="answer-text">' + current_ans.answer_text + '</span>');
        $(correct_class).html('<span class="answer-text">' + correct_ans.answer_text + '</span>');

				const correct_icon = '<i class="fa fa-check text-white mr-2"></i> ';
        const wrong_icon = '<i class="fa fa-times text-white mr-2"></i> ';
				
        if (current_ans.id === correct_ans.id) {
					$(elem_class).addClass('bg-success text-white');
            $(elem_class).html(correct_icon + '<span class="answer-text">' + correct_ans.answer_text + '</span>');
            $(elem_class).effect("bounce", { times: 3 }, 300);

            right_answers += increment_value;
        } else {
					$(elem_class).addClass('bg-danger text-white');

            $(elem_class).html(wrong_icon + '<span class="answer-text">' + current_ans.answer_text + '</span>');
					
            right_answers += increment_value;
					
            $(elem_class).effect("shake", { times: 2 }, 300);
					
            setTimeout(() => {
                $(correct_class).html(correct_icon + '<span class="answer-text">' + correct_ans.answer_text + '</span>');
					    $(correct_class).addClass('bg-success text-white');
                $(correct_class).effect("bounce", { times: 5 }, 800);
            }, 1000);
        }

        var stats = `<h3 class="text-dark">Statistics from <strong>${all_answers}</strong> visitors</h3>
                     <h4 class="text-success"><strong>${((right_answers / all_answers) * 100).toFixed(1)}%</strong> were right</h4>`;
        stats += `<div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-success progress-bar-striped" role="progressbar" 
                         style="width: ${((right_answers / all_answers) * 100).toFixed(1)}%;" 
                         aria-valuenow="${((right_answers / all_answers) * 100).toFixed(1)}" 
                         aria-valuemin="0" aria-valuemax="100">
                        <small>${((right_answers / all_answers) * 100).toFixed(1)}%</small>
                    </div>
                  </div>`;
        stats += `<h4 class="text-danger"><strong>${((wrong_answers / all_answers) * 100).toFixed(1)}%</strong> were wrong</h4>`;
        stats += `<div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-danger progress-bar-striped" role="progressbar" 
                         style="width: ${((wrong_answers / all_answers) * 100).toFixed(1)}%;" 
                         aria-valuenow="${((wrong_answers / all_answers) * 100).toFixed(1)}" 
                         aria-valuemin="0" aria-valuemax="100">
                        <small>${((wrong_answers / all_answers) * 100).toFixed(1)}%</small>
                    </div>
                  </div>`;
        
        if (correct_ans.answer_explanation) {
            stats += `<h4 class="text-primary">More about the answer</h4>
                      <p class="text-muted">${correct_ans.answer_explanation}</p>`;
        }

        $('.question_stats').html(stats).show().css('display', 'block');

        if (increment_value > 0)
            saveStats(question_id, answer_id);
    }

    // Initialize Slick slider when quiz is shown
    $(document).ready(function() {
        // Show quiz button handler - toggleQuiz will handle Slick initialization
        $('#showQuizBtn').on('click', function() {
            toggleQuiz();
        });
    });
		</script>
@endif