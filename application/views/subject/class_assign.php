<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#list" data-toggle="tab"><i class="fas fa-list-ul"></i> <?=translate('assign'). ' ' . translate('list')?></a>
			</li>
<?php if (get_permission('subject_class_assign', 'is_add')): ?>
			<li>
				<a href="#create" data-toggle="tab"><i class="far fa-edit"></i> <?=translate('assign')?></a>
			</li>
        	<li>
				<a href="#extra_subject" data-toggle="tab"><i class="far fa-edit"></i> Extra Subject</a>
			</li>
<?php endif; ?>
		</ul>
		<div class="tab-content">
        	
			<div  id="list" class="tab-pane active">
				<table class="table table-bordered table-hover table-condensed mb-none tbr-top table-export">
					<thead>
						<tr>
							<th><?=translate('sl')?></th>
						<?php if (is_superadmin_loggedin()) { ?>
							<th><?=translate('branch')?></th>
						<?php } ?>
							<th><?=translate('class')?></th>
							<th><?=translate('section')?></th>
							<th><?=translate('subject')?></th>
							<th><?=translate('action')?></th>
						</tr>
					</thead>
					<tbody>
						<?php 	
							$count = 1;
							if (count($assignlist)){
								foreach ($assignlist as $row):
									?>
						<tr>
							<td><?php echo $count++;?></td>
						<?php if (is_superadmin_loggedin()) { ?>
							<td><?php echo $row['branch_name'];?></td>
						<?php } ?>
							<td><?php echo $row['class_name'];?></td>
							<td><?php echo $row['section_name'];?></td>
							<td class="text-dark"><?php echo $this->subject_model->get_subject_list($row['class_id'], $row['section_id']);?></td>
							<td>
							<?php if (get_permission('subject_class_assign', 'is_edit')): ?>
								<!-- update link -->
								<a href="javascript:void(0);" class="btn btn-circle btn-default icon" onclick="getClassAssignM(<?=$row['class_id']?>,<?=$row['section_id']?>)">
									<i class="fas fa-pen-nib"></i>
								</a>
							<?php endif; if (get_permission('subject_class_assign', 'is_delete')): ?>
								<!-- delete link -->
								<?php echo btn_delete('subject/class_assign_delete/'. $row['class_id'] . '/' . $row['section_id']);?>
							<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; }?>
					</tbody>
				</table>
			</div>
<?php if (get_permission('subject_class_assign', 'is_add')): ?>
			<div class="tab-pane" id="create">
				<?php echo form_open('subject/class_assign_save', array('class' => 'form-horizontal form-bordered frm-submit')); ?>
					<?php if (is_superadmin_loggedin()): ?>
						<div class="form-group">
							<label class="control-label col-md-3"><?=translate('branch')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<?php
									$arrayBranch = $this->app_lib->getSelectList('branch');
									echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' id='branch_id'
									data-width='100%' data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
								<span class="error"></span>
							</div>
						</div>
					<?php endif; ?>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('class')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								$arrayClass = $this->app_lib->getClass($branch_id);
								echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id' onchange='getSectionByClass(this.value,0)'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('section')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								$arraySection = $this->app_lib->getSections(set_value('class_id'));
								echo form_dropdown("section_id", $arraySection, set_value('section_id'), "class='form-control' id='section_id' 
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('subject')?> <span class="required">*</span></label>
						<div class="col-md-6 mb-md">
							<select name="subjects[]" class="form-control" data-plugin-selectTwo multiple id='subject_holder' data-width="100%"
							data-plugin-options='{"placeholder": "<?=translate('select_multiple_subject')?>"}'>
								<?php 
								if(!empty($branch_id)):
								$subjects = $this->db->get_where('subject', array('branch_id' => $branch_id))->result();
								foreach ($subjects as $subject):
								?>
								<option value="<?=$subject->id?>" <?=set_select('subjects[]', $subject->id)?>><?=html_escape($subject->name)?></option>
								<?php endforeach; endif;?>
							</select>
							<span class="error"></span>
						</div>
					</div>
					<footer class="panel-footer">
						<div class="row">
							<div class="col-md-offset-3 col-md-2">
								<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
									<i class="fas fa-plus-circle"></i> <?=translate('save')?>
								</button>
							</div>
						</div>
					</footer>
				<?php echo form_close();?>
			</div>
        	<div  id="extra_subject" class="tab-pane">
            <style>
            .add_extra_subject .form-group {
            border-bottom: none;
   			padding-bottom: 0px;
    		margin-bottom: 0px; 
            }
            
            </style>
            	<div class="col-md-5">
        		<section class="panel">
            	<header class="panel-heading">
                <h4 class="panel-title"><i class="far fa-edit"></i> Add Extra Subject</h4>
            </header>            
            	<?php echo form_open('subject/extra_subject_assign_save', array('id'=>'submit_extra_subject_form','class' => 'form-horizontal form-bordered frm-submit')); ?>
        			<div class="panel-body add_extra_subject" style="margin-left:15px;margin-right:15px;">
						<?php if (is_superadmin_loggedin()): ?>
							<div class="form-group">
						<label class=" control-label"><?=translate('branch')?> <span class="required">*</span></label>
                    	
						<?php
							$arrayBranch = $this->app_lib->getSelectList('branch');
                        	//print_r($arrayBranch);
							echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' id='extra_branch_id'
							data-width='100%' data-plugin-selectTwo data-minimum-results-for-search='Infinity'");
                        	
						?>
						<span class="error"></span>
                            <div id="branch_option" style="display:none;">
                            	<?php if(count($arrayBranch)){ 
                            		foreach($arrayBranch as $k => $r){
                            	?>
                            <option value="<?php print $k; ?>"><?php print $r; ?></option>
                            <?php } } ?>
                            </div>
					</div>
						<?php endif; ?>
						<div class="form-group">
						<label class=" control-label"><?=translate('class')?> <span class="required">*</span></label>
                   		 
						<?php
							$arrayClass = $this->app_lib->getClass($branch_id);
                        	
							echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='extra_class_id' onchange='getSectionByClassForExtraSubject(this.value,0)'
							data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
						?>
						<span class="error"></span>
                    	
					</div>
						<div class="form-group">
						<label class=" control-label"><?=translate('section')?> <span class="required">*</span></label>
                    	
						<?php
							$arraySection = $this->app_lib->getSections(set_value('class_id'));
							echo form_dropdown("section_id", $arraySection, set_value('section_id'), "class='form-control' id='extra_section_id' onchange='getStudentByClassForExtraSubject(this.value)'
							data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
						?>
						<span class="error"></span>
                    	
					</div>
                		<div class="form-group">
						<label class=" control-label">Student <span class="required">*</span></label>
                    	
						<select name="student_id" class="form-control select2-hidden-accessible" id="extra_student_id" onchange="" data-plugin-selecttwo="" data-width="100%" data-minimum-results-for-search="Infinity" tabindex="-1" aria-hidden="true">
                        <option value=''>Select Section First</option>
                    	</select>
						<span class="error"></span>
                    	
					</div>
						<div class="form-group mb-md">
						<label class=" control-label"><?=translate('class_teacher')?> <span class="required">*</span></label>
                    	
						<?php
							$arrayTeacher = $this->app_lib->getStaffList($branch_id, 3);
							echo form_dropdown("staff_id", $arrayTeacher, set_value('staff_id'), "class='form-control' id='staff_id'
							data-plugin-selectTwo data-width='100%' ");
						?>
						<span class="error"></span>
                    	
					</div>
            			<div class="form-group">
						<label class="control-label"><?=translate('subject')?> <span class="required">*</span></label>
						
							<select name="subjects[]" class="form-control" data-plugin-selectTwo multiple id='extra_subject_holder' data-width="100%"
							data-plugin-options='{"placeholder": "<?=translate('select_multiple_subject')?>"}'>
								<?php 
								if(!empty($branch_id)):
								$subjects = $this->db->get_where('subject', array('branch_id' => $branch_id))->result();
								foreach ($subjects as $subject):
								?>
								<option value="<?=$subject->id?>" <?=set_select('subjects[]', $subject->id)?>><?=html_escape($subject->name)?></option>
								<?php endforeach; endif;?>
							</select>
							<span class="error"></span>
						
					</div>
				<div class="panel-footer">
					<div class="row">
						<div class="col-md-12">
			                <button type="submit" name="save" id="submit_extra_subject" value="1" class="btn btn-default pull-right" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
			                    <i class="fas fa-plus-circle"></i> <?=translate('save') ?>
			                </button>
						</div>	
					</div>
				</div>
                    
                    
						
        			</div>
				<?php echo form_close();?>
            </section>
            </div>
            
            
            <div class="col-md-7">
		<section class="panel">
			<header class="panel-heading">
				<h4 class="panel-title"><i class="fas fa-list-ul"></i> Extra Subject Lists</h4>
			</header>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed mb-none ">
						<thead>
							<tr>
							    <th>#</th>
								<th><?=translate('branch')?></th>
								<th><?=translate('class_teacher')?></th>
								<th><?=translate('class')?></th>
								<th><?=translate('section')?></th>
                           		 <th><?=translate('student')?></th>
                            	<th><?=translate('subject')?></th>
								<th><?=translate('action')?></th>
							</tr>
						</thead>
						<tbody id="extra_subject_table_area">
							
								
						</tbody>
					</table>
				</div>
			</div>
		</section>
	</div>
        	</div>

        
        
        
        	</div>
<?php endif; ?>
		</div>
</section>

<?php if (get_permission('subject_class_assign', 'is_edit')): ?>
<div class="zoom-anim-dialog modal-block modal-block-primary mfp-hide extra_subject_edit" id="modal">
	<section class="panel">
		<header class="panel-heading">
			<h4 class="panel-title">
				<i class="far fa-edit"></i> <?php echo translate('edit_assign'); ?>
			</h4>
		</header>
		<?php echo form_open('subject/class_assign_edit', array('class' => 'frm-submit')); ?>
			<div class="panel-body">
				<input type="hidden" name="branch_id" id="ebranch_id" value="" />
				<input type="hidden" name="class_id" id="eclass_id" value="" />
				<input type="hidden" name="section_id" id="esection_id" value="" />
				<div class="form-group mt-mb mb-lg">
					<label class="control-label"><?=translate('subject')?> <span class="required">*</span></label>
					<select name="subjects[]" class="form-control" data-plugin-selectTwo multiple id='esubject_holder' data-width="100%"
					data-plugin-options='{ "placeholder": "<?=translate('select_branch_first')?>" }'>
					</select>
					<span class="error"></span>
				</div>
			</div>
			<footer class="panel-footer">
				<div class="row">
					<div class="col-md-12 text-right">
						<button type="submit" class="btn btn-default mr-xs" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
							<i class="fas fa-plus-circle"></i> <?=translate('update')?>
						</button>
						<button class="btn btn-default modal-dismiss"><?php echo translate('cancel'); ?></button>
					</div>
				</div>
			</footer>
		<?php echo form_close(); ?>
	</section>
</div>

<div class="zoom-anim-dialog modal-block modal-block-primary mfp-hide " id="extra_subject_edit">
	<section class="panel">
		<header class="panel-heading">
			<h4 class="panel-title">
				<i class="far fa-edit"></i> <?php echo translate('edit_assign'); ?>
			</h4>
		</header>
    	<style>
            .add_edit_extra_subject .form-group {
            border-bottom: none;
   			padding-bottom: 0px;
    		margin-bottom: 0px; 
            }
            
            </style>
		<?php echo form_open('subject/extra_subject_assign_save', array('id'=>'submit_extra_subject_form','class' => 'form-horizontal form-bordered frm-submit')); ?>
        			<div class="panel-body add_edit_extra_subject" style="margin-left:15px;margin-right:15px;">
						                   
                    <div class="form-group">
					<label class="control-label"><?=translate('subject')?> <span class="required">*</span></label>
					<select name="subjects[]" class="form-control" data-plugin-selectTwo multiple id='extra_subject_holder' data-width="100%"
					data-plugin-options='{ "placeholder": "<?=translate('select_branch_first')?>" }'>
					</select>
					<span class="error"></span>
				</div>
						
        			</div>
    				<footer class="panel-footer">
				<div class="row">
					<div class="col-md-12 text-right">
						<button type="submit" class="btn btn-default mr-xs" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
							<i class="fas fa-plus-circle"></i> <?=translate('update')?>
						</button>
						<button class="btn btn-default modal-dismiss"><?php echo translate('cancel'); ?></button>
					</div>
				</div>
			</footer>
				<?php echo form_close();?>
	</section>
</div>
<?php endif; ?>

<script type="text/javascript">

extraStudentList();

	$(document).ready(function () {
		$(document).on('change', '#branch_id', function() {
			var branchID = $(this).val();
			getClassByBranch(branchID);

			$.ajax({
				url: "<?=base_url('ajax/getDataByBranch')?>",
				type: 'POST',
				data: {
					table: 'subject',
					branch_id: branchID
				},
				success: function (data) {
					$('#subject_holder').html(data);
				}
			});
		});
	});


	$(document).ready(function () {
		$(document).on('change', '#extra_branch_id', function() {
			var branchID = $(this).val();
        
			getClassByBranchForExtraSubject(branchID);
			getStaffListRole(branchID, 3);

			$.ajax({
				url: "<?=base_url('ajax/getDataByBranch')?>",
				type: 'POST',
				data: {
					table: 'subject',
					branch_id: branchID
				},
				success: function (data) {
					$('#extra_subject_holder').html(data);
				}
			});
		});
	});



function getSectionByClassForExtraSubject(class_id, all=0, multi=0,section_id='') {
    if (class_id !== "") {
        $.ajax({
            url: base_url + 'ajax/getSectionByClass',
            type: 'POST',
            data: {
                class_id: class_id,
                all : all,
                multi : multi,
            	section_id : section_id
            },
            success: function (response) {
                $('#extra_section_id').html(response);
            }
        });
    }
}

function getStudentByClassForExtraSubject(section_id,student_id='') {
	var extra_class_id = $('#extra_class_id').val();
	
    if (section_id !== "") {
        $.ajax({
            url: base_url + 'ajax/getStudentByClassForExtraSubject',
            type: 'POST',
            data: {
                class_id: extra_class_id,
                section_id : section_id,
            	student_id:student_id
            },
            success: function (response) {
            	//alert(response);
                $('#extra_student_id').html(response);
            }
        });
    }
}

function getClassByBranchForExtraSubject(branch_id,class_id='') {
    $.ajax({
        url: base_url + 'ajax/getClassByBranch',
        type: 'POST',
        data:{ branch_id: branch_id, class_id: class_id },
        success: function (data){
            $('#extra_class_id').html(data);
        }
    });
    $('#section_id').html('');
    $('#section_id').append('<option value="">Select Class First</option>');
}

	// get leave approvel details
	function getAllocationTeacher(id) {
	    $.ajax({
	        url: base_url + 'classes/getAllocationTeacher',
	        type: 'POST',
	        data: {'id': id},
	        dataType: "html",
	        success: function (data) {
				$('#allocation').html(data);
				mfp_modal('#modal');
	        },
			complete: function () {
				$('.selecttwo').select2({
					theme: 'bootstrap',
					width: '100%',
					minimumResultsForSearch: 'Infinity'
				});
			}
	    });
	}
	
	function edit_extra_subject(id,branch_id,class_id,section_id,student_id,teacher_id,subject_id){
    	var branchID = branch_id;
    	var subject_id = subject_id;
	    $.ajax({
	        url: base_url + 'subject/edit_extra_subject',
	        type: 'POST',
	        data: {'id': id},
	        dataType: "html",
	        success: function (data) {
				$('.add_edit_extra_subject').prepend(data);
				
            	
        
			$.ajax({
				url: "<?=base_url('ajax/getDataByBranch')?>",
				type: 'POST',
				data: {
					table: 'subject',
					branch_id: branchID,
                	subject_id:subject_id
				},
				success: function (data) {
					$('#extra_subject_holder').html(data);
				}
			});
            
            mfp_modal('#extra_subject_edit');
            	
	        }
	    });
	
    
    
    
    }

	function delete_extra_subject(id){
    	var delete_url = base_url + 'subject/extra_subject_delete/'+id;
    
			swal({
				title: "Are You Sure?",
				text: "Do You Want To Delete This Information?",
				type: "warning",
				showCancelButton: true,
				confirmButtonClass: "btn btn-default swal2-btn-default",
				cancelButtonClass: "btn btn-default swal2-btn-default",
				confirmButtonText: "Yes, Continue",
				cancelButtonText: "Cancel",
				buttonsStyling: false,
				footer: "*Note : This data will be permanently deleted"
			}).then((result) => {
				if (result.value) {
					$.ajax({
						url: delete_url,
						type: "POST",
						success:function(data) {
							swal({
							title: "Deleted",
							text: "The information has been successfully deleted",
							buttonsStyling: false,
							showCloseButton: true,
							focusConfirm: false,
							confirmButtonClass: "btn btn-default swal2-btn-default",
							type: "success"
							}).then((result) => {
								if (result.value) {
									extraStudentList();
								}
							});
						}
					});
				}
			});
		   
   		 
	}
	function extraStudentList() {
     var branch_option = $('#branch_option').html();
     $('#extra_branch_id').html('');
     $('#extra_branch_id').append(branch_option);
    
     $('#extra_class_id').html('');
     $('#extra_class_id').append('<option value="">First Select The Branch</option>');
       
     $('#extra_section_id').html('');
     $('#extra_section_id').append('<option value="">Select Class First</option>');
    
     $('#extra_student_id').html('');
     $('#extra_student_id').append('<option value="">Select Section First</option>');
    
     $('#staff_id').html('');
     $('#staff_id').append('<option value="">First Select The Branch</option>');
    
    $('#extra_subject_holder').html('');
    
	    $.ajax({
	        url: base_url + 'subject/extra_assign_subject',
	        type: 'GET',
	        dataType: "html",
	        success: function (data) {
				$('#extra_subject_table_area').html(data);
				
	        }
	    });
	}
</script>