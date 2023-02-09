<div class="row">

	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left"><?php echo $title; ?></h3>
			 <hr>
			<a href="#create-quize-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Add New Question</a>
		</div>

		<?php include 'includes/create-modal.php'; ?>

		<div class="card-body text-left">
			<table class="table">
				<thead>
					<tr>
						<th scope="col">Question ID</th>
						<th scope="col">Question Text</th>
						<th scope="col">Answers</th>
					</tr>
				</thead>
				<tbody>
					<!-- For each question in questions -->
					<?php foreach ($questions as $question) : ?>
						<tr>
							<td><?php echo $question['id']; ?></td>
							<td><?php echo $question['question_text']; ?></td>
							<td>
								<ul style="list-style: none;">
									<?php
									$answers = explode(',', $question['answers']);
									foreach ($answers as $answer) :
										$answer_parts = explode(':', $answer);
										$answer_id = $answer_parts[0];
										$answer_text = $answer_parts[1];
										$answer_is_correct = $answer_parts[2];
									?>
										<li>
											<input type="radio" name="answer" value="<?php echo $answer_id; ?>" id="answer-<?php echo $answer_id; ?>">
											<label for="answer-<?php echo $answer_id; ?>"><?php echo $answer_text; ?>
												<?php if ($answer_is_correct) : ?>
													<span style="color:green">(Correct Answer)</span>
												<?php endif; ?>
											</label>
										</li>
									<?php endforeach; ?>
								</ul>
							</td>
							<td>
								<a href="#edit-quize-modal" data-toggle="modal" class="btn btn-sm btn-primary edit-quize" data-id="<?php echo $question['id']; ?>" data-question="<?php echo $question['question_text']; ?>" data-answers="<?php echo $question['answers']; ?>"><i class="fa fa-edit"></i> Edit</a>
								<a href="javascript:void(0);" onclick="openDeleteModal(<?php echo $question['id']; ?>)" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Delete</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<?php include 'includes/delete-modal.php'; ?>
			<?php include 'includes/update-modal.php'; ?>

		</div>
	</div>

</div>