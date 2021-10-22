<?php $widget = (is_superadmin_loggedin() ? 2 : 3); ?>
<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <?php echo form_open('exam/CBSC_marksheet', array('class' => 'validate')); ?>
            <header class="panel-heading">
                <h4 class="panel-title"><?=translate('select_ground')?></h4>
            </header>
            <div class="panel-body">
                <div class="row mb-sm">
                    <?php if (is_superadmin_loggedin() ): ?>
                    <div class="col-md-3 mb-sm">
                        <div class="form-group">
                            <label class="control-label"><?=translate('branch')?> <span
                                    class="required">*</span></label>
                            <?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' id='branch_id' required
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
							?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-<?=$widget?> mb-sm">
                        <div class="form-group">
                            <label class="control-label"><?=translate('academic_year')?> <span
                                    class="required">*</span></label>
                            <?php
								$arrayYear = array("" => translate('select'));
								$years = $this->db->get('schoolyear')->result();
								foreach ($years as $year){
									$arrayYear[$year->id] = $year->school_year;
								}
								echo form_dropdown("session_id", $arrayYear, set_value('session_id', get_session_id()), "class='form-control' required
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
                        </div>
                    </div>
                    <div class="col-md-3 mb-sm">
                        <div class="form-group">
                            <label class="control-label"><?=translate('exam')?> <span class="required">*</span></label>
                            <select class="form-control" id="multiple_exam_id" name="exam_id[]" multiple="multiple"
                                required data-plugin-selectTwo data-width='100%'>
                                <!-- <option value=""><?=translate('select_branch_first')?> -->
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 mb-sm">
                        <div class="form-group">
                            <label class="control-label"><?=translate('class')?> <span class="required">*</span></label>
                            <?php
								$arrayClass = $this->app_lib->getClass($branch_id);
								echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id' onchange='getSectionByClass(this.value,0)'
								required data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
                        </div>
                    </div>

                    <div class="col-md-<?=$widget?>">
                        <div class="form-group">
                            <label class="control-label"><?=translate('section')?> <span
                                    class="required">*</span></label>
                            <?php
								$arraySection = $this->app_lib->getSections(set_value('class_id'), true);
								echo form_dropdown("section_id", $arraySection, set_value('section_id'), "class='form-control' id='section_id' required
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-md-offset-10 col-md-2">
                        <button type="submit" name="submit" value="search" class="btn btn-default btn-block"><i
                                class="fas fa-filter"></i> <?=translate('filter')?></button>
                    </div>
                </div>
            </div>
            <?php echo form_close();?>
        </section>

        <?php if (isset($student)): ?>
        <section class="panel appear-animation" data-appear-animation="<?php echo $global_config['animations']?>"
            data-appear-animation-delay="100">
            <?php echo form_open('exam/CBSCprint', array('class' => 'printIn')); ?>
            <input type="hidden" name="exam_id" value="<?=set_value('exam_id')?>">
            <input type="hidden" name="session_id" value="<?=set_value('session_id')?>">
            <header class="panel-heading">
                <h4 class="panel-title">
                    <i class="fas fa-users"></i> <?=translate('student_list')?>
                </h4>
                <div class="panel-btn">
                    <button type="submit" class="btn btn-default btn-circle"
                        data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
                        <i class="fas fa-print"></i> <?=translate('generate')?>
                    </button>
                </div>
            </header>
            <div class="panel-body">
                <div class="row mb-lg">
                    <div class="col-md-3">
                        <div class="checkbox-replace">
                            <label class="i-checks">
                                <input type="checkbox" name="attendance" value="true" checked=""><i></i> Print
                                Attendance
                            </label>
                        </div>
                        <div class="checkbox-replace mt-xs">
                            <label class="i-checks">
                                <input type="checkbox" name="grade_scale" value="true" checked=""><i></i> Print Grade
                                Scale
                            </label>
                        </div>
                        <div class="form-group mt-xs">
                            <label class="control-label"><?=translate('print_date')?></label>
                            <input type="text" name="print_date" data-plugin-datepicker
                                data-plugin-options='{ "todayHighlight" : true }' class="form-control"
                                autocomplete="off" value="<?=date('Y-m-d')?>">
                        </div>
                    </div>
                </div>

                <div class="table-responsive mt-sm mb-md">
                    <table class="table table-bordered table-hover table-condensed mb-none">
                        <thead class="text-weight-bold">
                            <tr>
                                <td><?=translate('sl')?></td>
                                <th>
                                    <div class="checkbox-replace">
                                        <label class="i-checks" data-toggle="tooltip"
                                            data-original-title="Print Show / Hidden">
                                            <input type="checkbox" name="select-all" id="selectAllchkbox"> <i></i>
                                        </label>
                                    </div>
                                </th>
                                <td><?=translate('student_name')?></td>
                                <td><?=translate('category')?></td>
                                <td><?=translate('register_no')?></td>
                                <td><?=translate('roll')?></td>
                                <td><?=translate('mobile_no')?></td>
                                <td><?=translate('remarks')?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
								$count = 1;
								if (count($student)){
								foreach ($student as $row):
									?>
                            <tr>
                                <td><?=$count++?></td>
                                <td class="hidden-print checked-area hidden-print" width="30">
                                    <div class="checkbox-replace">
                                        <label class="i-checks"><input type="checkbox" name="student_id[]"
                                                value="<?=$row['id']?>"><i></i></label>
                                    </div>
                                </td>
                                <td><?=$row['first_name'] . " " . $row['last_name']?></td>
                                <td><?=$row['category']?></td>
                                <td><?=$row['register_no']?></td>
                                <td><?=$row['roll']?></td>
                                <td><?=$row['mobileno']?></td>
                                <td class="min-w-sm">
                                    <div class="form-group">
                                        <input type="text" class="form-control" autocomplete="off" name="remarks[]"
                                            value="" />
                                        <span class="error"></span>
                                    </div>
                                </td>
                            </tr>
                            <?php 
								endforeach; 
							}else{
								echo '<tr><td colspan="8"><h5 class="text-danger text-center">' . translate('no_information_available') . '</td></tr>';
							}
							?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php echo form_close(); ?>
        </section>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {

    $('#branch_id').on("change", function() {
        var branchID = $(this).val();
        getClassByBranch(branchID);
        getExamsByBranch(branchID);
    });

    $('form.printIn').on('submit', function(e) {
        e.preventDefault();
        var btn = $(this).find('[type="submit"]');
        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: $(this).serialize(),
            dataType: 'html',
            beforeSend: function() {
                btn.button('loading');
            },
            success: function(data) {
                fn_printElem(data, true);
            },
            error: function() {
                btn.button('reset');
                alert("An error occured, please try again");
            },
            complete: function() {
                btn.button('reset');
            }
        });
    });
});
</script>