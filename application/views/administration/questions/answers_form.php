<form method="POST" action="" class="mainForm">
	<fieldset>
		<dl>
			<?php if(!empty($errors)): ?>
					<dd class="error">
						<strong>Errors:</strong><br />
						<?php foreach($errors as $e): ?>
									&nbsp;&nbsp;<img src="<?php echo site_url('img/bullet3.gif'); ?>" alt="" />&nbsp;<?php echo ucfirst($e) ?><br />
						<?php endforeach; ?>
					</dd>
			<?php endif; ?>

			<dd>
				<h3>Answer details</h3>
			</dd>

			<dd>
				<label for="title" class="long">Member</label>
				<label class="short" style="padding-top: 2px; width: 420px;"><?php echo h(st ($answer->username )) ?>
					<input type="hidden" name="member_id" value="<?php echo $answer->member_id ?>"/>
				</label>
			</dd>
			<dd>
				<label for="slug" class="long">Posted at</label>
				<input class="long" disabled="disabled" type="text" id="posted_at" value="<?php echo empty( $answer->posted_at )?date("d.m.Y h:m"):@date("d.m.Y h:m", $answer->posted_at) ?>" />
			</dd>
			<dd>
				<label for="idsubcategory" class="long">Host</label>
				<input class="long" disabled="disabled" type="text" id="ip" value="<?php echo @$answer->ip ?>" />
			</dd>
			<dd>
				<label for="title" class="long">Answer</label>
				<a href="/administration/questions/edit/<?php echo $answer->question_id ?>"><?php echo h(st( @$question->text )) ?></a>
			</dd>		
			<dd>
				<label for="title" class="long">Answer</label>
				<textarea id="text" name="text" class="noeditor long" rows="3" cols="15"><?php echo  h(st( @$answer->text )) ?></textarea>
			</dd>			
			<dd>
				<label for="active" class="long">Status</label>
					<select name="status" class="long validate">
						<option value="" DISABLED>- Please select -</option>
						<option value="1" <?php if(@$post['status'] == "active"){ ?>SELECTED<?php } ?>>Active</option>
						<option value="0" <?php if(@$post['status'] == "inactive"){ ?>SELECTED<?php } ?>>Inactive</option>
					</select>
			</dd>

			<dd class="submitDD">
				<a href="/administration/<?php echo $current_module?>/"><img src="<?php echo site_url('img/buttonCancel.gif'); ?>" alt="Cancel" title="Cancel" border="0" class="cancel" /></a>
				<input type="image" class="submit" src="<?php echo site_url('img/buttonSubmit.gif'); ?>" name="submit" />
			</dd>
		</dl>
	</fieldset>
</form>