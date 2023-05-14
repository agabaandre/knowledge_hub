
<div class="col-lg-12  py-3 px-4 rounded m-2 questions-slide" 
		style="background-color:#03343b; background-image:url(<?php echo asset('frontend/img/landing-bg.png') ?>); background-repeat:repeat;">

			<div class="card bg-transparent border-white">
				<div class="card-body text-white">
					<h3 class="text-white">Would you love to test your knowledge about Africa's health data?</h3>
					<h5 class="text-gray">If so, we are delighted to present this quiz!</h5>
					<button class="answer-pill bg-success text-white" onclick="$('.questions-slide').slick('slickNext')">
					Start Quiz
					</button>
				</div>
			</div>

		@php
			$count = 0;
		@endphp
		@foreach($questions as $qn)
		@php
			$count++;
		@endphp
		<!--quiz slide-->
		<div class="card bg-transparent border-white">
			<div class="card-body text-white">
			<h4 class="text-white">{{$count}}.{{$qn->question_text}}</h4>
			<div class="row d-flex">
				
			    @foreach($qn->answers as $ans)
				 <span class="answer-pill px-4 answer answer_{{$qn->id}}{{$ans->id}}" onclick="markQuestion({{$qn->id}},{{$ans->id}})">{{$ans->answer_text}}</span>
				@endforeach
			</div>
			@if($count>1)
			<div class="d-flex justify-content-end">
				<button class="slide-pill bg-dark text-white" onclick="$('.question_stats').hide();$('.questions-slide').slick('slickPrev')">
					Prev Question
				</button>
				@if($count < count($questions))
				<button class="slide-pill bg-success text-white" onclick="$('.question_stats').hide();$('.questions-slide').slick('slickNext')">
					Next Question
				</button>
				@endif
			</div>
			@endif
			</div>
		   </div>
		   <!--- end quiz slide -->
		   @endforeach
		   <div class="question_stats py-2 px-2"></div>
		</div>

		<script>

			function getCookie(name) {
				function escape(s) { return s.replace(/([.*+?\^$(){}|\[\]\/\\])/g, '\\$1'); }
				var match = document.cookie.match(RegExp('(?:^|;\\s*)' + escape(name) + '=([^;]*)'));
				return match ? match[1] : null;
			}

			function saveStats(question_id,answer_id){


				let formData = new FormData();
				formData.append('_token','<?php echo  csrf_token() ; ?>');
				formData.append('ans_id',answer_id);
				formData.append('qn_id',question_id);

				const token = '<?php echo  csrf_token() ; ?>'

				$.ajax({
					method:'POST',
					processData: false,
                    contentType: false,
					url:`{{ route('quiz.savestat') }}`,//?qn_id=${question_id}&ans_id=${answer_id}&_token=${token}
					data:formData, 
					success:function(data){
						console.log(data)
					}
				});
			}

		   function markQuestion(question_id,answer_id){

			    $('.answer').removeClass('bg-success').removeClass('bg-danger').removeClass('text-white');

				const questions   = JSON.parse('<?php echo json_encode($questions); ?>');
				const current_qn  = questions.find((item)=>item.id === parseInt(question_id));

				let all_answers    = current_qn.responses.length;
				let right_answers  = current_qn.right_answers;
				let wrong_answers  = current_qn.wrong_answers;
				let increment_value = 0;

				const already_answered = getCookie(`answered_${current_qn.id}`);

				if(!already_answered)
				increment_value=1;

				const current_ans = current_qn.answers.find( (item)=>item.id === parseInt(answer_id));
				const correct_ans = current_qn.answers.find( (item)=>item.is_correct === 1);

				const elem_class    = `.answer_${question_id}${answer_id}`;
				const correct_class = `.answer_${question_id}${correct_ans.id}`;

				
				all_answers+= increment_value;
				
				$(elem_class).html(current_ans.answer_text);
				$(correct_class).html(correct_ans.answer_text)

				const correct_icon = '<i class="fa fa-check text-white mr-2"></i> ';
				const wrong_icon   = '<i class="fa fa-times text-white mr-2"></i> ';

				
				if(current_ans.id === correct_ans.id){

					$(elem_class).addClass('bg-success text-white');
					$(elem_class).html(correct_icon+correct_ans.answer_text);
					$(elem_class).effect("bounce", { times:3 }, 300);

					//increment right answers
					right_answers+=increment_value;

				}
				else{

					$(elem_class).addClass('bg-danger text-white');

					$(elem_class).html(wrong_icon + current_ans.answer_text);

					//increment wrong answers
					right_answers+= increment_value;
					
					
					$(elem_class).effect("shake", { times:2 }, 300);
					
					setTimeout(()=>{
						
						$(correct_class).html(correct_icon + correct_ans.answer_text);
					    $(correct_class).addClass('bg-success text-white');
						$(correct_class).effect("bounce", { times:5 }, 800);
					
					},1000);

				}


				var stats = `<h3 class="text-white">Response Statistics:</h3><h4 class="text-success"><strong>${((right_answers/all_answers)*100).toFixed(1)}%</strong> were right</h4>`;
				    stats += `<h4 class="text-danger"><strong>${((wrong_answers/all_answers)*100).toFixed(1)}%</strong> were wrong</h4>`

				$('.question_stats').html(stats).show();


				if(increment_value>0)
				saveStats(question_id,answer_id);

			}
		</script>